<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToAsignacionVehiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asignacion_vehiculos', function (Blueprint $table) {
            // Agregar índice único compuesto para evitar asignaciones duplicadas
            $table->unique(['cuadrilla_id', 'vehiculo_id'], 'unique_cuadrilla_vehiculo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asignacion_vehiculos', function (Blueprint $table) {
            // Eliminar el índice único compuesto
            $table->dropUnique('unique_cuadrilla_vehiculo');
        });
    }
}