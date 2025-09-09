<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedors', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 100);
            $table->char('ruc', 11)->unique();
            $table->string('contacto', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telefono', 15)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->unsignedBigInteger('ubigeo_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

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
        Schema::dropIfExists('proveedors');
    }
}