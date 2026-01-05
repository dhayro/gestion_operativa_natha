<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('foto_ficha_actividades', function (Blueprint $table) {
            // Cambiar url a nullable para permitir que se use archivo en su lugar
            $table->text('url')->nullable()->change();
            
            // Agregar campos para soporte de archivos
            $table->string('archivo_nombre')->nullable()->after('url')->comment('Nombre original del archivo subido');
            $table->string('archivo_ruta')->nullable()->after('archivo_nombre')->comment('Ruta donde se guarda el archivo en el servidor');
            $table->string('archivo_mime')->nullable()->after('archivo_ruta')->comment('Tipo MIME del archivo (image/jpeg, image/png, etc)');
            $table->bigInteger('archivo_tamaño')->nullable()->after('archivo_mime')->comment('Tamaño del archivo en bytes');
            
            // Campo para indicar el tipo de origen (url o archivo)
            $table->enum('tipo_origen', ['url', 'archivo','camara'])->default('url')->after('archivo_tamaño')->comment('Origen de la foto: URL o archivo subido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foto_ficha_actividades', function (Blueprint $table) {
            $table->text('url')->nullable(false)->change();
            $table->dropColumn(['archivo_nombre', 'archivo_ruta', 'archivo_mime', 'archivo_tamaño', 'tipo_origen']);
        });
    }
};
