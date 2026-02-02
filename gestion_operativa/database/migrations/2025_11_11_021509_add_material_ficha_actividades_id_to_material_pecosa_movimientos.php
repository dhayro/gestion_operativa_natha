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
        Schema::table('material_pecosa_movimientos', function (Blueprint $table) {
            // Agregar referencia al material específico de la ficha
            $table->unsignedBigInteger('material_ficha_actividades_id')->nullable()->after('ficha_actividad_id');
            $table->foreign('material_ficha_actividades_id', 'mpm_mat_ficha_id_fk')
                  ->references('id')
                  ->on('material_ficha_actividades')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_pecosa_movimientos', function (Blueprint $table) {
            $table->dropForeign(['material_ficha_actividades_id']);
            $table->dropColumn('material_ficha_actividades_id');
        });
    }
};
