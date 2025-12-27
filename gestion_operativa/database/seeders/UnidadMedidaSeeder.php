<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnidadMedida;

class UnidadMedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnidadMedida::truncate();

        $unidades = [
            ['id' => 1, 'nombre' => 'Und'],
            ['id' => 2, 'nombre' => 'm'],
            ['id' => 3, 'nombre' => 'Kg'],
            ['id' => 4, 'nombre' => 'Caja'],
            ['id' => 5, 'nombre' => 'GL'],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::create($unidad);
        }

        $this->command->info('Unidades de Medida creadas exitosamente.');
    }
}
