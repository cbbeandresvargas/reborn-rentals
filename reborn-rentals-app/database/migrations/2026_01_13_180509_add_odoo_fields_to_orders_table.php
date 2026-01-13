<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('odoo_sale_order_id')->nullable()->after('status');
            $table->unsignedBigInteger('odoo_invoice_id')->nullable()->after('odoo_sale_order_id');
            $table->string('odoo_sync_status', 20)->default('pending')->after('odoo_invoice_id');
            
            $table->index('odoo_sale_order_id');
            $table->index('odoo_invoice_id');
            $table->index('odoo_sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['odoo_sync_status']);
            $table->dropIndex(['odoo_invoice_id']);
            $table->dropIndex(['odoo_sale_order_id']);
            
            $table->dropColumn(['odoo_sale_order_id', 'odoo_invoice_id', 'odoo_sync_status']);
        });
    }
};
