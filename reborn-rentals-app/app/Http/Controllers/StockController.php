<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockAvailabilityService;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockAvailabilityService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'quantity'   => 'required|integer|min:1',
        ]);

        $result = $this->stockService->checkAvailability(
            $validated['product_id'],
            $validated['start_date'],
            $validated['end_date'],
            $validated['quantity']
        );

        return response()->json($result);
    }
}
