<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoCombustible;

class TipoCombustibleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Gasolina 84', 'estado' => true],
            ['nombre' => 'Gasolina 90', 'estado' => true],
            ['nombre' => 'Gasolina 95', 'estado' => true],
            ['nombre' => 'Gasolina 97', 'estado' => true],
            ['nombre' => 'Diesel B5', 'estado' => true],
            ['nombre' => 'Diesel B20', 'estado' => true],
            ['nombre' => 'GLP (Gas Licuado de Petróleo)', 'estado' => true],
            ['nombre' => 'GNV (Gas Natural Vehicular)', 'estado' => true],
        ];

        foreach ($tipos as $tipo) {
            TipoCombustible::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }
}
