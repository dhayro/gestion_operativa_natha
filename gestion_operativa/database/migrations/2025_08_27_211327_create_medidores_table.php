<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedidoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medidores', function (Blueprint $table) {
            $table->id();
            $table->string('serie', 50)->unique();
            $table->string('modelo', 50);
            $table->string('capacidad_amperios', 10)->nullable();
            $table->char('año_fabricacion', 4)->nullable();
            $table->string('marca', 50)->nullable();
            $table->integer('numero_hilos')->nullable();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->string('fm', 50)->nullable();
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('material_id')->references('id')->on('materiales')->onDelete('restrict');
    
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
        Schema::dropIfExists('medidores');
    }
}
