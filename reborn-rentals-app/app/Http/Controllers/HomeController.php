<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('active', true)
            ->with('category')
            ->latest()
            ->paginate(12);
        
        $categories = Category::all();

        return view('home', compact('products', 'categories'));
    }
}

