<?php

namespace App\Http\Controllers;

use App\Models\Papeleta;
use App\Models\AsignacionVehiculo;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PapeletaController extends Controller
{
    /**
     * Obtener la fecha y hora actual de Perú
     */
    private function nowPeru()
    {
        return Carbon::now('America/Lima');
    }

    public function index()
    {
        return view('admin.papeletas.index', [
            'catName' => 'papeletas',
            'title' => 'Gestión de Papeletas',
            'breadcrumbs' => ['Vehículos', 'Papeletas'],
            'scrollspy' => 0,
            'simplePage' => 0,
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            
            // Obtener papeletas según el usuario logueado
            $papeletas = Papeleta::with([
                    'asignacionVehiculo.vehiculo',
                    'asignacionVehiculo.cuadrilla',
                    'usuarioCreacion'
                ])
                ->paraUsuario($user->id)
                ->select(['id', 'correlativo', 'asignacion_vehiculo_id', 'fecha', 'destino', 'motivo', 
                         'km_salida', 'km_llegada', 'fecha_hora_salida', 'fecha_hora_llegada', 
                         'estado', 'observaciones', 'usuario_creacion_id', 'created_at'])
                ->orderByRaw('CAST(SUBSTRING(correlativo, 6) AS UNSIGNED) DESC'); // Orden numérico por correlativo

            return DataTables::of($papeletas)
                ->addIndexColumn()
                ->addColumn('vehiculo_info', function ($row) {
                    if ($row->asignacionVehiculo && $row->asignacionVehiculo->vehiculo) {
                        $vehiculo = $row->asignacionVehiculo->vehiculo;
                        return $vehiculo->marca . ' ' . $vehiculo->modelo . ' - ' . $vehiculo->placa;
                    }
                    return 'Sin vehículo';
                })
                ->addColumn('cuadrilla_nombre', function ($row) {
                    return $row->asignacionVehiculo && $row->asignacionVehiculo->cuadrilla 
                        ? $row->asignacionVehiculo->cuadrilla->nombre 
                        : 'Sin cuadrilla';
                })
                ->addColumn('fecha_formatted', function ($row) {
                    return $row->fecha ? $row->fecha->format('d/m/Y') : '';
                })
                ->addColumn('km_recorridos', function ($row) {
                    return $row->km_recorridos ?? '-';
                })
                ->addColumn('estado_operacion', function ($row) {
                    if (!$row->estado) {
                        return '<span class="badge badge-danger">Anulada</span>';
                    } elseif ($row->completada) {
                        return '<span class="badge badge-success">Completada</span>';
                    } elseif ($row->en_curso) {
                        return '<span class="badge badge-warning">En Curso</span>';
                    } else {
                        return '<span class="badge badge-info">Programada</span>';
                    }
                })
                ->addColumn('estado_badge', function ($row) {
                    return $row->estado
                        ? '<span class="badge badge-success">Activa</span>'
                        : '<span class="badge badge-danger">Anulada</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="dropdown">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">';

                    // Ver detalles siempre disponible
                    $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="verPapeleta(' . $row->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye me-2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Ver Detalles
                                </a>';

                    if ($row->estado) {
                        // Solo si está activa
                        if (!$row->fecha_hora_salida) {
                            // Puede iniciar viaje
                            $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="iniciarViaje(' . $row->id . ')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-play me-2">
                                                <polygon points="5,3 19,12 5,21 5,3"></polygon>
                                            </svg>
                                            Iniciar Viaje
                                        </a>';
                            
                            // Puede editar
                            $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="editPapeleta(' . $row->id . ')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            Editar
                                        </a>';
                        } elseif ($row->fecha_hora_salida && !$row->fecha_hora_llegada) {
                            // Puede finalizar viaje
                            $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="finalizarViaje(' . $row->id . ')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-square me-2">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            </svg>
                                            Finalizar Viaje
                                        </a>';
                        }

                        // Botones de PDF - DomPDF únicamente
                        $actions .= '<div class="dropdown-divider"></div>';
                        $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="previsualizarPdf(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye me-2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        Vista 1
                                    </a>';
                        $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="imprimirDobleHorizontal(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy me-2">
                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                        </svg>
                                        Vista 2
                                    </a>';

                        // Separador antes de acciones destructivas
                        if (!$row->completada) {
                            $actions .= '<div class="dropdown-divider"></div>';
                        }

                        // Puede anular si no está completada
                        if (!$row->completada) {
                            $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="anularPapeleta(' . $row->id . ')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle me-2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                                <line x1="9" y1="9" x2="15" y2="15"></line>
                                            </svg>
                                            Anular
                                        </a>';
                        }
                    }

                    $actions .= '</div>
                                </div>';

                    return $actions;
                })
                ->rawColumns(['vehiculo_info', 'estado_operacion', 'estado_badge', 'action'])
                ->orderColumn('correlativo', function ($query, $order) {
                    return $query->orderByRaw('CAST(SUBSTRING(correlativo, 6) AS UNSIGNED) ' . $order);
                })
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'asignacion_vehiculo_id' => 'required|exists:asignacion_vehiculos,id',
            'fecha' => 'required|date|after_or_equal:today',
            'destino' => 'required|string|max:255',
            'motivo' => 'required|string',
            'km_salida' => 'required|numeric|min:0',
            'chofer_id' => 'nullable|exists:empleados,id',
            'miembros_cuadrilla' => 'nullable|array',
            'miembros_cuadrilla.*' => 'exists:empleados,id',
            'personal_adicional' => 'nullable|string|max:1000',
        ]);

        $papeleta = Papeleta::create([
            'asignacion_vehiculo_id' => $request->asignacion_vehiculo_id,
            'chofer_id' => $request->chofer_id,
            'miembros_cuadrilla' => $request->miembros_cuadrilla,
            'personal_adicional' => $request->personal_adicional,
            'fecha' => $request->fecha,
            'destino' => $request->destino,
            'motivo' => $request->motivo,
            'km_salida' => $request->km_salida,
            'usuario_creacion_id' => Auth::id(),
            'estado' => true
        ]);

        // Recargar con relaciones para mostrar información completa
        $papeleta->load([
            'asignacionVehiculo.vehiculo',
            'asignacionVehiculo.empleado',
            'asignacionVehiculo.cuadrilla'
        ]);

        return response()->json([
            'success' => true, 
            'data' => $papeleta,
            'correlativo' => $papeleta->correlativo,
            'message' => 'Papeleta creada exitosamente con correlativo: ' . $papeleta->correlativo
        ]);
    }

    public function show($id)
    {
        $papeleta = Papeleta::with([
            'asignacionVehiculo.vehiculo',
            'asignacionVehiculo.cuadrilla',
            'asignacionVehiculo.empleado', // Chofer permanente
            'usuarioCreacion',
            'usuarioActualizacion'
        ])->findOrFail($id);

        // Asegurar que miembros_cuadrilla se envíe como array de IDs para el frontend
        if ($papeleta->miembros_cuadrilla) {
            // Si ya es un array, convertir elementos a enteros
            if (is_array($papeleta->miembros_cuadrilla)) {
                $papeleta->miembros_cuadrilla = array_map('intval', $papeleta->miembros_cuadrilla);
            }
            // Si es JSON string, decodificar y convertir a enteros
            elseif (is_string($papeleta->miembros_cuadrilla)) {
                $decoded = json_decode($papeleta->miembros_cuadrilla, true);
                $papeleta->miembros_cuadrilla = is_array($decoded) ? array_map('intval', $decoded) : [];
            }
        } else {
            $papeleta->miembros_cuadrilla = [];
        }
        
        // Cargar empleados completos para otros usos (si se necesita)
        $papeleta->miembros_cuadrilla_empleados = $papeleta->miembrosCuadrillaEmpleados();
        
        // Agregar información formateada del vehículo para el frontend
        if ($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->vehiculo) {
            $vehiculo = $papeleta->asignacionVehiculo->vehiculo;
            $cuadrilla = $papeleta->asignacionVehiculo->cuadrilla;
            $chofer = $papeleta->asignacionVehiculo->empleado;
            
            $vehiculoInfo = $vehiculo->marca . ' ' . $vehiculo->modelo . ' - ' . $vehiculo->placa;
            if ($cuadrilla) {
                $vehiculoInfo .= ' (' . $cuadrilla->nombre . ')';
            }
            if ($chofer) {
                $vehiculoInfo .= ' | Chofer: ' . $chofer->nombre . ' ' . $chofer->apellido;
            }
            
            $papeleta->vehiculo_info = $vehiculoInfo;
        }

        return response()->json($papeleta);
    }

    /**
     * Obtener empleados disponibles para chofer
     */
    public function empleadosDisponibles(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $tipo = $request->get('tipo', 'todos');
        
        $query = Empleado::with('cargo')
            ->where('activo', true);
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('apellido', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$search}%"]);
            });
        }
        
        // Si es para chofer, filtrar por cargos relevantes (opcional)
        if ($tipo === 'chofer') {
            // Puedes agregar filtros específicos para choferes aquí
            // $query->whereHas('cargo', function($q) {
            //     $q->whereIn('nombre', ['Chofer', 'Conductor', 'Operador']);
            // });
        }
        
        $empleados = $query->paginate(10, ['*'], 'page', $page);
        
        $results = $empleados->map(function($empleado) {
            return [
                'id' => $empleado->id,
                'text' => $empleado->nombre . ' ' . $empleado->apellido . ' (' . $empleado->cargo->nombre . ')'
            ];
        });
        
        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $empleados->hasMorePages()
            ]
        ]);
    }

    /**
     * Obtener información de la cuadrilla para una asignación de vehículo
     */
    public function cuadrillaInfo($asignacionVehiculoId)
    {
        $asignacion = AsignacionVehiculo::with([
            'cuadrilla.empleados.cargo',
            'empleado' // Chofer permanente asignado al vehículo
        ])->find($asignacionVehiculoId);
        
        if (!$asignacion || !$asignacion->cuadrilla) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró cuadrilla para esta asignación'
            ]);
        }
        
        $cuadrilla = $asignacion->cuadrilla;
        $empleados = $cuadrilla->empleados->map(function($empleado) {
            return [
                'id' => $empleado->id,
                'nombre' => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'cargo' => $empleado->cargo->nombre ?? 'Sin cargo'
            ];
        });

        // Información del chofer permanente (si existe)
        $choferPermanente = null;
        if ($asignacion->empleado) {
            $choferPermanente = [
                'id' => $asignacion->empleado->id,
                'nombre' => $asignacion->empleado->nombre,
                'apellido' => $asignacion->empleado->apellido,
                'dni' => $asignacion->empleado->dni,
                'nombre_completo' => $asignacion->empleado->nombre . ' ' . $asignacion->empleado->apellido
            ];
        }
        
        return response()->json([
            'success' => true,
            'cuadrilla' => [
                'id' => $cuadrilla->id,
                'nombre' => $cuadrilla->nombre,
                'empleados' => $empleados
            ],
            'chofer_permanente' => $choferPermanente
        ]);
    }

    public function update(Request $request, $id)
    {
        $papeleta = Papeleta::findOrFail($id);

        // Solo se puede editar si no ha iniciado el viaje
        if ($papeleta->fecha_hora_salida) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede editar una papeleta que ya inició el viaje.'
            ], 422);
        }

        $request->validate([
            'asignacion_vehiculo_id' => 'required|exists:asignacion_vehiculos,id',
            'fecha' => 'required|date', // Remover after_or_equal:today para permitir editar fechas pasadas
            'destino' => 'required|string|max:255',
            'motivo' => 'required|string',
            'km_salida' => 'required|numeric|min:0',
            'miembros_cuadrilla' => 'nullable|array',
            'miembros_cuadrilla.*' => 'exists:empleados,id',
            'personal_adicional' => 'nullable|string'
        ]);

        $papeleta->update([
            'asignacion_vehiculo_id' => $request->asignacion_vehiculo_id,
            'fecha' => $request->fecha,
            'destino' => $request->destino,
            'motivo' => $request->motivo,
            'km_salida' => $request->km_salida,
            'miembros_cuadrilla' => $request->miembros_cuadrilla ?? [],
            'personal_adicional' => $request->personal_adicional,
            'usuario_actualizacion_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true, 
            'data' => $papeleta,
            'message' => 'Papeleta actualizada exitosamente'
        ]);
    }

    public function destroy($id)
    {
        $papeleta = Papeleta::findOrFail($id);

        // Solo se puede eliminar si no ha iniciado el viaje
        if ($papeleta->fecha_hora_salida) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar una papeleta que ya inició el viaje.'
            ], 422);
        }

        $papeleta->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Iniciar viaje
     */
    public function iniciarViaje(Request $request, $id)
    {
        $papeleta = Papeleta::findOrFail($id);

        if ($papeleta->fecha_hora_salida) {
            return response()->json([
                'success' => false,
                'message' => 'El viaje ya fue iniciado.'
            ], 422);
        }

        $request->validate([
            'km_salida' => 'required|numeric|min:0',
        ]);

        $papeleta->update([
            'km_salida' => $request->km_salida,
            'fecha_hora_salida' => $this->nowPeru(),
            'usuario_actualizacion_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Viaje iniciado correctamente.',
            'data' => $papeleta
        ]);
    }

    /**
     * Finalizar viaje
     */
    public function finalizarViaje(Request $request, $id)
    {
        $papeleta = Papeleta::findOrFail($id);

        if (!$papeleta->fecha_hora_salida) {
            return response()->json([
                'success' => false,
                'message' => 'Debe iniciar el viaje antes de finalizarlo.'
            ], 422);
        }

        if ($papeleta->fecha_hora_llegada) {
            return response()->json([
                'success' => false,
                'message' => 'El viaje ya fue finalizado.'
            ], 422);
        }

        $request->validate([
            'km_llegada' => 'required|numeric|min:' . $papeleta->km_salida,
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $papeleta->update([
            'km_llegada' => $request->km_llegada,
            'observaciones' => $request->observaciones,
            'fecha_hora_llegada' => $this->nowPeru(),
            'usuario_actualizacion_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Viaje finalizado correctamente.',
            'data' => $papeleta
        ]);
    }

    /**
     * Anular papeleta
     */
    public function anular(Request $request, $id)
    {
        $papeleta = Papeleta::findOrFail($id);

        if (!$papeleta->estado) {
            return response()->json([
                'success' => false,
                'message' => 'La papeleta ya está anulada.'
            ], 422);
        }

        $request->validate([
            'motivo_anulacion' => 'required|string|max:200',
        ]);

        $papeleta->update([
            'estado' => false,
            'fecha_anulacion' => $this->nowPeru(),
            'motivo_anulacion' => $request->motivo_anulacion,
            'usuario_actualizacion_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Papeleta anulada correctamente.',
            'data' => $papeleta
        ]);
    }

    /**
     * Obtener asignaciones de vehículos disponibles para el usuario
     */
    public function getAsignacionesDisponibles(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 10;

        $query = AsignacionVehiculo::with(['vehiculo', 'cuadrilla', 'empleado'])
            ->whereHas('cuadrilla.cuadrillaEmpleados', function ($q) use ($user) {
                $q->whereHas('empleado.usuario', function ($emp) use ($user) {
                    $emp->where('id', $user->id);
                })->where('estado', true);
            })
            ->where('estado', true);

        if (!empty($search)) {
            $query->whereHas('vehiculo', function ($q) use ($search) {
                $q->where('placa', 'LIKE', "%{$search}%")
                  ->orWhere('marca', 'LIKE', "%{$search}%")
                  ->orWhere('modelo', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $asignaciones = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $results = $asignaciones->map(function ($asignacion) {
            $vehiculo = $asignacion->vehiculo;
            $choferInfo = '';
            
            // Agregar información del chofer si existe
            if ($asignacion->empleado) {
                $choferInfo = ' | Chofer: ' . $asignacion->empleado->nombre . ' ' . $asignacion->empleado->apellido;
            }
            
            return [
                'id' => $asignacion->id,
                'text' => $vehiculo->marca . ' ' . $vehiculo->modelo . ' - ' . $vehiculo->placa . ' (' . $asignacion->cuadrilla->nombre . ')' . $choferInfo
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    /**
     * Obtener el último kilometraje registrado para un vehículo
     * CORREGIDO: Busca por vehículo físico, no por asignación específica
     */
    public function getUltimoKilometraje($asignacionVehiculoId)
    {
        try {
            $asignacionVehiculo = AsignacionVehiculo::findOrFail($asignacionVehiculoId);
            $vehiculoId = $asignacionVehiculo->vehiculo_id;
            
            // DEBUG: Log para ver qué estamos buscando
            \Log::info("Buscando último kilometraje para vehículo ID: {$vehiculoId} (desde asignación: {$asignacionVehiculoId})");
            
            // Primero: Buscar la última papeleta finalizada con km_llegada (DE CUALQUIER CHOFER DEL MISMO VEHÍCULO)
            $ultimaPapeletaFinalizada = Papeleta::whereHas('asignacionVehiculo', function($query) use ($vehiculoId) {
                    $query->where('vehiculo_id', $vehiculoId);
                })
                ->whereNotNull('km_llegada')
                ->where('estado', 'finalizada')
                ->orderBy('fecha_hora_llegada', 'desc')
                ->first();
            
            \Log::info("Papeleta finalizada encontrada: " . ($ultimaPapeletaFinalizada ? $ultimaPapeletaFinalizada->id : 'ninguna'));
            
            if ($ultimaPapeletaFinalizada) {
                return response()->json([
                    'success' => true,
                    'ultimo_kilometraje' => floatval($ultimaPapeletaFinalizada->km_llegada),
                    'origen' => 'última papeleta finalizada',
                    'fecha_origen' => $ultimaPapeletaFinalizada->fecha_hora_llegada,
                    'papeleta_id' => $ultimaPapeletaFinalizada->id,
                    'chofer_anterior' => $ultimaPapeletaFinalizada->asignacionVehiculo->empleado->nombre ?? 'N/A'
                ]);
            }
            
            // Segundo: Buscar cualquier papeleta con km_llegada (DE CUALQUIER CHOFER DEL MISMO VEHÍCULO)
            $ultimaPapeletaConLlegada = Papeleta::whereHas('asignacionVehiculo', function($query) use ($vehiculoId) {
                    $query->where('vehiculo_id', $vehiculoId);
                })
                ->whereNotNull('km_llegada')
                ->orderBy('fecha_hora_llegada', 'desc')
                ->orderBy('updated_at', 'desc')
                ->first();
            
            \Log::info("Papeleta con llegada encontrada: " . ($ultimaPapeletaConLlegada ? $ultimaPapeletaConLlegada->id : 'ninguna'));
            
            if ($ultimaPapeletaConLlegada) {
                return response()->json([
                    'success' => true,
                    'ultimo_kilometraje' => floatval($ultimaPapeletaConLlegada->km_llegada),
                    'origen' => 'última papeleta con km_llegada',
                    'fecha_origen' => $ultimaPapeletaConLlegada->fecha_hora_llegada ?? $ultimaPapeletaConLlegada->updated_at,
                    'papeleta_id' => $ultimaPapeletaConLlegada->id,
                    'chofer_anterior' => $ultimaPapeletaConLlegada->asignacionVehiculo->empleado->nombre ?? 'N/A'
                ]);
            }
            
            // Tercero: Buscar la papeleta más reciente (DE CUALQUIER CHOFER DEL MISMO VEHÍCULO)
            $ultimaPapeleta = Papeleta::whereHas('asignacionVehiculo', function($query) use ($vehiculoId) {
                    $query->where('vehiculo_id', $vehiculoId);
                })
                ->whereNotNull('km_salida')
                ->orderBy('fecha', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
            
            \Log::info("Última papeleta cualquiera encontrada: " . ($ultimaPapeleta ? $ultimaPapeleta->id : 'ninguna'));
            
            if ($ultimaPapeleta) {
                // Si tiene km_llegada, usarlo; si no, usar km_salida
                $kmUsar = $ultimaPapeleta->km_llegada ?? $ultimaPapeleta->km_salida;
                $origen = $ultimaPapeleta->km_llegada ? 'km_llegada de papeleta reciente' : 'km_salida de papeleta reciente';
                
                return response()->json([
                    'success' => true,
                    'ultimo_kilometraje' => floatval($kmUsar),
                    'origen' => $origen,
                    'fecha_origen' => $ultimaPapeleta->fecha,
                    'papeleta_id' => $ultimaPapeleta->id,
                    'chofer_anterior' => $ultimaPapeleta->asignacionVehiculo->empleado->nombre ?? 'N/A'
                ]);
            }
            
            // Cuarto: Buscar el kilometraje inicial del vehículo
            $vehiculo = $asignacionVehiculo->vehiculo;
            \Log::info("Vehículo encontrado: " . ($vehiculo ? $vehiculo->id : 'ninguno'));
            
            if ($vehiculo && isset($vehiculo->kilometraje_inicial) && $vehiculo->kilometraje_inicial > 0) {
                return response()->json([
                    'success' => true,
                    'ultimo_kilometraje' => floatval($vehiculo->kilometraje_inicial),
                    'origen' => 'kilometraje inicial del vehículo',
                    'fecha_origen' => $vehiculo->created_at
                ]);
            }
            
            // Quinto: Verificar si hay algún campo relacionado con kilometraje en el vehículo
            \Log::info("Campos del vehículo: " . ($vehiculo ? json_encode($vehiculo->toArray()) : 'sin vehículo'));
            
            // Si no hay ningún kilometraje registrado
            return response()->json([
                'success' => true,
                'ultimo_kilometraje' => null,
                'mensaje' => 'No se encontró kilometraje previo para este vehículo. Puede ingresar manualmente.',
                'debug_info' => [
                    'asignacion_vehiculo_id' => $asignacionVehiculoId,
                    'vehiculo_id' => $vehiculo ? $vehiculo->id : null,
                    'vehiculo_info' => $vehiculo ? "{$vehiculo->marca} {$vehiculo->modelo} - {$vehiculo->placa}" : null,
                    'papeletas_count' => Papeleta::whereHas('asignacionVehiculo', function($query) use ($vehiculoId) {
                        $query->where('vehiculo_id', $vehiculoId);
                    })->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Error al obtener último kilometraje: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener el último kilometraje',
                'mensaje' => $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Generar PDF individual de papeleta
     */
    public function imprimirPdf($id)
    {
        $papeleta = Papeleta::with([
            'asignacionVehiculo.vehiculo.tipoCombustible',
            'asignacionVehiculo.empleado.cargo',
            'asignacionVehiculo.empleado.area',
            'asignacionVehiculo.cuadrilla',
            'usuarioCreacion'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.papeletas.pdf_nuevo', compact('papeleta'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('papeleta_' . $papeleta->correlativo . '.pdf');
    }

    /**
     * Generar PDF con dos copias lado a lado en orientación horizontal
     */
    public function imprimirDobleHorizontal($id)
    {
        $papeleta = Papeleta::with([
            'asignacionVehiculo.vehiculo.tipoCombustible',
            'asignacionVehiculo.empleado.cargo',
            'asignacionVehiculo.empleado.area',
            'asignacionVehiculo.cuadrilla',
            'usuarioCreacion'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.papeletas.pdf_doble_horizontal', compact('papeleta'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('papeleta_doble_horizontal_' . $papeleta->correlativo . '.pdf');
    }

    /**
     * Previsualizar PDF en navegador
     */
    public function previsualizarPdf($id)
    {
        $papeleta = Papeleta::with([
            'asignacionVehiculo.vehiculo.tipoCombustible',
            'asignacionVehiculo.empleado.cargo',
            'asignacionVehiculo.empleado.area',
            'asignacionVehiculo.cuadrilla',
            'usuarioCreacion'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.papeletas.pdf_nuevo', compact('papeleta'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('papeleta_' . $papeleta->correlativo . '.pdf');
    }

    /**
     * Previsualizar PDF doble horizontal en navegador
     */
    public function previsualizarPdfDoble($id)
    {
        $papeleta = Papeleta::with([
            'asignacionVehiculo.vehiculo.tipoCombustible',
            'asignacionVehiculo.empleado.cargo',
            'asignacionVehiculo.empleado.area',
            'asignacionVehiculo.cuadrilla',
            'usuarioCreacion'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.papeletas.pdf_doble_horizontal', compact('papeleta'))
            ->setPaper('148mm 105mm', 'landscape');

        return $pdf->stream('papeleta_doble_horizontal_' . $papeleta->correlativo . '.pdf');
    }

}