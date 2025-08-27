<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePecosaDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pecosa_detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pecosa_id');
            $table->unsignedBigInteger('nea_detalle_id');
            $table->decimal('cantidad', 10, 3);
            $table->decimal('precio_unitario', 10, 3)->nullable();
            $table->boolean('incluye_igv')->default(false);
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('pecosa_id')->references('id')->on('pecosa')->onDelete('restrict');
            $table->foreign('nea_detalle_id')->references('id')->on('nea_detalle')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pecosa_detalle');
    }
}
