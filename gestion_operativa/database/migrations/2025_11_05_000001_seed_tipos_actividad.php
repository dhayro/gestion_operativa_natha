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
        // Insertar actividades principales
        $corte_id = DB::table('tipos_actividads')->insertGetId([
            'nombre' => 'CORTE',
            'dependencia_id' => null,
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Sub-actividades de CORTE
        DB::table('tipos_actividads')->insert([
            [
                'nombre' => 'Corte por No Pago',
                'dependencia_id' => $corte_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Corte por Orden Judicial',
                'dependencia_id' => $corte_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Corte Preventivo',
                'dependencia_id' => $corte_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        // RECONEXIONES
        $reconexion_id = DB::table('tipos_actividads')->insertGetId([
            'nombre' => 'RECONEXION',
            'dependencia_id' => null,
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Sub-actividades de RECONEXION
        DB::table('tipos_actividads')->insert([
            [
                'nombre' => 'Reconexión por Pago',
                'dependencia_id' => $reconexion_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Reconexión por Resolución Judicial',
                'dependencia_id' => $reconexion_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        // SUMO-B
        $sumo_b_id = DB::table('tipos_actividads')->insertGetId([
            'nombre' => 'SUMO-B',
            'dependencia_id' => null,
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Sub-actividades de SUMO-B
        DB::table('tipos_actividads')->insert([
            [
                'nombre' => 'Sumo-B: Cambio de Medidor',
                'dependencia_id' => $sumo_b_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Sumo-B: Instalación Nueva',
                'dependencia_id' => $sumo_b_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Sumo-B: Revisión Técnica',
                'dependencia_id' => $sumo_b_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        // INSPECCIONES
        $inspeccion_id = DB::table('tipos_actividads')->insertGetId([
            'nombre' => 'INSPECCION',
            'dependencia_id' => null,
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Sub-actividades de INSPECCION
        DB::table('tipos_actividads')->insert([
            [
                'nombre' => 'Inspección Técnica',
                'dependencia_id' => $inspeccion_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Inspección por Anomalía',
                'dependencia_id' => $inspeccion_id,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tipos_actividads')->whereIn('nombre', [
            'CORTE', 'Corte por No Pago', 'Corte por Orden Judicial', 'Corte Preventivo',
            'RECONEXION', 'Reconexión por Pago', 'Reconexión por Resolución Judicial',
            'SUMO-B', 'Sumo-B: Cambio de Medidor', 'Sumo-B: Instalación Nueva', 'Sumo-B: Revisión Técnica',
            'INSPECCION', 'Inspección Técnica', 'Inspección por Anomalía'
        ])->delete();
    }
};
