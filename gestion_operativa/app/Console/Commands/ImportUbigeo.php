<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ubigeo;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class ImportUbigeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ubigeo:import {archivo : Ruta del archivo Excel} {--limpiar : Limpiar la tabla antes de importar}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Importar datos UBIGEO desde un archivo Excel';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $archivo = $this->argument('archivo');
        $limpiar = $this->option('limpiar');

        // Validar que el archivo existe
        if (!file_exists($archivo)) {
            $this->error("❌ Archivo no encontrado: $archivo");
            return 1;
        }

        try {
            // Limpiar tabla si se solicita
            if ($limpiar) {
                $this->info('🗑️  Limpiando tabla ubigeos...');
                DB::statement('TRUNCATE TABLE ubigeos');
                $this->info('✓ Tabla limpiada');
            }

            // Leer el archivo Excel
            $this->info("📖 Leyendo archivo: $archivo");
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows)) {
                $this->error('❌ El archivo Excel está vacío');
                return 1;
            }

            // Obtener encabezados
            $headers = array_shift($rows);
            $this->info("📊 Columnas encontradas: " . implode(', ', $headers));

            $departamentos = [];
            $provincias = [];
            $distritos_procesados = 0;
            $errores = 0;
            $bar = $this->output->createProgressBar(count($rows));
            $bar->start();

            foreach ($rows as $idx => $row) {
                try {
                    // Mapear datos con los encabezados
                    $data = array_combine($headers, $row);

                    $iddist = trim($data['IDDIST'] ?? '');
                    $nombdep = trim($data['NOMBDEP'] ?? '');
                    $nombprov = trim($data['NOMBPROV'] ?? '');
                    $nombdist = trim($data['NOMBDIST'] ?? '');

                    if (!$iddist || !$nombdep || !$nombprov || !$nombdist) {
                        $errores++;
                        $bar->advance();
                        continue;
                    }

                    // Códigos
                    $cod_dep = substr($iddist, 0, 2) . '0000';
                    $cod_prov = substr($iddist, 0, 4) . '00';
                    $cod_dist = $iddist;

                    // Insertar Departamento
                    if (!isset($departamentos[$cod_dep])) {
                        $dep = Ubigeo::firstOrCreate(
                            ['codigo_postal' => $cod_dep],
                            [
                                'nombre' => $nombdep,
                                'estado' => true,
                            ]
                        );
                        $departamentos[$cod_dep] = $dep->id;
                    }

                    // Insertar Provincia
                    if (!isset($provincias[$cod_prov])) {
                        $prov = Ubigeo::firstOrCreate(
                            ['codigo_postal' => $cod_prov],
                            [
                                'nombre' => $nombprov,
                                'dependencia_id' => $departamentos[$cod_dep],
                                'estado' => true,
                            ]
                        );
                        $provincias[$cod_prov] = $prov->id;
                    }

                    // Insertar Distrito
                    Ubigeo::firstOrCreate(
                        ['codigo_postal' => $cod_dist],
                        [
                            'nombre' => $nombdist,
                            'dependencia_id' => $provincias[$cod_prov],
                            'estado' => true,
                        ]
                    );

                    $distritos_procesados++;
                    $bar->advance();

                } catch (Exception $e) {
                    $this->warn("⚠️  Error en fila " . ($idx + 2) . ": " . $e->getMessage());
                    $errores++;
                    $bar->advance();
                }
            }

            $bar->finish();

            $this->newLine(2);
            $this->info('✅ Importación completada:');
            $this->line("  • Distritos importados: <fg=green>$distritos_procesados</>");
            $this->line("  • Provincias: <fg=green>" . count($provincias) . "</>");
            $this->line("  • Departamentos: <fg=green>" . count($departamentos) . "</>");
            if ($errores > 0) {
                $this->line("  • Errores: <fg=yellow>$errores</>");
            }

            return 0;

        } catch (Exception $e) {
            $this->error('❌ Error durante la importación: ' . $e->getMessage());
            return 1;
        }
    }
}
