<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\UbigeoController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CuadrillaController;
use App\Http\Controllers\CuadrillaEmpleadoController;
use App\Http\Controllers\TipoCombustibleController; 
use App\Http\Controllers\TiposActividadController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\SoatController; 
use App\Http\Controllers\AsignacionVehiculoController; 
use App\Http\Controllers\PapeletaController;
use App\Http\Controllers\DotacionCombustibleController;
use App\Http\Controllers\MedidorController;
use App\Http\Controllers\SuministroController;
use App\Http\Controllers\TipoPropiedadController;
use App\Http\Controllers\ConstruccionController;
use App\Http\Controllers\UsoController;
use App\Http\Controllers\SituacionController;
use App\Http\Controllers\ServicioElectricoController;
use App\Http\Controllers\TipoComprobanteController;
use App\Http\Controllers\NeaController;
use App\Http\Controllers\FichaActividadController;
use App\Http\Controllers\DashboardController;

Route::get('/test-email', function () {
    try {
        // Cambia 'destinatario@example.com' por un correo que puedas revisar.
        $correoDestino = 'dhayro27@gmail.com';

        Mail::raw('Este es un correo de prueba para verificar la configuración SMTP de Laravel.', function ($message) use ($correoDestino) {
            $message->to($correoDestino)
                    ->subject('Prueba de Correo SMTP - Gestión Operativa');
        });

        return '¡Correo de prueba enviado exitosamente! Revisa la bandeja de entrada de: ' . $correoDestino;

    } catch (\Exception $e) {
        // Si hay un error, lo mostrará en pantalla.
        return 'Error al enviar el correo: <br><pre>' . $e->getMessage() . '</pre>';
    }
});

