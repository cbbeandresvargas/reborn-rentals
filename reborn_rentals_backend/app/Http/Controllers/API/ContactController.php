<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Contact",
 *   @OA\Property(property="id", type="integer", example=7),
 *   @OA\Property(property="first_name", type="string", example="Juan"),
 *   @OA\Property(property="last_name", type="string", example="Pérez"),
 *   @OA\Property(property="phone_number", type="string", example="+59170000000"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *   name="Contacts",
 *   description="Gestión de contactos"
 * )
 */
class ContactController extends Controller
{
    /**
     * GET /api/contacts
     * Protegido: solo ADMIN puede listar.
     *
     * @OA\Get(
     *   path="/api/contacts",
     *   tags={"Contacts"},
     *   summary="Listar contactos (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="q", in="query", required=false,
     *     description="Búsqueda por nombre/apellido/teléfono",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Listado paginado de contactos",
     *     @OA\JsonContent(
     *       @OA\Property(property="current_page", type="integer", example=1),
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Contact")),
     *       @OA\Property(property="total", type="integer", example=32),
     *       @OA\Property(property="per_page", type="integer", example=15)
     *     )
     *   ),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado")
     * )
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

        if ($request->filled('q')) {
            $term = $request->input('q');
            $q->where(function ($w) use ($term) {
                $w->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('phone_number', 'like', "%{$term}%");
            });
        }

        $contacts = $q->orderByDesc('created_at')->paginate(15);

        return response()->json($contacts, 200);
    }

    /**
     * POST /api/contact
     * Público: cualquier usuario puede crear un contacto.
     *
     * @OA\Post(
     *   path="/api/contact",
     *   tags={"Contacts"},
     *   summary="Crear contacto (público)",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"first_name","last_name","phone_number"},
     *       @OA\Property(property="first_name", type="string", maxLength=255, example="Juan"),
     *       @OA\Property(property="last_name", type="string", maxLength=255, example="Pérez"),
     *       @OA\Property(property="phone_number", type="string", maxLength=20, example="+59170000000")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/Contact")),
     *   @OA\Response(response=422, description="Error de validación")
     * )
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
     *
     * @OA\Get(
     *   path="/api/contact/{id}",
     *   tags={"Contacts"},
     *   summary="Ver contacto por ID (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=7),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Contact")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Contacto no encontrado")
     * )
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
     *
     * @OA\Put(
     *   path="/api/contact/{id}",
     *   tags={"Contacts"},
     *   summary="Actualizar contacto (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=7),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="first_name", type="string", maxLength=255, example="Juan"),
     *       @OA\Property(property="last_name", type="string", maxLength=255, example="Pérez"),
     *       @OA\Property(property="phone_number", type="string", maxLength=20, example="+59170000000")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/Contact")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Contacto no encontrado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
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
     *
     * @OA\Delete(
     *   path="/api/contact/{id}",
     *   tags={"Contacts"},
     *   summary="Eliminar contacto (solo admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=7),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Contacto no encontrado")
     * )
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