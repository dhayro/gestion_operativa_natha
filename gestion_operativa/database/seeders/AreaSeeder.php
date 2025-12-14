<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Borrar todas las áreas existentes porque el usuario indicó que
        // se realizará una nueva carga masiva y desea limpiar los datos previos.
        Area::truncate();

        $areas = [
            'SUPERVISION',
            'EMERGENCIAS',
            'FONOLUZ',
            'SUMOBS - NUEVOS SUMINISTROS-RECONEXIONES',
            'UU.NN. AGUAYTIA',
            'UU.NN. ATALAYA',
        ];

        foreach ($areas as $nombre) {
            Area::create([
                'nombre' => $nombre,
                'estado' => true,
            ]);
        }
    }
}
