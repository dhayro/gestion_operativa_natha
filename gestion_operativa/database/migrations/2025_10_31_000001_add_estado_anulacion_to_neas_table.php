<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoAnulacionToNeasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('neas', function (Blueprint $table) {
            // Agregar campos para anulación
            $table->boolean('anulada')->default(false)->after('estado');
            $table->text('motivo_anulacion')->nullable()->after('anulada');
            $table->unsignedBigInteger('usuario_anulacion_id')->nullable()->after('motivo_anulacion');
            $table->timestamp('fecha_anulacion')->nullable()->after('usuario_anulacion_id');
            
            // Agregar índice para búsquedas rápidas
            $table->index('anulada');
            
            // Agregar relación a usuarios
            $table->foreign('usuario_anulacion_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('neas', function (Blueprint $table) {
            $table->dropForeign(['usuario_anulacion_id']);
            $table->dropIndex(['anulada']);
            $table->dropColumn(['anulada', 'motivo_anulacion', 'usuario_anulacion_id', 'fecha_anulacion']);
        });
    }
}
