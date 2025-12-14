<?php

namespace Database\Seeders;

use App\Models\Ubigeo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class UbigeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/ubigeo.json');

        if (!File::exists($path)) {
            $this->command?->warn("No se encontró el archivo de datos en {$path}. Se omite el seed de ubigeo.");
            return;
        }

        $initialForeignKeyState = $this->getForeignKeyState();

        if ($initialForeignKeyState === 1) {
            Schema::disableForeignKeyConstraints();
        }

        Ubigeo::truncate();

        if ($initialForeignKeyState === 1) {
            Schema::enableForeignKeyConstraints();
        }

        $data = json_decode(File::get($path), true);

        if (!is_array($data)) {
            $this->command?->error('El archivo ubigeo.json no contiene un arreglo válido.');
            return;
        }

        $total = $this->seedUbigeos($data);

        $this->command?->info("UbigeoSeeder: {$total} registros insertados.");
    }

    private function seedUbigeos(array $items, ?int $parentId = null): int
    {
        $count = 0;

        foreach ($items as $item) {
            if (!isset($item['nombre'])) {
                continue;
            }

            $ubigeo = Ubigeo::create([
                'nombre' => $item['nombre'],
                'codigo_postal' => $item['codigo_postal'] ?? null,
                'dependencia_id' => $parentId,
                'estado' => $item['estado'] ?? true,
            ]);

            $count++;

            if (!empty($item['hijos']) && is_array($item['hijos'])) {
                $count += $this->seedUbigeos($item['hijos'], $ubigeo->id);
            }
        }

        return $count;
    }

    private function getForeignKeyState(): int
    {
        try {
            $result = DB::select('SELECT @@FOREIGN_KEY_CHECKS as value');
            return isset($result[0]->value) ? (int) $result[0]->value : 1;
        } catch (\Throwable $e) {
            return 1;
        }
    }
}
