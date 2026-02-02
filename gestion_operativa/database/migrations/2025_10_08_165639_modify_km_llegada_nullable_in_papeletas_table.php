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
        Schema::table('papeletas', function (Blueprint $table) {
            $table->decimal('km_llegada', 10, 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('papeletas', function (Blueprint $table) {
            $table->decimal('km_llegada', 10, 3)->nullable(false)->change();
        });
    }
};
