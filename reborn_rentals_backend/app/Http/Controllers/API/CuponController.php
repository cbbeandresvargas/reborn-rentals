<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Coupon",
 *   @OA\Property(property="id", type="integer", example=3),
 *   @OA\Property(property="code", type="string", example="BIENVENIDO10"),
 *   @OA\Property(property="discount_type", type="string", enum={"percent","fixed"}, example="percent"),
 *   @OA\Property(property="discount_value", type="number", format="float", example=10),
 *   @OA\Property(property="max_uses", type="integer", nullable=true, example=100),
 *   @OA\Property(property="min_order_total", type="number", format="float", nullable=true, example=50),
 *   @OA\Property(property="starts_at", type="string", format="date-time", nullable=true, example="2025-10-01T00:00:00Z"),
 *   @OA\Property(property="expires_at", type="string", format="date-time", nullable=true, example="2025-12-31T23:59:59Z"),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *   name="Coupons",
 *   description="Gestión de cupones"
 * )
 */
class CuponController extends Controller
{
    /**
     * GET /api/coupons
     * Público: lista de cupones (paginada) con filtros opcionales.
     * ?active=1           -> solo activos
     * ?valid_now=1        -> dentro de la ventana de fechas y activos
     * ?code=ABC           -> filtra por código exacto
     *
     * @OA\Get(
     *   path="/api/coupons",
     *   tags={"Coupons"},
     *   summary="Listar cupones (público, con filtros)",
     *   @OA\Parameter(
     *     name="active", in="query", required=false,
     *     description="Filtrar por activos (true/false)",
     *     @OA\Schema(type="boolean")
     *   ),
     *   @OA\Parameter(
     *     name="valid_now", in="query", required=false,
     *     description="Filtrar cupones válidos ahora (rango de fechas y activos)",
     *     @OA\Schema(type="boolean")
     *   ),
     *   @OA\Parameter(
     *     name="code", in="query", required=false,
     *     description="Código exacto del cupón",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Listado paginado de cupones",
     *     @OA\JsonContent(
     *       @OA\Property(property="current_page", type="integer", example=1),
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Coupon")),
     *       @OA\Property(property="total", type="integer", example=5),
     *       @OA\Property(property="per_page", type="integer", example=15)
     *     )
     *   )
     * )
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
     *
     * @OA\Post(
     *   path="/api/coupon",
     *   tags={"Coupons"},
     *   summary="Crear cupón (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"code","discount_type","discount_value"},
     *       @OA\Property(property="code", type="string", maxLength=50, example="BLACKFRIDAY"),
     *       @OA\Property(property="discount_type", type="string", enum={"percent","fixed"}, example="percent"),
     *       @OA\Property(property="discount_value", type="number", format="float", example=15),
     *       @OA\Property(property="max_uses", type="integer", nullable=true, example=500),
     *       @OA\Property(property="min_order_total", type="number", format="float", nullable=true, example=100),
     *       @OA\Property(property="starts_at", type="string", format="date-time", nullable=true, example="2025-11-20T00:00:00Z"),
     *       @OA\Property(property="expires_at", type="string", format="date-time", nullable=true, example="2025-11-30T23:59:59Z"),
     *       @OA\Property(property="is_active", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/Coupon")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
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
     *
     * @OA\Get(
     *   path="/api/coupon/{id}",
     *   tags={"Coupons"},
     *   summary="Ver cupón por ID (público)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=3),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Coupon")),
     *   @OA\Response(response=404, description="Cupón no encontrado")
     * )
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
     *
     * @OA\Put(
     *   path="/api/coupon/{id}",
     *   tags={"Coupons"},
     *   summary="Actualizar cupón (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=3),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="code", type="string", maxLength=50, example="CYBERMONDAY"),
     *       @OA\Property(property="discount_type", type="string", enum={"percent","fixed"}, example="fixed"),
     *       @OA\Property(property="discount_value", type="number", format="float", example=25),
     *       @OA\Property(property="max_uses", type="integer", nullable=true, example=250),
     *       @OA\Property(property="min_order_total", type="number", format="float", nullable=true, example=80),
     *       @OA\Property(property="starts_at", type="string", format="date-time", nullable=true),
     *       @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *       @OA\Property(property="is_active", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Coupon")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Cupón no encontrado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
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
     *
     * @OA\Delete(
     *   path="/api/coupon/{id}",
     *   tags={"Coupons"},
     *   summary="Eliminar cupón (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=3),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Cupón no encontrado")
     * )
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