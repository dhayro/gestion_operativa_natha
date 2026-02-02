<?php

namespace Database\Seeders;

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
        Vehiculo::truncate();

        $tiposCombustible = TipoCombustible::pluck('id', 'nombre');
        $dieselId = $tiposCombustible['Diesel B5'] ?? $tiposCombustible->first();
        $gasolinaId = $tiposCombustible['Gasolina 90'] ?? $tiposCombustible->first();

        $datosVehiculos = [
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKY-762', 'anio' => 2021, 'segmento' => 'EMERGENCIAS', 'chofer' => 'DAVID SAAVEDRA'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKZ-773', 'anio' => 2021, 'segmento' => 'INSTALACION N.S.', 'chofer' => 'MECANICA'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKZ-839', 'anio' => 2021, 'segmento' => 'SUMOBS', 'chofer' => 'JOSE FLORES'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKW-811', 'anio' => 2021, 'segmento' => 'SUMOBS', 'chofer' => 'MANUEL SANCHEZ'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKW-890', 'anio' => 2021, 'segmento' => 'SUMOBS', 'chofer' => 'PARI'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKX-796', 'anio' => 2021, 'segmento' => 'INSTALACION N.S.', 'chofer' => 'MECANICA'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKY-763', 'anio' => 2021, 'segmento' => 'SUMOBS', 'chofer' => 'LUIS GASLA'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKZ-772', 'anio' => 2021, 'segmento' => 'CAMPO VERDE', 'chofer' => 'WILLY TUESTA'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKW-810', 'anio' => 2021, 'segmento' => 'SAN ALEJANDRO', 'chofer' => 'ALBERTO CURI'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKZ-734', 'anio' => 2021, 'segmento' => 'AGUAYTIA', 'chofer' => 'ISAAC MURRIETA'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'EOS', 'tipo' => 'MITSUBISHI', 'placa' => 'BKZ-840', 'anio' => 2021, 'segmento' => 'NESHUYA', 'chofer' => 'ALEX ROSARIO'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'FRANSISCO', 'tipo' => 'MITSUBISHI', 'placa' => 'BKX-841', 'anio' => 2012, 'segmento' => 'ATALAYA', 'chofer' => 'JHONY CACERES'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-793', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'ANIBAL VASQUEZ'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-755', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'ROY CARDENAS'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-759', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'LEONCIO MARIN'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-754', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'BRITO-WILLY-GERSON'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-794', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'JHAN ROJAS'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-752', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'MARCO CANALES'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-792', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'FRANCO PANDURO'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-791', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'SUPERVISION'],
            ['vehiculo' => 'CAMIONETA', 'proveedor' => 'MMM', 'tipo' => 'JACK', 'placa' => 'W7R-753', 'anio' => 2024, 'segmento' => 'PUCALLPA', 'chofer' => 'FACTURACION'],
            ['vehiculo' => 'FURGONETA', 'proveedor' => 'EOS', 'tipo' => 'LIFAN', 'placa' => '3143-8U', 'anio' => 2012, 'segmento' => 'SUMOBS', 'chofer' => 'MORIO BARBARAN'],
            ['vehiculo' => 'FURGONETA', 'proveedor' => 'EOS', 'tipo' => 'LIFAN', 'placa' => 'SIN PLACA', 'anio' => 2019, 'segmento' => 'SUMOBS', 'chofer' => 'JHON LINARES'],
            ['vehiculo' => 'FURGONETA', 'proveedor' => 'EOS', 'tipo' => null, 'placa' => null, 'anio' => 2023, 'segmento' => 'SUMOBS', 'chofer' => 'NATORCE'],
            ['vehiculo' => 'FURGONETA', 'proveedor' => 'EOS', 'tipo' => null, 'placa' => null, 'anio' => 2023, 'segmento' => 'SUMOBS', 'chofer' => 'JORGE-EVER'],
            ['vehiculo' => 'FURGONETA', 'proveedor' => 'EOS', 'tipo' => 'LIFAN', 'placa' => '6597-IS', 'anio' => 2019, 'segmento' => 'ALMACEN', 'chofer' => 'MARCO HINOSTROZA'],
            ['vehiculo' => 'MOTO LINEAL', 'proveedor' => 'EOS', 'tipo' => 'HONDA', 'placa' => '3964-EU', 'anio' => 2021, 'segmento' => 'FACTIBILIDAD', 'chofer' => 'JORDAN GUSEPI'],
            ['vehiculo' => 'MOTO LINEAL', 'proveedor' => 'EOS', 'tipo' => 'HONDA', 'placa' => '5530-9T', 'anio' => 2020, 'segmento' => 'FACTIBILIDAD', 'chofer' => 'ALOYSO'],
            ['vehiculo' => 'MOTO LINEAL', 'proveedor' => 'EOS', 'tipo' => 'HONDA', 'placa' => '5545-9T', 'anio' => 2020, 'segmento' => 'FACTIBILIDAD', 'chofer' => 'JUNIOR VARGAS'],
            ['vehiculo' => 'MOTO LINEAL', 'proveedor' => 'EOS', 'tipo' => 'HONDA', 'placa' => '9484-IL', 'anio' => 2013, 'segmento' => 'FACTIBILIDAD', 'chofer' => 'VARIOS'],
        ];

        $contadorTemporal = 1;

        foreach ($datosVehiculos as $vehiculo) {
            $placa = strtoupper(trim((string) $vehiculo['placa']));
            if ($placa === '' || $placa === 'NULL') {
                $placa = sprintf('TEMP-%04d', $contadorTemporal++);
            }

            $marca = $vehiculo['tipo'] ? strtoupper(trim($vehiculo['tipo'])) : 'SIN ESPECIFICAR';
            $nombre = strtoupper(trim($vehiculo['vehiculo']));
            $year = $vehiculo['anio'] ?: null;

            $modeloPartes = [];
            if (!empty($vehiculo['proveedor'])) {
                $modeloPartes[] = 'Proveedor: ' . strtoupper(trim($vehiculo['proveedor']));
            }
            if (!empty($vehiculo['segmento'])) {
                $modeloPartes[] = 'Segmento: ' . strtoupper(trim($vehiculo['segmento']));
            }
            if (!empty($vehiculo['chofer'])) {
                $modeloPartes[] = 'Chofer: ' . strtoupper(trim($vehiculo['chofer']));
            }
            $modelo = implode(' | ', $modeloPartes) ?: 'SIN DETALLE';

            $esMoto = str_contains($nombre, 'MOTO');
            $tipoCombustibleId = $esMoto ? $gasolinaId : $dieselId;

            Vehiculo::create([
                'marca' => $marca,
                'nombre' => $nombre,
                'year' => $year,
                'modelo' => $modelo,
                'color' => 'NO ESPECIFICADO',
                'placa' => $placa,
                'tipo_combustible_id' => $tipoCombustibleId,
                'estado' => true,
            ]);
        }
    }
}
