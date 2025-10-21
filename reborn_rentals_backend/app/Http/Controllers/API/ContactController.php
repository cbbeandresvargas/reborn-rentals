<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::all();
        if (!$contacts) {
            return response()->json(['message' => 'Contactos aún no tiene datos registrados'], 404);
        }
        return response()->json($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'first_name'        => 'required|string|max:255',
        'last_name'         => 'required|string|max:255',
        'phone_number'      => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $contact = Contact::create($validator->validated());

        return response()->json($contact, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Contacto no encontrado'], 404);
        }
        return response()->json($contact, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Buscar el contacto por su ID
        $contact = Contact::find($id);

        // Si no existe, devolver error 404
        if (!$contact) {
            return response()->json(['message' => 'Contacto no encontrado'], 404);
        }

        // Validar los datos recibidos
        $validated = $request->validate([
            'first_name'   => 'sometimes|required|string|max:255',
            'last_name'    => 'sometimes|nullable|string|max:255',
            'phone_number' => [
                'sometimes', 'required', 'string', 'max:20',
                Rule::unique('contacts', 'phone_number')->ignore($contact->id),
            ],
        ]);

        // Actualizar el modelo con los campos validados
        $contact->update($validated);

        // Devolver el objeto actualizado
        return response()->json($contact->fresh(), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $contact->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente'], 200);
    }
}