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
            ->visible(); // Solo productos visibles (activos y no ocultos)

        // Búsqueda
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filtro por categoría (array)
        if ($request->has('categories') && is_array($request->categories)) {
            $query->whereIn('category_id', $request->categories);
        } elseif ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filtro por dimensiones
        if ($request->has('dimensions') && is_array($request->dimensions)) {
            $query->where(function($q) use ($request) {
                foreach ($request->dimensions as $dimension) {
                    $q->orWhere('description', 'like', '%' . $dimension . '%');
                }
            });
        }

        // Filtro por tonelaje
        if ($request->has('tonnage') && is_array($request->tonnage)) {
            $query->where(function($q) use ($request) {
                foreach ($request->tonnage as $tonnage) {
                    // Extract number from "18.25 Ton capacity"
                    if (preg_match('/([\d.]+)/', $tonnage, $matches)) {
                        $q->orWhere('description', 'like', '%' . $matches[1] . '%ton%');
                    }
                }
            });
        }

        // Filtro por capacidad de galones
        if ($request->has('gallons') && is_array($request->gallons)) {
            $query->where(function($q) use ($request) {
                foreach ($request->gallons as $gallon) {
                    // Extract number from "587 Gallon capacity"
                    if (preg_match('/(\d+)/', $gallon, $matches)) {
                        $q->orWhere('description', 'like', '%' . $matches[1] . '%gallon%');
                    }
                }
            });
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
        $product = Product::with('category')->visible()->findOrFail($id);
        
        // Productos relacionados (solo visibles)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->visible()
            ->limit(4)
            ->get();

        return view('product', compact('product', 'relatedProducts'));
    }
}

