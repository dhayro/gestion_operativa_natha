<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empleado_id')->nullable();
            $table->string('usuario', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->enum('perfil', ['admin', 'supervisor', 'tecnico'])->default('tecnico');
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}