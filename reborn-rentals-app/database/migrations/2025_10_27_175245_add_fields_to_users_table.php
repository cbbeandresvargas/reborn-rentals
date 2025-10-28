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
        // Esta migración ya no es necesaria porque los campos están en la migración base
        // Se mantiene para compatibilidad con bases de datos existentes
        if (!Schema::hasColumn('users', 'last_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('last_name')->nullable()->after('name');
                $table->string('second_last_name')->nullable()->after('last_name');
                $table->string('phone_number')->nullable()->after('email');
                $table->string('address')->nullable()->after('phone_number');
                $table->string('username')->nullable()->after('address');
            });
        }
        
        // Migrar de is_admin a role si existe is_admin
        if (Schema::hasColumn('users', 'is_admin') && !Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['user', 'admin'])->default('user')->after('password');
            });
            
            // Actualizar roles basados en is_admin
            DB::statement("UPDATE users SET role = 'admin' WHERE is_admin = 1");
            DB::statement("UPDATE users SET role = 'user' WHERE is_admin = 0 OR is_admin IS NULL");
            
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Solo revertir si los campos adicionales existen
        if (Schema::hasColumn('users', 'last_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['last_name', 'second_last_name', 'phone_number', 'address', 'username']);
            });
        }
    }
};
