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
        Schema::create('payment_infos', function (Blueprint $table) {
            $table->id();

            // FK al usuario (nullable, por si no siempre hay user)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Campos de tarjeta (los que usa tu factory/seeder)
            $table->string('card_holder_name');
            $table->string('card_number', 32);      // string para no perder ceros
            $table->string('card_expiration', 7);   // "MM/YY" o "MM/YYYY"
            $table->unsignedSmallInteger('cvv');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_infos');
    }
};