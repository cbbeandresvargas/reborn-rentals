<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;

/**
 * @OA\Tag(
 *   name="Users",
 *   description="Gestión de usuarios: autenticación, perfil, y administración (solo admin)."
 * )
 *
 * @OA\Schema(
 *   schema="UserResource",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=10),
 *   @OA\Property(property="name", type="string", example="Fernando Almaraz"),
 *   @OA\Property(property="last_name", type="string", nullable=true, example="Almaraz"),
 *   @OA\Property(property="second_last_name", type="string", nullable=true, example="De la Quintana"),
 *   @OA\Property(property="email", type="string", example="ferchex@example.com"),
 *   @OA\Property(property="username", type="string", example="ferchex"),
 *   @OA\Property(property="phone_number", type="string", nullable=true, example="+59177777777"),
 *   @OA\Property(property="address", type="string", nullable=true, example="Cochabamba, Bolivia"),
 *   @OA\Property(property="is_admin", type="boolean", example=true),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="UserStoreRequest",
 *   type="object",
 *   required={"name","email","username","password","password_confirmation"},
 *   @OA\Property(property="name", type="string", example="Fernando Almaraz"),
 *   @OA\Property(property="last_name", type="string", example="Almaraz"),
 *   @OA\Property(property="second_last_name", type="string", example="De la Quintana"),
 *   @OA\Property(property="email", type="string", example="ferchex@example.com"),
 *   @OA\Property(property="username", type="string", example="ferchex"),
 *   @OA\Property(property="password", type="string", example="123456"),
 *   @OA\Property(property="password_confirmation", type="string", example="123456"),
 *   @OA\Property(property="is_admin", type="boolean", example=false)
 * )
 *
 * @OA\Schema(
 *   schema="UserUpdateRequest",
 *   type="object",
 *   @OA\Property(property="name", type="string", example="Fer Almaraz"),
 *   @OA\Property(property="email", type="string", example="ferchex@fusionpax.com"),
 *   @OA\Property(property="username", type="string", example="ferchex"),
 *   @OA\Property(property="password", type="string", example="654321"),
 *   @OA\Property(property="password_confirmation", type="string", example="654321"),
 *   @OA\Property(property="is_admin", type="boolean", example=true)
 * )
 */
class UserController extends Controller
{
    /**
     * Listar usuarios (solo admin) o ver perfil propio.
     *
     * @OA\Get(
     *   path="/api/auth/users",
     *   tags={"Users"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="q", in="query", description="Buscar por nombre, email o username", @OA\Schema(type="string")),
     *   @OA\Parameter(name="per_page", in="query", description="Tamaño de página (solo admin)", @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       oneOf={
     *         @OA\Schema(ref="#/components/schemas/UserResource"),
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(property="current_page", type="integer", example=1),
     *           @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UserResource"))
     *         )
     *       }
     *     )
     *   ),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(Request $request)
    {
        auth()->shouldUse('api');
        $auth = auth('api')->user();
        if (!$auth) return response()->json(['message' => 'No autenticado'], 401);

        if (AuthHelper::isAdmin($auth)) {
            $perPage = (int) $request->query('per_page', 15);
            $q = trim((string) $request->query('q', ''));

            $users = User::query()
                ->when($q !== '', function ($qb) use ($q) {
                    $qb->where(function ($w) use ($q) {
                        $w->where('name', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%")
                          ->orWhere('username', 'like', "%{$q}%");
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($perPage);

            return response()->json($users, 200);
        }

        return response()->json($auth, 200);
    }

    /**
     * Crear un usuario (solo admin).
     *
     * @OA\Post(
     *   path="/api/auth/user",
     *   tags={"Users"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UserStoreRequest")),
     *   @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/UserResource")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        auth()->shouldUse('api');
        $auth = auth('api')->user();
        if (!$auth) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($auth)) return response()->json(['message' => 'No autorizado'], 403);

        $validated = $request->validate([
            'name'              => ['required','string','max:255'],
            'last_name'         => ['sometimes','nullable','string','max:255'],
            'second_last_name'  => ['sometimes','nullable','string','max:255'],
            'phone_number'      => ['sometimes','nullable','string','max:50'],
            'address'           => ['sometimes','nullable','string','max:255'],
            'email'             => ['required','email','max:255','unique:users,email'],
            'username'          => ['required','string','max:255','unique:users,username'],
            'password'          => ['required','string','min:6','confirmed'],
            'is_admin'          => ['sometimes','boolean'],
        ]);

        $user = User::create($validated);
        return response()->json($user, 201);
    }

    /**
     * Ver detalles de usuario (propio o admin).
     *
     * @OA\Get(
     *   path="/api/auth/user/{id}",
     *   tags={"Users"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/UserResource")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function show($id)
    {
        auth()->shouldUse('api');
        $auth = auth('api')->user();
        if (!$auth) return response()->json(['message' => 'No autenticado'], 401);

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $isOwner = (int)$auth->id === (int)$user->id;
        $isAdmin = AuthHelper::isAdmin($auth);

        if (!($isOwner || $isAdmin)) return response()->json(['message' => 'No autorizado'], 403);

        return response()->json($user, 200);
    }

    /**
     * Actualizar usuario (propio o admin).
     *
     * @OA\Put(
     *   path="/api/auth/user/{id}",
     *   tags={"Users"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")),
     *   @OA\Response(response=200, description="Actualizado", @OA\JsonContent(ref="#/components/schemas/UserResource")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Usuario no encontrado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function update(Request $request, $id)
    {
        auth()->shouldUse('api');
        $auth = auth('api')->user();
        if (!$auth) return response()->json(['message' => 'No autenticado'], 401);

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $isOwner = (int)$auth->id === (int)$user->id;
        $isAdmin = AuthHelper::isAdmin($auth);

        if (!($isOwner || $isAdmin)) return response()->json(['message' => 'No autorizado'], 403);

        $rules = [
            'name'              => ['sometimes','required','string','max:255'],
            'last_name'         => ['sometimes','nullable','string','max:255'],
            'second_last_name'  => ['sometimes','nullable','string','max:255'],
            'phone_number'      => ['sometimes','nullable','string','max:50'],
            'address'           => ['sometimes','nullable','string','max:255'],
            'email'             => ['sometimes','required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'username'          => ['sometimes','required','string','max:255', Rule::unique('users','username')->ignore($user->id)],
            'password'          => ['sometimes','required','string','min:6','confirmed'],
        ];

        if ($isAdmin) $rules['is_admin'] = ['sometimes','boolean'];
        else $rules['is_admin'] = ['prohibited'];

        $validated = $request->validate($rules);

        $user->update($validated);
        return response()->json($user->fresh(), 200);
    }

    /**
     * Eliminar usuario (solo admin).
     *
     * @OA\Delete(
     *   path="/api/auth/user/{id}",
     *   tags={"Users"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="Eliminado correctamente"),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function destroy($id)
    {
        auth()->shouldUse('api');
        $auth = auth('api')->user();
        if (!$auth) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($auth)) return response()->json(['message' => 'No autorizado'], 403);

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $user->delete();
        return response()->json(null, 204);
    }
}