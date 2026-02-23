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
        // Cambiar estado de boolean a integer en tabla medidors
        Schema::table('medidors', function (Blueprint $table) {
            // Primero, hacer backup de los datos
            // TRUE (1) = Estado 1 (Disponible)
            // FALSE (0) = Estado 2 (Asignado)
            
            // Cambiar a integer permitiendo nulls temporalmente
            $table->integer('estado')->nullable()->change();
        });
        
        // Convertir los valores: true (1) -> 1, false (0) -> 2
        DB::table('medidors')->where('estado', 1)->update(['estado' => 1]); // Disponible
        DB::table('medidors')->where('estado', 0)->update(['estado' => 2]); // Asignado
        DB::table('medidors')->whereNull('estado')->update(['estado' => 1]); // Por defecto disponible
        
        // Ahora hacer NOT NULL
        Schema::table('medidors', function (Blueprint $table) {
            $table->integer('estado')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medidors', function (Blueprint $table) {
            $table->boolean('estado')->default(true)->change();
        });
    }
};
