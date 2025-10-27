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
        Schema::create('orders', function (Blueprint $table) {
           $table->id();
            $table->foreignId('job_id')
                  ->constrained('job_locations')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreignId('cupon_id')
                  ->nullable()
                  ->constrained('cupons')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->boolean('status')->default(true);
            $table->decimal('discount_total', 10, 2)->nullable();
            $table->dateTime('ordered_at');
            $table->unsignedTinyInteger('payment_method'); 
            $table->decimal('tax_total', 10, 2)->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('ordered_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
