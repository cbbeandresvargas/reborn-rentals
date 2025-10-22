<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $auth = auth('api')->user();
        if (!$auth) return response()->json(['message' => 'No autenticado'], 401);

        $isAdmin = !empty($auth->is_admin) && $auth->is_admin;

        if ($isAdmin) {
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $auth = auth('api')->user();
        if (!$auth || empty($auth->is_admin) || !$auth->is_admin) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

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

        // Con el cast 'password' => 'hashed' del modelo, NO necesitas Hash::make
        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
         $auth = auth('api')->user();
        if (!$auth) return response()->json(['message' => 'No autenticado'], 401);

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $isOwner = ((int)$auth->id === (int)$user->id);
        $isAdmin = !empty($auth->is_admin) && $auth->is_admin;

        if (!($isOwner || $isAdmin)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
         $auth = auth('api')->user();
        if (!$auth) return response()->json(['message' => 'No autenticado'], 401);

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $isOwner = ((int)$auth->id === (int)$user->id);
        $isAdmin = !empty($auth->is_admin) && $auth->is_admin;

        if (!($isOwner || $isAdmin)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'name'              => ['sometimes','required','string','max:255'],
            'last_name'         => ['sometimes','nullable','string','max:255'],
            'second_last_name'  => ['sometimes','nullable','string','max:255'],
            'phone_number'      => ['sometimes','nullable','string','max:50'],
            'address'           => ['sometimes','nullable','string','max:255'],
            'email'             => ['sometimes','required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'username'          => ['sometimes','required','string','max:255', Rule::unique('users','username')->ignore($user->id)],
            'password'          => ['sometimes','required','string','min:6','confirmed'],
            // Solo admin puede editar is_admin
            'is_admin'          => [$isAdmin ? 'sometimes' : 'prohibited', 'boolean'],
        ]);

        // El cast del modelo hashea password automáticamente si vino
        $user->update($validated);

        return response()->json($user->fresh(), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $auth = auth('api')->user();
        if (!$auth || empty($auth->is_admin) || !$auth->is_admin) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $user->delete();

        return response()->json(null, 204);
    }
}