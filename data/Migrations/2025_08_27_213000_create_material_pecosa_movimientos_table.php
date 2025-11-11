<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialPecosaMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_pecosa_movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pecosa_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('cantidad', 8, 2);
            $table->enum('tipo_movimiento', ['entrada', 'salida'])->comment('entrada: ingresa material a pecosa, salida: sale de pecosa a ficha');
            $table->unsignedBigInteger('ficha_actividad_id')->nullable()->comment('Si es salida, registra a qué ficha se usó');
            $table->unsignedBigInteger('cuadrilla_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('pecosa_id')->references('id')->on('pecosas')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materiales')->onDelete('restrict');
            $table->foreign('ficha_actividad_id')->references('id')->on('fichas_actividades')->onDelete('set null');
            $table->foreign('cuadrilla_id')->references('id')->on('cuadrillas')->onDelete('restrict');
            $table->foreign('usuario_creacion_id')->references('id')->on('users')->onDelete('restrict');

            // Índices para búsquedas
            $table->index('pecosa_id');
            $table->index('material_id');
            $table->index('ficha_actividad_id');
            $table->index('tipo_movimiento');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_pecosa_movimientos');
    }
}
