<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedidorSuministroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medidor_suministros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suministro_id');
            $table->unsignedBigInteger('medidor_id');
            $table->timestamp('fecha_cambio')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('ficha_actividad_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('suministro_id')->references('id')->on('suministros')->onDelete('restrict');
            $table->foreign('medidor_id')->references('id')->on('medidors')->onDelete('restrict');
            $table->foreign('ficha_actividad_id')->references('id')->on('ficha_actividads')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medidor_suministros');
    }
}
