<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePecosaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pecosas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuadrilla_empleado_id');
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->string('nro_documento', 50)->unique();
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('usuarios')->onDelete('restrict');
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
        Schema::dropIfExists('pecosas');
    }
}
