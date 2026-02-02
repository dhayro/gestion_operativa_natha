<?php

namespace Database\Seeders;

use App\Models\Cargo;
use Illuminate\Database\Seeder;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar todos los cargos existentes para dejar solo los nuevos
        Cargo::truncate();

        $cargos = [
            'SUPERVISOR GENERAL',
            'ING DE SEGURIDAD',
            'ANALISTA DE SUMINISTRO NUEVO',
            'ANALISTA DE MTTO CONEXIONES Y RECLAMOS',
            'ANALISTA DE DENUNCIAS Y EMERGENCIA',
            'ASISTENTE ANALISTAS DE SUMINISTROS NUEVOS',
            'TECNICO ELECTRICISTA',
            'CHOFER EMERGENCIA',
            'Aten al Cliente las 24 horas - Fonoluz',
            'Técnico-Auxiliares de Reparto',
            'CHOFER ELECTRISISTA',
            'CHOFER FURGONETA',
            'Asistente Aguaytia',
        ];

        // Muchos de los cargos de la lista son "TECNICO ELECTRICISTA" repetido.
        // Para evitar duplicados en la tabla, usamos solo un registro por nombre.
        $cargos = array_unique($cargos);

        foreach ($cargos as $nombre) {
            Cargo::create([
                'nombre' => $nombre,
                'estado' => true,
            ]);
        }
    }
}
