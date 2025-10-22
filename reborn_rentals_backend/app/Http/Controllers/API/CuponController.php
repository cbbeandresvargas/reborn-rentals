<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;
use Carbon\Carbon;

class CuponController extends Controller
{
    /**
     * GET /api/coupons
     * Público: lista de cupones (paginada) con filtros opcionales.
     * ?active=1           -> solo activos
     * ?valid_now=1        -> dentro de la ventana de fechas y activos
     * ?code=ABC           -> filtra por código exacto
     */
    public function index(Request $request)
    {
        $q = Cupon::query();

        if ($request->filled('active')) {
            $q->where('is_active', (bool)$request->boolean('active'));
        }

        if ($request->filled('code')) {
            $q->where('code', $request->input('code'));
        }

        if ($request->filled('valid_now')) {
            $now = Carbon::now();
            $q->where(function ($w) use ($now) {
                $w->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($w) use ($now) {
                $w->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->where('is_active', true);
        }

        $coupons = $q->orderByDesc('created_at')->paginate(15);
        return response()->json($coupons, 200);
    }

    /**
     * POST /api/coupon
     * Solo ADMIN.
     */
    public function store(Request $request)
    {
        // Autenticación y autorización admin
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:50', 'unique:cupons,code'],
            'discount_type'   => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value'  => ['required', 'numeric', 'min:0'],
            'max_uses'        => ['nullable', 'integer', 'min:1'],
            'min_order_total' => ['nullable', 'numeric', 'min:0'],
            'starts_at'       => ['nullable', 'date'],
            'expires_at'      => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'       => ['sometimes', 'boolean'],
        ]);

        $cupon = Cupon::create($validated);

        return response()->json($cupon, 201);
    }

    /**
     * GET /api/coupon/{id}
     * Público.
     */
    public function show($id)
    {
        $cupon = Cupon::find($id);
        if (!$cupon) {
            return response()->json(['message' => 'Cupón no encontrado'], 404);
        }

        return response()->json($cupon, 200);
    }

    /**
     * PUT /api/coupon/{id}
     * Solo ADMIN.
     */
    public function update(Request $request, $id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $cupon = Cupon::find($id);
        if (!$cupon) {
            return response()->json(['message' => 'Cupón no encontrado'], 404);
        }

        $validated = $request->validate([
            'code'            => ['sometimes', 'string', 'max:50', Rule::unique('cupons', 'code')->ignore($cupon->id)],
            'discount_type'   => ['sometimes', Rule::in(['percent', 'fixed'])],
            'discount_value'  => ['sometimes', 'numeric', 'min:0'],
            'max_uses'        => ['sometimes', 'nullable', 'integer', 'min:1'],
            'min_order_total' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'starts_at'       => ['sometimes', 'nullable', 'date'],
            'expires_at'      => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'       => ['sometimes', 'boolean'],
        ]);

        $cupon->update($validated);

        return response()->json($cupon->fresh(), 200);
    }

    /**
     * DELETE /api/coupon/{id}
     * Solo ADMIN.
     */
    public function destroy($id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $cupon = Cupon::find($id);
        if (!$cupon) {
            return response()->json(['message' => 'Cupón no encontrado'], 404);
        }

        $cupon->delete();
        return response()->json(null, 204);
    }
}