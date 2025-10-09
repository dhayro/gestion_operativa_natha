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
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\SoatController; 
use App\Http\Controllers\AsignacionVehiculoController; 
use App\Http\Controllers\PapeletaController; 

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
Route::get('/', function () {
    return redirect(getRouterValue() . 'dashboard/analytics');
});


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
    Route::get('/data', [PapeletaController::class, 'getData'])->name('papeletas.data');
    Route::get('/asignaciones-disponibles', [PapeletaController::class, 'getAsignacionesDisponibles']);
    Route::get('/ultimo-kilometraje/{asignacionVehiculoId}', [PapeletaController::class, 'getUltimoKilometraje']);
    Route::get('/empleados-disponibles', [PapeletaController::class, 'empleadosDisponibles']);
    Route::get('/cuadrilla-info/{asignacionVehiculoId}', [PapeletaController::class, 'cuadrillaInfo']);
    Route::get('/', [PapeletaController::class, 'index'])->name('papeletas.index');
    Route::post('/', [PapeletaController::class, 'store'])->name('papeletas.store');
    Route::get('/{papeleta}', [PapeletaController::class, 'show'])->name('papeletas.show');
    Route::put('/{papeleta}', [PapeletaController::class, 'update'])->name('papeletas.update');
    Route::delete('/{papeleta}', [PapeletaController::class, 'destroy'])->name('papeletas.destroy');
    
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
});

/**
 * =======================
 *          Dashboard
 * =======================
 */
