<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDotacionCombustibleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dotacion_combustible', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('papeleta_id');
            $table->decimal('cantidad_gl', 10, 3);
            $table->decimal('precio_unitario', 10, 3)->nullable();
            $table->date('fecha_carga')->default(DB::raw('CURRENT_DATE'));
            $table->string('numero_vale', 200);
            $table->unsignedBigInteger('tipo_combustible_id');
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('tipo_combustible_id')->references('id')->on('tipo_combustible')->onDelete('restrict');
            $table->foreign('papeleta_id')->references('id')->on('papeletas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dotacion_combustible');
    }
}
