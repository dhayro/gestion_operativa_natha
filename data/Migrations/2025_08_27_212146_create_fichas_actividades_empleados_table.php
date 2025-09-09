<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFichasActividadesEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ficha_actividad_empleados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ficha_actividad_id');
            $table->unsignedBigInteger('cuadrilla_empleado_id');
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('ficha_actividad_id')->references('id')->on('ficha_actividads')->onDelete('cascade');
            $table->foreign('cuadrilla_empleado_id')->references('id')->on('cuadrillas_empleados')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ficha_actividad_empleados');
    }
}
