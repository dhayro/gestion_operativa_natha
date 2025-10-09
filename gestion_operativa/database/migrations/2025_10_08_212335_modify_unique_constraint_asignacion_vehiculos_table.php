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
        // Primero agregar el nuevo constraint único sin eliminar el anterior
        DB::statement('ALTER TABLE asignacion_vehiculos ADD UNIQUE KEY unique_cuadrilla_vehiculo_empleado (cuadrilla_id, vehiculo_id, empleado_id)');
        
        // Luego eliminar el constraint anterior
        DB::statement('ALTER TABLE asignacion_vehiculos DROP INDEX unique_cuadrilla_vehiculo');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios
        DB::statement('ALTER TABLE asignacion_vehiculos DROP INDEX unique_cuadrilla_vehiculo_empleado');
        DB::statement('ALTER TABLE asignacion_vehiculos ADD UNIQUE KEY unique_cuadrilla_vehiculo (cuadrilla_id, vehiculo_id)');
    }
};
