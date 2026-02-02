<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SuministrosMedidoresSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Iniciando seeder de Suministros y Medidores...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            $this->insertarMedidores();
            $this->insertarSuministros();
            $this->command->info('Seeder completado exitosamente');
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    private function insertarMedidores(): void
    {
        $jsonFile = database_path('seeders/data/medidores.json');

        if (!File::exists($jsonFile)) {
            $this->command->error("Archivo no encontrado: $jsonFile");
            return;
        }

        $jsonContent = File::get($jsonFile);
        $datos = json_decode($jsonContent, true);

        if (!isset($datos['medidores']) || empty($datos['medidores'])) {
            $this->command->warn('No hay medidores para insertar');
            return;
        }

        $medidores = $datos['medidores'];
        $total = count($medidores);
        $batchSize = 500;

        $this->command->info("Insertando $total medidores...");
        DB::table('medidors')->truncate();

        for ($i = 0; $i < $total; $i += $batchSize) {
            $lote = array_slice($medidores, $i, $batchSize);
            DB::table('medidors')->insert($lote);
            $actual = min($i + $batchSize, $total);
            $this->command->line("  [$actual/$total] medidores");
            unset($lote);
        }

        unset($medidores);
        $this->command->info("Medidores: $total insertados");
    }

    private function insertarSuministros(): void
    {
        $jsonFile = database_path('seeders/data/suministros.json');

        if (!File::exists($jsonFile)) {
            $this->command->error("Archivo no encontrado: $jsonFile");
            return;
        }

        $jsonContent = File::get($jsonFile);
        $datos = json_decode($jsonContent, true);

        if (!isset($datos['suministros']) || empty($datos['suministros'])) {
            $this->command->warn('No hay suministros para insertar');
            return;
        }

        $suministros = $datos['suministros'];
        $total = count($suministros);
        $batchSize = 500;

        $this->command->info("Insertando $total suministros...");
        DB::table('suministros')->truncate();

        for ($i = 0; $i < $total; $i += $batchSize) {
            $lote = array_slice($suministros, $i, $batchSize);
            DB::table('suministros')->insert($lote);
            $actual = min($i + $batchSize, $total);
            $this->command->line("  [$actual/$total] suministros");
            unset($lote);
        }

        unset($suministros);
        $this->command->info("Suministros: $total insertados");
    }
}
