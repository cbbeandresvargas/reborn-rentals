<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Changes:
     * - Convert status from boolean to string (values: 'pending_odoo', 'completed', etc.)
     * - Add subtotal field to store base subtotal estimate
     * - Make payment_method nullable (not collected in website)
     */
    public function up(): void
    {
        // First, update existing data before changing column type
        // Convert boolean to string: false -> 'pending_odoo', true -> 'completed'
        DB::table('orders')
            ->where('status', 1)
            ->update(['status' => 'completed']);
        
        DB::table('orders')
            ->where('status', 0)
            ->orWhereNull('status')
            ->update(['status' => 'pending_odoo']);

        // Add subtotal field
        Schema::table('orders', function (Blueprint $table) {
            // Add subtotal field after cupon_id
            $table->decimal('subtotal', 10, 2)->nullable()->after('cupon_id');
        });

        // Convert status column from boolean to string
        // Using raw SQL for column type change
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending_odoo'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(50) USING status::text");
            DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'pending_odoo'");
        } else {
            // SQLite or other - use Schema builder
            Schema::table('orders', function (Blueprint $table) {
                $table->string('status', 50)->default('pending_odoo')->change();
            });
        }
        
        // Make payment_method nullable since it's not collected
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('payment_method')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert status back to boolean before changing column type
        DB::table('orders')
            ->where('status', 'completed')
            ->update(['status' => 1]);
        
        DB::table('orders')
            ->where('status', 'pending_odoo')
            ->orWhere('status', '!=', 1)
            ->update(['status' => 0]);

        // Convert status column back to boolean
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status BOOLEAN DEFAULT 0");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE BOOLEAN USING CASE WHEN status = 'completed' THEN true ELSE false END");
            DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT false");
        } else {
            // SQLite or other
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('status')->default(false)->change();
            });
        }
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('subtotal');
            $table->unsignedTinyInteger('payment_method')->nullable(false)->change();
        });
    }
};
