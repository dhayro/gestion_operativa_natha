<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AreaSeeder;
use Database\Seeders\CargoSeeder;
use Database\Seeders\EmpleadoSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\TipoCombustibleSeeder;
use Database\Seeders\VehiculoSeeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed de catálogo principal
        Schema::disableForeignKeyConstraints();

        $this->call([
            UbigeoSeeder::class,
            AreaSeeder::class,
            CargoSeeder::class,
            TipoCombustibleSeeder::class,
            VehiculoSeeder::class,
            EmpleadoSeeder::class,
            UserSeeder::class,
            // Otros seeders se pueden agregar aquí si es necesario
        ]);

        Schema::enableForeignKeyConstraints();

        // Seed de datos de prueba (si se requiere)
        // $this->call(TestDataSeeder::class);
    }
}
