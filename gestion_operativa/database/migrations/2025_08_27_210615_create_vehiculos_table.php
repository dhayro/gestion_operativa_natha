<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('marca', 100);
            $table->string('nombre', 100);
            $table->integer('year')->nullable();
            $table->string('modelo', 100);
            $table->string('color', 50);
            $table->string('placa', 20)->unique();
            $table->unsignedBigInteger('tipo_combustible_id');
            $table->boolean('estado')->default(true);
            $table->timestamps();
    
            $table->foreign('tipo_combustible_id')->references('id')->on('tipo_combustibles')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehiculos');
    }
}
