<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empleado;
use App\Models\Area;
use App\Models\Cargo;
use Illuminate\Support\Facades\Hash;

class SimpleUserSeeder extends Seeder
{
    public function run()
    {
        // Obtener el primer area y cargo disponible
        $area = Area::first();
        $cargo = Cargo::first();
        
        if (!$area || !$cargo) {
            $this->command->error('No hay áreas o cargos disponibles.');
            return;
        }

        // Crear empleado de prueba
        $empleado = Empleado::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez González',
            'dni' => '12345678',
            'email' => 'juan.perez@test.com',
            'telefono' => '987654321',
            'cargo_id' => $cargo->id,
            'area_id' => $area->id,
            'estado' => true
        ]);

        // Crear usuario asociado al empleado
        User::create([
            'empleado_id' => $empleado->id,
            'name' => $empleado->nombre . ' ' . $empleado->apellido,
            'email' => $empleado->email,
            'password' => Hash::make('password123'),
            'perfil' => 'tecnico',
            'estado' => true
        ]);

        // Crear usuario administrador sin empleado asociado
        User::create([
            'empleado_id' => null,
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'perfil' => 'admin',
            'estado' => true
        ]);

        $this->command->info('Usuarios creados exitosamente.');
    }
}