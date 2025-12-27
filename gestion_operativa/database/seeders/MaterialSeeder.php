<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Material::truncate();

        $materiales = [
            ['id' => 1, 'categoria_id' => 1, 'nombre' => 'Fases', 'unidad_medida_id' => 1, 'stock_minimo' => 10, 'codigo_material' => 'MAT-001'],
            ['id' => 2, 'categoria_id' => 2, 'nombre' => 'Cable de aluminio SET 4×6 mm²', 'unidad_medida_id' => 2, 'stock_minimo' => 20, 'codigo_material' => 'MAT-002'],
            ['id' => 3, 'categoria_id' => 2, 'nombre' => 'Cable de aluminio tipo SET 2×6 mm²', 'unidad_medida_id' => 2, 'stock_minimo' => 20, 'codigo_material' => 'MAT-003'],
            ['id' => 4, 'categoria_id' => 2, 'nombre' => 'Cable de cobre SET 4×6 mm²', 'unidad_medida_id' => 2, 'stock_minimo' => 20, 'codigo_material' => 'MAT-004'],
            ['id' => 5, 'categoria_id' => 2, 'nombre' => 'Cable de cobre tipo SET 2×4 mm²', 'unidad_medida_id' => 2, 'stock_minimo' => 20, 'codigo_material' => 'MAT-005'],
            ['id' => 6, 'categoria_id' => 3, 'nombre' => 'Curva PVC SAP 1 1/2"', 'unidad_medida_id' => 1, 'stock_minimo' => 15, 'codigo_material' => 'MAT-006'],
            ['id' => 7, 'categoria_id' => 3, 'nombre' => 'Curva PVC SAP 3/4"', 'unidad_medida_id' => 1, 'stock_minimo' => 15, 'codigo_material' => 'MAT-007'],
            ['id' => 8, 'categoria_id' => 3, 'nombre' => 'Tubo de F° G° 3/4" × 3 m', 'unidad_medida_id' => 1, 'stock_minimo' => 10, 'codigo_material' => 'MAT-008'],
            ['id' => 9, 'categoria_id' => 3, 'nombre' => 'Tubo de F° G° 3/4" × 6 m', 'unidad_medida_id' => 1, 'stock_minimo' => 10, 'codigo_material' => 'MAT-009'],
            ['id' => 10, 'categoria_id' => 3, 'nombre' => 'Tubo PVC de 1 1/2"', 'unidad_medida_id' => 1, 'stock_minimo' => 15, 'codigo_material' => 'MAT-010'],
            ['id' => 11, 'categoria_id' => 3, 'nombre' => 'Tubo PVC de 3/4"', 'unidad_medida_id' => 1, 'stock_minimo' => 15, 'codigo_material' => 'MAT-011'],
            ['id' => 12, 'categoria_id' => 4, 'nombre' => 'Conector tipo dentado 10–95 mm²', 'unidad_medida_id' => 1, 'stock_minimo' => 25, 'codigo_material' => 'MAT-012'],
            ['id' => 13, 'categoria_id' => 4, 'nombre' => 'Conector tipo dentado 16–95 mm²', 'unidad_medida_id' => 1, 'stock_minimo' => 25, 'codigo_material' => 'MAT-013'],
            ['id' => 14, 'categoria_id' => 4, 'nombre' => 'Conector tipo dentado 25–120 mm²', 'unidad_medida_id' => 1, 'stock_minimo' => 25, 'codigo_material' => 'MAT-014'],
            ['id' => 15, 'categoria_id' => 5, 'nombre' => 'Caja portamedidor polimérica 1Ø', 'unidad_medida_id' => 1, 'stock_minimo' => 20, 'codigo_material' => 'MAT-015'],
            ['id' => 16, 'categoria_id' => 5, 'nombre' => 'Caja portamedidor polimérica 3Ø', 'unidad_medida_id' => 1, 'stock_minimo' => 20, 'codigo_material' => 'MAT-016'],
            ['id' => 17, 'categoria_id' => 5, 'nombre' => 'Murete CAC monofásico', 'unidad_medida_id' => 1, 'stock_minimo' => 15, 'codigo_material' => 'MAT-017'],
            ['id' => 18, 'categoria_id' => 5, 'nombre' => 'Murete CAC trifásico', 'unidad_medida_id' => 1, 'stock_minimo' => 15, 'codigo_material' => 'MAT-018'],
            ['id' => 19, 'categoria_id' => 6, 'nombre' => 'Abrazadera de F° G° de 1 1/2"', 'unidad_medida_id' => 1, 'stock_minimo' => 30, 'codigo_material' => 'MAT-019'],
            ['id' => 20, 'categoria_id' => 6, 'nombre' => 'Abrazadera para cable de 3/4" de 2 orejas', 'unidad_medida_id' => 1, 'stock_minimo' => 30, 'codigo_material' => 'MAT-020'],
            ['id' => 21, 'categoria_id' => 6, 'nombre' => 'Alambre de amarre', 'unidad_medida_id' => 3, 'stock_minimo' => 50, 'codigo_material' => 'MAT-021'],
            ['id' => 22, 'categoria_id' => 6, 'nombre' => 'Clavos de acero de 1"', 'unidad_medida_id' => 4, 'stock_minimo' => 100, 'codigo_material' => 'MAT-022'],
            ['id' => 23, 'categoria_id' => 6, 'nombre' => 'Correas plásticas de amarre', 'unidad_medida_id' => 1, 'stock_minimo' => 40, 'codigo_material' => 'MAT-023'],
            ['id' => 24, 'categoria_id' => 6, 'nombre' => 'Pines', 'unidad_medida_id' => 1, 'stock_minimo' => 50, 'codigo_material' => 'MAT-024'],
            ['id' => 25, 'categoria_id' => 6, 'nombre' => 'Remaches', 'unidad_medida_id' => 1, 'stock_minimo' => 50, 'codigo_material' => 'MAT-025'],
            ['id' => 26, 'categoria_id' => 6, 'nombre' => 'Templador de cable de 3/4"', 'unidad_medida_id' => 1, 'stock_minimo' => 10, 'codigo_material' => 'MAT-026'],
            ['id' => 27, 'categoria_id' => 6, 'nombre' => 'Tornillos autoroscante', 'unidad_medida_id' => 1, 'stock_minimo' => 50, 'codigo_material' => 'MAT-027'],
            ['id' => 28, 'categoria_id' => 6, 'nombre' => 'Tarugos', 'unidad_medida_id' => 1, 'stock_minimo' => 50, 'codigo_material' => 'MAT-028'],
            ['id' => 29, 'categoria_id' => 6, 'nombre' => 'Tornillos', 'unidad_medida_id' => 1, 'stock_minimo' => 50, 'codigo_material' => 'MAT-029'],
            ['id' => 30, 'categoria_id' => 7, 'nombre' => 'Medidores 1Ø de 2 hilos', 'unidad_medida_id' => 1, 'stock_minimo' => 5, 'codigo_material' => 'MAT-030'],
            ['id' => 31, 'categoria_id' => 7, 'nombre' => 'Medidores 3Ø de 4 hilos', 'unidad_medida_id' => 1, 'stock_minimo' => 5, 'codigo_material' => 'MAT-031'],
            ['id' => 32, 'categoria_id' => 8, 'nombre' => 'Pintura esmalte color azul', 'unidad_medida_id' => 5, 'stock_minimo' => 10, 'codigo_material' => 'MAT-032'],
            ['id' => 33, 'categoria_id' => 9, 'nombre' => 'Cinta aislante x 20 m T/G', 'unidad_medida_id' => 1, 'stock_minimo' => 20, 'codigo_material' => 'MAT-033'],
            ['id' => 34, 'categoria_id' => 9, 'nombre' => 'Presinto de seguridad de medidor', 'unidad_medida_id' => 1, 'stock_minimo' => 30, 'codigo_material' => 'MAT-034'],
            ['id' => 35, 'categoria_id' => 10, 'nombre' => 'Interruptor termomagnético 2×16 A', 'unidad_medida_id' => 1, 'stock_minimo' => 15, 'codigo_material' => 'MAT-035'],
            ['id' => 36, 'categoria_id' => 10, 'nombre' => 'Interruptor termomagnético tripolar 32 A – 500 V – ICC 3 kA', 'unidad_medida_id' => 1, 'stock_minimo' => 12, 'codigo_material' => 'MAT-036'],
            ['id' => 37, 'categoria_id' => 10, 'nombre' => 'Interruptor termomagnético tripolar 40 A – 500 V – ICC 3 kA', 'unidad_medida_id' => 1, 'stock_minimo' => 12, 'codigo_material' => 'MAT-037'],
            ['id' => 38, 'categoria_id' => 10, 'nombre' => 'Interruptor termomagnético tripolar 50 A – 500 V – ICC 3 kA', 'unidad_medida_id' => 1, 'stock_minimo' => 12, 'codigo_material' => 'MAT-038'],
            ['id' => 39, 'categoria_id' => 10, 'nombre' => 'Interruptor termomagnético tripolar 63 A – 500 V – ICC 3 kA', 'unidad_medida_id' => 1, 'stock_minimo' => 12, 'codigo_material' => 'MAT-039'],
            ['id' => 40, 'categoria_id' => 11, 'nombre' => 'Tapas metálicas monofásicas', 'unidad_medida_id' => 1, 'stock_minimo' => 25, 'codigo_material' => 'MAT-040'],
            ['id' => 41, 'categoria_id' => 11, 'nombre' => 'Tapas metálicas trifásicas', 'unidad_medida_id' => 1, 'stock_minimo' => 25, 'codigo_material' => 'MAT-041'],
            ['id' => 42, 'categoria_id' => 11, 'nombre' => 'Tapas poliméricas monofásicas', 'unidad_medida_id' => 1, 'stock_minimo' => 25, 'codigo_material' => 'MAT-042'],
            ['id' => 43, 'categoria_id' => 11, 'nombre' => 'Tapas poliméricas trifásicas', 'unidad_medida_id' => 1, 'stock_minimo' => 25, 'codigo_material' => 'MAT-043'],
        ];

        foreach ($materiales as $material) {
            Material::create($material);
        }

        $this->command->info('Materiales creados exitosamente.');
    }
}
