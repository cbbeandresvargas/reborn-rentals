<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;

/**
 * @OA\Tag(
 *   name="PaymentInfos",
 *   description="Gestión de tarjetas / info de pago. Requiere JWT."
 * )
 *
 * @OA\Schema(
 *   schema="PaymentInfoResource",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=12),
 *   @OA\Property(property="user_id", type="integer", example=23),
 *   @OA\Property(property="card_holder_name", type="string", example="Fernando Perez"),
 *   @OA\Property(property="card_number_mask", type="string", example="**** **** **** 4242"),
 *   @OA\Property(property="card_expiration", type="string", example="12/27"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="PaymentInfoStoreRequest",
 *   type="object",
 *   required={"user_id","card_holder_name","card_number","card_expiration"},
 *   @OA\Property(property="user_id", type="integer", example=23, description="Si no eres admin, debe ser tu mismo ID."),
 *   @OA\Property(property="card_holder_name", type="string", example="Fernando Perez"),
 *   @OA\Property(property="card_number", type="string", example="4242424242424242"),
 *   @OA\Property(property="card_expiration", type="string", example="12/27")
 * )
 *
 * @OA\Schema(
 *   schema="PaymentInfoUpdateRequest",
 *   type="object",
 *   @OA\Property(property="user_id", type="integer", example=23),
 *   @OA\Property(property="card_holder_name", type="string", example="F. Perez"),
 *   @OA\Property(property="card_number", type="string", example="4000000000000002"),
 *   @OA\Property(property="card_expiration", type="string", example="01/28")
 * )
 */
class PaymentInfoController extends Controller
{
    /**
     * Listado paginado (admin ve todas; usuario solo las suyas).
     *
     * @OA\Get(
     *   path="/api/paymentInfos",
     *   tags={"PaymentInfos"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="user_id", in="query", description="(Solo admin) filtra por usuario", @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="current_page", type="integer", example=1),
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/PaymentInfoResource")
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        $q = PaymentInfo::query()->with(['user:id,name,email']);

        if ($isAdmin) {
            if ($request->filled('user_id')) {
                $q->where('user_id', (int)$request->input('user_id'));
            }
        } else {
            $q->where('user_id', $user->id);
        }

        $paginator = $q->orderByDesc('created_at')->paginate(15);

        $paginator->getCollection()->transform(function ($pi) {
            return [
                'id'               => $pi->id,
                'user_id'          => $pi->user_id,
                'card_holder_name' => $pi->card_holder_name,
                'card_number_mask' => $pi->masked_number,
                'card_expiration'  => $pi->card_expiration,
                'created_at'       => $pi->created_at,
                'updated_at'       => $pi->updated_at,
            ];
        });

        return response()->json($paginator, 200);
    }

    /**
     * Crear una tarjeta (admin puede crear para cualquiera; user solo para sí mismo).
     *
     * @OA\Post(
     *   path="/api/paymentInfo",
     *   tags={"PaymentInfos"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/PaymentInfoStoreRequest")),
     *   @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/PaymentInfoResource")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=422, description="Validación / duplicado")
     * )
     */
    public function store(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        $targetUserId = $isAdmin
            ? ($request->input('user_id') ?? $user->id)
            : $user->id;

        $rules = [
            'user_id'          => ['required', 'integer', 'exists:users,id'],
            'card_holder_name' => ['required', 'string', 'max:255'],
            'card_number'      => ['required', 'string', 'digits_between:13,19'],
            'card_expiration'  => ['required', 'string', 'max:7'],
        ];

        if (!$isAdmin) {
            $rules['user_id'][] = Rule::in([$user->id]);
        }

        $request->merge(['user_id' => $targetUserId]);

        $validated = $request->validate($rules);

        $exists = PaymentInfo::where('user_id', $targetUserId)
            ->where('card_number', $validated['card_number'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Esta tarjeta ya está registrada.'], 422);
        }

        $paymentInfo = PaymentInfo::create($validated);

        return response()->json([
            'id'               => $paymentInfo->id,
            'user_id'          => $paymentInfo->user_id,
            'card_holder_name' => $paymentInfo->card_holder_name,
            'card_number_mask' => $paymentInfo->masked_number,
            'card_expiration'  => $paymentInfo->card_expiration,
            'created_at'       => $paymentInfo->created_at,
            'updated_at'       => $paymentInfo->updated_at,
        ], 201);
    }

    /**
     * Ver una tarjeta por ID (admin cualquiera; user solo suya).
     *
     * @OA\Get(
     *   path="/api/paymentInfo/{paymentInfo}",
     *   tags={"PaymentInfos"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="paymentInfo", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaymentInfoResource")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado")
     * )
     */
    public function show(PaymentInfo $paymentInfo)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        if (!$isAdmin && $paymentInfo->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json([
            'id'               => $paymentInfo->id,
            'user_id'          => $paymentInfo->user_id,
            'card_holder_name' => $paymentInfo->card_holder_name,
            'card_number_mask' => $paymentInfo->masked_number,
            'card_expiration'  => $paymentInfo->card_expiration,
            'created_at'       => $paymentInfo->created_at,
            'updated_at'       => $paymentInfo->updated_at,
        ], 200);
    }

    /**
     * Actualizar una tarjeta (admin cualquiera; user solo suya).
     *
     * @OA\Put(
     *   path="/api/paymentInfo/{paymentInfo}",
     *   tags={"PaymentInfos"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="paymentInfo", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/PaymentInfoUpdateRequest")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaymentInfoResource")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=422, description="Validación / duplicado")
     * )
     */
    public function update(Request $request, PaymentInfo $paymentInfo)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        if (!$isAdmin && $paymentInfo->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $rules = [
            'user_id'          => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'card_holder_name' => ['sometimes', 'required', 'string', 'max:255'],
            'card_number'      => ['sometimes', 'required', 'string', 'digits_between:13,19'],
            'card_expiration'  => ['sometimes', 'required', 'string', 'max:7'],
        ];

        if (!$isAdmin) {
            $rules['user_id'][] = Rule::in([$paymentInfo->user_id]);
        }

        $validated = $request->validate($rules);

        if (array_key_exists('card_number', $validated)) {
            $destinationUserId = $validated['user_id'] ?? $paymentInfo->user_id;

            $dup = PaymentInfo::where('user_id', $destinationUserId)
                ->where('card_number', $validated['card_number'])
                ->where('id', '!=', $paymentInfo->id)
                ->exists();

            if ($dup) {
                return response()->json(['message' => 'Esta tarjeta ya está registrada.'], 422);
            }
        }

        $paymentInfo->update($validated);

        return response()->json([
            'id'               => $paymentInfo->id,
            'user_id'          => $paymentInfo->user_id,
            'card_holder_name' => $paymentInfo->card_holder_name,
            'card_number_mask' => $paymentInfo->masked_number,
            'card_expiration'  => $paymentInfo->card_expiration,
            'created_at'       => $paymentInfo->created_at,
            'updated_at'       => $paymentInfo->updated_at,
        ], 200);
    }

    /**
     * Eliminar una tarjeta (admin cualquiera; user solo suya).
     *
     * @OA\Delete(
     *   path="/api/paymentInfo/{paymentInfo}",
     *   tags={"PaymentInfos"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="paymentInfo", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado")
     * )
     */
    public function destroy(PaymentInfo $paymentInfo)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        if (!$isAdmin && $paymentInfo->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $paymentInfo->delete();

        return response()->json(null, 204);
    }
}