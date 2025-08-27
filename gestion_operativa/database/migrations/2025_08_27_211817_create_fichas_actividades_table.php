<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFichasActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fichas_actividades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipo_actividad_id');
            $table->unsignedBigInteger('suministro_id');
            $table->unsignedBigInteger('tipo_propiedad_id')->nullable();
            $table->unsignedBigInteger('construccion_id')->nullable();
            $table->unsignedBigInteger('servicio_electrico_id')->nullable();
            $table->unsignedBigInteger('uso_id')->nullable();
            $table->string('numero_piso', 10)->nullable();
            $table->unsignedBigInteger('situacion_id')->nullable();
            $table->string('situacion_detalle', 100)->nullable();
            $table->string('suministro_derecho', 50)->nullable();
            $table->string('suministro_izquierdo', 50)->nullable();
            $table->string('latitud', 50)->nullable();
            $table->string('longitud', 50)->nullable();
            $table->text('observacion')->nullable();
            $table->string('documento', 100)->nullable();
            $table->timestamp('fecha')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('servicio_electrico_id')->references('id')->on('servicios_electrico')->onDelete('restrict');
            $table->foreign('tipo_actividad_id')->references('id')->on('tipos_actividad')->onDelete('restrict');
            $table->foreign('suministro_id')->references('id')->on('suministros')->onDelete('restrict');
            $table->foreign('tipo_propiedad_id')->references('id')->on('tipos_propiedad')->onDelete('restrict');
            $table->foreign('construccion_id')->references('id')->on('construcciones')->onDelete('restrict');
            $table->foreign('uso_id')->references('id')->on('usos')->onDelete('restrict');
            $table->foreign('situacion_id')->references('id')->on('situaciones')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fichas_actividades');
    }
}
