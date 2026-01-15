<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'odoo_product_id' => 'nullable|integer|min:1',
            'active' => 'boolean',
            'hidden' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Guardar directamente en public/products
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Crear carpeta si no existe
            if (!File::exists(public_path('products'))) {
                File::makeDirectory(public_path('products'), 0755, true);
            }
            
            $file->move(public_path('products'), $filename);
            $validated['image_url'] = 'products/' . $filename;
        }

        $validated['active'] = $request->has('active');
        $validated['hidden'] = $request->has('hidden');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load('category', 'orderItems.order');
        $categories = Category::all();
        return view('admin.products.show', compact('product', 'categories'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'odoo_product_id' => 'nullable|integer|min:1',
            'active' => 'boolean',
            'hidden' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Eliminar imagen antigua si existe
            if ($product->image_url && File::exists(public_path($product->image_url))) {
                File::delete(public_path($product->image_url));
            }
            
            // Guardar nueva imagen en public/products
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Crear carpeta si no existe
            if (!File::exists(public_path('products'))) {
                File::makeDirectory(public_path('products'), 0755, true);
            }
            
            $file->move(public_path('products'), $filename);
            $validated['image_url'] = 'products/' . $filename;
        }

        $validated['active'] = $request->has('active');
        $validated['hidden'] = $request->has('hidden');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Eliminar imagen si existe
        if ($product->image_url && File::exists(public_path($product->image_url))) {
            File::delete(public_path($product->image_url));
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }
}