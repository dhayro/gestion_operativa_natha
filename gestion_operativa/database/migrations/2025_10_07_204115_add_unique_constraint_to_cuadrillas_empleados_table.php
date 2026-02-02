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
        Schema::table('cuadrillas_empleados', function (Blueprint $table) {
            // Agregar índice único compuesto para evitar asignaciones duplicadas
            $table->unique(['cuadrilla_id', 'empleado_id'], 'unique_cuadrilla_empleado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuadrillas_empleados', function (Blueprint $table) {
            // Eliminar el índice único
            $table->dropUnique('unique_cuadrilla_empleado');
        });
    }
};
