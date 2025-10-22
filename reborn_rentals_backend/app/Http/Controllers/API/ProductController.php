<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Helpers\AuthHelper;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Público: lista productos (paginado) con filtros opcionales.
     * Filtros: ?q=, ?category_id=, ?active=1
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
     * POST /api/product
     * Protegido: solo ADMIN puede crear.
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
     * GET /api/product/{id}
     * Público: ver detalle de producto.
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
     * PUT /api/product/{id}
     * Protegido: solo ADMIN puede actualizar.
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
     * DELETE /api/product/{id}
     * Protegido: solo ADMIN puede eliminar.
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