<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Job;
use App\Models\Cupon;
use App\Models\Product;
class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users    = User::query()->pluck('id');
        $jobs     = Job::query()->pluck('id');
        $cupons   = Cupon::query()->pluck('id');     // puede estar vacío
        $products = Product::query()->get();         // necesitamos precios

        if ($users->isEmpty() || $jobs->isEmpty() || $products->isEmpty()) {
            // No hay suficientes datos base
            return;
        }

        // Crea 20 órdenes
        for ($i = 0; $i < 20; $i++) {
            $userId  = $users->random();
            $jobId   = $jobs->random();
            $cuponId = fake()->boolean(35) && $cupons->isNotEmpty() ? $cupons->random() : null;

            // Crea la orden con valores provisionales
            /** @var Order $order */
            $order = Order::create([
                'user_id'        => $userId,
                'job_id'         => $jobId,
                'cupon_id'       => $cuponId,
                'status'         => fake()->boolean(85),
                'ordered_at'     => fake()->dateTimeBetween('-15 days', 'now'),
                'payment_method' => fake()->numberBetween(1, 3),
                'transaction_id' => fake()->optional(0.7)->uuid(),
                'notes'          => fake()->optional()->sentence(12),

                // Se recalcula luego
                'total_amount'   => 0,
                'discount_total' => null,
                'tax_total'      => null,
            ]);

            // Agrega entre 1 y 4 productos como items
            /** @var Collection<int,\App\Models\Product> $picked */
            $picked = $products->random(fake()->numberBetween(1, 4));

            $subtotal = 0;
            $totalQty = 0;

            foreach ($picked as $product) {
                $qty = fake()->numberBetween(1, 3);
                $unit = $product->price; // precio del producto
                $line = $unit * $qty;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'unit_price' => $unit,
                    'line_total' => $line,
                ]);

                $subtotal += $line;
                $totalQty += $qty;
            }

            // Calcular descuentos/impuestos simples (demo)
            $discount = fake()->boolean(40) ? round(min($subtotal * 0.15, 30), 2) : 0.00; // máx 30
            $tax      = round($subtotal * 0.13, 2); // por ejemplo 13%

            $order->update([
                'discount_total' => $discount ?: null,
                'tax_total'      => $tax,
                'total_amount'   => max(0, $subtotal - $discount + $tax),
                // Si tu tabla 'orders' tiene 'quantity' total (según tu diagrama):
                // 'quantity' => $totalQty,
            ]);
        }
    }
}