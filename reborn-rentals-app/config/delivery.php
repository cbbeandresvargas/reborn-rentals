<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shop Location
    |--------------------------------------------------------------------------
    |
    | The coordinates of the rental shop/warehouse. Used as the starting point
    | for calculating delivery distances.
    | Default: Central Denver (approximate)
    |
    */
    'shop_latitude' => env('SHOP_LATITUDE', 39.7392),
    'shop_longitude' => env('SHOP_LONGITUDE', -104.9903),

    /*
    |--------------------------------------------------------------------------
    | Delivery Fees
    |--------------------------------------------------------------------------
    |
    | Fees for delivery and pickup services.
    |
    */
    'flat_rate_inside_loop' => env('DELIVERY_FLAT_RATE_INSIDE_LOOP', 500.00),
    'rate_per_mile' => env('DELIVERY_RATE_PER_MILE', 4.00),

    /*
    |--------------------------------------------------------------------------
    | Denver Loop Polygon (Approximate C-470/E-470 Loop)
    |--------------------------------------------------------------------------
    |
    | Array of [latitude, longitude] points defining the polygon of the
    | Denver metropolitan area where the flat rate applies.
    | 
    | This is a simplified approximation of the loop.
    |
    */
    'denver_loop_polygon' => [
        [39.539, -105.203], // SW: C-470 & Ken Caryl approx
        [39.715, -105.244], // W: Golden approx
        [39.845, -105.155], // NW: Arvada approx
        [39.954, -104.985], // N: North of Broomfield/Thornton
        [39.914, -104.770], // NE: Brighton/DIA area
        [39.761, -104.664], // E: East of Aurora
        [39.567, -104.738], // SE: Parker approx
        [39.539, -105.203], // Closing the loop (SW)
    ],
];
