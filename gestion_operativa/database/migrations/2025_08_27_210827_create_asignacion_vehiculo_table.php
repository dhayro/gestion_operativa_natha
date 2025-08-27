<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsignacionVehiculoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignacion_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuadrilla_id');
            $table->unsignedBigInteger('vehiculo_id');
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->boolean('estado')->default(true);
            $table->timestamps();
    
            $table->foreign('cuadrilla_id')->references('id')->on('cuadrillas')->onDelete('restrict');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asignacion_vehiculo');
    }
}
