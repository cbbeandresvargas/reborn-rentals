<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class CuponController extends Controller
{

    public function index()
    {
       $cupons = Cupon::orderBy('created_at', 'desc')->get();

        return response()->json($cupons, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:50', 'unique:cupons,code'],
            'discount_type'   => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value'  => ['required', 'numeric', 'min:0'],
            'max_uses'        => ['nullable', 'integer', 'min:1'],
            'min_order_total' => ['nullable', 'numeric', 'min:0'],
            'starts_at'       => ['nullable', 'date'],
            'expires_at'      => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'       => ['boolean'],
        ]);

        $cupon = Cupon::create($validated);

        return response()->json($cupon, 201);
    }


    public function show( $id)
    {
        $cupon = Cupon::find($id);
        if (!$cupon) {
            return response()->json(['message' => 'Cupón no encontrado'], 404);
        }
        
        return response()->json($cupon, 200);
    }


    public function update(Request $request, $id)
    {
        $cupon = Cupon::find($id);
        if (!$cupon) {
            return response()->json(['message' => 'Cupón no encontrado'], 404); 
        }
        $validated = $request->validate([
            'code'            => ['sometimes', 'string', 'max:50', Rule::unique('cupons')->ignore($cupon->id)],
            'discount_type'   => ['sometimes', Rule::in(['percent', 'fixed'])],
            'discount_value'  => ['sometimes', 'numeric', 'min:0'],
            'max_uses'        => ['sometimes', 'nullable', 'integer', 'min:1'],
            'min_order_total' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'starts_at'       => ['sometimes', 'nullable', 'date'],
            'expires_at'      => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],
            'is_active'       => ['sometimes', 'boolean'],
        ]);
        $cupon->update($validated);
        return response()->json($cupon, 200);

    }

    public function destroy( $id)
    {
        $cupon = Cupon::find($id);
        if (!$cupon) {
            return response()->json(['message' => 'Cupón no encontrado'], 404); 
        }
        $cupon->delete();
        return response()->json(['message' => 'Cupón eliminado correctamente'], 200);
    }
}