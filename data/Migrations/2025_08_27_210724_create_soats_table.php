<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehiculo_id');
            $table->unsignedBigInteger('proveedor_id');
            $table->string('numero_soat', 200);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->boolean('estado')->default(true);
            $table->timestamps();
    
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('restrict');
            $table->foreign('proveedor_id')->references('id')->on('proveedors')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('soats');
    }
}
