<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;

class PaymentInfoController extends Controller
{
    // GET /api/paymentInfos
    public function index(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        $q = PaymentInfo::query()->with(['user:id,name,email']);

        if ($isAdmin) {
            // Admin ve todas; puede filtrar por user_id
            if ($request->filled('user_id')) {
                $q->where('user_id', (int)$request->input('user_id'));
            }
        } else {
            // Usuario normal: solo las suyas
            $q->where('user_id', $user->id);
        }

        $paginator = $q->orderByDesc('created_at')->paginate(15);

        // Transformar la colección manteniendo la paginación
        $paginator->getCollection()->transform(function ($pi) {
            return [
                'id'               => $pi->id,
                'user_id'          => $pi->user_id,
                'card_holder_name' => $pi->card_holder_name,
                'card_number_mask' => $pi->masked_number,   // accessor en el modelo
                'card_expiration'  => $pi->card_expiration, // (si está encriptado, ya llega desencriptado por cast)
                'created_at'       => $pi->created_at,
                'updated_at'       => $pi->updated_at,
            ];
        });

        return response()->json($paginator, 200);
    }

    // POST /api/paymentInfo
    public function store(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        // Si es admin, puede crear para cualquier user_id; si no, fuerza al suyo
        $targetUserId = $isAdmin
            ? ($request->input('user_id') ?? $user->id)
            : $user->id;

        $rules = [
            'user_id'          => ['required', 'integer', 'exists:users,id'],
            'card_holder_name' => ['required', 'string', 'max:255'],
            'card_number'      => ['required', 'string', 'digits_between:13,19'],
            'card_expiration'  => ['required', 'string', 'max:7'],
        ];

        // Si NO es admin, validamos que user_id == su id
        if (!$isAdmin) {
            $rules['user_id'][] = Rule::in([$user->id]);
        }

        // Inyecta el targetUserId al request si no vino (para admin que no mandó user_id)
        $request->merge(['user_id' => $targetUserId]);

        $validated = $request->validate($rules);

        // Evitar duplicados por usuario
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

    // GET /api/paymentInfo/{paymentInfo}
    public function show(PaymentInfo $paymentInfo)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        // Si no es admin, solo owner
        if (!$isAdmin && $paymentInfo->user_id !== $user->id) {
            // (Opcional) puedes revisar si el usuario no tiene ninguno y devolver 404 "No tienes registros"
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

    // PUT /api/paymentInfo/{paymentInfo}
    public function update(Request $request, PaymentInfo $paymentInfo)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = AuthHelper::isAdmin($user);

        if (!$isAdmin && $paymentInfo->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Admin puede moverlo a otro user si lo deseas; si NO, bloquea user_id:
        $rules = [
            'user_id'          => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'card_holder_name' => ['sometimes', 'required', 'string', 'max:255'],
            'card_number'      => ['sometimes', 'required', 'string', 'digits_between:13,19'],
            'card_expiration'  => ['sometimes', 'required', 'string', 'max:7'],
        ];

        // Si no quieres permitir que un usuario normal cambie user_id, lo forzamos:
        if (!$isAdmin) {
            $rules['user_id'][] = Rule::in([$paymentInfo->user_id]);
        }

        $validated = $request->validate($rules);

        // Si cambia número, valida duplicados por usuario destino
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

    // DELETE /api/paymentInfo/{paymentInfo}
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