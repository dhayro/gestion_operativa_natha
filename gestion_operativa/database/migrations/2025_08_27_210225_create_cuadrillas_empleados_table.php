<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuadrillasEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuadrillas_empleados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuadrilla_id');
            $table->unsignedBigInteger('empleado_id');
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('cuadrilla_id')->references('id')->on('cuadrillas')->onDelete('restrict');
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuadrillas_empleados');
    }
}