<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePapeletasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('papeletas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asignacion_vehiculo_id');
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->string('destino', 255);
            $table->text('motivo');
            $table->decimal('km_salida', 10, 3);
            $table->decimal('km_llegada', 10, 3);
            $table->dateTime('fecha_hora_salida')->nullable();
            $table->dateTime('fecha_hora_llegada')->nullable();
            $table->boolean('estado')->default(true);
            $table->dateTime('fecha_anulacion')->nullable();
            $table->string('motivo_anulacion', 200)->nullable();
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('asignacion_vehiculo_id')->references('id')->on('asignacion_vehiculo')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('papeletas');
    }
}
