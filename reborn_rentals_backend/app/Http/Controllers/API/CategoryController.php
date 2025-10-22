<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Helpers\AuthHelper;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Público: lista todas las categorías.
     */
    public function index()
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No existen categorías registradas aún.'], 404);
        }

        return response()->json($categories, 200);
    }

    /**
     * POST /api/categories
     * Protegido: solo admin puede crear.
     */
    public function store(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if (!AuthHelper::isAdmin($user)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $category = Category::create($validator->validated());

        return response()->json($category, 201);
    }

    /**
     * GET /api/category/{id}
     * Público: cualquier usuario puede ver detalles.
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada.'], 404);
        }

        return response()->json($category, 200);
    }

    /**
     * PUT /api/category/{id}
     * Protegido: solo admin puede actualizar.
     */
    public function update(Request $request, $id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if (!AuthHelper::isAdmin($user)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada.'], 404);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes', 'required', 'string', 'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $category->update($validated);

        return response()->json($category->fresh(), 200);
    }

    /**
     * DELETE /api/category/{id}
     * Protegido: solo admin puede eliminar.
     */
    public function destroy($id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if (!AuthHelper::isAdmin($user)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada.'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Categoría eliminada correctamente.'], 200);
    }
}