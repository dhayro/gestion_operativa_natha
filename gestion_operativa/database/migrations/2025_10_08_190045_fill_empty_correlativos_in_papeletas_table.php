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
        // Llenar los correlativos vacíos para papeletas existentes
        $papeletas = DB::table('papeletas')
            ->whereNull('correlativo')
            ->orWhere('correlativo', '')
            ->orderBy('created_at')
            ->get();

        foreach ($papeletas as $papeleta) {
            $fecha = $papeleta->fecha;
            $periodo = date('Y-m', strtotime($fecha));
            
            // Buscar el último correlativo del período
            $ultimoCorrelativo = DB::table('papeletas')
                ->where('correlativo', 'LIKE', $periodo . '%')
                ->where('correlativo', '!=', '')
                ->whereNotNull('correlativo')
                ->orderBy('correlativo', 'desc')
                ->first();

            if ($ultimoCorrelativo) {
                // Extraer el número secuencial del correlativo
                $ultimoNumero = (int) substr($ultimoCorrelativo->correlativo, -4);
                $nuevoNumero = $ultimoNumero + 1;
            } else {
                $nuevoNumero = 1;
            }

            $nuevoCorrelativo = $periodo . '-' . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
            
            DB::table('papeletas')
                ->where('id', $papeleta->id)
                ->update(['correlativo' => $nuevoCorrelativo]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada en el rollback, no podemos deshacer esto de forma segura
    }
};
