<?php

namespace App\Http\Controllers;

use App\Models\Pecosa;
use App\Models\PecosaDetalle;
use App\Models\CuadrillasEmpleado;
use App\Models\NeaDetalle;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PecosaController extends Controller
{
    /**
     * Obtener fecha y hora actual de Perú
     */
    private function nowPeru()
    {
        return Carbon::now('America/Lima');
    }

    /**
     * Mostrar página principal de gestión de PECOSAs
     */
    public function index()
    {
        $cuadrillas = CuadrillasEmpleado::where('estado', true)
            ->with('empleado', 'cuadrilla')
            ->get()
            ->mapWithKeys(function ($ce) {
                $empleadoNombre = $ce->empleado ? ($ce->empleado->nombre . ' ' . ($ce->empleado->apellido ?? '')) : 'N/A';
                $cuadrillaNombre = $ce->cuadrilla ? $ce->cuadrilla->nombre : 'N/A';
                return [$ce->id => $cuadrillaNombre . ' - ' . $empleadoNombre];
            });
        
        return view('admin.pecosas.index', [
            'catName' => 'pecosas',
            'title' => 'Gestión de PECOSAs (Partes de Entrega de Comisión de Obra)',
            'breadcrumbs' => ['Almacén', 'PECOSAs'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'cuadrillas' => $cuadrillas
        ]);
    }

    /**
     * Obtener datos para DataTable
     */
    public function getData(Request $request)
    {
        $pecosas = Pecosa::with([
            'cuadrillaEmpleado.empleado',
            'cuadrillaEmpleado.cuadrilla',
            'detalles.neaDetalle.material',
            'usuarioCreacion'
        ])->orderByDesc('id');

        return DataTables::of($pecosas)
            ->addIndexColumn()
            ->addColumn('empleado_nombre', function ($pecosa) {
                return $pecosa->cuadrillaEmpleado && $pecosa->cuadrillaEmpleado->empleado 
                    ? $pecosa->cuadrillaEmpleado->empleado->nombre_completo 
                    : 'N/A';
            })
            ->addColumn('cuadrilla_nombre', function ($pecosa) {
                return $pecosa->cuadrillaEmpleado && $pecosa->cuadrillaEmpleado->cuadrilla 
                    ? $pecosa->cuadrillaEmpleado->cuadrilla->nombre 
                    : 'N/A';
            })
            ->addColumn('fecha_formatted', function ($pecosa) {
                return $pecosa->fecha ? $pecosa->fecha->format('d/m/Y') : '';
            })
            ->addColumn('cantidad_detalles', function ($pecosa) {
                return count($pecosa->detalles);
            })
            ->addColumn('total_sin_igv', function ($pecosa) {
                return 'S/ ' . number_format($pecosa->total_sin_igv, 2, ',', '.');
            })
            ->addColumn('igv_total', function ($pecosa) {
                return 'S/ ' . number_format($pecosa->igv_total, 2, ',', '.');
            })
            ->addColumn('total_con_igv', function ($pecosa) {
                return 'S/ ' . number_format($pecosa->total_con_igv, 2, ',', '.');
            })
            ->addColumn('estado_badge', function ($pecosa) {
                if ($pecosa->anulada) {
                    return '<span class="badge bg-danger" style="font-size: 12px; padding: 6px 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                Anulada
                            </span>';
                }
                $color = $pecosa->estado ? 'success' : 'warning';
                $texto = $pecosa->estado ? 'Activo' : 'Inactivo';
                return '<span class="badge bg-' . $color . '" style="font-size: 12px; padding: 6px 10px;">' . $texto . '</span>';
            })
            ->addColumn('action', function ($pecosa) {
                $actions = '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $pecosa->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $pecosa->id . '">';

                // Ver detalles
                $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="verPecosa(' . $pecosa->id . ')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye me-2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Ver Detalles
                            </a>';

                // Previsualizar PDF
                $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="previsualizarPecosaPdf(' . $pecosa->id . ')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-pdf me-2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Previsualizar PDF
                            </a>';

                // Editar (solo si no está anulada)
                if (!$pecosa->anulada) {
                    $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="editPecosa(' . $pecosa->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Editar
                                </a>';
                }

                // Anular (si no está anulada)
                if (!$pecosa->anulada) {
                    $actions .= '<a class="dropdown-item text-warning" href="javascript:void(0);" onclick="anularPecosa(' . $pecosa->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle me-2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    Anular
                                </a>';
                }

                // Eliminar (solo si no está anulada)
                if (!$pecosa->anulada) {
                    $actions .= '<div class="dropdown-divider"></div>';
                    $actions .= '<a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminarPecosa(' . $pecosa->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 me-2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                    Eliminar
                                </a>';
                } else {
                    $actions .= '<div class="dropdown-divider"></div>';
                    $actions .= '<span class="dropdown-item" style="color: #95a5a6; cursor: not-allowed;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock me-2" style="vertical-align: middle;">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    Bloqueada (Anulada)
                                </span>';
                }

                $actions .= '</div>
                            </div>';

                return $actions;
            })
            ->rawColumns(['estado_badge', 'action'])
            ->make(true);
    }

    /**
     * Obtener PECOSAs de una cuadrilla específica (para agregar materiales en fichas)
     * GET /pecosas/cuadrilla/{cuadrillaId}/pecosas
     */
    public function getPecosasPorCuadrillaId($cuadrillaId)
    {
        try {
            // Obtener todas las PECOSAS activas asignadas a esta cuadrilla
            $pecosas = Pecosa::with([
                'detalles.neaDetalle.material.unidadMedida',
                'cuadrillaEmpleado.cuadrilla'
            ])
            ->whereHas('cuadrillaEmpleado', function ($query) use ($cuadrillaId) {
                $query->where('cuadrilla_id', $cuadrillaId);
            })
            ->where('estado', true)
            ->latest('fecha')
            ->get();

            // Construir respuesta con materiales disponibles
            $materialesDisponibles = [];
            
            foreach ($pecosas as $pecosa) {
                foreach ($pecosa->detalles as $detalle) {
                    $material = $detalle->neaDetalle->material;
                    
                    // Calcular saldo disponible del material en esta PECOSA
                    $saldo = \App\Models\MaterialPecosaMovimiento::where('pecosa_id', $pecosa->id)
                        ->where('material_id', $material->id)
                        ->where('estado', true)
                        ->sum(DB::raw("CASE WHEN tipo_movimiento = 'entrada' THEN cantidad ELSE -cantidad END"));
                    
                    if ($saldo > 0) {
                        $materialesDisponibles[] = [
                            'id' => $material->id,
                            'nombre' => $material->nombre,
                            'codigo_material' => $material->codigo_material,
                            'unidad_medida' => $material->unidadMedida ? $material->unidadMedida->nombre : 'UND',
                            'precio_unitario' => $detalle->precio_unitario,
                            'saldo_disponible' => $saldo,
                            'pecosa_id' => $pecosa->id,
                            'text' => $material->nombre . ' (' . number_format($saldo, 2) . ' ' . ($material->unidadMedida ? $material->unidadMedida->abreviatura : 'UND') . ')'
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $materialesDisponibles,
                'pecosas' => $pecosas,
                'message' => count($materialesDisponibles) . ' materiales disponibles'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en getPecosasPorCuadrillaId: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error al cargar materiales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva PECOSA
     */
    public function store(Request $request)
    {
        $request->validate([
            'cuadrilla_empleado_id' => 'required|exists:cuadrillas_empleados,id',
            'fecha' => 'required|date',
            'nro_documento' => 'nullable|string|max:50|unique:pecosas',
            'observaciones' => 'nullable|string',
            'estado' => 'required|boolean',
            'detalles' => 'required|array|min:1',
            'detalles.*.nea_detalle_id' => 'required|exists:nea_detalles,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.001'
        ]);

        try {
            DB::beginTransaction();

            // Crear PECOSA
            $pecosa = Pecosa::create([
                'cuadrilla_empleado_id' => $request->cuadrilla_empleado_id,
                'fecha' => $request->fecha,
                'nro_documento' => $request->nro_documento ?: Pecosa::generarNumeroPecosa($request->fecha),
                'observaciones' => $request->observaciones,
                'estado' => $request->estado,
                'usuario_creacion_id' => Auth::id()
            ]);

            // Crear detalles y movimientos
            foreach ($request->detalles as $detalle) {
                // Obtener precio del NEA
                $neaDetalle = NeaDetalle::findOrFail($detalle['nea_detalle_id']);
                
                $pecosaDetalle = PecosaDetalle::create([
                    'pecosa_id' => $pecosa->id,
                    'nea_detalle_id' => $detalle['nea_detalle_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $neaDetalle->precio_unitario,
                    'incluye_igv' => $neaDetalle->incluye_igv,
                    'estado' => true,
                    'usuario_creacion_id' => Auth::id()
                ]);

                // Crear movimiento de entrada en tabla 'movimientos' (NEA -> PECOSA)
                // NOTA: La ENTRADA ya se registró cuando llegó la NEA
                // Aquí solo registramos la SALIDA hacia la cuadrilla
                // Movimiento::create([
                //     'material_id' => $neaDetalle->material_id,
                //     'tipo_movimiento' => 'entrada',
                //     'nea_detalle_id' => $detalle['nea_detalle_id'],
                //     'pecosa_detalle_id' => $pecosaDetalle->id,
                //     'cantidad' => $detalle['cantidad'],
                //     'precio_unitario' => $neaDetalle->precio_unitario,
                //     'incluye_igv' => $neaDetalle->incluye_igv,
                //     'fecha' => $request->fecha,
                //     'estado' => true,
                //     'usuario_creacion_id' => Auth::id()
                // ]);

                // Crear movimiento de SALIDA en tabla 'movimientos' (PECOSA -> CUADRILLA)
                // Esto registra la salida del almacén hacia la cuadrilla
                Movimiento::create([
                    'material_id' => $neaDetalle->material_id,
                    'tipo_movimiento' => 'salida',
                    'nea_detalle_id' => $detalle['nea_detalle_id'],
                    'pecosa_detalle_id' => $pecosaDetalle->id,
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $neaDetalle->precio_unitario,
                    'incluye_igv' => $neaDetalle->incluye_igv,
                    'fecha' => $request->fecha,
                    'estado' => true,
                    'usuario_creacion_id' => Auth::id()
                ]);

                // TAMBIÉN crear entrada en material_pecosa_movimientos para controlar PECOSA -> FICHA
                \App\Models\MaterialPecosaMovimiento::create([
                    'pecosa_id' => $pecosa->id,
                    'material_id' => $neaDetalle->material_id,
                    'cantidad' => $detalle['cantidad'],
                    'tipo_movimiento' => 'entrada',
                    'estado' => true,
                    'usuario_creacion_id' => Auth::id()
                ]);
            }

            DB::commit();

            $pecosa->load(['cuadrillaEmpleado', 'detalles.neaDetalle.material']);

            return response()->json([
                'success' => true,
                'data' => $pecosa,
                'message' => 'PECOSA creada correctamente con número: ' . $pecosa->nro_documento
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear PECOSA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la PECOSA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener PECOSA con todos sus detalles
     */
    public function show($id)
    {
        $pecosa = Pecosa::with([
            'cuadrillaEmpleado.empleado',
            'cuadrillaEmpleado.cuadrilla',
            'detalles.neaDetalle.material.unidadMedida',
            'detalles.neaDetalle.nea',
            'detalles.neaDetalle.movimientos',
            'usuarioCreacion',
            'usuarioActualizacion'
        ])->findOrFail($id);

        // Calcular stock disponible para cada NEA Detalle
        $pecosa->detalles->each(function ($detalle) {
            if ($detalle->neaDetalle) {
                $stockIngreso = $detalle->neaDetalle->movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad');
                $stockSalida = $detalle->neaDetalle->movimientos->where('tipo_movimiento', 'salida')->sum('cantidad');
                $detalle->neaDetalle->stock_disponible = $stockIngreso - $stockSalida;
            }
        });

        return response()->json($pecosa);
    }

    /**
     * Obtener PECOSA para edición (devuelve JSON)
     */
    public function edit($id)
    {
        // Redirigir a la página de índice con parámetro para abrir el modal
        return redirect()->route('pecosas.index')->with('edit_id', $id);
    }

    /**
     * Actualizar PECOSA desde el modal
     */
    public function update(Request $request, $id)
    {
        $pecosa = Pecosa::findOrFail($id);

        // Validación básica
        $request->validate([
            'cuadrilla_empleado_id' => 'required|exists:cuadrillas_empleados,id',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.cantidad' => 'required|numeric|min:0.001'
        ]);

        try {
            DB::beginTransaction();

            // PASO 1: Eliminar TODOS los movimientos (salida) de esta PECOSA
            Movimiento::whereIn('pecosa_detalle_id', 
                PecosaDetalle::where('pecosa_id', $pecosa->id)->pluck('id')
            )->where('tipo_movimiento', 'salida')->delete();

            // PASO 2: Eliminar TODOS los detalles existentes
            PecosaDetalle::where('pecosa_id', $pecosa->id)->delete();

            // PASO 3: Actualizar información general de la PECOSA
            $pecosa->update([
                'cuadrilla_empleado_id' => $request->cuadrilla_empleado_id,
                'fecha' => $request->fecha,
                'observaciones' => $request->observaciones,
                'estado' => $request->has('estado') ? ($request->estado ? true : false) : false,
                'usuario_actualizacion_id' => Auth::id()
            ]);

            // PASO 4: Recrear TODOS los detalles y movimientos con los nuevos valores
            foreach ($request->detalles as $detalle) {
                $neaDetalle = NeaDetalle::findOrFail($detalle['nea_detalle_id']);
                
                // Crear nuevo detalle PECOSA
                $pecosaDetalle = PecosaDetalle::create([
                    'pecosa_id' => $pecosa->id,
                    'nea_detalle_id' => $detalle['nea_detalle_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $neaDetalle->precio_unitario,
                    'incluye_igv' => $neaDetalle->incluye_igv,
                    'estado' => true,
                    'usuario_creacion_id' => Auth::id()
                ]);

                // Crear nuevo movimiento de salida
                Movimiento::create([
                    'material_id' => $neaDetalle->material_id,
                    'tipo_movimiento' => 'salida',
                    'nea_detalle_id' => $detalle['nea_detalle_id'],
                    'pecosa_detalle_id' => $pecosaDetalle->id,
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $neaDetalle->precio_unitario,
                    'incluye_igv' => $neaDetalle->incluye_igv,
                    'fecha' => $request->fecha,
                    'estado' => true,
                    'usuario_creacion_id' => Auth::id()
                ]);
            }

            DB::commit();

            $pecosa->load(['cuadrillaEmpleado', 'detalles.neaDetalle.material']);

            return response()->json([
                'success' => true,
                'data' => $pecosa,
                'message' => 'PECOSA actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar PECOSA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la PECOSA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar PECOSA
     */
    public function destroy($id)
    {
        $pecosa = Pecosa::findOrFail($id);

        try {
            DB::beginTransaction();

            // Eliminar detalles
            PecosaDetalle::where('pecosa_id', $pecosa->id)->delete();

            // Eliminar PECOSA
            $pecosa->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PECOSA eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar PECOSA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la PECOSA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Anular una PECOSA
     */
    public function anular(Request $request, $id)
    {
        $pecosa = Pecosa::findOrFail($id);

        if ($pecosa->anulada) {
            return response()->json([
                'success' => false,
                'message' => 'La PECOSA ya está anulada'
            ], 422);
        }

        $request->validate([
            'motivo_anulacion' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $pecosa->update([
                'anulada' => true,
                'motivo_anulacion' => $request->motivo_anulacion,
                'usuario_anulacion_id' => Auth::id(),
                'fecha_anulacion' => $this->nowPeru(),
                'estado' => false
            ]);

            // Invertir los movimientos de salida creados al generar la PECOSA
            // Buscamos los movimientos de salida asociados a los detalles de esta PECOSA
            $detalles = PecosaDetalle::where('pecosa_id', $id)->get();
            
            foreach ($detalles as $detalle) {
                // Marcar el movimiento de salida como inactivo
                Movimiento::where('pecosa_detalle_id', $detalle->id)
                    ->where('tipo_movimiento', 'salida')
                    ->update(['estado' => false]);
            }

            DB::commit();

            $pecosa->load(['cuadrillaEmpleado', 'usuarioAnulacion', 'detalles.neaDetalle.material']);

            return response()->json([
                'success' => true,
                'message' => 'PECOSA anulada correctamente',
                'data' => $pecosa
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al anular la PECOSA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar próximo número de PECOSA
     */
    public function proximoNumeroPecosa()
    {
        $numeroPecosa = Pecosa::generarNumeroPecosa(now());

        return response()->json([
            'success' => true,
            'numero_pecosa' => $numeroPecosa,
            'proximo_numero' => $numeroPecosa
        ]);
    }

    /**
     * Obtener empleados de una cuadrilla para retornar con nombre_completo
     */
    public function getEmpleadosCuadrilla($cuadrillaId)
    {
        try {
            $empleados = CuadrillasEmpleado::where('cuadrilla_id', $cuadrillaId)
                ->where('estado', true)
                ->with('empleado')
                ->get()
                ->map(function ($ce) {
                    return [
                        'id' => $ce->id,
                        'empleado_nombre' => $ce->empleado ? $ce->empleado->nombre : 'N/A',
                        'empleado_apellido' => $ce->empleado ? $ce->empleado->apellido : ''
                    ];
                });

            return response()->json($empleados);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtener detalles NEA disponibles con stock calculado
     */
    public function getNeaDetallesDisponibles($cuadrillaEmpleadoId = null)
    {
        try {
            $neaDetalles = NeaDetalle::with(['material', 'nea', 'movimientos'])
                ->where('estado', true)
                ->whereHas('nea', function ($query) {
                    $query->where('anulada', false);
                })
                ->get()
                ->map(function ($nd) {
                    // Calcular stock disponible desde movimientos
                    $stockIngreso = $nd->movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad');
                    $stockSalida = $nd->movimientos->where('tipo_movimiento', 'salida')->sum('cantidad');
                    $stockDisponible = $stockIngreso - $stockSalida;

                    return [
                        'id' => $nd->id,
                        'material_nombre' => "[" . ($nd->material ? $nd->material->codigo_material : 'N/A') . "] " . 
                                  ($nd->material ? $nd->material->nombre : 'N/A') .
                                  " - NEA: " . ($nd->nea ? $nd->nea->nro_documento : 'N/A'),
                        'precio_unitario' => $nd->precio_unitario,
                        'incluye_igv' => $nd->incluye_igv,
                        'stock_disponible' => max(0, $stockDisponible),
                        'unidad_medida' => $nd->material && $nd->material->unidadMedida ? $nd->material->unidadMedida->nombre : 'UND'
                    ];
                })
                // Filtrar solo los que tienen stock disponible > 0
                ->filter(function ($item) {
                    return $item['stock_disponible'] > 0;
                })
                ->values();

            return response()->json($neaDetalles);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Previsualizar PECOSA en PDF
     */
    public function previsualizarPecosaPdf($id)
    {
        try {
            $pecosa = Pecosa::with([
                'cuadrillaEmpleado.cuadrilla', 
                'cuadrillaEmpleado.empleado', 
                'detalles' => function($query) {
                    $query->with([
                        'neaDetalle' => function($q) {
                            $q->with(['material' => function($m) {
                                $m->with('unidadMedida');
                            }]);
                        }
                    ]);
                },
                'usuarioCreacion', 
                'usuarioAnulacion'
            ])->findOrFail($id);

            // Generar HTML desde la vista
            $html = view('admin.pecosas.pdf_pecosa', compact('pecosa'))->render();

            // Generar PDF
            $pdf = \PDF::loadHTML($html);
            
            // Retornar como stream para visualización
            return $pdf->stream('PECOSA-' . $pecosa->nro_documento . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al previsualizar PECOSA PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar la vista previa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Imprimir PECOSA en PDF
     */
    public function imprimirPecosaPdf($id)
    {
        try {
            $pecosa = Pecosa::with([
                'cuadrillaEmpleado.cuadrilla', 
                'cuadrillaEmpleado.empleado', 
                'detalles' => function($query) {
                    $query->with([
                        'neaDetalle' => function($q) {
                            $q->with(['material' => function($m) {
                                $m->with('unidadMedida');
                            }]);
                        }
                    ]);
                },
                'usuarioCreacion', 
                'usuarioAnulacion'
            ])->findOrFail($id);

            // Generar HTML desde la vista
            $html = view('admin.pecosas.pdf_pecosa', compact('pecosa'))->render();

            // Generar PDF
            $pdf = \PDF::loadHTML($html);
            
            // Retornar como descarga
            return $pdf->download('PECOSA-' . $pecosa->nro_documento . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al imprimir PECOSA PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
