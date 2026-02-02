<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AreaSeeder;
use Database\Seeders\CargoSeeder;
use Database\Seeders\CategoriaSeeder;
use Database\Seeders\EmpleadoSeeder;
use Database\Seeders\MaterialSeeder;
use Database\Seeders\ProveedorSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\EmpleadoUsuarioSeeder;
use Database\Seeders\TipoCombustibleSeeder;
use Database\Seeders\VehiculoSeeder;
use Database\Seeders\SoatRealSeeder;
use Database\Seeders\UnidadMedidaSeeder;
use Database\Seeders\SuministrosMedidoresSeeder;
use Database\Seeders\ConstruccionesSeeder;
use Database\Seeders\TiposPropiedadSeeder;
use Database\Seeders\SituacionesSeeder;
use Database\Seeders\UsosSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
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
            PermissionSeeder::class,
            RoleSeeder::class,
            UbigeoSeeder::class,
            AreaSeeder::class,
            CargoSeeder::class,
            CategoriaSeeder::class,
            TipoCombustibleSeeder::class,
            UnidadMedidaSeeder::class,
            MaterialSeeder::class,
            ProveedorSeeder::class,
            VehiculoSeeder::class,
            SoatRealSeeder::class,
            EmpleadoSeeder::class,
            UserSeeder::class,
            EmpleadoUsuarioSeeder::class,
            EmpleadoUsuarioSeeder::class,
            ConstruccionesSeeder::class,
            TiposPropiedadSeeder::class,
            SituacionesSeeder::class,
            UsosSeeder::class,
            SuministrosMedidoresSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();

        // Seed de datos de prueba (si se requiere)
        // $this->call(TestDataSeeder::class);
    }
}
