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

        // Extract filter options from product descriptions
        $allProducts = Product::where('active', true)->get();
        $dimensions = [];
        $tonnageCapacities = [];
        $gallonCapacities = [];

        foreach ($allProducts as $product) {
            if ($product->description) {
                // Extract dimensions (format: 7'x7'x27" or similar)
                if (preg_match("/(\d+'x\d+'x\d+\")/", $product->description, $matches)) {
                    $dimensions[] = $matches[1];
                }
                
                // Extract tonnage capacity (format: 18.25 ton or similar)
                if (preg_match("/([\d.]+)\s*ton/i", $product->description, $matches)) {
                    $tonnageCapacities[] = $matches[1] . ' Ton capacity';
                }
                
                // Extract gallon capacity (format: 587 gallons or similar)
                if (preg_match("/(\d+)\s*gallons?/i", $product->description, $matches)) {
                    $gallonCapacities[] = $matches[1] . ' Gallon capacity';
                }
            }
        }

        // Get unique values and sort
        $dimensions = array_unique($dimensions);
        sort($dimensions);
        $tonnageCapacities = array_unique($tonnageCapacities);
        sort($tonnageCapacities);
        $gallonCapacities = array_unique($gallonCapacities);
        sort($gallonCapacities);

        return view('home', compact('products', 'categories', 'dimensions', 'tonnageCapacities', 'gallonCapacities'));
    }
}

