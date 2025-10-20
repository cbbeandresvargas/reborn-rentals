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
            $table->unsignedBigInteger('Id_jobs');
            $table->foreign('Id_jobs')->references(
                /* 'id' o 'Id_jobs' */ 'id'
            )->on('jobs')->cascadeOnDelete();

            $table->unsignedBigInteger('Id_products');
            $table->foreign('Id_products')->references(
            )->on('products')->restrictOnDelete();

            $table->unsignedBigInteger('Id_cupon')->nullable();
            $table->foreign('Id_cupon')->references(
                /* 'id' o 'Id_cupon' */ 'id'
            )->on('cupons')->nullOnDelete();

            $table->unsignedBigInteger('Id_users');
            $table->foreign('Id_users')->references(
                /* 'id' o 'Id_users' */ 'id'
            )->on('users')->cascadeOnDelete();

            // ----- Campos de la orden (según diagrama) -----
            $table->decimal('total_amount', 10, 2);
            $table->integer('quantity');
            $table->boolean('status');

            $table->decimal('discount_total', 10, 2)->nullable();
            $table->dateTime('ordered_at');

            $table->integer('payment_method'); // mapearás a tus códigos (p.ej. 1=efectivo, 2=tarjeta)
            $table->decimal('tax_total', 10, 2)->nullable();

            $table->string('transaction_id');
            $table->text('notes');
            $table->timestamps();
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