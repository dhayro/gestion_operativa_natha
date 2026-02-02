<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cargo_id');
            $table->unsignedBigInteger('area_id');
            $table->string('nombre', 50);
            $table->string('apellido', 100);
            $table->string('email', 100)->unique();
            $table->char('dni', 8)->unique();
            $table->string('licencia', 20)->nullable();
            $table->string('telefono', 15)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->unsignedBigInteger('ubigeo_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('cargo_id')->references('id')->on('cargos')->onDelete('restrict');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('restrict');
            $table->foreign('ubigeo_id')->references('id')->on('ubigeos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empleados');
    }
}
