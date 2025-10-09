<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar columna solo si no existe
        if (!Schema::hasColumn('papeletas', 'correlativo')) {
            Schema::table('papeletas', function (Blueprint $table) {
                $table->string('correlativo', 20)->nullable()->after('id');
            });
        }

        // Generar correlativos para registros existentes que no tengan correlativo
        $papeletas = DB::table('papeletas')->whereNull('correlativo')->orderBy('created_at')->get();
        $correlativosPorPeriodo = [];

        // Obtener el máximo correlativo existente por período
        $maxCorrelativos = DB::table('papeletas')
            ->whereNotNull('correlativo')
            ->select(DB::raw('LEFT(correlativo, 7) as periodo'), DB::raw('MAX(CAST(RIGHT(correlativo, 4) AS UNSIGNED)) as max_num'))
            ->groupBy(DB::raw('LEFT(correlativo, 7)'))
            ->get();

        foreach ($maxCorrelativos as $max) {
            $correlativosPorPeriodo[$max->periodo] = $max->max_num;
        }

        foreach ($papeletas as $papeleta) {
            $fecha = $papeleta->fecha;
            $periodo = date('Y-m', strtotime($fecha));
            
            if (!isset($correlativosPorPeriodo[$periodo])) {
                $correlativosPorPeriodo[$periodo] = 1;
            } else {
                $correlativosPorPeriodo[$periodo]++;
            }
            
            $correlativo = $periodo . '-' . str_pad($correlativosPorPeriodo[$periodo], 4, '0', STR_PAD_LEFT);
            
            DB::table('papeletas')
                ->where('id', $papeleta->id)
                ->update(['correlativo' => $correlativo]);
        }

        // Agregar restricción unique si no existe
        $indexes = DB::select("SHOW INDEX FROM papeletas WHERE Key_name = 'papeletas_correlativo_unique'");
        if (empty($indexes)) {
            Schema::table('papeletas', function (Blueprint $table) {
                $table->unique('correlativo');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('papeletas', function (Blueprint $table) {
            $table->dropUnique(['correlativo']);
            $table->dropColumn('correlativo');
        });
    }
};
