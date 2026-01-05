<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario admin
        $adminUser = User::updateOrCreate(
            ['email' => 'nathalyvr25@gmail.com'],
            [
                'empleado_id' => null,
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'perfil' => 'admin',
                'estado' => true,
            ]
        );

        // Asignar rol admin
        $adminRole = Role::where('nombre', 'admin')->first();
        if ($adminRole && !$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminUser->roles()->attach($adminRole);
        }

        // Crear usuario técnico de prueba
        $tecnicoUser = User::updateOrCreate(
            ['email' => 'tecnico@example.com'],
            [
                'empleado_id' => null,
                'name' => 'Técnico Ejemplo',
                'password' => Hash::make('password'),
                'perfil' => 'tecnico',
                'estado' => true,
            ]
        );

        // Asignar rol técnico
        $tecnicoRole = Role::where('nombre', 'tecnico')->first();
        if ($tecnicoRole && !$tecnicoUser->roles()->where('role_id', $tecnicoRole->id)->exists()) {
            $tecnicoUser->roles()->attach($tecnicoRole);
        }

        // Crear usuario operario de prueba
        $operarioUser = User::updateOrCreate(
            ['email' => 'operario@example.com'],
            [
                'empleado_id' => null,
                'name' => 'Operario Ejemplo',
                'password' => Hash::make('password'),
                'perfil' => 'operario',
                'estado' => true,
            ]
        );

        // Asignar rol operario
        $operarioRole = Role::where('nombre', 'operario')->first();
        if ($operarioRole && !$operarioUser->roles()->where('role_id', $operarioRole->id)->exists()) {
            $operarioUser->roles()->attach($operarioRole);
        }

        // Crear usuario supervisor de prueba
        $supervisorUser = User::updateOrCreate(
            ['email' => 'supervisor@example.com'],
            [
                'empleado_id' => null,
                'name' => 'Supervisor Ejemplo',
                'password' => Hash::make('password'),
                'perfil' => 'supervisor',
                'estado' => true,
            ]
        );

        // Asignar rol supervisor
        $supervisorRole = Role::where('nombre', 'supervisor')->first();
        if ($supervisorRole && !$supervisorUser->roles()->where('role_id', $supervisorRole->id)->exists()) {
            $supervisorUser->roles()->attach($supervisorRole);
        }

        $this->command->info('');
        $this->command->info('╔═══════════════════════════════════════════════╗');
        $this->command->info('║  USUARIOS Y ROLES CREADOS EXITOSAMENTE        ║');
        $this->command->info('╚═══════════════════════════════════════════════╝');
        $this->command->info('');
        $this->command->info('👤 Admin:      nathalyvr25@gmail.com / password');
        $this->command->info('👤 Técnico:    tecnico@example.com / password');
        $this->command->info('👤 Operario:   operario@example.com / password');
        $this->command->info('👤 Supervisor: supervisor@example.com / password');
        $this->command->info('');
    }
}
