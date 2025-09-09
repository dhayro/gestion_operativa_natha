<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoria_id');
            $table->string('nombre', 100);
            $table->string('descripcion', 255)->nullable();
            $table->unsignedBigInteger('unidad_medida_id');
            $table->decimal('precio_unitario', 10, 3)->nullable();
            $table->integer('stock_minimo');
            $table->string('codigo_material', 50)->unique();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('restrict');
            $table->foreign('unidad_medida_id')->references('id')->on('unidad_medidas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materials');
    }
}