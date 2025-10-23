<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;

/**
 * @OA\Tag(
 *   name="Products",
 *   description="Gestión de productos. Endpoints públicos y protegidos con JWT (admin)."
 * )
 *
 * @OA\Schema(
 *   schema="CategoryMini",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=3),
 *   @OA\Property(property="name", type="string", example="Herramientas")
 * )
 *
 * @OA\Schema(
 *   schema="ProductResource",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=15),
 *   @OA\Property(property="name", type="string", example="Taladro Inalámbrico"),
 *   @OA\Property(property="description", type="string", nullable=true, example="Taladro 18V con dos baterías"),
 *   @OA\Property(property="price", type="number", format="float", example=129.90),
 *   @OA\Property(property="image_url", type="string", nullable=true, example="https://cdn.example.com/p/taladro.jpg"),
 *   @OA\Property(property="active", type="boolean", example=true),
 *   @OA\Property(property="category_id", type="integer", nullable=true, example=3),
 *   @OA\Property(property="category", ref="#/components/schemas/CategoryMini"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="ProductStoreRequest",
 *   type="object",
 *   required={"name","price"},
 *   @OA\Property(property="name", type="string", example="Taladro Inalámbrico"),
 *   @OA\Property(property="description", type="string", nullable=true, example="Taladro 18V con dos baterías"),
 *   @OA\Property(property="price", type="number", format="float", example=129.90),
 *   @OA\Property(property="image_url", type="string", nullable=true, example="https://cdn.example.com/p/taladro.jpg"),
 *   @OA\Property(property="active", type="boolean", example=true),
 *   @OA\Property(property="category_id", type="integer", nullable=true, example=3)
 * )
 *
 * @OA\Schema(
 *   schema="ProductUpdateRequest",
 *   type="object",
 *   @OA\Property(property="name", type="string", example="Taladro 18V Pro"),
 *   @OA\Property(property="description", type="string", example="Versión actualizada con maletín"),
 *   @OA\Property(property="price", type="number", format="float", example=149.50),
 *   @OA\Property(property="image_url", type="string", example="https://cdn.example.com/p/taladro-pro.jpg"),
 *   @OA\Property(property="active", type="boolean", example=false),
 *   @OA\Property(property="category_id", type="integer", nullable=true, example=4)
 * )
 */
class ProductController extends Controller
{
    /**
     * Listado paginado de productos (público) con filtros.
     *
     * @OA\Get(
     *   path="/api/products",
     *   tags={"Products"},
     *   @OA\Parameter(name="q", in="query", description="Texto a buscar en nombre/descripcion", @OA\Schema(type="string")),
     *   @OA\Parameter(name="category_id", in="query", description="Filtra por categoría", @OA\Schema(type="integer")),
     *   @OA\Parameter(name="active", in="query", description="1 activos / 0 inactivos", @OA\Schema(type="boolean")),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="current_page", type="integer", example=1),
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/ProductResource")
     *       )
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $q = Product::query()->with('category:id,name');

        if ($request->filled('q')) {
            $term = $request->input('q');
            $q->where(function ($w) use ($term) {
                $w->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category_id')) {
            $q->where('category_id', (int)$request->input('category_id'));
        }

        if ($request->filled('active')) {
            $q->where('active', (bool)$request->boolean('active'));
        }

        $products = $q->orderByDesc('created_at')->paginate(15);

        return response()->json($products, 200);
    }

    /**
     * Crear producto (solo ADMIN).
     *
     * @OA\Post(
     *   path="/api/product",
     *   tags={"Products"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProductStoreRequest")),
     *   @OA\Response(response=201, description="Creado", @OA\JsonContent(ref="#/components/schemas/ProductResource")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $validated = $request->validate([
            'name'        => ['required','string','max:255','unique:products,name'],
            'description' => ['nullable','string'],
            'price'       => ['required','numeric','min:0'],
            'image_url'   => ['nullable','string','max:500'],
            'active'      => ['sometimes','boolean'],
            'category_id' => ['nullable','integer','exists:categories,id'],
        ]);

        $product = Product::create($validated);

        return response()->json($product->load('category:id,name'), 201);
    }

    /**
     * Ver detalle de producto (público).
     *
     * @OA\Get(
     *   path="/api/product/{id}",
     *   tags={"Products"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ProductResource")),
     *   @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function show($id)
    {
        $product = Product::with('category:id,name')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        return response()->json($product, 200);
    }

    /**
     * Actualizar producto (solo ADMIN).
     *
     * @OA\Put(
     *   path="/api/product/{id}",
     *   tags={"Products"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProductUpdateRequest")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/ProductResource")),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Producto no encontrado"),
     *   @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function update(Request $request, $id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validated = $request->validate([
            'name'        => ['sometimes','required','string','max:255', Rule::unique('products','name')->ignore($product->id)],
            'description' => ['sometimes','nullable','string'],
            'price'       => ['sometimes','required','numeric','min:0'],
            'image_url'   => ['sometimes','nullable','string','max:500'],
            'active'      => ['sometimes','boolean'],
            'category_id' => ['sometimes','nullable','integer','exists:categories,id'],
        ]);

        $product->update($validated);

        return response()->json($product->fresh()->load('category:id,name'), 200);
    }

    /**
     * Eliminar producto (solo ADMIN).
     *
     * @OA\Delete(
     *   path="/api/product/{id}",
     *   tags={"Products"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=401, description="No autenticado"),
     *   @OA\Response(response=403, description="No autorizado"),
     *   @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function destroy($id)
    {
        auth()->shouldUse('api');
        $user = auth('api')->user();
        if (!$user) return response()->json(['message' => 'No autenticado'], 401);
        if (!AuthHelper::isAdmin($user)) return response()->json(['message' => 'No autorizado'], 403);

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}