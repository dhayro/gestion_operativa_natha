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
        Schema::create('medidor_ficha_actividads', function (Blueprint $table) {
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
    
            $table->foreign('usuario_creacion_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('ficha_actividad_id')->references('id')->on('ficha_actividads')->onDelete('restrict');
            $table->foreign('medidor_id')->references('id')->on('medidors')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medidor_ficha_actividads');
    }
}
