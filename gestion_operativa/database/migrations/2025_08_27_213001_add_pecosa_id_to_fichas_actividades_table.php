<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPecosaIdToFichasActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ficha_actividads', function (Blueprint $table) {
            $table->unsignedBigInteger('pecosa_id')->nullable()->after('suministro_id')->comment('Pecosa de donde salen los materiales');
            $table->foreign('pecosa_id')->references('id')->on('pecosas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ficha_actividads', function (Blueprint $table) {
            $table->dropForeign(['pecosa_id']);
            $table->dropColumn('pecosa_id');
        });
    }
}
