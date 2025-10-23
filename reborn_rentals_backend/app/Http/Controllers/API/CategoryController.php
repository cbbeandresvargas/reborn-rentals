<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Helpers\AuthHelper;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Category",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Electrónica"),
 *   @OA\Property(property="description", type="string", nullable=true, example="Gadgets y dispositivos"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-22T12:34:56Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-22T12:34:56Z")
 * )
 *
 * @OA\Tag(
 *   name="Categories",
 *   description="Endpoints para categorías"
 * )
 */
class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Público: lista todas las categorías.
     *
     * @OA\Get(
     *   path="/api/categories",
     *   tags={"Categories"},
     *   summary="Listar categorías (público)",
     *   @OA\Response(
     *     response=200,
     *     description="Listado de categorías",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Category"))
     *   ),
     *   @OA\Response(response=404, description="No existen categorías registradas aún")
     * )
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
     *
     * @OA\Post(
     *   path="/api/categories",
     *   tags={"Categories"},
     *   summary="Crear categoría (admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", maxLength=255, example="Electrodomésticos"),
     *       @OA\Property(property="description", type="string", nullable=true, example="Linea blanca y cocina")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Creada", @OA\JsonContent(ref="#/components/schemas/Category")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
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
     *
     * @OA\Get(
     *   path="/api/category/{id}",
     *   tags={"Categories"},
     *   summary="Ver una categoría (público)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Category")),
     *   @OA\Response(response=404, description="Categoría no encontrada")
     * )
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
     *
     * @OA\Put(
     *   path="/api/category/{id}",
     *   tags={"Categories"},
     *   summary="Actualizar categoría (admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", maxLength=255, example="Electrónica y audio"),
     *       @OA\Property(property="description", type="string", nullable=true, example="Audio, video y gadgets")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Actualizada", @OA\JsonContent(ref="#/components/schemas/Category")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Categoría no encontrada"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
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
     *
     * @OA\Delete(
     *   path="/api/category/{id}",
     *   tags={"Categories"},
     *   summary="Eliminar categoría (admin)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *   @OA\Response(response=204, description="Eliminada"),
     *   @OA\Response(response=200, description="Eliminada (mensaje)"),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Categoría no encontrada")
     * )
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

        // Tu implementación responde 200 con mensaje; también es válido devolver 204 sin cuerpo
        return response()->json(['message' => 'Categoría eliminada correctamente.'], 200);
        // return response()->json(null, 204);
    }
}