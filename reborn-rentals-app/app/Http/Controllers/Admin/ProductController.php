<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(15);
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
            'category_id' => 'nullable|exists:categories,id',
            'active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('products', 'public');
        }

        $validated['active'] = $request->has('active');

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
            'category_id' => 'nullable|exists:categories,id',
            'active' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('products', 'public');
        }

        $validated['active'] = $request->has('active');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }
}
