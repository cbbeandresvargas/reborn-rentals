<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;

class ContactController extends Controller
{
    /**
     * GET /api/contacts
     * Protegido: solo ADMIN puede listar.
     */
    public function index(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        if (!AuthHelper::isAdmin($user)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $q = Contact::query();

        // Filtros opcionales simples
        if ($request->filled('q')) {
            $term = $request->input('q');
            $q->where(function ($w) use ($term) {
                $w->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('phone_number', 'like', "%{$term}%");
            });
        }

        $contacts = $q->orderByDesc('created_at')->paginate(15);

        // Si prefieres 404 sin datos, descomenta:
        // if ($contacts->isEmpty()) {
        //     return response()->json(['message' => 'Contactos aún no tiene datos registrados'], 404);
        // }

        return response()->json($contacts, 200);
    }

    /**
     * POST /api/contact
     * Público: cualquier usuario puede crear un contacto.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => ['required','string','max:255'],
            'last_name'    => ['required','string','max:255'],
            'phone_number' => ['required','string','max:20'],
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
     * GET /api/contact/{id}
     * Protegido: solo ADMIN puede ver un contacto.
     */
    public function show($id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        if (!AuthHelper::isAdmin($user)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Contacto no encontrado'], 404);
        }

        return response()->json($contact, 200);
    }

    /**
     * PUT /api/contact/{id}
     * Protegido: solo ADMIN puede actualizar.
     */
    public function update(Request $request, $id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        if (!AuthHelper::isAdmin($user)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Contacto no encontrado'], 404);
        }

        $validated = $request->validate([
            'first_name'   => ['sometimes','required','string','max:255'],
            'last_name'    => ['sometimes','nullable','string','max:255'],
            'phone_number' => [
                'sometimes','required','string','max:20',
                Rule::unique('contacts', 'phone_number')->ignore($contact->id),
            ],
        ]);

        $contact->update($validated);

        return response()->json($contact->fresh(), 200);
    }

    /**
     * DELETE /api/contact/{id}
     * Protegido: solo ADMIN puede eliminar.
     */
    public function destroy($id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        if (!AuthHelper::isAdmin($user)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Contacto no encontrado'], 404);
        }

        $contact->delete();

        return response()->json(null, 204);
    }
}