<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ubigeo;
use App\Models\Cargo;
use App\Models\Area;
use App\Models\Empleado;
use App\Models\Cuadrilla;
use App\Models\CuadrillaEmpleado;
use App\Models\TiposActividad;
use App\Models\Medidor;
use App\Models\Suministro;
use App\Models\FichaActividad;
use App\Models\TipoCombustible;
use App\Models\Vehiculo;
use App\Models\AsignacionVehiculo;
use App\Models\Papeleta;
use App\Models\DotacionCombustible;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n========== INICIANDO SEEDER COMPLETO ===========\n";

        try {
            // DATOS MAESTROS
            echo "\n[1] Creando Usuario Admin...";
            $userAdmin = User::create([
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'estado' => true
            ]);
            echo " ✓\n";

            echo "[2] Creando Ubigeo...";
            $ubigeo = Ubigeo::create(['nombre' => 'Lima - Lima - Lima', 'codigo_postal' => '150131', 'estado' => true]);
            echo " ✓\n";

            echo "[3] Creando Cargos...";
            $cargoOperario = Cargo::create(['nombre' => 'Operario', 'estado' => true]);
            $cargoSupervisor = Cargo::create(['nombre' => 'Supervisor', 'estado' => true]);
            $cargoChofer = Cargo::create(['nombre' => 'Chofer', 'estado' => true]);
            echo " ✓\n";

            echo "[4] Creando Áreas...";
            $areaOperaciones = Area::create(['nombre' => 'Operaciones', 'estado' => true]);
            $areaAdmin = Area::create(['nombre' => 'Administrativo', 'estado' => true]);
            echo " ✓\n";

            // EMPLEADOS PRIMERO (sin usuarios)
            echo "[5] Creando Empleados...";
            $emp1 = Empleado::create([
                'dni' => '12345678',
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'juan@test.com',
                'cargo_id' => $cargoOperario->id,
                'area_id' => $areaOperaciones->id,
                'ubigeo_id' => $ubigeo->id,
                'estado' => true
            ]);
            $emp2 = Empleado::create([
                'dni' => '87654321',
                'nombre' => 'María',
                'apellido' => 'González',
                'email' => 'maria@test.com',
                'cargo_id' => $cargoOperario->id,
                'area_id' => $areaOperaciones->id,
                'ubigeo_id' => $ubigeo->id,
                'estado' => true
            ]);
            $emp3 = Empleado::create([
                'dni' => '11111111',
                'nombre' => 'Carlos',
                'apellido' => 'López',
                'email' => 'carlos@test.com',
                'cargo_id' => $cargoSupervisor->id,
                'area_id' => $areaOperaciones->id,
                'ubigeo_id' => $ubigeo->id,
                'estado' => true
            ]);
            $emp4 = Empleado::create([
                'dni' => '22222222',
                'nombre' => 'Roberto',
                'apellido' => 'Martínez',
                'email' => 'dhayro.kong@hotmail.com',
                'cargo_id' => $cargoChofer->id,
                'area_id' => $areaOperaciones->id,
                'ubigeo_id' => $ubigeo->id,
                'estado' => true
            ]);
            echo " ✓ (4 empleados)\n";

            // USUARIOS PARA EMPLEADOS
            echo "[6] Creando Usuarios para Empleados...";
            
            // Usuario para Juan
            $userJuan = User::create([
                'name' => 'Juan Pérez',
                'email' => 'juan@test.com',
                'password' => bcrypt('password'),
                'empleado_id' => $emp1->id,
                'estado' => true
            ]);
            
            // Usuario para María
            $userMaria = User::create([
                'name' => 'María González',
                'email' => 'maria@test.com',
                'password' => bcrypt('password'),
                'empleado_id' => $emp2->id,
                'estado' => true
            ]);
            
            // Usuario para Carlos
            $userCarlos = User::create([
                'name' => 'Carlos López',
                'email' => 'carlos@test.com',
                'password' => bcrypt('password'),
                'empleado_id' => $emp3->id,
                'estado' => true
            ]);
            
            // Usuario para Roberto (dhayro.kong@hotmail.com)
            $userRoberto = User::create([
                'name' => 'Roberto Martínez',
                'email' => 'dhayro.kong@hotmail.com',
                'password' => bcrypt('password'),
                'empleado_id' => $emp4->id,
                'estado' => true
            ]);
            echo " ✓ (4 usuarios con empleado_id)\n";

            // CUADRILLA
            echo "[7] Creando Cuadrilla...";
            $cuadrilla = Cuadrilla::create(['nombre' => 'Cuadrilla A', 'estado' => true]);
            CuadrillaEmpleado::create(['cuadrilla_id' => $cuadrilla->id, 'empleado_id' => $emp1->id, 'estado' => true]);
            CuadrillaEmpleado::create(['cuadrilla_id' => $cuadrilla->id, 'empleado_id' => $emp2->id, 'estado' => true]);
            CuadrillaEmpleado::create(['cuadrilla_id' => $cuadrilla->id, 'empleado_id' => $emp3->id, 'estado' => true]);
            echo " ✓ (3 empleados asignados)\n";

            // VEHÍCULOS Y COMBUSTIBLE
            echo "[8] Creando Tipos de Combustible...";
            $tipoDiesel = TipoCombustible::create(['nombre' => 'Diesel', 'estado' => true]);
            $tipoGasolina = TipoCombustible::create(['nombre' => 'Gasolina 95', 'estado' => true]);
            echo " ✓\n";

            echo "[9] Creando Vehículos...";
            $vehiculo1 = Vehiculo::create([
                'marca' => 'Toyota',
                'nombre' => 'Toyota Hilux',
                'year' => 2022,
                'modelo' => 'Hilux 2.8',
                'color' => 'Blanco',
                'placa' => 'ABC-123',
                'tipo_combustible_id' => $tipoDiesel->id,
                'estado' => true
            ]);
            $vehiculo2 = Vehiculo::create([
                'marca' => 'Honda',
                'nombre' => 'Honda Civic',
                'year' => 2021,
                'modelo' => 'Civic 1.8',
                'color' => 'Gris',
                'placa' => 'XYZ-789',
                'tipo_combustible_id' => $tipoGasolina->id,
                'estado' => true
            ]);
            echo " ✓ (2 vehículos)\n";

            // PAPELETAS Y DOTACIÓN DE COMBUSTIBLE
            echo "[10] Creando Asignaciones de Vehículos...";
            $asignacion1 = AsignacionVehiculo::create([
                'cuadrilla_id' => $cuadrilla->id,
                'vehiculo_id' => $vehiculo1->id,
                'estado' => true
            ]);
            $asignacion2 = AsignacionVehiculo::create([
                'cuadrilla_id' => $cuadrilla->id,
                'vehiculo_id' => $vehiculo2->id,
                'estado' => true
            ]);
            echo " ✓ (2 asignaciones)\n";

            echo "[11] Creando Papeletas...";
            $papeleta1 = Papeleta::create([
                'asignacion_vehiculo_id' => $asignacion1->id,
                'fecha' => now()->toDateString(),
                'destino' => 'San Isidro - Centro Operativo',
                'motivo' => 'Traslado de material y personal a obra',
                'km_salida' => 12500.500,
                'km_llegada' => 12650.750,
                'fecha_hora_salida' => now()->setHour(8)->setMinute(0),
                'fecha_hora_llegada' => now()->setHour(11)->setMinute(30),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            $papeleta2 = Papeleta::create([
                'asignacion_vehiculo_id' => $asignacion2->id,
                'fecha' => now()->subDays(1)->toDateString(),
                'destino' => 'La Molina - Inspección',
                'motivo' => 'Inspección de red eléctrica',
                'km_salida' => 45000.000,
                'km_llegada' => 45150.250,
                'fecha_hora_salida' => now()->subDays(1)->setHour(9)->setMinute(0),
                'fecha_hora_llegada' => now()->subDays(1)->setHour(14)->setMinute(30),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            echo " ✓ (2 papeletas)\n";

            echo "[12] Creando Dotación de Combustible...";
            DotacionCombustible::create([
                'papeleta_id' => $papeleta1->id,
                'tipo_combustible_id' => $tipoDiesel->id,
                'cantidad_gl' => 25.500,
                'precio_unitario' => 12.50,
                'numero_vale' => 'VALE-001',
                'fecha_carga' => now()->toDateString(),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            DotacionCombustible::create([
                'papeleta_id' => $papeleta2->id,
                'tipo_combustible_id' => $tipoGasolina->id,
                'cantidad_gl' => 15.000,
                'precio_unitario' => 11.80,
                'numero_vale' => 'VALE-002',
                'fecha_carga' => now()->subDays(1)->toDateString(),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            echo " ✓ (2 dotaciones)\n";

            // FICHAS DE ACTIVIDAD
            echo "[13] Creando Tipos de Actividad...";
            $tipoInspeccion = TiposActividad::create(['nombre' => 'Inspección', 'estado' => true]);
            $tipoMantenimiento = TiposActividad::create(['nombre' => 'Mantenimiento', 'estado' => true]);
            echo " ✓\n";

            echo "[14] Creando Medidores...";
            $med1 = Medidor::create(['serie' => 'MED-001', 'modelo' => 'Modelo A', 'estado' => true]);
            $med2 = Medidor::create(['serie' => 'MED-002', 'modelo' => 'Modelo B', 'estado' => true]);
            echo " ✓\n";

            echo "[15] Creando Suministro...";
            $sumin = Suministro::create([
                'codigo' => 'SUM-001',
                'nombre' => 'Residencial Test',
                'direccion' => 'Calle Principal 123, Lima',
                'estado' => true
            ]);
            echo " ✓\n";

            echo "[16] Creando Ficha Actividad...";
            $ficha = FichaActividad::create([
                'suministro_id' => $sumin->id,
                'tipo_actividad_id' => $tipoInspeccion->id,
                'estado' => true
            ]);
            echo " ✓\n";

            echo "[17] Asociando Medidores a Ficha...";
            $ficha->medidores()->attach([$med1->id, $med2->id]);
            echo " ✓\n";

            echo "\n========== SEEDER COMPLETADO EXITOSAMENTE ✓ ===========\n";
            echo "\n��� DATOS CREADOS:\n";
            echo "  ✓ 5 Usuarios (1 Admin + 4 Empleados con empleado_id)\n";
            echo "    - admin@test.com (password)\n";
            echo "    - juan@test.com (password)\n";
            echo "    - maria@test.com (password)\n";
            echo "    - carlos@test.com (password)\n";
            echo "    - dhayro.kong@hotmail.com (password) ← Roberto Martínez\n";
            echo "  ✓ 1 Ubigeo\n";
            echo "  ✓ 3 Cargos\n";
            echo "  ✓ 2 Áreas\n";
            echo "  ✓ 4 Empleados\n";
            echo "  ✓ 1 Cuadrilla (3 empleados)\n";
            echo "  ✓ 2 Tipos de Combustible\n";
            echo "  ✓ 2 Vehículos\n";
            echo "  ✓ 2 Asignaciones de Vehículos\n";
            echo "  ✓ 2 Papeletas\n";
            echo "  ✓ 2 Dotaciones de Combustible\n";
            echo "  ✓ 2 Tipos de Actividad\n";
            echo "  ✓ 2 Medidores\n";
            echo "  ✓ 1 Suministro\n";
            echo "  ✓ 1 Ficha Actividad\n";
            echo "\n";

        } catch (\Exception $e) {
            echo "\n❌ ERROR: " . $e->getMessage() . "\n";
            echo "Línea: " . $e->getLine() . "\n";
            echo "Archivo: " . $e->getFile() . "\n";
        }
    }
}
