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
            $table->text('foreman_details_json')->nullable()->after('notes');
            $table->text('billing_details_json')->nullable()->after('foreman_details_json');
            $table->text('payment_method_details_json')->nullable()->after('billing_details_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['foreman_details_json', 'billing_details_json', 'payment_method_details_json']);
        });
    }
};
