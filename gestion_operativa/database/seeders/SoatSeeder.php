<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Soat;
use App\Models\Vehiculo;
use App\Models\Proveedor;
use Carbon\Carbon;

class SoatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener vehículos y proveedores existentes
        $vehiculos = Vehiculo::all();
        $proveedores = Proveedor::all();

        if ($vehiculos->isEmpty() || $proveedores->isEmpty()) {
            $this->command->info('No hay vehículos o proveedores disponibles. Ejecuta primero VehiculoSeeder y ProveedorSeeder.');
            return;
        }

        $soats = [
            [
                'vehiculo_id' => $vehiculos->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'numero_soat' => 'SOA-2024-001',
                'fecha_emision' => Carbon::now('America/Lima')->subMonths(11),
                'fecha_vencimiento' => Carbon::now('America/Lima')->addMonth(1), // Vence pronto
                'estado' => true
            ],
            [
                'vehiculo_id' => $vehiculos->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'numero_soat' => 'SOA-2024-002',
                'fecha_emision' => Carbon::now('America/Lima')->subMonths(10),
                'fecha_vencimiento' => Carbon::now('America/Lima')->addMonths(2), // Vigente
                'estado' => true
            ],
            [
                'vehiculo_id' => $vehiculos->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'numero_soat' => 'SOA-2023-015',
                'fecha_emision' => Carbon::now('America/Lima')->subMonths(15),
                'fecha_vencimiento' => Carbon::now('America/Lima')->subMonths(3), // Vencido
                'estado' => false
            ],
            [
                'vehiculo_id' => $vehiculos->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'numero_soat' => 'SOA-2024-003',
                'fecha_emision' => Carbon::now('America/Lima')->subMonths(6),
                'fecha_vencimiento' => Carbon::now('America/Lima')->addMonths(6), // Vigente
                'estado' => true
            ],
            [
                'vehiculo_id' => $vehiculos->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'numero_soat' => 'SOA-2024-004',
                'fecha_emision' => Carbon::now('America/Lima')->subMonths(8),
                'fecha_vencimiento' => Carbon::now('America/Lima')->addDays(15), // Por vencer
                'estado' => true
            ],
            [
                'vehiculo_id' => $vehiculos->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'numero_soat' => 'SOA-2024-005',
                'fecha_emision' => Carbon::now('America/Lima')->subMonths(4),
                'fecha_vencimiento' => Carbon::now('America/Lima')->addMonths(8), // Vigente
                'estado' => true
            ],
            [
                'vehiculo_id' => $vehiculos->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'numero_soat' => 'SOA-2023-020',
                'fecha_emision' => Carbon::now('America/Lima')->subMonths(18),
                'fecha_vencimiento' => Carbon::now('America/Lima')->subMonths(6), // Vencido
                'estado' => false
            ],
            [
                'vehiculo_id' => $vehiculos->random()->id,
                'proveedor_id' => $proveedores->random()->id,
                'numero_soat' => 'SOA-2024-006',
                'fecha_emision' => Carbon::now('America/Lima')->subMonths(2),
                'fecha_vencimiento' => Carbon::now('America/Lima')->addMonths(10), // Vigente
                'estado' => true
            ]
        ];

        foreach ($soats as $soatData) {
            // Verificar que el vehículo no tenga ya un SOAT activo
            $vehiculoId = $soatData['vehiculo_id'];
            $existeSoatActivo = Soat::where('vehiculo_id', $vehiculoId)
                ->where('estado', true)
                ->exists();

            if (!$existeSoatActivo || !$soatData['estado']) {
                Soat::create($soatData);
                $this->command->info("SOAT creado: {$soatData['numero_soat']}");
            } else {
                // Si ya existe un SOAT activo, buscar otro vehículo
                $vehiculoAlternativo = $vehiculos->filter(function($vehiculo) {
                    return !Soat::where('vehiculo_id', $vehiculo->id)
                        ->where('estado', true)
                        ->exists();
                })->first();

                if ($vehiculoAlternativo) {
                    $soatData['vehiculo_id'] = $vehiculoAlternativo->id;
                    Soat::create($soatData);
                    $this->command->info("SOAT creado: {$soatData['numero_soat']} (vehículo alternativo)");
                } else {
                    $this->command->info("SOAT omitido: {$soatData['numero_soat']} (no hay vehículos disponibles)");
                }
            }
        }

        $this->command->info('SOATs de prueba creados exitosamente.');
    }
}