<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empleado;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear algunos empleados de prueba primero
        $empleados = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez González',
                'dni' => '12345678',
                'email' => 'juan.perez@test.com',
                'telefono' => '987654321',
                'cargo_id' => 1, // Asegúrate que existan cargos
                'area_id' => 1,  // Asegúrate que existan areas
                'estado' => true
            ],
            [
                'nombre' => 'María',
                'apellido' => 'García López',
                'dni' => '87654321',
                'email' => 'maria.garcia@test.com',
                'telefono' => '987654322',
                'cargo_id' => 1,
                'area_id' => 1,
                'estado' => true
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez Silva',
                'dni' => '11223344',
                'email' => 'carlos.rodriguez@test.com',
                'telefono' => '987654323',
                'cargo_id' => 1,
                'area_id' => 1,
                'estado' => true
            ]
        ];

        foreach ($empleados as $empleadoData) {
            $empleado = Empleado::create($empleadoData);
            
            // Crear usuario asociado al empleado
            User::create([
                'empleado_id' => $empleado->id,
                'name' => $empleado->nombre . ' ' . $empleado->apellido,
                'email' => $empleado->email,
                'password' => Hash::make('password123'),
                'perfil' => 'tecnico',
                'estado' => true
            ]);
        }

        // Crear un usuario administrador sin empleado asociado
        User::create([
            'empleado_id' => null,
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'perfil' => 'admin',
            'estado' => true
        ]);
    }
}