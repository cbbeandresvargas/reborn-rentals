<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\JobLocation;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_availability_logic()
    {
        // 1. Setup Data
        // Create dependencies
        $user = User::factory()->create();
        
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Test Cat',
            'description' => 'Test Description'
        ]);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Desc',
            'price' => 10.00,
            'stock' => 7, // Fixed stock
            'category_id' => $categoryId,
             'active' => true,
             'hidden' => false
        ]);

        // 2. Create an existing order/rental
        // Rent 3 units from Jan 10 to Jan 15
        $job = JobLocation::create([
            'date' => '2026-01-10',
            'end_date' => '2026-01-15',
            'latitude' => 0,
            'longitude' => 0,
            'status' => true // active
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'job_id' => $job->id,
            'total_amount' => 100,
            'status' => 'completed',
            'ordered_at' => now(),
            'payment_method' => 1 // Cash or whatever
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 10.00,
            'line_total' => 30.00
        ]);

        // 3. Test Cases via API

        // Case A: Request overlap with existing rent (Jan 12-14)
        // Existing: 3 used. Total: 7. Available: 4.
        // Requesting 4 -> Should PASS.
        $response = $this->get('/stock/check?product_id='.$product->id.'&start_date=2026-01-12&end_date=2026-01-14&quantity=4');
        $response->assertStatus(200)
                 ->assertJson([
                     'allowed' => true,
                     'available_stock' => 4,
                     'message' => 'Stock suficiente.'
                 ]);

        // Case B: Request overlap with existing rent (Jan 12-14)
        // Requesting 5 -> Should FAIL (3+5 > 7).
        $responseFail = $this->get('/stock/check?product_id='.$product->id.'&start_date=2026-01-12&end_date=2026-01-14&quantity=5');
        $responseFail->assertStatus(200)
                 ->assertJson([
                     'allowed' => false,
                     // available_stock might be 4
                 ]);
        
        $this->assertFalse($responseFail->json('allowed'));
        $this->assertEquals(4, $responseFail->json('available_stock'));

        // Case C: No overlap (Jan 16-20)
        // Existing ends on Jan 15.
        // Available should be 7.
        $responseFree = $this->get('/stock/check?product_id='.$product->id.'&start_date=2026-01-16&end_date=2026-01-20&quantity=7');
        $responseFree->assertStatus(200)
                 ->assertJson([
                     'allowed' => true,
                     'available_stock' => 7
                 ]);
                 
        // Case D: Partial overlap (Jan 14-16)
        // Overlap on Jan 14 and Jan 15 (Usage 3). Jan 16 (Usage 0).
        // Max usage in range is 3 (on 14, 15). Min available is 7-3=4.
         $responsePartial = $this->get('/stock/check?product_id='.$product->id.'&start_date=2026-01-14&end_date=2026-01-16&quantity=5');
         $responsePartial->assertJson([
             'allowed' => false,
             'available_stock' => 4
         ]);

    }
}
