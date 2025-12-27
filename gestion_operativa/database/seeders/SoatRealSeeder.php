<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Soat;
use App\Models\Vehiculo;
use App\Models\Proveedor;
use Carbon\Carbon;

class SoatRealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Datos reales de SOAT de La Positiva Seguros
     */
    public function run(): void
    {
        // Obtener proveedor La Positiva del seeder
        $laPositiva = Proveedor::where('razon_social', 'LA POSITIVA SEGUROS Y REASEGUROS S.A.')
            ->first();

        if (!$laPositiva) {
            $this->command->error('Proveedor "LA POSITIVA SEGUROS Y REASEGUROS S.A." no encontrado. Ejecuta primero ProveedorSeeder.');
            return;
        }

        // Datos reales de SOAT con placa, número de póliza y fechas
        $datosSOAT = [
            // Camionetas Mitsubishi EOS - Grupo 1
            ['placa' => 'BKW810', 'numero_soat' => '141786619', 'fecha_emision' => '2025-08-01', 'fecha_vencimiento' => '2026-08-01'],
            ['placa' => 'BKW890', 'numero_soat' => '141786705', 'fecha_emision' => '2025-08-01', 'fecha_vencimiento' => '2026-08-01'],
            ['placa' => 'BKX796', 'numero_soat' => '141786738', 'fecha_emision' => '2025-08-01', 'fecha_vencimiento' => '2026-08-01'],
            ['placa' => 'BKY762', 'numero_soat' => '141824716', 'fecha_emision' => '2025-08-05', 'fecha_vencimiento' => '2026-08-05'],
            ['placa' => 'BKY763', 'numero_soat' => '141824619', 'fecha_emision' => '2025-08-05', 'fecha_vencimiento' => '2026-08-05'],
            ['placa' => 'BKZ734', 'numero_soat' => '141824870', 'fecha_emision' => '2025-08-14', 'fecha_vencimiento' => '2026-08-14'],
            ['placa' => 'BKZ772', 'numero_soat' => '141825130', 'fecha_emision' => '2025-08-14', 'fecha_vencimiento' => '2026-08-14'],
            ['placa' => 'BKZ773', 'numero_soat' => '141825093', 'fecha_emision' => '2025-08-14', 'fecha_vencimiento' => '2026-08-14'],

            // Camionetas Jack - Grupo 2
            ['placa' => 'W7R752', 'numero_soat' => '142225785', 'fecha_emision' => '2025-11-03', 'fecha_vencimiento' => '2026-11-03'],
            ['placa' => 'W7R753', 'numero_soat' => '142225758', 'fecha_emision' => '2025-11-03', 'fecha_vencimiento' => '2026-11-03'],
            ['placa' => 'W7R754', 'numero_soat' => '142225773', 'fecha_emision' => '2025-11-03', 'fecha_vencimiento' => '2026-11-03'],
            ['placa' => 'W7R759', 'numero_soat' => '142225810', 'fecha_emision' => '2025-11-06', 'fecha_vencimiento' => '2026-11-06'],
            ['placa' => 'W7R791', 'numero_soat' => '142225867', 'fecha_emision' => '2025-11-09', 'fecha_vencimiento' => '2026-11-09'],
            ['placa' => 'W7R792', 'numero_soat' => '142225819', 'fecha_emision' => '2025-11-09', 'fecha_vencimiento' => '2026-11-09'],
            ['placa' => 'W7R793', 'numero_soat' => '142225824', 'fecha_emision' => '2025-11-09', 'fecha_vencimiento' => '2026-11-09'],
            ['placa' => 'W7R794', 'numero_soat' => '142226102', 'fecha_emision' => '2025-11-09', 'fecha_vencimiento' => '2026-11-09'],

            // Vehículos menores y furgonetas
            ['placa' => '31438U', 'numero_soat' => '141849804', 'fecha_emision' => '2025-08-10', 'fecha_vencimiento' => '2026-08-10'],
            ['placa' => '0040IU', 'numero_soat' => '140976148', 'fecha_emision' => '2025-01-11', 'fecha_vencimiento' => '2026-01-11'],
            ['placa' => '0039IU', 'numero_soat' => '141526479', 'fecha_emision' => '2025-05-24', 'fecha_vencimiento' => '2026-05-24'],
            ['placa' => '3964EU', 'numero_soat' => '141718910', 'fecha_emision' => '2025-07-11', 'fecha_vencimiento' => '2026-07-11'],
            ['placa' => '55309T', 'numero_soat' => '140976119', 'fecha_emision' => '2025-01-11', 'fecha_vencimiento' => '2026-01-11'],
            ['placa' => '94841L', 'numero_soat' => '141718921', 'fecha_emision' => '2025-07-11', 'fecha_vencimiento' => '2026-07-11'],
        ];

        // Normalizar placas para búsqueda (agregar guión si es necesario)
        $vehiculosConSOAT = 0;
        $vehiculosSinEncontrar = [];

        foreach ($datosSOAT as $soat) {
            // Buscar el vehículo por placa (intentar con y sin guión)
            $placa = strtoupper(str_replace('-', '', $soat['placa']));
            $placaConGuion = substr($placa, 0, 3) . '-' . substr($placa, 3);
            
            $vehiculo = Vehiculo::whereRaw("REPLACE(UPPER(placa), '-', '') = ?", [$placa])
                ->first();

            if ($vehiculo) {
                // Verificar si ya existe un SOAT activo para este vehículo
                $soatExistente = Soat::where('vehiculo_id', $vehiculo->id)
                    ->where('estado', true)
                    ->first();

                if (!$soatExistente) {
                    Soat::create([
                        'vehiculo_id' => $vehiculo->id,
                        'proveedor_id' => $laPositiva->id,
                        'numero_soat' => $soat['numero_soat'],
                        'fecha_emision' => Carbon::createFromFormat('Y-m-d', $soat['fecha_emision']),
                        'fecha_vencimiento' => Carbon::createFromFormat('Y-m-d', $soat['fecha_vencimiento']),
                        'estado' => true
                    ]);
                    $vehiculosConSOAT++;
                    $this->command->info("SOAT {$soat['numero_soat']} registrado para {$soat['placa']}");
                } else {
                    $this->command->warn("SOAT activo existente para {$soat['placa']}, actualizando...");
                    $soatExistente->update([
                        'numero_soat' => $soat['numero_soat'],
                        'fecha_emision' => Carbon::createFromFormat('Y-m-d', $soat['fecha_emision']),
                        'fecha_vencimiento' => Carbon::createFromFormat('Y-m-d', $soat['fecha_vencimiento']),
                        'proveedor_id' => $laPositiva->id
                    ]);
                    $vehiculosConSOAT++;
                }
            } else {
                $vehiculosSinEncontrar[] = $soat['placa'];
                $this->command->error("No se encontró vehículo con placa: {$soat['placa']}");
            }
        }

        $this->command->info("========================================");
        $this->command->info("SOATs reales cargados exitosamente: $vehiculosConSOAT");
        if (!empty($vehiculosSinEncontrar)) {
            $this->command->warn("Placas no encontradas: " . implode(', ', $vehiculosSinEncontrar));
        }
    }
}