Route::prefix('dashboard')->group(function () {
    Route::get('/analytics', function () {
        return view('admin/dashboard/analytics',
            [
                'catName' => 'dashboard',
                'title' => 'CORK Admin - Multipurpose Bootstrap Dashboard Template',
                "breadcrumbs" => ["Dashboard", "Analytics"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('analytics');
    
    Route::get('/sales', function () {
        return view('admin/dashboard/sales',
            [
                'catName' => 'dashboard',
                'title' => 'Sales Admin',
                "breadcrumbs" => ["Dashboard", "Sales"],
                'scrollspy' => 0,
                'simplePage' => 0,
            ]
        );
    })->name('sales');
});


/**
 * =======================
 *          Apps
 * =======================
 */
Route::prefix('app')->group(function () {
    Route::get('/calendar', function () {
        return view('admin/apps/calendar',
            [
                'catName' => 'app',
                'title' => 'Javascript Calendar',
                "breadcrumbs" => ["Apps", "Calendar"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('calendar');
    Route::get('/chat', function () {
        return view('admin/apps/chat',
            [
                'catName' => 'app',
                'title' => 'Chat Application',
                "breadcrumbs" => ["Apps", "Chat"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('chat');
    Route::get('/contacts', function () {
        return view('admin/apps/contacts',
            [
                'catName' => 'app',
                'title' => 'Contact Profile',
                "breadcrumbs" => ["Apps", "Contact"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('contacts');
    Route::get('/mailbox', function () {
        return view('admin/apps/mailbox',
            [
                'catName' => 'app',
                'title' => 'Mailbox',
                "breadcrumbs" => ["Apps", "analytics"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('mailbox');
    Route::get('/notes', function () {
        return view('admin/apps/notes',
            [
                'catName' => 'app',
                'title' => 'Notes',
                "breadcrumbs" => ["Apps", "analytics"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('notes');
    Route::get('/scrumboard', function () {
        return view('admin/apps/scrumboard',
            [
                'catName' => 'app',
                'title' => 'Scrum Task Board',
                "breadcrumbs" => ["Apps", "Scrumboard"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('scrumboard');
    Route::get('/todolist', function () {
        return view('admin/apps/todo-list',
            [
                'catName' => 'app',
                'title' => 'Todo List',
                "breadcrumbs" => ["Apps", "Todo List"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('todolist');

    // Apps => Invoice
    Route::prefix('invoice')->group(function () {
        Route::get('/create', function () {
            return view('admin/apps/invoice/create',
                [
                    'catName' => 'app',
                    'submenu' => 'invoice',
                    'title' => 'Invoice Add',
                    "breadcrumbs" => ["Apps", "Invoice", "Create"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('icreate');
        Route::get('/edit', function () {
            return view('admin/apps/invoice/edit',
                [
                    'catName' => 'app',
                    'submenu' => 'invoice',
                    'title' => 'Invoice Edit',
                    "breadcrumbs" => ["Apps", "Invoice", "Edit"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('iedit');
        Route::get('/list', function () {
            return view('admin/apps/invoice/list',
                [
                    'catName' => 'app',
                    'submenu' => 'invoice',
                    'title' => 'Invoice List',
                    "breadcrumbs" => ["Apps", "Invoice", "List"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('ilist');
        Route::get('/preview', function () {
            return view('admin/apps/invoice/preview',
                [
                    'catName' => 'app',
                    'submenu' => 'invoice',
                    'title' => 'Invoice Preview',
                    "breadcrumbs" => ["Apps", "Invoice", "Preview"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('ipreview');
    });

    // Apps => Ecommerce
    Route::prefix('ecommerce')->group(function () {
        Route::get('/create', function () {
            return view('admin/apps/ecommerce/create',
                [
                    'catName' => 'app',
                    'submenu' => 'ecommerce',
                    'title' => 'Ecommerce Create',
                    "breadcrumbs" => ["Apps", "Ecommerce", "Create"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('bcreate');
        Route::get('/edit', function () {
            return view('admin/apps/ecommerce/edit',
                [
                    'catName' => 'app',
                    'submenu' => 'ecommerce',
                    'title' => 'Ecommerce Edit',
                    "breadcrumbs" => ["Apps", "Ecommerce", "Edit"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('bedit');
        Route::get('/list', function () {
            return view('admin/apps/ecommerce/list',
                [
                    'catName' => 'app',
                    'submenu' => 'ecommerce',
                    'title' => 'Ecommerce List',
                    "breadcrumbs" => ["Apps", "Ecommerce", "List"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('blist');
        Route::get('/product', function () {
            return view('admin/apps/ecommerce/product',
                [
                    'catName' => 'app',
                    'submenu' => 'ecommerce',
                    'title' => 'Ecommerce Product Details',
                    "breadcrumbs" => ["Apps", "Ecommerce", "Product"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('bproduct');
        Route::get('/shop', function () {
            return view('admin/apps/ecommerce/shop',
                [
                    'catName' => 'app',
                    'submenu' => 'ecommerce',
                    'title' => 'Ecommerce Shop',
                    "breadcrumbs" => ["Apps", "Ecommerce", "Shop"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('bshop');
    });

    // Apps => Blog
    Route::prefix('blog')->group(function () {
        Route::get('/create', function () {
            return view('admin/apps/blog/create',
                [
                    'catName' => 'app',
                    'submenu' => 'blog',
                    'title' => 'Blog Create',
                    "breadcrumbs" => ["Apps", "Blog", "Create"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('bcreate');
        Route::get('/edit', function () {
            return view('admin/apps/blog/edit',
                [
                    'catName' => 'app',
                    'submenu' => 'blog',
                    'title' => 'Blog Edit',
                    "breadcrumbs" => ["Apps", "Blog", "Edit"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('bedit');
        Route::get('/list', function () {
            return view('admin/apps/blog/list',
                [
                    'catName' => 'app',
                    'submenu' => 'blog',
                    'title' => 'Blog List',
                    "breadcrumbs" => ["Apps", "Blog", "List"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('blist');
        Route::get('/grid', function () {
            return view('admin/apps/blog/grid',
                [
                    'catName' => 'app',
                    'submenu' => 'blog',
                    'title' => 'Blog',
                    "breadcrumbs" => ["Apps", "Blog", "Grid"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('bgrid');
        Route::get('/post', function () {
            return view('admin/apps/blog/post',
                [
                    'catName' => 'app',
                    'submenu' => 'blog',
                    'title' => 'Post Content',
                    "breadcrumbs" => ["Apps", "Blog", "Post"],
                    'scrollspy' => 0,
                    'simplePage' => 0
                ]
            );
        })->name('bpost');
    });
});


/**
 * =======================
 *          Components
 * =======================
 */
Route::prefix('component')->group(function () {
    Route::get('/tabs', function () {
        return view('admin/components/tabs',
            [
                'catName' => 'component',
                'title' => 'Tabs',
                "breadcrumbs" => ["Component", "Tabs"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('tabs');
    Route::get('/accordions', function () {
        return view('admin/components/accordions',
            [
                'catName' => 'component',
                'title' => 'Accordions',
                "breadcrumbs" => ["Component", "Accordions"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('accordions');
    Route::get('/modals', function () {
        return view('admin/components/modals',
            [
                'catName' => 'component',
                'title' => 'Modals',
                "breadcrumbs" => ["Component", "Modals"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('modals');
    Route::get('/cards', function () {
        return view('admin/components/cards',
            [
                'catName' => 'component',
                'title' => 'Card',
                "breadcrumbs" => ["Component", "Cards"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('cards');
    Route::get('/carousel', function () {
        return view('admin/components/carousel',
            [
                'catName' => 'component',
                'title' => 'Bootstrap Carousel',
                "breadcrumbs" => ["Component", "Carousel"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('carousel');
    Route::get('/splide', function () {
        return view('admin/components/splide',
            [
                'catName' => 'component',
                'title' => 'Splide',
                "breadcrumbs" => ["Component", "Splide"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('splide');
    Route::get('/sweet-alerts', function () {
        return view('admin/components/sweetalerts',
            [
                'catName' => 'component',
                'title' => 'SweetAlert',
                "breadcrumbs" => ["Component", "Sweetalerts"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('sweetAlert');
    Route::get('/timeline', function () {
        return view('admin/components/timeline',
            [
                'catName' => 'component',
                'title' => 'Timeline',
                "breadcrumbs" => ["Component", "Timeline"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('timeline');
    Route::get('/notifications', function () {
        return view('admin/components/notifications',
            [
                'catName' => 'component',
                'title' => 'Snackbar',
                "breadcrumbs" => ["Component", "Notifications"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('notifications');
    Route::get('/media-objects', function () {
        return view('admin/components/media-object',
            [
                'catName' => 'component',
                'title' => 'Media Object',
                "breadcrumbs" => ["Component", "Media Objects"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('mediaObject');
    Route::get('/list-group', function () {
        return view('admin/components/list-group',
            [
                'catName' => 'component',
                'title' => 'List Group',
                "breadcrumbs" => ["Component", "List Group"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('listGroup');
    Route::get('/pricing-tables', function () {
        return view('admin/components/pricing-tables',
            [
                'catName' => 'component',
                'title' => 'Pricing Tables',
                "breadcrumbs" => ["Component", "Pricing Tables"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('pricingTable');
    Route::get('/lightbox', function () {
        return view('admin/components/lightbox',
            [
                'catName' => 'component',
                'title' => 'Lightbox',
                "breadcrumbs" => ["Component", "Lightbox"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('lightbox');
    Route::get('/drag-drop', function () {
        return view('admin/components/drag-drop',
            [
                'catName' => 'component',
                'title' => 'Dragula Drag and Drop',
                "breadcrumbs" => ["Component", "Drag and Drop"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('dragDrop');
    Route::get('/font-icons', function () {
        return view('admin/components/font-icon',
            [
                'catName' => 'component',
                'title' => 'Fonticon Icon',
                "breadcrumbs" => ["Component", "Font Icons"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('fontIcons');
    Route::get('/flag-icons', function () {
        return view('admin/components/flag-icon',
            [
                'catName' => 'component',
                'title' => 'SVG Flag Icons',
                "breadcrumbs" => ["Component", "Flag Icons"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('flagIcons');
});


/**
 * =======================
 *          Elements
 * =======================
 */
Route::prefix('element')->group(function () {
    Route::get('/alerts', function () {
        return view(
            'admin/elements/alerts',
            [
                'catName' => 'element',
                'title' => 'Alerts',
                "breadcrumbs" => ["Element", "Alerts"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('alerts');
    Route::get('/avatar', function () {
        return view('admin/elements/avatar',
            [
                'catName' => 'element',
                'title' => 'Avatar',
                "breadcrumbs" => ["Element", "Avatar"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('avatar');
    Route::get('/badges', function () {
        return view('admin/elements/badges',
            [
                'catName' => 'element',
                'title' => 'Badge',
                "breadcrumbs" => ["Element", "Badge"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('badges');
    Route::get('/breadcrumbs', function () {
        return view('admin/elements/breadcrumbs',
            [
                'catName' => 'element',
                'title' => 'Breadcrumb',
                "breadcrumbs" => ["Element", "Breadcrumb"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('breadcrumbs');
    Route::get('/button-groups', function () {
        return view('admin/elements/button-groups',
            [
                'catName' => 'element',
                'title' => 'Button Group',
                "breadcrumbs" => ["Element", "Button Group"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('buttonGroups');
    Route::get('/buttons', function () {
        return view('admin/elements/buttons',
            [
                'catName' => 'element',
                'title' => 'Bootstrap Buttons',
                "breadcrumbs" => ["Element", "Bootstrap Buttons"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('buttons');
    Route::get('/color-library', function () {
        return view('admin/elements/color-lib',
            [
                'catName' => 'element',
                'title' => 'Color Library',
                "breadcrumbs" => ["Element", "Color Library"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('colorLibrary');
    Route::get('/dropdown', function () {
        return view('admin/elements/dropdown',
            [
                'catName' => 'element',
                'title' => 'Dropdown',
                "breadcrumbs" => ["Element", "Dropdown"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('dropdown');
    Route::get('/infobox', function () {
        return view('admin/elements/infobox',
            [
                'catName' => 'element',
                'title' => 'Infobox',
                "breadcrumbs" => ["Element", "Infobox"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('infobox');
    Route::get('/loader', function () {
        return view('admin/elements/loader',
            [
                'catName' => 'element',
                'title' => 'Loaders',
                "breadcrumbs" => ["Element", "Loaders"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('loader');
    Route::get('/pagination', function () {
        return view('admin/elements/pagination',
            [
                'catName' => 'element',
                'title' => 'Pagination',
                "breadcrumbs" => ["Element", "Pagination"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('pagination');
    Route::get('/popovers', function () {
        return view('admin/elements/popovers',
            [
                'catName' => 'element',
                'title' => 'Popovers',
                "breadcrumbs" => ["Element", "Popovers"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('popovers');
    Route::get('/progressbar', function () {
        return view('admin/elements/progress-bar',
            [
                'catName' => 'element',
                'title' => 'Bootstrap Progress Bar',
                "breadcrumbs" => ["Element", "Bootstrap Progress Bar"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('progressbar');
    Route::get('/search', function () {
        return view('admin/elements/search',
            [
                'catName' => 'element',
                'title' => 'Search',
                "breadcrumbs" => ["Element", "Search"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('search');
    Route::get('/tooltips', function () {
        return view('admin/elements/tooltips',
            [
                'catName' => 'element',
                'title' => 'Tooltips',
                "breadcrumbs" => ["Element", "Tooltips"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('tooltips');
    Route::get('/treeview', function () {
        return view('admin/elements/treeview',
            [
                'catName' => 'element',
                'title' => 'Tree View',
                "breadcrumbs" => ["Element", "Tree View"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('treeview');
    Route::get('/typography', function () {
        return view('admin/elements/typography',
            [
                'catName' => 'element',
                'title' => 'Typography',
                "breadcrumbs" => ["Element", "Typography"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('typography');
});


/**
 * =======================
 *          Maps
 * =======================
 */
Route::get('/maps', function () {
    return view('admin/maps',
        [
            'catName' => 'maps',
            'title' => 'jVector Maps',
            "breadcrumbs" => ["Maps"],
            'scrollspy' => 1,
            'simplePage' => 0
        ]
    );
})->name('maps');


/**
 * =======================
 *          Charts
 * =======================
 */
Route::get('/charts', function () {
    return view('admin/charts',
        [
            'catName' => 'charts',
            'title' => 'Apex Chart',
            "breadcrumbs" => ["User Interface", "Chart"],
            'scrollspy' => 1,
            'simplePage' => 0
        ]
    );
})->name('charts');


/**
 * =======================
 *          Widgets
 * =======================
 */
Route::get('/widgets', function () {
    return view('admin/widgets',
        [
            'catName' => 'widgets',
            'title' => 'Widgets',
            "breadcrumbs" => ["User Interface", "Widgets"],
            'scrollspy' => 0,
            'simplePage' => 0
        ]
    );
})->name('widgets');


/**
 * =======================
 *          Tables
 * =======================
 */
Route::get('/tables', function () {
    return view('admin/tables',
        [
            'catName' => 'tables',
            'title' => 'Bootstrap Tables',
            "breadcrumbs" => ["Tables", "Bootstrap"],
            'scrollspy' => 1,
            'simplePage' => 0
        ]
    );
})->name('tables');


/**
 * =======================
 *          Tables => Datatable
 * =======================
 */
Route::prefix('datatable')->group(function () {
    Route::get('/basic', function () {
        return view('admin/datatables/basic',
            [
                'catName' => 'datatable',
                'title' => 'DataTables Basic',
                "breadcrumbs" => ["DataTables", "Basic"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('basic');    
    Route::get('/striped', function () {
        return view('admin/datatables/striped',
            [
                'catName' => 'datatable',
                'title' => 'DataTables Striped',
                "breadcrumbs" => ["DataTables", "Striped"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('striped');    
    Route::get('/custom', function () {
        return view('admin/datatables/custom',
            [
                'catName' => 'datatable',
                'title' => 'Custom DataTables',
                "breadcrumbs" => ["DataTables", "Custom"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('custom');    
    Route::get('/miscellaneous', function () {
        return view('admin/datatables/miscellaneous',
            [
                'catName' => 'datatable',
                'title' => 'Miscellaneous DataTables',
                "breadcrumbs" => ["DataTables", "Miscellaneous"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('miscellaneous');    
});


/**
 * =======================
 *          Forms
 * =======================
 */
Route::prefix('form')->group(function () {
    Route::get('/auto-complete', function () {
        return view('admin/forms/auto-complete',
            [
                'catName' => 'form',
                'title' => 'AutoComplete',
                "breadcrumbs" => ["Form", "AutoComplete"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('autoComplete');
    Route::get('/basic', function () {
        return view('admin/forms/basic',
            [
                'catName' => 'form',
                'title' => 'Bootstrap Forms',
                "breadcrumbs" => ["Form", "Basic"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('basic');
    Route::get('/checkbox', function () {
        return view('admin/forms/checkbox',
            [
                'catName' => 'form',
                'title' => 'Checkbox',
                "breadcrumbs" => ["Form", "Checkbox"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('checkbox');
    Route::get('/clipboard', function () {
        return view('admin/forms/clipboard',
            [
                'catName' => 'form',
                'title' => 'Clipboard',
                "breadcrumbs" => ["Form", "Clipboard"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('clipboard');
    Route::get('/date-time-picker', function () {
        return view('admin/forms/date-time-picker',
            [
                'catName' => 'form',
                'title' => 'Date and Time Picker',
                "breadcrumbs" => ["Form", "Date Time Picker"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('dateTimePicker');
    Route::get('/file-upload', function () {
        return view('admin/forms/file-upload',
            [
                'catName' => 'form',
                'title' => 'File Upload',
                "breadcrumbs" => ["Form", "File Upload"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('fileUpload');
    Route::get('/input-group', function () {
        return view('admin/forms/input-group',
            [
                'catName' => 'form',
                'title' => 'Input Group',
                "breadcrumbs" => ["Form", "Input Group"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('inputGroup');
    Route::get('/input-mask', function () {
        return view('admin/forms/input-mask',
            [
                'catName' => 'form',
                'title' => 'Input Mask',
                "breadcrumbs" => ["Form", "Input Mask"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('inputMask');
    Route::get('/layouts', function () {
        return view('admin/forms/layouts',
            [
                'catName' => 'form',
                'title' => 'Form Layouts',
                "breadcrumbs" => ["Form", "Layouts"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('layouts');
    Route::get('/markdown-editor', function () {
        return view('admin/forms/markdown-editor',
            [
                'catName' => 'form',
                'title' => 'Markdown Editor',
                "breadcrumbs" => ["Form", "Markdown"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('markdownEditor');
    Route::get('/maxlength', function () {
        return view('admin/forms/maxlength',
            [
                'catName' => 'form',
                'title' => 'Bootstrap Maxlength',
                "breadcrumbs" => ["Form", "Maxlength"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('maxlength');
    Route::get('/quill-editor', function () {
        return view('admin/forms/quill-editor',
            [
                'catName' => 'form',
                'title' => 'Quill Editor',
                "breadcrumbs" => ["Form", "Quill"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('quillEditor');
    Route::get('/radio', function () {
        return view('admin/forms/radio',
            [
                'catName' => 'form',
                'title' => 'Radio',
                "breadcrumbs" => ["Form", "Radio"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('radio');
    Route::get('/slider', function () {
        return view('admin/forms/slider',
            [
                'catName' => 'form',
                'title' => 'Range Slider',
                "breadcrumbs" => ["Form", "Range Slider"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('slider');
    Route::get('/switches', function () {
        return view('admin/forms/switches',
            [
                'catName' => 'form',
                'title' => 'Bootstrap Toggle',
                "breadcrumbs" => ["Form", "Toggle"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('switches');
    Route::get('/tagify', function () {
        return view('admin/forms/tagify',
            [
                'catName' => 'form',
                'title' => 'Tagify',
                "breadcrumbs" => ["Form", "Tagify"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('tagify');
    Route::get('/tom-select', function () {
        return view('admin/forms/tom-select',
            [
                'catName' => 'form',
                'title' => 'Bootstrap Select',
                "breadcrumbs" => ["Form", "Tom Select"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('tomSelect');
    Route::get('/touchspin', function () {
        return view('admin/forms/touch-spin',
            [
                'catName' => 'form',
                'title' => 'Bootstrap Touchspin',
                "breadcrumbs" => ["Form", "Touchspin"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('touchspin');
    Route::get('/validation', function () {
        return view('admin/forms/validation',
            [
                'catName' => 'form',
                'title' => 'Bootstrap Form Validation',
                "breadcrumbs" => ["Form", "Validation"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('validation');
    Route::get('/wizards', function () {
        return view('admin/forms/wizards',
            [
                'catName' => 'form',
                'title' => 'Wizards',
                "breadcrumbs" => ["Form", "Wizards"],
                'scrollspy' => 1,
                'simplePage' => 0
            ]
        );
    })->name('wizards');
});


/**
 * =======================
 *          Layout
 * =======================
 */
Route::prefix('layout')->group(function () {
    Route::get('/blank', function () {
        return view('admin/layouts/blank',
            [
                'catName' => 'layout',
                'title' => 'Blank Page',
                "breadcrumbs" => ["Layout", "Blank"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('blank');    
    Route::get('/empty', function () {
        return view('admin/layouts/empty',
            [
                'catName' => 'layout',
                'title' => 'Empty Page',
                "breadcrumbs" => ["Layout", "Empty"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('empty');
    Route::get('/boxed', function () {
        return view('admin/layouts/boxed',
            [
                'catName' => 'layout',
                'title' => 'Boxed',
                "breadcrumbs" => ["Layout", "Boxed"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('boxed');
    Route::get('/collapsible', function () {
        return view('admin/layouts/collapsed',
            [
                'catName' => 'layout',
                'title' => 'Collapsible Menu',
                "breadcrumbs" => ["Layout", "Collapsible"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('collapsed');
});


/**
 * =======================
 *          Users
 * =======================
 */
Route::prefix('user')->group(function () {
    Route::get('/account-setting', function () {
        return view('admin/users/account-settings',
            [
                'catName' => 'user',
                'title' => 'Account Settings',
                "breadcrumbs" => ["User", "Account Settings"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('accountSetting');    
    Route::get('/profile', function () {
        return view('admin/users/profile',
            [
                'catName' => 'user',
                'title' => 'User Profile',
                "breadcrumbs" => ["User", "Profile"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('profile');
});


/**
 * =======================
 *          Pages
 * =======================
 */
Route::prefix('pages')->group(function () {
    Route::get('/knowledge-base', function () {
        return view('admin/pages/knowledge-base',
            [
                'catName' => 'page',
                'title' => 'Knowledge Base',
                "breadcrumbs" => ["Pages", "Knowledge Base"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('knowledgeBase');
    Route::get('/faq', function () {
        return view('admin/pages/faq',
            [
                'catName' => 'page',
                'title' => 'FAQs',
                "breadcrumbs" => ["Pages", "FAQs"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('faq');
    Route::get('/contact-us', function () {
        return view('admin/pages/contact-form',
            [
                'catName' => 'page',
                'title' => 'Contact Us',
                "breadcrumbs" => ["Pages", "Contact Us"],
                'scrollspy' => 0,
                'simplePage' => 0
            ]
        );
    })->name('contactForm');
    Route::get('/404', function () {
        return view('admin/pages/404',
            [
                'catName' => 'page',
                'title' => '404',
                "breadcrumbs" => ["Pages", "404"],
                'scrollspy' => 0,
                'simplePage' => 1
            ]
        );
    })->name('error404');
    Route::get('/maintenance', function () {
        return view('admin/pages/maintenance',
            [
                'catName' => 'page',
                'title' => 'Maintenence',
                "breadcrumbs" => ["Pages", "Maintenence"],
                'scrollspy' => 0,
                'simplePage' => 1
            ]
        );
    })->name('maintenance');
});


/**
 * =======================
 *          Auth
 * =======================
 */
Route::prefix('authentication')->group(function () {

    Route::prefix('boxed')->group(function () {
        Route::get('/sign-in', function () {
            // return 'boxed-sign-in';
            return view('admin/auth/boxed/sign-in',
                [
                    'catName' => 'auth',
                    'title' => 'Sign In Boxed',
                    "breadcrumbs" => ["Authentication", "Sign In"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('boxedSignIn');    
        Route::get('/sign-up', function () {
            // return 'boxed-sign-up';
            return view('admin/auth/boxed/sign-up',
                [
                    'catName' => 'auth',
                    'title' => 'Sign Up Boxed',
                    "breadcrumbs" => ["Authentication", "Sign Up"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('boxedSignUp');
        Route::get('/lockscreen', function () {
            // return 'boxed-lockscreen';
            return view('admin/auth/boxed/unlock',
                [
                    'catName' => 'auth',
                    'title' => 'LockScreen Boxed',
                    "breadcrumbs" => ["Authentication", "LockScreen"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('boxedLockscreen');
        Route::get('/password-reset', function () {
            // return 'boxed-password-reset';
            return view('admin/auth/boxed/reset',
                [
                    'catName' => 'auth',
                    'title' => 'Password Reset Boxed',
                    "breadcrumbs" => ["Authentication", "Password Reset"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('boxedPasswordReset');
        Route::get('/2-step-verification', function () {
            // return 'boxed-2-step-verification';
            return view('admin/auth/boxed/2-step',
                [
                    'catName' => 'auth',
                    'title' => '2 Step Verification Boxed',
                    "breadcrumbs" => ["Authentication", "2 Step Verification"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('boxed2sv');
    });


    Route::prefix('cover')->group(function () {
        Route::get('/sign-in', function () {
            // return 'cover-sign-in';
            return view('admin/auth/cover/sign-in',
                [
                    'catName' => 'auth',
                    'title' => 'Sign In Cover',
                    "breadcrumbs" => ["Authentication", "Sign In"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('coverSignIn');    
        Route::get('/sign-up', function () {
            // return 'cover-sign-up';
            return view('admin/auth/cover/sign-up',
                [
                    'catName' => 'auth',
                    'title' => 'Sign Up Cover',
                    "breadcrumbs" => ["Authentication", "Sign Up"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('coverSignUp');
        Route::get('/lockscreen', function () {
            // return 'cover-lockscreen';
            return view('admin/auth/cover/unlock',
                [
                    'catName' => 'auth',
                    'title' => 'LockScreen Cover',
                    "breadcrumbs" => ["Authentication", "LockScreen"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('coverLockscreen');
        Route::get('/password-reset', function () {
            // return 'cover-password-reset';
            return view('admin/auth/cover/reset',
                [
                    'catName' => 'auth',
                    'title' => 'Password Reset Cover',
                    "breadcrumbs" => ["Authentication", "Password Reset"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('coverPasswordReset');
        Route::get('/2-step-verification', function () {
            // return 'cover-2-step-verification';
            return view('admin/auth/cover/2-step',
                [
                    'catName' => 'auth',
                    'title' => '2 Step Verification Cover',
                    "breadcrumbs" => ["Authentication", "2 Step Verification"],
                    'scrollspy' => 0,
                    'simplePage' => 1
                ]
            );
        })->name('cover2sv');
    });
    
});

// Rutas globales de API para Select2
Route::get('/api/select/tipo-combustibles', [TipoCombustibleController::class, 'getTiposCombustibleForSelect']);
Route::get('/api/select/vehiculos', [VehiculoController::class, 'getVehiculosForSelect']);
Route::get('/api/select/proveedores', [ProveedorController::class, 'getProveedoresForSelect']);

});

