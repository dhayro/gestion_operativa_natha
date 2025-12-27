<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::truncate();

        $categorias = [
            ['id' => 1, 'nombre' => 'Accesorios Eléctricos'],
            ['id' => 2, 'nombre' => 'Cables Conductores'],
            ['id' => 3, 'nombre' => 'Canalización'],
            ['id' => 4, 'nombre' => 'Conectores Eléctricos'],
            ['id' => 5, 'nombre' => 'Estructuras y Soportes de Medición'],
            ['id' => 6, 'nombre' => 'Fijación y Accesorios'],
            ['id' => 7, 'nombre' => 'Medición y Equipos de Medida'],
            ['id' => 8, 'nombre' => 'Pinturas y Recubrimientos'],
            ['id' => 9, 'nombre' => 'Protección y Seguridad'],
            ['id' => 10, 'nombre' => 'Protecciones Eléctricas'],
            ['id' => 11, 'nombre' => 'Tapas de Medición'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria);
        }

        $this->command->info('Categorias creadas exitosamente.');
    }
}
