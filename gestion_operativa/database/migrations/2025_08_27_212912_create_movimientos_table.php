<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->enum('tipo_movimiento', ['entrada', 'salida']);
            $table->unsignedBigInteger('nea_detalle_id')->nullable();
            $table->unsignedBigInteger('pecosa_detalle_id')->nullable();
            $table->decimal('cantidad', 10, 3);
            $table->decimal('precio_unitario', 10, 3)->nullable();
            $table->boolean('incluye_igv')->default(false);
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->boolean('estado')->default(true);
            $table->unsignedBigInteger('usuario_creacion_id')->nullable();
            $table->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $table->timestamps();
    
            $table->foreign('usuario_creacion_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('usuario_actualizacion_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('restrict');
            $table->foreign('nea_detalle_id')->references('id')->on('nea_detalles')->onDelete('restrict');
            $table->foreign('pecosa_detalle_id')->references('id')->on('pecosa_detalles')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientos');
    }
}
