<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Proveedor::truncate();

        $proveedores = [
            [
                'id' => 1,
                'razon_social' => 'LA POSITIVA SEGUROS Y REASEGUROS S.A.',
                'ruc' => '20100210909',
                'contacto' => null,
                'email' => null,
                'telefono' => '211 0211',
                'direccion' => 'CAL.FRANCISCO MASIAS NRO. 370 (CRUCE CON AV. JAVIE...)',
                'estado' => true
            ],
            [
                'id' => 2,
                'razon_social' => 'EMPRESA CONCESIONARIA DE ELECTRICIDAD DE UCAYALI SOCIEDAD ANONIMA',
                'ruc' => '20232236273',
                'contacto' => null,
                'email' => null,
                'telefono' => '61596454',
                'direccion' => 'Av. Circunvalacion Nro. 300 (Planta Electroucayali)',
                'estado' => true
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }

        $this->command->info('Proveedores creados exitosamente.');
    }
}
