<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    // ===== Utilidad: detectar admin =====
    // Ajusta según tu implementación: campo 'role', Spatie, etc.
    private function isAdmin($user): bool
    {
        return (bool) data_get($user, 'is_admin', false);
    }

    // GET /api/orders -> lista mis órdenes o todas si es admin (paginado + filtros)
    public function index(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = $this->isAdmin($user);

        $q = Order::with([
            'user:id,name,email',
            'job:id',
            'cupon:id,code,discount_type,discount_value,min_order_total,max_uses,starts_at,expires_at,is_active',
            'items:id,order_id,product_id,quantity,unit_price,line_total',
            'items.product:id,name,price',
        ]);

        if ($isAdmin) {
            // Admin puede ver todas; opcionalmente filtrar por user_id
            if ($request->filled('user_id')) {
                $q->where('user_id', (int)$request->input('user_id'));
            }
        } else {
            // Usuario normal: solo sus órdenes
            $q->where('user_id', $user->id);
        }

        // Filtros opcionales
        if ($request->filled('status')) {
            $q->where('status', (bool)$request->boolean('status'));
        }
        if ($request->filled('from')) {
            $from = Carbon::parse($request->input('from'))->startOfDay();
            $q->where('created_at', '>=', $from);
        }
        if ($request->filled('to')) {
            $to = Carbon::parse($request->input('to'))->endOfDay();
            $q->where('created_at', '<=', $to);
        }

        return response()->json($q->orderByDesc('created_at')->paginate(15), 200);
    }

    // GET /api/orders/{id}
    public function show($id)
{
    auth()->shouldUse('api');
    $user = auth('api')->user();
    if (!$user) return response()->json(['message' => 'No autenticado'], 401);

    $isAdmin = $this->isAdmin($user);

    if ($isAdmin) {
        // Admin puede ver cualquier orden
        $order = Order::with([
            'user:id,name,email',
            'job:id',
            'cupon:id,code,discount_type,discount_value,min_order_total,max_uses,starts_at,expires_at,is_active',
            'items:id,order_id,product_id,quantity,unit_price,line_total',
            'items.product:id,name,price',
        ])->find($id);

        if (!$order) return response()->json(['message' => 'Orden no encontrada'], 404);
        return response()->json($order, 200);
    }

        // Usuario normal: buscar solo entre sus órdenes
        $order = Order::with([
            'user:id,name,email',
            'job:id',
            'cupon:id,code,discount_type,discount_value,min_order_total,max_uses,starts_at,expires_at,is_active',
            'items:id,order_id,product_id,quantity,unit_price,line_total',
            'items.product:id,name,price',
        ])
        ->where('id', $id)
        ->where('user_id', $user->id)
        ->first();

        if ($order) {
            return response()->json($order, 200);
        }

        // No encontró esa orden entre las suyas: ver si no tiene ninguna orden
        $hasAny = Order::where('user_id', $user->id)->exists();
        if (!$hasAny) {
            return response()->json(['message' => 'No tienes órdenes actualmente'], 404);
        }

        // Tiene órdenes, pero no esta específica
        return response()->json(['message' => 'No autorizado'], 403);
    }

    // POST /api/orders
    public function store(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        $userId = $user->id;

        $validated = $request->validate([
            'payment_method' => ['nullable','string','max:50'],
            'notes'          => ['nullable','string'],
            'status'         => ['sometimes','boolean'],
            'ordered_at'     => ['sometimes','date'],
            'job_id'         => ['nullable','integer','exists:jobs,id'],
            'cupon_id'       => ['nullable','integer','exists:cupons,id'],
            'tax_total'      => ['sometimes','numeric','min:0'],

            'items'              => ['required','array','min:1'],
            'items.*.product_id' => ['required','integer','exists:products,id'],
            'items.*.quantity'   => ['required','integer','min:1'],
            'items.*.unit_price' => ['nullable','numeric','min:0'],
        ]);

        return DB::transaction(function () use ($validated, $userId) {
            [$lines, $subtotal] = $this->buildLinesAndSubtotal($validated['items']);

            $cuponId  = $validated['cupon_id'] ?? null;
            $discount = $this->computeDiscount($cuponId, $subtotal);
            $tax      = (float)($validated['tax_total'] ?? 0);
            $total    = max(round($subtotal - $discount + $tax, 2), 0);

            $order = Order::create([
                'user_id'        => $userId,
                'job_id'         => $validated['job_id'] ?? null,
                'cupon_id'       => $cuponId,
                'payment_method' => $validated['payment_method'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'status'         => $validated['status'] ?? true,
                'ordered_at'     => $validated['ordered_at'] ?? now(),
                'discount_total' => $discount,
                'tax_total'      => $tax,
                'total_amount'   => $total,
            ]);

            $rows = array_map(fn($l) => [
                'order_id'   => $order->id,
                'product_id' => $l['product_id'],
                'quantity'   => $l['quantity'],
                'unit_price' => $l['unit_price'],
                'line_total' => $l['line_total'],
                'created_at' => now(),
                'updated_at' => now(),
            ], $lines);
            OrderItem::insert($rows);

            $order->load([
                'user:id,name,email',
                'job:id',
                'cupon:id,code,discount_type,discount_value',
                'items:id,order_id,product_id,quantity,unit_price,line_total',
                'items.product:id,name,price',
            ]);

            return response()->json($order, 201);
        });
    }

    // PUT /api/orders/{id}
    public function update(Request $request, $id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = $this->isAdmin($user);

        $order = Order::with('items')->find($id);
        if (!$order) return response()->json(['message' => 'Orden no encontrada'], 404);

        // No admin solo puede modificar su propia orden
        if (!$isAdmin && $order->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'payment_method' => ['sometimes','nullable','string','max:50'],
            'notes'          => ['sometimes','nullable','string'],
            'status'         => ['sometimes','boolean'],
            'ordered_at'     => ['sometimes','date'],
            'job_id'         => ['sometimes','nullable','integer','exists:jobs,id'],
            'cupon_id'       => ['sometimes','nullable','integer','exists:cupons,id'],
            'tax_total'      => ['sometimes','numeric','min:0'],

            'items'              => ['sometimes','array','min:1'],
            'items.*.product_id' => ['required_with:items','integer','exists:products,id'],
            'items.*.quantity'   => ['required_with:items','integer','min:1'],
            'items.*.unit_price' => ['nullable','numeric','min:0'],
        ]);

        return DB::transaction(function () use ($validated, $order) {
            if (array_key_exists('items', $validated)) {
                [$lines, $subtotal] = $this->buildLinesAndSubtotal($validated['items']);

                $cuponId  = $validated['cupon_id'] ?? $order->cupon_id;
                $discount = $this->computeDiscount($cuponId, $subtotal);
                $tax      = (float)($validated['tax_total'] ?? $order->tax_total ?? 0);
                $total    = max(round($subtotal - $discount + $tax, 2), 0);

                $order->update([
                    'payment_method' => $validated['payment_method'] ?? $order->payment_method,
                    'notes'          => array_key_exists('notes', $validated)   ? $validated['notes']   : $order->notes,
                    'status'         => array_key_exists('status', $validated)  ? $validated['status']  : $order->status,
                    'ordered_at'     => $validated['ordered_at'] ?? $order->ordered_at,
                    'job_id'         => array_key_exists('job_id', $validated)  ? $validated['job_id']  : $order->job_id,
                    'cupon_id'       => $cuponId,
                    'discount_total' => $discount,
                    'tax_total'      => $tax,
                    'total_amount'   => $total,
                ]);

                $order->items()->delete();
                $rows = array_map(fn($l) => [
                    'order_id'   => $order->id,
                    'product_id' => $l['product_id'],
                    'quantity'   => $l['quantity'],
                    'unit_price' => $l['unit_price'],
                    'line_total' => $l['line_total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $lines);
                OrderItem::insert($rows);

            } else {
                $payment_method = $validated['payment_method'] ?? $order->payment_method;
                $notes          = array_key_exists('notes', $validated)   ? $validated['notes']   : $order->notes;
                $status         = array_key_exists('status', $validated)  ? $validated['status']  : $order->status;
                $ordered_at     = $validated['ordered_at'] ?? $order->ordered_at;
                $job_id         = array_key_exists('job_id', $validated)  ? $validated['job_id']  : $order->job_id;
                $cupon_id       = array_key_exists('cupon_id', $validated)? $validated['cupon_id']: $order->cupon_id;
                $tax_total      = array_key_exists('tax_total', $validated)? (float)$validated['tax_total'] : (float)$order->tax_total;

                $subtotal = (float)$order->items()->sum('line_total');
                $discount = $this->computeDiscount($cupon_id, $subtotal);
                $total    = max(round($subtotal - $discount + $tax_total, 2), 0);

                $order->update([
                    'payment_method' => $payment_method,
                    'notes'          => $notes,
                    'status'         => $status,
                    'ordered_at'     => $ordered_at,
                    'job_id'         => $job_id,
                    'cupon_id'       => $cupon_id,
                    'discount_total' => $discount,
                    'tax_total'      => $tax_total,
                    'total_amount'   => $total,
                ]);
            }

            $order->load([
                'user:id,name,email',
                'job:id',
                'cupon:id,code,discount_type,discount_value',
                'items:id,order_id,product_id,quantity,unit_price,line_total',
                'items.product:id,name,price',
            ]);

            return response()->json($order, 200);
        });
    }

    // DELETE /api/orders/{id}
    public function destroy($id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = $this->isAdmin($user);

        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Orden no encontrada'], 404);

        // No admin solo puede borrar su orden
        if (!$isAdmin && $order->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return DB::transaction(function () use ($order) {
            $order->items()->delete(); // si no tienes ON DELETE CASCADE
            $order->delete();
            return response()->json(null, 204);
        });
    }

    // ================== Helpers ==================

    private function buildLinesAndSubtotal(array $items): array
    {
        $lines = [];
        $subtotal = 0.0;

        foreach ($items as $row) {
            $p = Product::findOrFail($row['product_id']);
            $unit = array_key_exists('unit_price', $row) && $row['unit_price'] !== null
                ? (float)$row['unit_price']
                : (float)$p->price;

            $qty  = (int)$row['quantity'];
            $line = round($unit * $qty, 2);

            $lines[] = [
                'product_id' => $p->id,
                'quantity'   => $qty,
                'unit_price' => $unit,
                'line_total' => $line,
            ];
            $subtotal += $line;
        }
        return [$lines, $subtotal];
    }

    private function computeDiscount(?int $cuponId, float $subtotal): float
    {
        if (!$cuponId) return 0.0;
        $cup = Cupon::find($cuponId);
        if (!$cup) return 0.0;

        $now = Carbon::now();
        $isActive = (bool)$cup->is_active;
        $inWindow = (!$cup->starts_at || $now->gte($cup->starts_at)) &&
                    (!$cup->expires_at || $now->lte($cup->expires_at));
        $minOk    = (!$cup->min_order_total) || ($subtotal >= (float)$cup->min_order_total);

        $usageOk  = true;
        if (!empty($cup->max_uses)) {
            // Si quieres contar solo órdenes efectivas, filtra por estado 'paid'
            $currentUses = Order::where('cupon_id', $cup->id)->count();
            $usageOk = $currentUses < (int)$cup->max_uses;
        }

        if (!($isActive && $inWindow && $minOk && $usageOk)) return 0.0;

        if ($cup->discount_type === 'percent') {
            $percent = max(0, min(100, (float)$cup->discount_value));
            $discount = round($subtotal * ($percent / 100), 2);
        } else {
            $discount = round((float)$cup->discount_value, 2);
        }

        return min($discount, $subtotal);
    }
}