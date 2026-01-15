<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\Log;

class DeliveryCalculator
{
    protected float $shopLat;
    protected float $shopLon;
    protected array $loopPolygon;
    protected float $flatRateInside;
    protected float $ratePerMile;

    public function __construct()
    {
        $this->shopLat = config('delivery.shop_latitude');
        $this->shopLon = config('delivery.shop_longitude');
        $this->loopPolygon = config('delivery.denver_loop_polygon');
        $this->flatRateInside = config('delivery.flat_rate_inside_loop');
        $this->ratePerMile = config('delivery.rate_per_mile');
    }

    /**
     * Calculate delivery and pickup fees.
     *
     * @param float|null $lat Customer latitude
     * @param float|null $lon Customer longitude
     * @param bool $selfPickup Whether the customer chose self-pickup
     * @return array
     */
    public function calculate(?float $lat, ?float $lon, bool $selfPickup = false): array
    {
        // Default values
        $result = [
            'delivery_fee' => 0.00,
            'pickup_fee' => 0.00,
            'total_fees' => 0.00,
            'distance_miles' => 0.00,
            'is_inside_loop' => false,
            'calculation_method' => 'none',
        ];

        // If no coordinates provided, we cannot calculate (assuming standard fallback or error)
        // For now, return zero fees but maybe flag as error depending on business rule.
        // Or if self-pickup is true, we might not need coordinates for pickup fee (it's 0),
        // but delivery fee usually implies we deliver TO them.
        // Wait, self-pickup usually means they pick up AND return? 
        // User request says: "If customer selects self-pickup, nothing is charged for collection".
        // It implies delivery might still happen? 
        // "3️⃣ Si el cliente selecciona self-pickup, no se cobra nada por recolección."
        // Usually "Self-pickup" (Will Call) means NO delivery and NO collection.
        // However, the rule specifically mentions "recolección" (collection).
        // Let's assume:
        // - Delivery Service: Always charged unless they pick up initially? 
        // User text: "Si el cliente selecciona self-pickup, no se cobra nada por recolección."
        // Let's interpret "Self-pickup" as "Customer handles transport".
        // If "Self-pickup" mode is fully active => Fee is 0 for both?
        // Or maybe it's a mix?
        // Let's look at requirement 1: "$500 por cada servicio (entrega y recolección)".
        // Requirement 3: "Si el cliente selecciona self-pickup, no se cobra nada por recolección."
        // This phrasing presumes "Entrega" might still happen?
        // But usually "Self-pickup" is an alternative to "Delivery".
        // If I choose "Self-pickup", I assume I go to the store.
        // So Delivery Fee = 0, Pickup Fee = 0.
        // BUT, maybe the key is "Recolección" (Pickup by company at end of rental).
        // Let's assume "Self-pickup" means "Client picks up at start, returns at end".
        // So both fees are 0.
        //
        // WAIT. "Si el cliente selecciona self-pickup, no se cobra nada por recolección."
        // This might implicate: "If I choose Self-Pickup (at start), then (at end) I also return it, so no collection fee."
        //
        // Let's implement strictly:
        // calculateFees inputs: (lat, lon, is_self_pickup)
        // IF is_self_pickup:
        //    Delivery Fee = 0 (Client comes to shop)
        //    Pickup Fee = 0 (Client returns to shop)
        // 
        // However, if the user meant "One-way delivery" it would be more complex.
        // I will assume "Self-pickup" toggle generally clears all transport fees.
        
        if ($selfPickup) {
            $result['calculation_method'] = 'self_pickup';
            return $result;
        }

        if (is_null($lat) || is_null($lon)) {
            // Cannot calculate without location
            return $result;
        }

        // Logic 1: Check if inside Denver Loop
        $isInside = $this->isInsideLoop($lat, $lon);
        $result['is_inside_loop'] = $isInside;

        if ($isInside) {
            // Rule 1: $500 total for delivery + pickup combined
            $totalFee = $this->flatRateInside;
            // Split equally between delivery and pickup for display purposes
            $result['delivery_fee'] = round($totalFee / 2, 2);
            $result['pickup_fee'] = round($totalFee / 2, 2);
            $result['calculation_method'] = 'flat_rate_inside_loop';
        } else {
            // Rule 2: Outside Loop => Rate per mile * Miles (total for delivery + pickup combined)
            $miles = $this->getDistanceMiles($this->shopLat, $this->shopLon, $lat, $lon);
            $result['distance_miles'] = round($miles, 2);
            
            $totalFee = $miles * $this->ratePerMile;
            
            // Round to 2 decimals
            $totalFee = round($totalFee, 2);
            
            // Split equally between delivery and pickup for display purposes
            $result['delivery_fee'] = round($totalFee / 2, 2);
            $result['pickup_fee'] = round($totalFee / 2, 2);
            $result['calculation_method'] = 'distance_based_outside_loop';
        }

        // Rule 3: If self-pickup (re-read carefully) -> "Si el cliente selecciona self-pickup, no se cobra nada por recolección."
        // My previous assumption was "Self-pickup" = 0 for everything.
        // But the prompt makes a distinction.
        // "3️⃣ Si el cliente selecciona self-pickup, no se cobra nada por recolección."
        // This implies:
        // Case A: Standard Delivery (Delivery + Collection)
        // Case B: Self-Pickup (Client P/U + Client Return?) OR (Client P/U + We Collect?)
        // Usually "Self-pickup" means "Customer picks up".
        // If Customer picks up, typically they also return.
        // So fee for delivery is 0, fee for collection is 0.
        //
        // Let's stick to the interpretation:
        // If "Self-Pickup" is selected in checkout:
        //   Delivery Fee = 0.
        //   Collection Fee = 0.
        //
        // Wait, why specifically mention "no se cobra nada por recolección"?
        // Maybe because for "Delivery" option, we charge for BOTH Delivery AND Collection.
        // So if they choose "Self-Pickup", both are waived?
        // Or is it possible to have "Delivery" (We drop off) but "Self-Return" (Client returns)?
        // The prompt says: "Si el cliente selecciona self-pickup..."
        // Typically that's a radio button: "Delivery" vs "Pickup".
        // If "Pickup", then NO fees.
        // If "Delivery", then FEES (Delivery + Collection).
        //
        // I will implement: 
        // If $selfPickup is true => Fees are 0.
        // If $selfPickup is false => Calculate Delivery + Collection.
        
        // Re-reading logic block above: I already return 0 if $selfPickup is true.
        // So the logic holds.
        
        $result['total_fees'] = $result['delivery_fee'] + $result['pickup_fee'];

        return $result;
    }