// Rutas de Autenticación
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::middleware(['auth'])->group(function () {

/**
 * =======================
 *          Redirect
 * =======================
 */
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');


// Materiales CRUD (rutas agrupadas igual que empleados)
Route::prefix('materiales')->group(function () {
    Route::get('/data', [MaterialController::class, 'getData'])->name('materiales.data');
    Route::get('/', [MaterialController::class, 'index'])->name('materiales.index');
    Route::post('/', [MaterialController::class, 'store'])->name('materiales.store');
    Route::get('/{material}', [MaterialController::class, 'show'])->name('materiales.show');
    Route::put('/{material}', [MaterialController::class, 'update'])->name('materiales.update');
    Route::delete('/{material}', [MaterialController::class, 'destroy'])->name('materiales.destroy');
});


// Rutas CRUD para Empleados
Route::prefix('empleados')->group(function () {
    Route::get('/data', [EmpleadoController::class, 'getData'])->name('empleados.data');
    Route::get('/', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::post('/', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/{empleado}', [EmpleadoController::class, 'show'])->name('empleados.show');
    Route::put('/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    
    // Rutas para gestión de usuarios de empleados
    Route::get('/{empleado}/usuario', [EmpleadoController::class, 'getUsuario'])->name('empleados.usuario.get');
    Route::post('/{empleado}/usuario', [EmpleadoController::class, 'crearUsuario'])->name('empleados.usuario.crear');
    Route::put('/{empleado}/usuario', [EmpleadoController::class, 'actualizarUsuario'])->name('empleados.usuario.actualizar');
    Route::delete('/{empleado}/usuario', [EmpleadoController::class, 'eliminarUsuario'])->name('empleados.usuario.eliminar');
});

// Rutas CRUD para Áreas
Route::prefix('areas')->group(function () {
    Route::get('/data', [AreaController::class, 'getData'])->name('areas.data');
    Route::get('/api/select', [AreaController::class, 'getAreasForSelect'])->name('areas.select');
    Route::get('/', [AreaController::class, 'index'])->name('areas.index');
    Route::post('/', [AreaController::class, 'store'])->name('areas.store');
    Route::get('/{area}', [AreaController::class, 'show'])->name('areas.show');
    Route::put('/{area}', [AreaController::class, 'update'])->name('areas.update');
    Route::delete('/{area}', [AreaController::class, 'destroy'])->name('areas.destroy');
});

// Rutas CRUD para Unidades de Medida
Route::prefix('unidad_medidas')->group(function () {
    Route::get('/data', [UnidadMedidaController::class, 'getData'])->name('unidad_medidas.data');
    Route::get('/api/select', [UnidadMedidaController::class, 'getUnidadesForSelect'])->name('unidades.select');
    Route::get('/', [UnidadMedidaController::class, 'index'])->name('unidad_medidas.index');
    Route::post('/', [UnidadMedidaController::class, 'store'])->name('unidad_medidas.store');
    Route::get('/{unidad_medida}', [UnidadMedidaController::class, 'show'])->name('unidad_medidas.show');
    Route::put('/{unidad_medida}', [UnidadMedidaController::class, 'update'])->name('unidad_medidas.update');
    Route::delete('/{unidad_medida}', [UnidadMedidaController::class, 'destroy'])->name('unidad_medidas.destroy');
});


// Rutas CRUD para Categorias
Route::prefix('categorias')->group(function () {
    Route::get('/data', [CategoriaController::class, 'getData'])->name('categorias.data');
    Route::get('/api/select', [CategoriaController::class, 'getCategoriasForSelect'])->name('categorias.select');
    Route::get('/', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::get('/{categoria}', [CategoriaController::class, 'show'])->name('categorias.show');
    Route::put('/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
});

// Rutas CRUD para Cargos
Route::prefix('cargos')->group(function () {
    Route::get('/data', [CargoController::class, 'getData'])->name('cargos.data');
    Route::get('/api/select', [CargoController::class, 'getCargosForSelect'])->name('cargos.select');
    Route::get('/', [CargoController::class, 'index'])->name('cargos.index');
    Route::post('/', [CargoController::class, 'store'])->name('cargos.store');
    Route::get('/{cargo}', [CargoController::class, 'show'])->name('cargos.show');
    Route::put('/{cargo}', [CargoController::class, 'update'])->name('cargos.update');
    Route::delete('/{cargo}', [CargoController::class, 'destroy'])->name('cargos.destroy');
});

// Rutas CRUD para Tipos de Combustible
Route::prefix('tipo_combustibles')->group(function () {
    Route::get('/data', [TipoCombustibleController::class, 'getData'])->name('tipo_combustibles.data');
    Route::get('/api/select', [TipoCombustibleController::class, 'getTiposCombustibleForSelect'])->name('tipos_combustible.select');
    Route::get('/', [TipoCombustibleController::class, 'index'])->name('tipo_combustibles.index');
    Route::post('/', [TipoCombustibleController::class, 'store'])->name('tipo_combustibles.store');
    Route::get('/{tipoCombustible}', [TipoCombustibleController::class, 'show'])->name('tipo_combustibles.show');
    Route::put('/{tipoCombustible}', [TipoCombustibleController::class, 'update'])->name('tipo_combustibles.update');
    Route::delete('/{tipoCombustible}', [TipoCombustibleController::class, 'destroy'])->name('tipo_combustibles.destroy');
});

// Rutas CRUD para Tipos de Propiedad
Route::prefix('tipo_propiedades')->group(function () {
    Route::get('/data', [TipoPropiedadController::class, 'getData'])->name('tipo_propiedades.data');
    Route::get('/select', [TipoPropiedadController::class, 'select'])->name('tipo_propiedades.select');
    Route::get('/', [TipoPropiedadController::class, 'index'])->name('tipo_propiedades.index');
    Route::post('/', [TipoPropiedadController::class, 'store'])->name('tipo_propiedades.store');
    Route::get('/{tipoPropiedad}', [TipoPropiedadController::class, 'show'])->name('tipo_propiedades.show');
    Route::put('/{tipoPropiedad}', [TipoPropiedadController::class, 'update'])->name('tipo_propiedades.update');
    Route::delete('/{tipoPropiedad}', [TipoPropiedadController::class, 'destroy'])->name('tipo_propiedades.destroy');
});

// Rutas CRUD para Construcciones
Route::prefix('construcciones')->group(function () {
    Route::get('/data', [ConstruccionController::class, 'getData'])->name('construcciones.data');
    Route::get('/select', [ConstruccionController::class, 'select'])->name('construcciones.select');
    Route::get('/', [ConstruccionController::class, 'index'])->name('construcciones.index');
    Route::post('/', [ConstruccionController::class, 'store'])->name('construcciones.store');
    Route::get('/{construccion}', [ConstruccionController::class, 'show'])->name('construcciones.show');
    Route::put('/{construccion}', [ConstruccionController::class, 'update'])->name('construcciones.update');
    Route::delete('/{construccion}', [ConstruccionController::class, 'destroy'])->name('construcciones.destroy');
});

// Rutas CRUD para Usos
Route::prefix('usos')->group(function () {
    Route::get('/data', [UsoController::class, 'getData'])->name('usos.data');
    Route::get('/select', [UsoController::class, 'select'])->name('usos.select');
    Route::get('/', [UsoController::class, 'index'])->name('usos.index');
    Route::post('/', [UsoController::class, 'store'])->name('usos.store');
    Route::get('/{uso}', [UsoController::class, 'show'])->name('usos.show');
    Route::put('/{uso}', [UsoController::class, 'update'])->name('usos.update');
    Route::delete('/{uso}', [UsoController::class, 'destroy'])->name('usos.destroy');
});

// Rutas CRUD para Situaciones
Route::prefix('situaciones')->group(function () {
    Route::get('/data', [SituacionController::class, 'getData'])->name('situaciones.data');
    Route::get('/select', [SituacionController::class, 'select'])->name('situaciones.select');
    Route::get('/', [SituacionController::class, 'index'])->name('situaciones.index');
    Route::post('/', [SituacionController::class, 'store'])->name('situaciones.store');
    Route::get('/{situacion}', [SituacionController::class, 'show'])->name('situaciones.show');
    Route::put('/{situacion}', [SituacionController::class, 'update'])->name('situaciones.update');
    Route::delete('/{situacion}', [SituacionController::class, 'destroy'])->name('situaciones.destroy');
});

// Rutas CRUD para Servicios Eléctricos
Route::prefix('servicios-electricos')->group(function () {
    Route::get('/data', [ServicioElectricoController::class, 'getData'])->name('servicios-electricos.data');
    Route::get('/select', [ServicioElectricoController::class, 'select'])->name('servicios-electricos.select');
    Route::get('/', [ServicioElectricoController::class, 'index'])->name('servicios-electricos.index');
    Route::post('/', [ServicioElectricoController::class, 'store'])->name('servicios-electricos.store');
    Route::get('/{servicioElectrico}', [ServicioElectricoController::class, 'show'])->name('servicios-electricos.show');
    Route::put('/{servicioElectrico}', [ServicioElectricoController::class, 'update'])->name('servicios-electricos.update');
    Route::delete('/{servicioElectrico}', [ServicioElectricoController::class, 'destroy'])->name('servicios-electricos.destroy');
});

// Rutas CRUD para Tipos de Comprobante
Route::prefix('tipo-comprobantes')->group(function () {
    Route::get('/data', [TipoComprobanteController::class, 'getData'])->name('tipo-comprobantes.data');
    Route::get('/select', [TipoComprobanteController::class, 'select'])->name('tipo-comprobantes.select');
    Route::get('/', [TipoComprobanteController::class, 'index'])->name('tipo-comprobantes.index');
    Route::post('/', [TipoComprobanteController::class, 'store'])->name('tipo-comprobantes.store');
    Route::get('/{tipoComprobante}', [TipoComprobanteController::class, 'show'])->name('tipo-comprobantes.show');
    Route::put('/{tipoComprobante}', [TipoComprobanteController::class, 'update'])->name('tipo-comprobantes.update');
    Route::delete('/{tipoComprobante}', [TipoComprobanteController::class, 'destroy'])->name('tipo-comprobantes.destroy');
});

// Rutas CRUD para NEAs
Route::prefix('neas')->group(function () {
    Route::get('/data', [NeaController::class, 'getData'])->name('neas.data');
    Route::get('/select', [NeaController::class, 'select'])->name('neas.select');
    Route::get('/', [NeaController::class, 'index'])->name('neas.index');
    Route::post('/', [NeaController::class, 'store'])->name('neas.store');
    Route::get('/{nea}', [NeaController::class, 'show'])->name('neas.show');
    Route::put('/{nea}', [NeaController::class, 'update'])->name('neas.update');
    Route::delete('/{nea}', [NeaController::class, 'destroy'])->name('neas.destroy');
});

// Rutas CRUD para Tipos de Actividad
Route::prefix('tipos-actividad')->group(function () {
    Route::get('/data', [TiposActividadController::class, 'getData'])->name('tipos-actividad.data');
    Route::get('/select', [TiposActividadController::class, 'select'])->name('tipos-actividad.select');
    Route::get('/padres/select', [TiposActividadController::class, 'getPadresForSelect'])->name('tipos-actividad.padres-select');
    Route::get('/', [TiposActividadController::class, 'index'])->name('tipos-actividad.index');
    Route::post('/', [TiposActividadController::class, 'store'])->name('tipos-actividad.store');
    Route::get('/{tiposActividad}', [TiposActividadController::class, 'show'])->name('tipos-actividad.show');
    Route::put('/{tiposActividad}', [TiposActividadController::class, 'update'])->name('tipos-actividad.update');
    Route::delete('/{tiposActividad}', [TiposActividadController::class, 'destroy'])->name('tipos-actividad.destroy');
});

// Ruta API para árbol de tipos de actividad
Route::get('/api/tipos-actividad-tree', [TiposActividadController::class, 'getTree']);

// Rutas CRUD para Vehículos
Route::prefix('vehiculos')->group(function () {
    Route::get('/data', [VehiculoController::class, 'getData'])->name('vehiculos.data');
    Route::get('/api/select', [VehiculoController::class, 'getVehiculosForSelect'])->name('vehiculos.select');
    Route::get('/{vehiculo}/soats', [VehiculoController::class, 'getSoats'])->name('vehiculos.soats');
    Route::get('/{vehiculo}/soats/data', [VehiculoController::class, 'getSoatsData'])->name('vehiculos.soats.data');
    Route::get('/', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::post('/', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::get('/{vehiculo}', [VehiculoController::class, 'show'])->name('vehiculos.show');
    Route::put('/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');
});

// Rutas CRUD para SOATs
Route::prefix('soats')->group(function () {
    Route::get('/data', [SoatController::class, 'getData'])->name('soats.data');
    Route::get('/', [SoatController::class, 'index'])->name('soats.index');
    Route::post('/', [SoatController::class, 'store'])->name('soats.store');
    Route::get('/{soat}', [SoatController::class, 'show'])->name('soats.show');
    Route::put('/{soat}', [SoatController::class, 'update'])->name('soats.update');
    Route::delete('/{soat}', [SoatController::class, 'destroy'])->name('soats.destroy');
});



Route::prefix('ubigeo')->group(function () {
    // Rutas adicionales
    Route::get('/data', [UbigeoController::class, 'getData'])->name('ubigeo.data');
    Route::get('/api/select', [UbigeoController::class, 'getUbigeosForSelect'])->name('ubigeo.select');
    Route::get('/', [UbigeoController::class, 'index'])->name('ubigeo.index');
    Route::post('/', [UbigeoController::class, 'store'])->name('ubigeo.store');
    Route::get('/{ubigeo}', [UbigeoController::class, 'show'])->name('ubigeo.show');
    Route::put('/{ubigeo}', [UbigeoController::class, 'update'])->name('ubigeo.update');
    Route::delete('/{ubigeo}', [UbigeoController::class, 'destroy'])->name('ubigeo.destroy');
    
    
});

// Rutas para Medidores
Route::prefix('medidor')->group(function () {
    Route::get('/data', [MedidorController::class, 'getData'])->name('medidor.data');
    Route::get('/api/materiales', [MedidorController::class, 'getMateriales'])->name('medidor.materiales');
    Route::get('/', [MedidorController::class, 'index'])->name('medidor.index');
    Route::post('/', [MedidorController::class, 'store'])->name('medidor.store');
    Route::get('/{medidor}', [MedidorController::class, 'show'])->name('medidor.show');
    Route::put('/{medidor}', [MedidorController::class, 'update'])->name('medidor.update');
    Route::delete('/{medidor}', [MedidorController::class, 'destroy'])->name('medidor.destroy');
});

// Rutas para Suministros
Route::prefix('suministro')->group(function () {
    Route::get('/data', [SuministroController::class, 'getData'])->name('suministro.data');
    Route::get('/api/select', [SuministroController::class, 'getForSelect'])->name('suministro.select');
    Route::get('/departamentos', [SuministroController::class, 'getDepartamentos'])->name('suministro.departamentos');
    Route::get('/provincias/{departamento_id}', [SuministroController::class, 'getProvincias'])->name('suministro.provincias');
    Route::get('/distritos/{provincia_id}', [SuministroController::class, 'getDistritos'])->name('suministro.distritos');
    Route::get('/ubigeo-jerarquia/{ubigeo_id}', [SuministroController::class, 'getUbigeoHierarquia'])->name('suministro.ubigeo.jerarquia');
    Route::get('/medidores', [SuministroController::class, 'getMedidores'])->name('suministro.medidores');
    Route::get('/', [SuministroController::class, 'index'])->name('suministro.index');
    Route::post('/', [SuministroController::class, 'store'])->name('suministro.store');
    Route::get('/{suministro}', [SuministroController::class, 'show'])->name('suministro.show');
    Route::put('/{suministro}', [SuministroController::class, 'update'])->name('suministro.update');
    Route::delete('/{suministro}', [SuministroController::class, 'destroy'])->name('suministro.destroy');
});

// Rutas para Fichas de Actividad
Route::prefix('fichas-actividad')->name('fichas_actividad.')->group(function () {
    Route::get('/data', [FichaActividadController::class, 'getData'])->name('getData');
    Route::get('/', [FichaActividadController::class, 'index'])->name('index');
    Route::get('/create', [FichaActividadController::class, 'create'])->name('create');
    Route::post('/', [FichaActividadController::class, 'store'])->name('store');
    Route::get('/{id}', [FichaActividadController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [FichaActividadController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FichaActividadController::class, 'update'])->name('update');
    Route::delete('/{id}', [FichaActividadController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/estado', [FichaActividadController::class, 'cambiarEstado'])->name('cambiarEstado');
    Route::get('/api/buscar', [FichaActividadController::class, 'buscar'])->name('buscar');
    Route::get('/api/suministro/{suministroId}', [FichaActividadController::class, 'porSuministro'])->name('porSuministro');
});

// Rutas para Proveedores
Route::prefix('proveedores')->group(function () {
    Route::get('/data', [ProveedorController::class, 'getData'])->name('proveedores.data');
    Route::get('/api/select', [ProveedorController::class, 'getProveedoresForSelect'])->name('proveedores.select');
    Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::post('/', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::get('/{proveedor}', [ProveedorController::class, 'show'])->name('proveedores.show');
    Route::put('/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
});

// Rutas para Cuadrillas
Route::prefix('cuadrillas')->group(function () {
    Route::get('/data', [CuadrillaController::class, 'getData'])->name('cuadrillas.data');
    Route::get('/api/select', [CuadrillaController::class, 'select'])->name('cuadrillas.select');
    Route::get('/', [CuadrillaController::class, 'index'])->name('cuadrillas.index');
    Route::post('/', [CuadrillaController::class, 'store'])->name('cuadrillas.store');
    Route::get('/{cuadrilla}', [CuadrillaController::class, 'show'])->name('cuadrillas.show');
    Route::put('/{cuadrilla}', [CuadrillaController::class, 'update'])->name('cuadrillas.update');
    Route::delete('/{cuadrilla}', [CuadrillaController::class, 'destroy'])->name('cuadrillas.destroy');
    
    // Rutas para gestión de empleados en cuadrillas
    Route::get('/{cuadrilla}/empleados/data', [CuadrillaEmpleadoController::class, 'getEmpleadosAsignados'])->name('cuadrillas.empleados.data');
    Route::get('/{cuadrilla}/empleados/disponibles', [CuadrillaEmpleadoController::class, 'getEmpleadosDisponibles'])->name('cuadrillas.empleados.disponibles');
    Route::post('/empleados/asignar', [CuadrillaEmpleadoController::class, 'asignarEmpleado'])->name('cuadrillas.empleados.asignar');
    Route::put('/empleados/{asignacion}/toggle', [CuadrillaEmpleadoController::class, 'toggleEstado'])->name('cuadrillas.empleados.toggle');
    Route::delete('/empleados/{asignacion}', [CuadrillaEmpleadoController::class, 'removeEmpleado'])->name('cuadrillas.empleados.remove');
    
    // Rutas para gestión de vehículos en cuadrillas
    Route::get('/{cuadrilla}/vehiculos/data', [AsignacionVehiculoController::class, 'getVehiculosAsignados'])->name('cuadrillas.vehiculos.data');
    Route::get('/{cuadrilla}/vehiculos/disponibles', [AsignacionVehiculoController::class, 'getVehiculosDisponibles'])->name('cuadrillas.vehiculos.disponibles');
    Route::get('/{cuadrilla}/empleados-chofer', [AsignacionVehiculoController::class, 'getEmpleadosChofer'])->name('cuadrillas.empleados.chofer');
    Route::post('/vehiculos/asignar', [AsignacionVehiculoController::class, 'asignarVehiculo'])->name('cuadrillas.vehiculos.asignar');
    Route::put('/vehiculos/{asignacion}/toggle', [AsignacionVehiculoController::class, 'toggleEstado'])->name('cuadrillas.vehiculos.toggle');
    Route::delete('/vehiculos/{asignacion}', [AsignacionVehiculoController::class, 'removeVehiculo'])->name('cuadrillas.vehiculos.remove');
});

// Rutas CRUD para Asignación de Vehículos
Route::prefix('asignacion-vehiculos')->group(function () {
    Route::get('/data', [AsignacionVehiculoController::class, 'getData'])->name('asignacion_vehiculos.data');
    Route::get('/', [AsignacionVehiculoController::class, 'index'])->name('asignacion_vehiculos.index');
    Route::post('/', [AsignacionVehiculoController::class, 'store'])->name('asignacion_vehiculos.store');
    Route::get('/{asignacion}', [AsignacionVehiculoController::class, 'show'])->name('asignacion_vehiculos.show');
    Route::put('/{asignacion}', [AsignacionVehiculoController::class, 'update'])->name('asignacion_vehiculos.update');
    Route::delete('/{asignacion}', [AsignacionVehiculoController::class, 'destroy'])->name('asignacion_vehiculos.destroy');
});

// Rutas CRUD para Papeletas
Route::prefix('papeletas')->group(function () {
    // Rutas específicas PRIMERO (antes de /{papeleta})
    Route::get('/data', [PapeletaController::class, 'getData'])->name('papeletas.data');
    Route::get('/asignaciones-disponibles', [PapeletaController::class, 'getAsignacionesDisponibles']);
    Route::get('/ultimo-kilometraje/{asignacionVehiculoId}', [PapeletaController::class, 'getUltimoKilometraje']);
    Route::get('/empleados-disponibles', [PapeletaController::class, 'empleadosDisponibles']);
    Route::get('/cuadrilla-info/{asignacionVehiculoId}', [PapeletaController::class, 'cuadrillaInfo']);
    
    // Rutas genéricas DESPUÉS
    Route::get('/', [PapeletaController::class, 'index'])->name('papeletas.index');
    Route::post('/', [PapeletaController::class, 'store'])->name('papeletas.store');
    Route::get('/{papeleta}', [PapeletaController::class, 'show'])->name('papeletas.show');
    Route::put('/{papeleta}', [PapeletaController::class, 'update'])->name('papeletas.update');
    Route::delete('/{papeleta}', [PapeletaController::class, 'destroy'])->name('papeletas.destroy');
    
    // Rutas para verificar dotación
    Route::get('/{papeleta}/dotacion-existe', [PapeletaController::class, 'verificarDotacionExiste'])->name('papeletas.dotacion.existe');
    Route::get('/{papeleta}/proximo-numero-vale', [PapeletaController::class, 'proximoNumerovale'])->name('papeletas.proximo.numero.vale');
    
    // Rutas para gestión de viajes
    Route::post('/{papeleta}/iniciar', [PapeletaController::class, 'iniciarViaje'])->name('papeletas.iniciar');
    Route::post('/{papeleta}/finalizar', [PapeletaController::class, 'finalizarViaje'])->name('papeletas.finalizar');
    Route::post('/{papeleta}/anular', [PapeletaController::class, 'anular'])->name('papeletas.anular');
    
    // Rutas para impresión PDF
    Route::get('/{papeleta}/pdf', [PapeletaController::class, 'imprimirPdf'])->name('papeletas.pdf');
    Route::get('/{papeleta}/pdf-doble', [PapeletaController::class, 'imprimirDosPdf'])->name('papeletas.pdf.doble');
    Route::get('/{papeleta}/pdf-doble-horizontal', [PapeletaController::class, 'imprimirDobleHorizontal'])->name('papeletas.pdf.doble.horizontal');
    Route::get('/{papeleta}/preview', [PapeletaController::class, 'previsualizarPdf'])->name('papeletas.preview');
    Route::get('/{papeleta}/preview-doble', [PapeletaController::class, 'previsualizarPdfDoble'])->name('papeletas.preview.doble');
    
    // Rutas para Vale de Combustible
    Route::get('/vale/{dotacion}/preview', [PapeletaController::class, 'previsualizarVale'])->name('vale.preview');
    Route::get('/vale/{dotacion}/descargar', [PapeletaController::class, 'descargarVale'])->name('vale.descargar');

    // Rutas para gestión de dotación de combustible
    Route::prefix('{papeleta}/dotaciones')->group(function () {
        Route::get('/', [DotacionCombustibleController::class, 'index'])->name('dotaciones.index');
        Route::post('/', [DotacionCombustibleController::class, 'store'])->name('dotaciones.store');
        Route::get('/{dotacion}', [DotacionCombustibleController::class, 'show'])->name('dotaciones.show');
        Route::put('/{dotacion}', [DotacionCombustibleController::class, 'update'])->name('dotaciones.update');
        Route::delete('/{dotacion}', [DotacionCombustibleController::class, 'destroy'])->name('dotaciones.destroy');
        Route::get('/resumen/datos', [DotacionCombustibleController::class, 'resumen'])->name('dotaciones.resumen');
    });

    // Helper para obtener tipos de combustible
    Route::get('/dotaciones/tipos/combustible', [DotacionCombustibleController::class, 'tiposCombustible'])->name('dotaciones.tipos');
});

});
