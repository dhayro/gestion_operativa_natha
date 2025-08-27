<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedidoresFichaActividadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medidores_ficha_actividad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ficha_actividad_id');
            $table->unsignedBigInteger('medidor_id');
            $table->enum('tipo', ['nuevo', 'retirado', 'existente']);
            $table->integer('digitos_enteros')->nullable();
            $table->integer('digitos_decimales')->nullable();
            $table->integer('lectura')->nullable();
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('ficha_actividad_id')->references('id')->on('fichas_actividades')->onDelete('restrict');
            $table->foreign('medidor_id')->references('id')->on('medidores')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medidores_ficha_actividad');
    }
}
