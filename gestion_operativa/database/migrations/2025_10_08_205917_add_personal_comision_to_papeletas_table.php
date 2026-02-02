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
        Schema::table('papeletas', function (Blueprint $table) {
            // Campo para chofer específico (puede ser diferente al asignado al vehículo)
            $table->unsignedBigInteger('chofer_id')->nullable()->after('asignacion_vehiculo_id');
            
            // Campo JSON para almacenar IDs de empleados de la cuadrilla que van en la comisión
            $table->json('miembros_cuadrilla')->nullable()->after('chofer_id');
            
            // Campo de texto para personas adicionales (no empleados)
            $table->text('personal_adicional')->nullable()->after('miembros_cuadrilla');
            
            // Agregar foreign key para el chofer
            $table->foreign('chofer_id')->references('id')->on('empleados')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('papeletas', function (Blueprint $table) {
            $table->dropForeign(['chofer_id']);
            $table->dropColumn(['chofer_id', 'miembros_cuadrilla', 'personal_adicional']);
        });
    }
};
