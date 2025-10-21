<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentInfoController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth('api')->id();

        $items = PaymentInfo::where('user_id', $userId)->get()
            ->map(function ($pi) {
                return [
                    'id'               => $pi->id,
                    'user_id'          => $pi->user_id,
                    'card_holder_name' => $pi->card_holder_name,
                    'card_number_mask' => $pi->masked_number,   // atributo accessor
                    'card_expiration'  => $pi->card_expiration, // encriptado en DB
                    'created_at'       => $pi->created_at,
                    'updated_at'       => $pi->updated_at,
                ];
            });

        return response()->json($items, 200);
    }

    public function store(Request $request)
    {
         $userId = auth('api')->id();

        $validated = $request->validate([
            'user_id'          => ['required', 'integer', 'exists:users,id', Rule::in([$userId])],
            'card_holder_name' => ['required', 'string', 'max:255'],
            'card_number'      => ['required', 'string', 'digits_between:13,19'],
            'card_expiration'  => ['required', 'string', 'max:7'],
        ]);

        $exists = PaymentInfo::where('user_id', $userId)
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

    public function show(PaymentInfo $paymentInfo)
    {
        $this->authorizeOwner($paymentInfo);

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

    public function update(Request $request, PaymentInfo $paymentInfo)
    {
        $this->authorizeOwner($paymentInfo);

        $validated = $request->validate([
           
            'user_id'          => ['sometimes', 'required', 'integer', 'exists:users,id', Rule::in([$paymentInfo->user_id])],
            'card_holder_name' => ['sometimes', 'required', 'string', 'max:255'],
            'card_number'      => ['sometimes', 'required', 'string', 'digits_between:13,19'],
            'card_expiration'  => ['sometimes', 'required', 'string', 'max:7'],
            // 'cvv'           => ['sometimes', 'nullable', 'string', 'digits_between:3,4'],
        ]);

        if (array_key_exists('card_number', $validated)) {
            $dup = PaymentInfo::where('user_id', $paymentInfo->user_id)
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

    public function destroy(PaymentInfo $paymentInfo)
    {
        $this->authorizeOwner($paymentInfo);

        $paymentInfo->delete();

        return response()->json(null, 204);
    }

    protected function authorizeOwner(PaymentInfo $paymentInfo): void
    {
        $user = auth('api')->user();

        $isOwner = $user && $paymentInfo->user_id === $user->id;

        if (!$isOwner) {
            abort(response()->json(['message' => 'No autorizado'], 403));
        }
    }
}