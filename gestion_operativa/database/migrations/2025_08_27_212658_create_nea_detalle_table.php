<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNeaDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nea_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nea_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('cantidad', 10, 3);
            $table->decimal('precio_unitario', 10, 3)->nullable();
            $table->boolean('incluye_igv')->default(false);
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('nea_id')->references('id')->on('neas')->onDelete('restrict');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nea_detalles');
    }
}
