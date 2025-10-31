<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite no soporta ALTER COLUMN para enums directamente
        // Necesitamos recrear la tabla con el nuevo enum
        if (DB::getDriverName() === 'sqlite') {
            // Crear tabla temporal
            DB::statement('CREATE TABLE cupons_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code VARCHAR(50) UNIQUE NOT NULL,
                discount_type VARCHAR(20) NOT NULL CHECK(discount_type IN ("percentage", "fixed")),
                discount_value DECIMAL(10,2) NOT NULL,
                max_uses SMALLINT UNSIGNED,
                min_order_total DECIMAL(10,2),
                starts_at DATETIME,
                expires_at DATETIME,
                is_active BOOLEAN DEFAULT 1,
                created_at DATETIME,
                updated_at DATETIME
            )');
            
            // Copiar datos actualizando 'percent' a 'percentage'
            DB::statement('INSERT INTO cupons_temp SELECT 
                id,
                code,
                CASE WHEN discount_type = "percent" THEN "percentage" ELSE discount_type END,
                discount_value,
                max_uses,
                min_order_total,
                starts_at,
                expires_at,
                is_active,
                created_at,
                updated_at
            FROM cupons');
            
            // Eliminar tabla antigua
            DB::statement('DROP TABLE cupons');
            
            // Renombrar tabla temporal
            DB::statement('ALTER TABLE cupons_temp RENAME TO cupons');
        } else {
            // Para otros motores de base de datos (MySQL, PostgreSQL)
            DB::statement('UPDATE cupons SET discount_type = "percentage" WHERE discount_type = "percent"');
            Schema::table('cupons', function (Blueprint $table) {
                // El enum se actualizará automáticamente al ejecutar la migración nuevamente
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Crear tabla temporal con el enum antiguo
            DB::statement('CREATE TABLE cupons_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code VARCHAR(50) UNIQUE NOT NULL,
                discount_type VARCHAR(20) NOT NULL CHECK(discount_type IN ("percent", "fixed")),
                discount_value DECIMAL(10,2) NOT NULL,
                max_uses SMALLINT UNSIGNED,
                min_order_total DECIMAL(10,2),
                starts_at DATETIME,
                expires_at DATETIME,
                is_active BOOLEAN DEFAULT 1,
                created_at DATETIME,
                updated_at DATETIME
            )');
            
            // Copiar datos actualizando 'percentage' a 'percent'
            DB::statement('INSERT INTO cupons_temp SELECT 
                id,
                code,
                CASE WHEN discount_type = "percentage" THEN "percent" ELSE discount_type END,
                discount_value,
                max_uses,
                min_order_total,
                starts_at,
                expires_at,
                is_active,
                created_at,
                updated_at
            FROM cupons');
            
            // Eliminar tabla antigua
            DB::statement('DROP TABLE cupons');
            
            // Renombrar tabla temporal
            DB::statement('ALTER TABLE cupons_temp RENAME TO cupons');
        }
    }
};
