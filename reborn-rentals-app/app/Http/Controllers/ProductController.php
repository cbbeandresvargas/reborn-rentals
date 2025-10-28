<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('active', true);

        // Búsqueda
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filtro por categoría
        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filtro por precio
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('products', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        // Productos relacionados
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->limit(4)
            ->get();

        return view('product', compact('product', 'relatedProducts'));
    }
}

