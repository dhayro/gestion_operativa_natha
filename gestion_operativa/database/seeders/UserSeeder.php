<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'nathalyvr25@gmail.com'],
            [
                'empleado_id' => null,
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'perfil' => 'admin',
                'estado' => true,
            ]
        );
    }
}