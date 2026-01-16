<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->visible(); // Solo productos visibles (activos y no ocultos)

        // Búsqueda - busca en nombre y descripción de productos
        if ($request->has('search') && !empty(trim($request->search))) {
            $searchTerm = trim($request->search);
            $query->search($searchTerm);
        }

        // Filtro por categoría (array)
        if ($request->has('categories') && is_array($request->categories)) {
            $query->whereIn('category_id', $request->categories);
        } elseif ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filtro por descripciones - busca descripciones exactas o similares
        if ($request->has('descriptions') && is_array($request->descriptions) && count($request->descriptions) > 0) {
            $query->where(function($q) use ($request) {
                foreach ($request->descriptions as $description) {
                    $q->orWhere('description', 'like', '%' . $description . '%');
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

        // Get all unique descriptions from visible products for filters
        $productDescriptions = Product::visible()
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->distinct()
            ->orderBy('description')
            ->pluck('description')
            ->filter()
            ->values()
            ->toArray();

        // Si es una petición AJAX o espera JSON, devolver solo el HTML de los productos
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'html' => view('partials.products-grid', compact('products'))->render(),
                'pagination' => $products->hasPages() ? $products->links()->render() : ''
            ]);
        }

        return view('home', compact('products', 'categories', 'productDescriptions'));
    }
}

