<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialesFichaActividadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materiales_ficha_actividad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ficha_actividad_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('cantidad', 10, 3);
            $table->text('observacion')->nullable();
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('ficha_actividad_id')->references('id')->on('fichas_actividades')->onDelete('restrict');
            $table->foreign('material_id')->references('id')->on('materiales')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materiales_ficha_actividad');
    }
}
