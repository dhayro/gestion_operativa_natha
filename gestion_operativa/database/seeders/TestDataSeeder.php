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
use App\Models\FichaActividadEmpleado;
use App\Models\TipoCombustible;
use App\Models\Vehiculo;
use App\Models\AsignacionVehiculo;
use App\Models\Papeleta;
use App\Models\DotacionCombustible;
use App\Models\UnidadMedida;
use App\Models\Categoria;
use App\Models\Material;
use App\Models\Nea;
use App\Models\NeaDetalle;
use App\Models\Pecosa;
use App\Models\PecosaDetalle;
use App\Models\Movimiento;
use App\Models\MaterialPecosaMovimiento;
use App\Models\Proveedor;
use App\Models\TipoComprobante;
use App\Models\PrecintoFichaActividad;
use App\Models\MaterialFichaActividad;
use App\Models\MedidorFichaActividad;
use App\Models\FotoFichaActividad;

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
            echo "[7] Creando Cuadrillas...";
            $cuadrillaA = Cuadrilla::create(['nombre' => 'Cuadrilla A - Instalaciones', 'estado' => true]);
            $cuadrillaB = Cuadrilla::create(['nombre' => 'Cuadrilla B - Mantenimiento', 'estado' => true]);
            
            // Asignar empleados a Cuadrilla A
            $cuadrillaEmp1 = CuadrillaEmpleado::create(['cuadrilla_id' => $cuadrillaA->id, 'empleado_id' => $emp1->id, 'estado' => true]);
            CuadrillaEmpleado::create(['cuadrilla_id' => $cuadrillaA->id, 'empleado_id' => $emp2->id, 'estado' => true]);
            
            // Asignar empleados a Cuadrilla B
            $cuadrillaEmp3 = CuadrillaEmpleado::create(['cuadrilla_id' => $cuadrillaB->id, 'empleado_id' => $emp3->id, 'estado' => true]);
            CuadrillaEmpleado::create(['cuadrilla_id' => $cuadrillaB->id, 'empleado_id' => $emp4->id, 'estado' => true]);
            echo " ✓ (2 cuadrillas con 2 empleados cada una)\n";

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
                'cuadrilla_id' => $cuadrillaA->id,
                'vehiculo_id' => $vehiculo1->id,
                'estado' => true
            ]);
            $asignacion2 = AsignacionVehiculo::create([
                'cuadrilla_id' => $cuadrillaB->id,
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

            echo "[15] Creando Suministros...";
            $sumin1 = Suministro::create([
                'codigo' => 'SUM-001',
                'nombre' => 'Residencial Test A',
                'direccion' => 'Calle Principal 123, Lima',
                'estado' => true
            ]);
            $sumin2 = Suministro::create([
                'codigo' => 'SUM-002',
                'nombre' => 'Comercial Test B',
                'direccion' => 'Calle Secundaria 456, Lima',
                'estado' => true
            ]);
            echo " ✓ (2 suministros)\n";

            echo "[16] Creando Fichas Actividad...";
            $fichaA = FichaActividad::create([
                'suministro_id' => $sumin1->id,
                'tipo_actividad_id' => $tipoInspeccion->id,
                'estado' => true
            ]);
            $fichaB = FichaActividad::create([
                'suministro_id' => $sumin2->id,
                'tipo_actividad_id' => $tipoMantenimiento->id,
                'estado' => true
            ]);
            echo " ✓ (2 fichas)\n";

            echo "[17] Asociando Medidores a Fichas...";
            $fichaA->medidores()->attach([$med1->id, $med2->id]);
            $fichaB->medidores()->attach([$med1->id]);
            echo " ✓\n";

            // ==================== MATERIALES Y NEA ====================
            
            echo "[18] Creando Categorías...";
            $catElectricos = Categoria::create(['nombre' => 'Eléctricos', 'estado' => true]);
            $catPlasticos = Categoria::create(['nombre' => 'Plásticos', 'estado' => true]);
            $catManuales = Categoria::create(['nombre' => 'Herramientas Manuales', 'estado' => true]);
            echo " ✓\n";

            echo "[19] Creando Unidades de Medida...";
            $unKilos = UnidadMedida::create(['nombre' => 'kilogramos', 'estado' => true]);
            $unPack = UnidadMedida::create(['nombre' => 'paquetes', 'estado' => true]);
            $unUnidades = UnidadMedida::create(['nombre' => 'unidades', 'estado' => true]);
            echo " ✓\n";

            echo "[20] Creando Materiales...";
            $matCable = Material::create([
                'nombre' => 'Cable TW 12 AWG',
                'codigo_material' => 'CAB-001',
                'categoria_id' => $catElectricos->id,
                'unidad_medida_id' => $unKilos->id,
                'precio_unitario' => 15.50,
                'stock_minimo' => 10,
                'estado' => true
            ]);
            $matConector = Material::create([
                'nombre' => 'Conector Eléctrico',
                'codigo_material' => 'CON-001',
                'categoria_id' => $catPlasticos->id,
                'unidad_medida_id' => $unPack->id,
                'precio_unitario' => 8.75,
                'stock_minimo' => 5,
                'estado' => true
            ]);
            $matAlicate = Material::create([
                'nombre' => 'Alicate de corte',
                'codigo_material' => 'HER-001',
                'categoria_id' => $catManuales->id,
                'unidad_medida_id' => $unUnidades->id,
                'precio_unitario' => 25.00,
                'stock_minimo' => 2,
                'estado' => true
            ]);
            $matCinta = Material::create([
                'nombre' => 'Cinta Aislante',
                'codigo_material' => 'CIN-001',
                'categoria_id' => $catPlasticos->id,
                'unidad_medida_id' => $unPack->id,
                'precio_unitario' => 3.50,
                'stock_minimo' => 20,
                'estado' => true
            ]);
            // Materiales adicionales
            $matTubo = Material::create([
                'nombre' => 'Tubo PVC 3/4"',
                'codigo_material' => 'TUB-001',
                'categoria_id' => $catPlasticos->id,
                'unidad_medida_id' => $unUnidades->id,
                'precio_unitario' => 5.25,
                'stock_minimo' => 15,
                'estado' => true
            ]);
            $matFusible = Material::create([
                'nombre' => 'Fusible 15A',
                'codigo_material' => 'FUS-001',
                'categoria_id' => $catElectricos->id,
                'unidad_medida_id' => $unPack->id,
                'precio_unitario' => 0.85,
                'stock_minimo' => 50,
                'estado' => true
            ]);
            $matTerminal = Material::create([
                'nombre' => 'Terminal de cobre',
                'codigo_material' => 'TER-001',
                'categoria_id' => $catElectricos->id,
                'unidad_medida_id' => $unPack->id,
                'precio_unitario' => 2.30,
                'stock_minimo' => 30,
                'estado' => true
            ]);
            $matDesarmador = Material::create([
                'nombre' => 'Desarmador Phillips',
                'codigo_material' => 'HER-002',
                'categoria_id' => $catManuales->id,
                'unidad_medida_id' => $unUnidades->id,
                'precio_unitario' => 12.50,
                'stock_minimo' => 3,
                'estado' => true
            ]);
            $matPinza = Material::create([
                'nombre' => 'Pinza Punta Fina',
                'codigo_material' => 'HER-003',
                'categoria_id' => $catManuales->id,
                'unidad_medida_id' => $unUnidades->id,
                'precio_unitario' => 18.75,
                'stock_minimo' => 2,
                'estado' => true
            ]);
            echo " ✓ (9 materiales)\n";

            echo "[21] Creando Proveedor...";
            $proveedorSupremo = Proveedor::create([
                'razon_social' => 'Supremo Materiales S.A.C.',
                'ruc' => '20123456789',
                'contacto' => 'Juan Vendedor',
                'email' => 'ventas@supremo.com',
                'telefono' => '555-0001',
                'estado' => true
            ]);
            echo " ✓\n";

            echo "[22] Creando Tipo de Comprobante...";
            $tipoFactura = TipoComprobante::create([
                'nombre' => 'Factura',
                'estado' => true
            ]);
            echo " ✓\n";

            echo "[23] Creando NEA (Nota de Entrada de Almacén)...";
            $nea = Nea::create([
                'proveedor_id' => $proveedorSupremo->id,
                'fecha' => now()->subDays(5)->toDateString(),
                'nro_documento' => 'NEA-2025-00001',
                'tipo_comprobante_id' => $tipoFactura->id,
                'numero_comprobante' => 'FAC-001-2025',
                'observaciones' => 'Compra de materiales para suministro eléctrico',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓\n";

            echo "[24] Agregando detalles a NEA...";
            $neaDetCable = NeaDetalle::create([
                'nea_id' => $nea->id,
                'material_id' => $matCable->id,
                'cantidad' => 100,
                'precio_unitario' => 15.50,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $neaDetConector = NeaDetalle::create([
                'nea_id' => $nea->id,
                'material_id' => $matConector->id,
                'cantidad' => 500,
                'precio_unitario' => 8.75,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $neaDetAlicate = NeaDetalle::create([
                'nea_id' => $nea->id,
                'material_id' => $matAlicate->id,
                'cantidad' => 10,
                'precio_unitario' => 25.00,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $neaDetCinta = NeaDetalle::create([
                'nea_id' => $nea->id,
                'material_id' => $matCinta->id,
                'cantidad' => 200,
                'precio_unitario' => 3.50,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (4 detalles)\n";

            echo "[25] Creando PECOSA para Cuadrilla A...";
            $pecosaA = Pecosa::create([
                'cuadrilla_empleado_id' => $cuadrillaEmp1->id,
                'fecha' => now()->toDateString(),
                'nro_documento' => 'PECOSA-2025-00001',
                'observaciones' => 'Materiales para instalaciones eléctricas',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓\n";

            echo "[25.1] Creando PECOSA para Cuadrilla B...";
            $pecosaB = Pecosa::create([
                'cuadrilla_empleado_id' => $cuadrillaEmp3->id,
                'fecha' => now()->toDateString(),
                'nro_documento' => 'PECOSA-2025-00002',
                'observaciones' => 'Materiales para mantenimiento y reparación',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓\n";

            echo "[25.2] Asignando PECOSAs a FICHAs...";
            // Asignar PECOSA A a FICHA A (Cuadrilla A)
            $fichaA->update(['pecosa_id' => $pecosaA->id]);
            // Asignar PECOSA B a FICHA B (Cuadrilla B)
            $fichaB->update(['pecosa_id' => $pecosaB->id]);
            echo " ✓ (2 FICHAs con PECOSAs asignadas)\n";

            echo "[26] Agregando detalles a PECOSA A...";
            $pecosaDetCable = PecosaDetalle::create([
                'pecosa_id' => $pecosaA->id,
                'nea_detalle_id' => $neaDetCable->id,
                'cantidad' => 50,
                'precio_unitario' => 15.50,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $pecosaDetConector = PecosaDetalle::create([
                'pecosa_id' => $pecosaA->id,
                'nea_detalle_id' => $neaDetConector->id,
                'cantidad' => 250,
                'precio_unitario' => 8.75,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $pecosaDetAlicate = PecosaDetalle::create([
                'pecosa_id' => $pecosaA->id,
                'nea_detalle_id' => $neaDetAlicate->id,
                'cantidad' => 5,
                'precio_unitario' => 25.00,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (3 detalles para PECOSA A)\n";

            echo "[26.1] Agregando detalles a PECOSA B...";
            $pecosaDetCintaB = PecosaDetalle::create([
                'pecosa_id' => $pecosaB->id,
                'nea_detalle_id' => $neaDetCinta->id,
                'cantidad' => 100,
                'precio_unitario' => 3.50,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $pecosaDetCableB = PecosaDetalle::create([
                'pecosa_id' => $pecosaB->id,
                'nea_detalle_id' => $neaDetCable->id,
                'cantidad' => 30,
                'precio_unitario' => 15.50,
                'incluye_igv' => true,
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (2 detalles para PECOSA B)\n";

            echo "[27] Registrando movimientos de ENTRADA en tabla 'movimientos'...";
            // Movimientos PECOSA A (NEA -> PECOSA A)
            Movimiento::create([
                'material_id' => $matCable->id,
                'tipo_movimiento' => 'entrada',
                'nea_detalle_id' => $neaDetCable->id,
                'pecosa_detalle_id' => $pecosaDetCable->id,
                'cantidad' => 50,
                'precio_unitario' => 15.50,
                'incluye_igv' => true,
                'fecha' => now()->toDateString(),
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            Movimiento::create([
                'material_id' => $matConector->id,
                'tipo_movimiento' => 'entrada',
                'nea_detalle_id' => $neaDetConector->id,
                'pecosa_detalle_id' => $pecosaDetConector->id,
                'cantidad' => 250,
                'precio_unitario' => 8.75,
                'incluye_igv' => true,
                'fecha' => now()->toDateString(),
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            Movimiento::create([
                'material_id' => $matAlicate->id,
                'tipo_movimiento' => 'entrada',
                'nea_detalle_id' => $neaDetAlicate->id,
                'pecosa_detalle_id' => $pecosaDetAlicate->id,
                'cantidad' => 5,
                'precio_unitario' => 25.00,
                'incluye_igv' => true,
                'fecha' => now()->toDateString(),
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            // Movimientos PECOSA B (NEA -> PECOSA B)
            Movimiento::create([
                'material_id' => $matCinta->id,
                'tipo_movimiento' => 'entrada',
                'nea_detalle_id' => $neaDetCinta->id,
                'pecosa_detalle_id' => $pecosaDetCintaB->id,
                'cantidad' => 100,
                'precio_unitario' => 3.50,
                'incluye_igv' => true,
                'fecha' => now()->toDateString(),
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            Movimiento::create([
                'material_id' => $matCable->id,
                'tipo_movimiento' => 'entrada',
                'nea_detalle_id' => $neaDetCable->id,
                'pecosa_detalle_id' => $pecosaDetCableB->id,
                'cantidad' => 30,
                'precio_unitario' => 15.50,
                'incluye_igv' => true,
                'fecha' => now()->toDateString(),
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (5 movimientos NEA->PECOSA)\n";

            echo "[28] Registrando movimientos de ENTRADA en tabla 'material_pecosa_movimientos'...";
            // Saldos iniciales PECOSA A
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaA->id,
                'material_id' => $matCable->id,
                'cantidad' => 50,
                'tipo_movimiento' => 'entrada',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaA->id,
                'material_id' => $matConector->id,
                'cantidad' => 250,
                'tipo_movimiento' => 'entrada',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaA->id,
                'material_id' => $matAlicate->id,
                'cantidad' => 5,
                'tipo_movimiento' => 'entrada',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            // Saldos iniciales PECOSA B
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaB->id,
                'material_id' => $matCinta->id,
                'cantidad' => 100,
                'tipo_movimiento' => 'entrada',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaB->id,
                'material_id' => $matCable->id,
                'cantidad' => 30,
                'tipo_movimiento' => 'entrada',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (5 saldos iniciales material_pecosa_movimientos)\n";

            echo "[29] Asignando empleados a FICHAs...";
            // Asignar empleado a FICHA A
            FichaActividadEmpleado::create([
                'ficha_actividad_id' => $fichaA->id,
                'cuadrilla_empleado_id' => $cuadrillaEmp1->id,
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            // Asignar empleado a FICHA B
            FichaActividadEmpleado::create([
                'ficha_actividad_id' => $fichaB->id,
                'cuadrilla_empleado_id' => $cuadrillaEmp3->id,
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            echo " ✓ (2 fichas con empleados asignados)\n";

            // ==================== MEDIDORES Y MEDIDOR_FICHA_ACTIVIDAD ====================
            // (CREADOS ANTES DE PRECINTOS porque los precintos necesitan referencias a medidores)
            echo "[30] Creando Medidores adicionales...";
            $med3 = Medidor::create(['serie' => 'MED-003', 'modelo' => 'Modelo C - ABB', 'estado' => true]);
            $med4 = Medidor::create(['serie' => 'MED-004', 'modelo' => 'Modelo D - Schneider', 'estado' => true]);
            $med5 = Medidor::create(['serie' => 'MED-005', 'modelo' => 'Modelo E - Siemens', 'estado' => true]);
            echo " ✓ (3 medidores adicionales, total 5)\n";

            echo "[31] Asociando Medidores a FICHA A...";
            $medFichaA1 = MedidorFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'medidor_id' => $med1->id,
                'tipo' => 'existente',
                'digitos_enteros' => 5,
                'digitos_decimales' => 2,
                'lectura' => 12345,
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            $medFichaA2 = MedidorFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'medidor_id' => $med3->id,
                'tipo' => 'existente',
                'digitos_enteros' => 4,
                'digitos_decimales' => 2,
                'lectura' => 5678,
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            echo " ✓ (2 medidores en FICHA A)\n";

            echo "[32] Asociando Medidores a FICHA B...";
            $medFichaB1 = MedidorFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'medidor_id' => $med2->id,
                'tipo' => 'nuevo',
                'digitos_enteros' => 5,
                'digitos_decimales' => 2,
                'lectura' => 98765,
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            $medFichaB2 = MedidorFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'medidor_id' => $med4->id,
                'tipo' => 'retirado',
                'digitos_enteros' => 4,
                'digitos_decimales' => 2,
                'lectura' => 3456,
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            echo " ✓ (2 medidores en FICHA B)\n";

            // ==================== PRECINTOS ====================
            echo "[30] Creando Precintos para FICHA A...";
            // Precintos asociados a medidores de FICHA A
            $precinto1 = PrecintoFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'medidor_ficha_actividad_id' => $medFichaA1->id,
                'material_id' => $matCable->id,
                'tipo' => 'tapa',
                'numero_precinto' => 'PREC-2025-001',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $precinto2 = PrecintoFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'medidor_ficha_actividad_id' => $medFichaA1->id,
                'material_id' => $matCinta->id,
                'tipo' => 'caja',
                'numero_precinto' => 'PREC-2025-002',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $precinto3 = PrecintoFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'medidor_ficha_actividad_id' => $medFichaA2->id,
                'material_id' => $matConector->id,
                'tipo' => 'bornera',
                'numero_precinto' => 'PREC-2025-003',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (3 precintos para FICHA A)\n";

            echo "[30] Creando Precintos para FICHA B...";
            // Precintos asociados a medidores de FICHA B
            $precinto4 = PrecintoFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'medidor_ficha_actividad_id' => $medFichaB1->id,
                'material_id' => $matDesarmador->id,
                'tipo' => 'tapa',
                'numero_precinto' => 'PREC-2025-004',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $precinto5 = PrecintoFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'medidor_ficha_actividad_id' => $medFichaB2->id,
                'material_id' => $matFusible->id,
                'tipo' => 'caja',
                'numero_precinto' => 'PREC-2025-005',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (2 precintos para FICHA B)\n";

            // ==================== MATERIALES EN FICHAS ====================
            echo "[33] Agregando Materiales a FICHA A...";
            $matFichaA1 = MaterialFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'material_id' => $matCable->id,
                'cantidad' => 5.5,
                'observacion' => 'Cable para reconexión de circuito principal',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $matFichaA2 = MaterialFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'material_id' => $matConector->id,
                'cantidad' => 10,
                'observacion' => 'Conectores para empalmes de seguridad',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $matFichaA3 = MaterialFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'material_id' => $matCinta->id,
                'cantidad' => 2,
                'observacion' => 'Cinta aislante para aislamientos de emergencia',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (3 materiales en FICHA A)\n";

            echo "[34] Agregando Materiales a FICHA B...";
            $matFichaB1 = MaterialFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'material_id' => $matAlicate->id,
                'cantidad' => 1,
                'observacion' => 'Alicate para corte de conductores antiguos',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $matFichaB2 = MaterialFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'material_id' => $matDesarmador->id,
                'cantidad' => 1,
                'observacion' => 'Desarmador para desmontaje de tablero de control',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            $matFichaB3 = MaterialFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'material_id' => $matFusible->id,
                'cantidad' => 3,
                'observacion' => 'Fusibles de reemplazo en protecciones',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (3 materiales en FICHA B)\n";

            // ==================== FOTOS EN FICHAS ====================
            echo "[35] Agregando Fotos a FICHA A...";
            FotoFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'url' => 'https://via.placeholder.com/600x400?text=Instalacion+Electrica+1',
                'descripcion' => 'Vista general de la instalación eléctrica antes del trabajo',
                'fecha' => now()->toDateString(),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            FotoFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'url' => 'https://via.placeholder.com/600x400?text=Medidor+Principal',
                'descripcion' => 'Medidor principal después del mantenimiento',
                'fecha' => now()->toDateString(),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            FotoFichaActividad::create([
                'ficha_actividad_id' => $fichaA->id,
                'url' => 'https://via.placeholder.com/600x400?text=Caja+Distribucion',
                'descripcion' => 'Caja de distribución con precintos de seguridad colocados',
                'fecha' => now()->toDateString(),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            echo " ✓ (3 fotos en FICHA A)\n";

            echo "[36] Agregando Fotos a FICHA B...";
            FotoFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'url' => 'https://via.placeholder.com/600x400?text=Mantenimiento+Preventivo',
                'descripcion' => 'Personal realizando mantenimiento preventivo de equipos',
                'fecha' => now()->toDateString(),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            FotoFichaActividad::create([
                'ficha_actividad_id' => $fichaB->id,
                'url' => 'https://via.placeholder.com/600x400?text=Inspeccion+Final',
                'descripcion' => 'Inspección final de trabajos realizados',
                'fecha' => now()->toDateString(),
                'usuario_creacion_id' => $userAdmin->id,
                'estado' => true
            ]);
            echo " ✓ (2 fotos en FICHA B)\n";

            // ==================== MOVIMIENTOS DE MATERIALES EN PECOSA ====================
            echo "[37] Registrando Movimientos de SALIDA en material_pecosa_movimientos (por materiales en fichas)...";
            // Materiales FICHA A -> PECOSA A
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaA->id,
                'material_id' => $matCable->id,
                'ficha_actividad_id' => $fichaA->id,
                'material_ficha_actividades_id' => $matFichaA1->id,
                'cantidad' => 5.5,
                'tipo_movimiento' => 'salida',
                'observaciones' => 'Asignado a FICHA A (Inspección)',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaA->id,
                'material_id' => $matConector->id,
                'ficha_actividad_id' => $fichaA->id,
                'material_ficha_actividades_id' => $matFichaA2->id,
                'cantidad' => 10,
                'tipo_movimiento' => 'salida',
                'observaciones' => 'Asignado a FICHA A (Inspección)',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaA->id,
                'material_id' => $matCinta->id,
                'ficha_actividad_id' => $fichaA->id,
                'material_ficha_actividades_id' => $matFichaA3->id,
                'cantidad' => 2,
                'tipo_movimiento' => 'salida',
                'observaciones' => 'Asignado a FICHA A (Inspección)',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            // Materiales FICHA B -> PECOSA B
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaB->id,
                'material_id' => $matAlicate->id,
                'ficha_actividad_id' => $fichaB->id,
                'material_ficha_actividades_id' => $matFichaB1->id,
                'cantidad' => 1,
                'tipo_movimiento' => 'salida',
                'observaciones' => 'Asignado a FICHA B (Mantenimiento)',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaB->id,
                'material_id' => $matDesarmador->id,
                'ficha_actividad_id' => $fichaB->id,
                'material_ficha_actividades_id' => $matFichaB2->id,
                'cantidad' => 1,
                'tipo_movimiento' => 'salida',
                'observaciones' => 'Asignado a FICHA B (Mantenimiento)',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            MaterialPecosaMovimiento::create([
                'pecosa_id' => $pecosaB->id,
                'material_id' => $matFusible->id,
                'ficha_actividad_id' => $fichaB->id,
                'material_ficha_actividades_id' => $matFichaB3->id,
                'cantidad' => 3,
                'tipo_movimiento' => 'salida',
                'observaciones' => 'Asignado a FICHA B (Mantenimiento)',
                'estado' => true,
                'usuario_creacion_id' => $userAdmin->id
            ]);
            echo " ✓ (6 movimientos de salida con trazabilidad)\n";

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
            echo "  ✓ 2 Cuadrillas (4 empleados total: 2 por cuadrilla)\n";
            echo "  ✓ 2 Tipos de Combustible\n";
            echo "  ✓ 2 Vehículos\n";
            echo "  ✓ 2 Asignaciones de Vehículos\n";
            echo "  ✓ 2 Papeletas\n";
            echo "  ✓ 2 Dotaciones de Combustible\n";
            echo "  ✓ 2 Tipos de Actividad\n";
            echo "  ✓ 5 Medidores (con MedidorFichaActividad)\n";
            echo "  ✓ 2 Suministros\n";
            echo "  ✓ 2 Fichas Actividad\n";
            echo "  ✓ 9 Materiales (con categorías y unidades de medida)\n";
            echo "  ✓ 5 Precintos en Fichas\n";
            echo "  ✓ 4 Medidor-Ficha-Actividad (lecturas de medidores)\n";
            echo "  ✓ 6 Materiales en Fichas (MaterialFichaActividad)\n";
            echo "  ✓ 5 Fotos en Fichas\n";
            echo "  ✓ 2 PECOSAs\n";
            echo "  ✓ 5 Detalles en NEA\n";
            echo "  ✓ 5 Detalles en PECOSAs\n";
            echo "  ✓ 11 Movimientos en material_pecosa_movimientos (con trazabilidad)\n";
            echo "\n🎯 PRUEBAS FUNCIONALES DISPONIBLES:\n";
            echo "  1. Crear/editar FICHA → Ver materiales disponibles de PECOSA\n";
            echo "  2. Agregar material a FICHA → Verifica movimiento de SALIDA\n";
            echo "  3. Eliminar material de FICHA → Verifica movimiento de ENTRADA con trazabilidad\n";
            echo "  4. Ver precintos de FICHA → Lista completa con búsqueda\n";
            echo "  5. Ver medidores y lecturas → Con cálculo de consumo\n";
            echo "  6. Ver fotos por FICHA → Galería de trabajos realizados\n";
            echo "  7. Verificar saldos de PECOSA → Con movimientos de entrada/salida\n";
            echo "\n";

        } catch (\Exception $e) {
            echo "\n❌ ERROR: " . $e->getMessage() . "\n";
            echo "Línea: " . $e->getLine() . "\n";
            echo "Archivo: " . $e->getFile() . "\n";
        }
    }
}
