<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehiculo;
use App\Models\TipoCombustible;

class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos tipos de combustible para usar en los vehículos
        $gasolina84 = TipoCombustible::where('nombre', 'Gasolina 84')->first();
        $gasolina90 = TipoCombustible::where('nombre', 'Gasolina 90')->first();
        $diesel = TipoCombustible::where('nombre', 'Diesel B5')->first();
        $gnv = TipoCombustible::where('nombre', 'GNV (Gas Natural Vehicular)')->first();

        $vehiculos = [
            [
                'marca' => 'Toyota',
                'nombre' => 'Hilux',
                'year' => 2021,
                'modelo' => 'SR5',
                'color' => 'Blanco',
                'placa' => 'ABC-123',
                'tipo_combustible_id' => $diesel ? $diesel->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Nissan',
                'nombre' => 'Frontier',
                'year' => 2020,
                'modelo' => 'LE',
                'color' => 'Azul',
                'placa' => 'DEF-456',
                'tipo_combustible_id' => $diesel ? $diesel->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Ford',
                'nombre' => 'Ranger',
                'year' => 2019,
                'modelo' => 'XLT',
                'color' => 'Negro',
                'placa' => 'GHI-789',
                'tipo_combustible_id' => $diesel ? $diesel->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Chevrolet',
                'nombre' => 'D-Max',
                'year' => 2022,
                'modelo' => 'High Country',
                'color' => 'Gris',
                'placa' => 'JKL-012',
                'tipo_combustible_id' => $diesel ? $diesel->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Hyundai',
                'nombre' => 'Atos',
                'year' => 2018,
                'modelo' => 'Prime',
                'color' => 'Rojo',
                'placa' => 'MNO-345',
                'tipo_combustible_id' => $gasolina84 ? $gasolina84->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Kia',
                'nombre' => 'Picanto',
                'year' => 2020,
                'modelo' => 'EX',
                'color' => 'Verde',
                'placa' => 'PQR-678',
                'tipo_combustible_id' => $gasolina90 ? $gasolina90->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Volkswagen',
                'nombre' => 'Amarok',
                'year' => 2021,
                'modelo' => 'Comfortline',
                'color' => 'Plateado',
                'placa' => 'STU-901',
                'tipo_combustible_id' => $diesel ? $diesel->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Toyota',
                'nombre' => 'Yaris',
                'year' => 2019,
                'modelo' => 'XLE',
                'color' => 'Celeste',
                'placa' => 'VWX-234',
                'tipo_combustible_id' => $gnv ? $gnv->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Mitsubishi',
                'nombre' => 'L200',
                'year' => 2020,
                'modelo' => 'GLS',
                'color' => 'Marrón',
                'placa' => 'YZA-567',
                'tipo_combustible_id' => $diesel ? $diesel->id : 1,
                'estado' => true
            ],
            [
                'marca' => 'Mazda',
                'nombre' => 'BT-50',
                'year' => 2018,
                'modelo' => 'Pro',
                'color' => 'Naranja',
                'placa' => 'BCD-890',
                'tipo_combustible_id' => $diesel ? $diesel->id : 1,
                'estado' => false // Vehículo inactivo
            ]
        ];

        foreach ($vehiculos as $vehiculo) {
            Vehiculo::create($vehiculo);
        }
    }
}
