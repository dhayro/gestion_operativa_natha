<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumeroComprobanteToNeasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('neas', function (Blueprint $table) {
            // Agregar número de comprobante del tipo de documento
            $table->string('numero_comprobante', 50)->nullable()->after('tipo_comprobante_id')->comment('Número del comprobante fiscal (ej: Factura, Boleta)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('neas', function (Blueprint $table) {
            $table->dropColumn('numero_comprobante');
        });
    }
}
