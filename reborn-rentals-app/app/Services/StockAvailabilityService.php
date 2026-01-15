<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class StockAvailabilityService
{
    /**
     * Check if a product has enough stock for a given date range and quantity.
     *
     * @param int $productId
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param int $quantity
     * @return array
     */
    public function checkAvailability(int $productId, string $startDate, string $endDate, int $quantity): array
    {
        $product = Product::findOrFail($productId);
        $totalStock = $product->stock;

        // If product has no stock limit (e.g. 0 means unlimited? Or 0 means 0?)
        // Assuming 0 means 0. If unlimited logic is needed, we'd check a flag.
        // Requirement said "stock total es fijo (ej: 7 unidades)".

        if ($totalStock <= 0) {
             return [
                'allowed' => false,
                'available_stock' => 0,
                'message' => 'This product has no stock available or stock is set to 0.'
            ];
        }

        // Parse dates
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Find existing orders that overlap with the requested range
        // Overlap condition: (JobStartDate <= RequestEndDate) AND (JobEndDate >= RequestStartDate)
        $conflictingItems = OrderItem::where('product_id', $productId)
            ->whereHas('order.job', function ($query) use ($start, $end) {
                $query->where('date', '<=', $end) // Job starts before or on request end
                      ->where('end_date', '>=', $start); // Job ends after or on request start
                      // Note: JobLocation.status is boolean, but we check order status separately
            })
            // Ensure order is not cancelled
            ->whereHas('order', function($q) {
                $q->where('status', '!=', 'cancelled'); // Only count non-cancelled orders
            })
            ->with(['order.job'])
            ->get();

        // Calculate max usage per day in the range
        // Because "stock available for a date = total - sum of overlapping rents"
        // We need to check EACH DAY in the requested range to find the bottleneck.
        
        $minAvailable = $totalStock;
        $bottleneckDate = null;

        $current = $start->copy();
        while ($current->lte($end)) {
            $usedToday = 0;
            
            foreach ($conflictingItems as $item) {
                $job = $item->order->job;
                $jobStart = Carbon::parse($job->date)->startOfDay();
                $jobEnd = Carbon::parse($job->end_date)->endOfDay();

                if ($current->between($jobStart, $jobEnd)) {
                    $usedToday += $item->quantity;
                }
            }

            $availableToday = $totalStock - $usedToday;
            
            if ($availableToday < $minAvailable) {
                $minAvailable = $availableToday;
                $bottleneckDate = $current->toDateString();
            }

            $current->addDay();
        }

        if ($quantity <= $minAvailable) {
            return [
                'allowed' => true,
                'available_stock' => $minAvailable,
                'message' => 'Product is available for the selected dates.'
            ];
        } else {
            $bottleneckFormatted = $bottleneckDate ? Carbon::parse($bottleneckDate)->format('M d, Y') : 'selected dates';
            return [
                'allowed' => false,
                'available_stock' => $minAvailable,
                'message' => "Insufficient stock. Available: {$minAvailable} unit(s) on {$bottleneckFormatted}."
            ];
        }
    }
}
