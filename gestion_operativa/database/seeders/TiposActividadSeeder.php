<?php

namespace Database\Seeders;

use App\Models\TiposActividad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiposActividadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear tipos de actividad padres
        $mantenimiento = TiposActividad::create([
            'nombre' => 'Mantenimiento',
            'estado' => true
        ]);

        $reparacion = TiposActividad::create([
            'nombre' => 'Reparación',
            'estado' => true
        ]);

        $inspeccion = TiposActividad::create([
            'nombre' => 'Inspección',
            'estado' => true
        ]);

        // Crear tipos de actividad hijo de Mantenimiento
        TiposActividad::create([
            'nombre' => 'Mantenimiento Preventivo',
            'dependencia_id' => $mantenimiento->id,
            'estado' => true
        ]);

        TiposActividad::create([
            'nombre' => 'Mantenimiento Correctivo',
            'dependencia_id' => $mantenimiento->id,
            'estado' => true
        ]);

        // Crear tipos de actividad hijo de Reparación
        TiposActividad::create([
            'nombre' => 'Reparación Eléctrica',
            'dependencia_id' => $reparacion->id,
            'estado' => true
        ]);

        TiposActividad::create([
            'nombre' => 'Reparación Mecánica',
            'dependencia_id' => $reparacion->id,
            'estado' => true
        ]);

        TiposActividad::create([
            'nombre' => 'Reparación Hidráulica',
            'dependencia_id' => $reparacion->id,
            'estado' => true
        ]);

        // Crear tipos de actividad hijo de Inspección
        TiposActividad::create([
            'nombre' => 'Inspección Diaria',
            'dependencia_id' => $inspeccion->id,
            'estado' => true
        ]);

        TiposActividad::create([
            'nombre' => 'Inspección Semanal',
            'dependencia_id' => $inspeccion->id,
            'estado' => true
        ]);

        TiposActividad::create([
            'nombre' => 'Inspección Mensual',
            'dependencia_id' => $inspeccion->id,
            'estado' => true
        ]);
    }
}