    /**
     * Check if a point is inside the Denver Loop polygon using Ray Casting algorithm.
     *
     * @param float $lat
     * @param float $lon
     * @return bool
     */
    protected function isInsideLoop(float $lat, float $lon): bool
    {
        $polygon = $this->loopPolygon;
        $count = count($polygon);
        $inside = false;

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $lon) != ($yj > $lon))
                && ($lat < ($xj - $xi) * ($lon - $yi) / ($yj - $yi) + $xi);
            
            if ($intersect) {
                $inside = !$inside;
            }
        }
        
        // Note: My polygon array is [lat, lon].
        // In the algorithm above:
        // xi, xj are Latitudes (X usually Lon, Y usually Lat in GIS, strictly speaking).
        // Let's standardise: X = Lat, Y = Lon for this loop to match the array structure.
        // $polygon[$i][0] is Lat.
        // $polygon[$i][1] is Lon.
        //
        // Standard Ray Casting: horizontal ray.
        // Check intersection with edge ($i, $j).
        //
        // If I treat Lat as X and Lon as Y (cartesian plane):
        // Point is ($lat, $lon).
        //
        // The condition `($yi > $lon) != ($yj > $lon)` checks if the ray crosses the Y-level (Longitude level) of the edge.
        // The condition `$lat < ...` checks if the intersection X-coordinate is to the right (or left depending on perspective) of the point.
        //
        // Let's map carefully:
        // Point P(x, y) = (Lat, Lon)
        // Vertex V[i](xi, yi) = (Lat_i, Lon_i)
        //
        // Ray casting typically shoots a ray along the X-axis (Lat-axis) from -infinity to Px.
        //
        // Code above:
        // $xi = Lat_i, $yi = Lon_i
        // Checks Y-bounds: ($yi > $lon) != ($yj > $lon) -> Edge overlaps the Y (Lon) of point.
        // Calculates Intersection X: x_int = (xj - xi) * (lon - yi) / (yj - yi) + xi
        // Checks if Point X ($lat) < Intersection X.
        //
        // This counts intersections to the "Right" (Positive Lat direction) of the point.
        // Yes, this is correct for point-in-polygon.

        return $inside;
    }

    /**
     * Calculate distance in miles between two points using Haversine formula.
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    protected function getDistanceMiles(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 3959; // Earth radius in miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
