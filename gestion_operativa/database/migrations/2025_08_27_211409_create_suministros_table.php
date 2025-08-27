<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuministrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suministros', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->text('nombre');
            $table->string('ruta', 50)->nullable();
            $table->text('direccion')->nullable();
            $table->unsignedBigInteger('ubigeo_id')->nullable();
            $table->text('referencia')->nullable();
            $table->string('caja', 50)->nullable();
            $table->string('tarifa', 50)->nullable();
            $table->string('latitud', 50)->nullable();
            $table->string('longitud', 50)->nullable();
            $table->string('serie', 50)->nullable();
            $table->unsignedBigInteger('medidor_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('medidor_id')->references('id')->on('medidores')->onDelete('restrict');
            $table->foreign('ubigeo_id')->references('id')->on('ubigeo')->onDelete('restrict');
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suministros');
    }
}
