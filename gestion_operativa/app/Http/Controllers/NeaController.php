<?php

namespace App\Http\Controllers;

use App\Models\Nea;
use App\Models\NeaDetalle;
use App\Models\Proveedor;
use App\Models\Material;
use App\Models\TipoComprobante;
use App\Models\UnidadMedida;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NeaController extends Controller
{
    /**
     * Obtener fecha y hora actual de Perú
     */
    private function nowPeru()
    {
        return Carbon::now('America/Lima');
    }

    /**
     * Mostrar página principal de gestión de NEAs
     */
    public function index()
    {
        $proveedores = Proveedor::where('estado', true)->pluck('razon_social', 'id');
        $tiposComprobantes = TipoComprobante::where('estado', true)->pluck('nombre', 'id');
        $materiales = Material::where('estado', true)->get(['id', 'codigo_material', 'nombre']);
        
        return view('admin.neas.index', [
            'catName' => 'neas',
            'title' => 'Gestión de NEAs (Notas de Entrada de Almacén)',
            'breadcrumbs' => ['Almacén', 'NEAs'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'proveedores' => $proveedores,
            'tiposComprobantes' => $tiposComprobantes,
            'materiales' => $materiales
        ]);
    }

    /**
     * Obtener datos para DataTable
     */
    public function getData(Request $request)
    {
        $neas = Nea::with([
            'proveedor',
            'tipoComprobante',
            'detalles.material',
            'usuarioCreacion'
        ])->orderByDesc('id'); // Ordenar por ID descendente (más reciente primero)

        return DataTables::of($neas)
            ->addIndexColumn()
            ->addColumn('proveedor_nombre', function ($nea) {
                return $nea->proveedor ? $nea->proveedor->razon_social : 'N/A';
            })
            ->addColumn('tipo_comprobante_nombre', function ($nea) {
                return $nea->tipoComprobante ? $nea->tipoComprobante->nombre : 'N/A';
            })
            ->addColumn('fecha_formatted', function ($nea) {
                return $nea->fecha ? $nea->fecha->format('d/m/Y') : '';
            })
            ->addColumn('cantidad_detalles', function ($nea) {
                return count($nea->detalles);
            })
            ->addColumn('total_sin_igv', function ($nea) {
                return 'S/ ' . number_format($nea->total_sin_igv, 2, ',', '.');
            })
            ->addColumn('igv_total', function ($nea) {
                return 'S/ ' . number_format($nea->igv_total, 2, ',', '.');
            })
            ->addColumn('total_con_igv', function ($nea) {
                return 'S/ ' . number_format($nea->total_con_igv, 2, ',', '.');
            })
            ->addColumn('estado_badge', function ($nea) {
                if ($nea->anulada) {
                    return '<span class="badge bg-danger" style="font-size: 12px; padding: 6px 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                Anulada
                            </span>';
                }
                $color = $nea->estado ? 'success' : 'warning';
                $texto = $nea->estado ? 'Activo' : 'Inactivo';
                return '<span class="badge bg-' . $color . '" style="font-size: 12px; padding: 6px 10px;">' . $texto . '</span>';
            })
            ->addColumn('action', function ($nea) {
                // Verificar si tiene movimientos de salida
                $detallesIds = $nea->detalles()->pluck('id')->toArray();
                $tieneSalidas = Movimiento::where('tipo_movimiento', 'salida')
                    ->whereIn('nea_detalle_id', $detallesIds)
                    ->where('estado', true)
                    ->exists();

                $actions = '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $nea->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $nea->id . '">';

                // Ver detalles
                $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="verNea(' . $nea->id . ')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye me-2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Ver Detalles
                            </a>';

                // Previsualizar PDF
                $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="previsualizarPdf(' . $nea->id . ')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-pdf me-2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                Previsualizar PDF
                            </a>';

                // Editar (solo si no está anulada y no tiene salidas)
                if (!$nea->anulada && !$tieneSalidas) {
                    $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="editNea(' . $nea->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Editar
                                </a>';
                } elseif ($tieneSalidas) {
                    $actions .= '<a class="dropdown-item disabled" href="javascript:void(0);" onclick="alertaSinEdicion()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2" style="opacity: 0.5;">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Editar (deshabilitado)
                                </a>';
                }

                // Anular (si no está anulada y no tiene salidas)
                if (!$nea->anulada && !$tieneSalidas) {
                    $actions .= '<a class="dropdown-item text-warning" href="javascript:void(0);" onclick="anualarNea(' . $nea->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle me-2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    Anular
                                </a>';
                } elseif (!$nea->anulada && $tieneSalidas) {
                    $actions .= '<a class="dropdown-item disabled" href="javascript:void(0);" onclick="alertaSinAnular()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle me-2" style="opacity: 0.5;">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    Anular (deshabilitado)
                                </a>';
                }

                // Eliminar (solo si no está anulada y no tiene salidas)
                if (!$nea->anulada && !$tieneSalidas) {
                    $actions .= '<div class="dropdown-divider"></div>';
                    $actions .= '<a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteNea(' . $nea->id . ')">
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
     * Crear nueva NEA
     */
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'fecha' => 'required|date',
            'nro_documento' => 'nullable|string|max:50|unique:neas',
            'tipo_comprobante_id' => 'required|exists:tipo_comprobantes,id',
            'numero_comprobante' => 'required|string|max:50',
            'observaciones' => 'nullable|string',
            'estado' => 'required|boolean',
            'detalles' => 'required|array|min:1',
            'detalles.*.material_id' => 'required|exists:materials,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.001',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.incluye_igv' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Crear NEA
            $nea = Nea::create([
                'proveedor_id' => $request->proveedor_id,
                'fecha' => $request->fecha,
                'nro_documento' => $request->nro_documento ?: Nea::generarNumeroNea($request->fecha),
                'tipo_comprobante_id' => $request->tipo_comprobante_id,
                'numero_comprobante' => $request->numero_comprobante,
                'observaciones' => $request->observaciones,
                'estado' => $request->estado,
                'usuario_creacion_id' => Auth::id()
            ]);

            // Crear detalles
            foreach ($request->detalles as $detalle) {
                $neaDetalle = NeaDetalle::create([
                    'nea_id' => $nea->id,
                    'material_id' => $detalle['material_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'incluye_igv' => $detalle['incluye_igv'],
                    'estado' => true,
                    'usuario_creacion_id' => Auth::id()
                ]);

                // Crear movimiento de entrada automáticamente
                Movimiento::create([
                    'material_id' => $detalle['material_id'],
                    'tipo_movimiento' => 'entrada',
                    'nea_detalle_id' => $neaDetalle->id,
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'incluye_igv' => $detalle['incluye_igv'],
                    'fecha' => $request->fecha,
                    'estado' => true,
                    'usuario_creacion_id' => Auth::id()
                ]);
            }

            DB::commit();

            $nea->load(['proveedor', 'tipoComprobante', 'detalles.material']);

            return response()->json([
                'success' => true,
                'data' => $nea,
                'message' => 'NEA creada correctamente con número: ' . $nea->nro_documento
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear NEA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la NEA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener NEA con todos sus detalles
     */
    public function show($id)
    {
        $nea = Nea::with([
            'proveedor',
            'tipoComprobante',
            'detalles.material.unidadMedida',
            'usuarioCreacion',
            'usuarioActualizacion'
        ])->findOrFail($id);

        // Agregar información calculada
        $nea->total_sin_igv = $nea->total_sin_igv;
        $nea->igv_total = $nea->igv_total;
        $nea->total_con_igv = $nea->total_con_igv;

        return response()->json($nea);
    }

    /**
     * Actualizar NEA completa con detalles
     */
    public function update(Request $request, $id)
    {
        $nea = Nea::findOrFail($id);

        // Validar que no haya movimientos de salida si se intenta editar
        $detallesIds = $nea->detalles()->pluck('id')->toArray();
        $tieneSalidas = Movimiento::where('tipo_movimiento', 'salida')
            ->whereIn('nea_detalle_id', $detallesIds)
            ->where('estado', true)
            ->exists();

        if ($tieneSalidas) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede editar esta NEA porque ya tiene movimientos de salida (PECOSA) activos asociados. Primero debe anular las PECOSAs relacionadas.'
            ], 422);
        }

        $request->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'fecha' => 'required|date',
            'nro_documento' => 'required|string|max:50|unique:neas,nro_documento,' . $id,
            'tipo_comprobante_id' => 'required|exists:tipo_comprobantes,id',
            'numero_comprobante' => 'required|string|max:50',
            'observaciones' => 'nullable|string',
            'estado' => 'required|boolean',
            'detalles' => 'required|array|min:1',
            'detalles.*.id' => 'nullable|exists:nea_detalles',
            'detalles.*.material_id' => 'required|exists:materials,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.001',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.incluye_igv' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar NEA
            $nea->update([
                'proveedor_id' => $request->proveedor_id,
                'fecha' => $request->fecha,
                'nro_documento' => $request->nro_documento,
                'tipo_comprobante_id' => $request->tipo_comprobante_id,
                'numero_comprobante' => $request->numero_comprobante,
                'observaciones' => $request->observaciones,
                'estado' => $request->estado,
                'usuario_actualizacion_id' => Auth::id()
            ]);

            // Obtener IDs de detalles que vienen en la request
            $detallesActuales = collect($request->detalles)
                ->pluck('id')
                ->filter()
                ->toArray();

            // Eliminar detalles que no están en la request (solo si no tienen salidas)
            $detallesAEliminar = NeaDetalle::where('nea_id', $nea->id)
                ->whereNotIn('id', $detallesActuales)
                ->pluck('id');

            if ($detallesAEliminar->count() > 0) {
                // Verificar que estos detalles no tengan salidas
                $tienenSalidas = Movimiento::where('tipo_movimiento', 'salida')
                    ->whereIn('nea_detalle_id', $detallesAEliminar)
                    ->where('estado', true)
                    ->exists();

                if ($tienenSalidas) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede eliminar detalles que tienen movimientos de salida activos.'
                    ], 422);
                }

                Movimiento::whereIn('nea_detalle_id', $detallesAEliminar)->delete();
                NeaDetalle::whereIn('id', $detallesAEliminar)->delete();
            }

            // Crear o actualizar detalles
            foreach ($request->detalles as $detalle) {
                if (isset($detalle['id']) && $detalle['id']) {
                    // Actualizar detalle existente
                    NeaDetalle::where('id', $detalle['id'])->update([
                        'material_id' => $detalle['material_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'incluye_igv' => $detalle['incluye_igv'],
                        'usuario_actualizacion_id' => Auth::id(),
                        'updated_at' => $this->nowPeru()
                    ]);
                } else {
                    // Crear nuevo detalle
                    $neaDetalle = NeaDetalle::create([
                        'nea_id' => $nea->id,
                        'material_id' => $detalle['material_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'incluye_igv' => $detalle['incluye_igv'],
                        'estado' => true,
                        'usuario_creacion_id' => Auth::id()
                    ]);

                    // Crear movimiento de entrada automáticamente para nuevo detalle
                    Movimiento::create([
                        'material_id' => $detalle['material_id'],
                        'tipo_movimiento' => 'entrada',
                        'nea_detalle_id' => $neaDetalle->id,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'incluye_igv' => $detalle['incluye_igv'],
                        'fecha' => $request->fecha,
                        'estado' => true,
                        'usuario_creacion_id' => Auth::id()
                    ]);
                }
            }

            DB::commit();

            $nea->load(['proveedor', 'tipoComprobante', 'detalles.material']);

            return response()->json([
                'success' => true,
                'data' => $nea,
                'message' => 'NEA actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar NEA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la NEA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar NEA (solo si no tiene documentos relacionados)
     */
    public function destroy($id)
    {
        $nea = Nea::findOrFail($id);

        try {
            DB::beginTransaction();

            // Obtener IDs de detalles para eliminar movimientos
            $detallesIds = NeaDetalle::where('nea_id', $nea->id)->pluck('id');
            
            // Eliminar movimientos asociados a los detalles
            Movimiento::whereIn('nea_detalle_id', $detallesIds)->delete();

            // Eliminar detalles
            NeaDetalle::where('nea_id', $nea->id)->delete();

            // Eliminar NEA
            $nea->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'NEA eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar NEA: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la NEA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista de materiales disponibles para select2
     */
    public function getMateriales(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

        $query = Material::where('estado', true)
            ->with('unidadMedida');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('codigo_material', 'LIKE', "%{$search}%");
            });
        }

        $materiales = $query->paginate(10, ['*'], 'page', $page);

        $results = $materiales->map(function($material) {
            $unidad = $material->unidadMedida ? $material->unidadMedida->nombre : 'N/A';
            return [
                'id' => $material->id,
                'text' => "[{$material->codigo_material}] {$material->nombre} ({$unidad})",
                'codigo_material' => $material->codigo_material,
                'nombre' => $material->nombre,
                'unidad_medida' => $unidad,
                'unidad_medida_id' => $material->unidad_medida_id
            ];
        });

        return response()->json([
            'data' => $results->toArray(),
            'current_page' => $materiales->currentPage(),
            'last_page' => $materiales->lastPage()
        ]);
    }

    /**
     * Obtener información de un material específico
     */
    public function getDetallesMaterial($materialId)
    {
        $material = Material::with('unidadMedida')->find($materialId);

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'codigo_material' => $material->codigo_material,
            'nombre' => $material->nombre,
            'precio_unitario' => $material->precio_unitario ?? 0,
            'unidad_medida' => $material->unidadMedida ? $material->unidadMedida->nombre : 'N/A',
            'descripcion' => $material->descripcion
        ]);
    }

    /**
     * Generar próximo número de NEA
     */
    public function proximoNumeroNea()
    {
        $anio = date('Y');
        $numeroNea = Nea::generarNumeroNea(now());

        return response()->json([
            'success' => true,
            'numero_nea' => $numeroNea
        ]);
    }

    /**
     * Select para NEAs activas
     */
    public function select()
    {
        $neas = Nea::where('estado', true)
            ->with('proveedor')
            ->get()
            ->map(function($nea) {
                return [
                    'id' => $nea->id,
                    'text' => "{$nea->nro_documento} - {$nea->proveedor->razon_social} ({$nea->fecha->format('d/m/Y')})"
                ];
            });

        return response()->json($neas);
    }

    /**
     * Anular una NEA completa
     * No elimina los datos, solo marca como anulada
     */
    public function anular(Request $request, $id)
    {
        $nea = Nea::findOrFail($id);

        // Validar que no esté ya anulada
        if ($nea->anulada) {
            return response()->json([
                'success' => false,
                'message' => 'La NEA ya está anulada'
            ], 422);
        }

        // Validar que no haya movimientos de salida activos
        $detallesIds = $nea->detalles()->pluck('id')->toArray();
        $tieneSalidas = Movimiento::where('tipo_movimiento', 'salida')
            ->whereIn('nea_detalle_id', $detallesIds)
            ->where('estado', true)
            ->exists();

        if ($tieneSalidas) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede anular esta NEA porque tiene movimientos de salida (PECOSA) activos asociados. Primero debe anular las PECOSAs relacionadas.'
            ], 422);
        }

        $request->validate([
            'motivo_anulacion' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Marcar NEA como anulada
            $nea->update([
                'anulada' => true,
                'motivo_anulacion' => $request->motivo_anulacion,
                'usuario_anulacion_id' => Auth::id(),
                'fecha_anulacion' => $this->nowPeru(),
                'estado' => false  // Inactivar también
            ]);

            // Revertir los movimientos de stock asociados a los detalles de esta NEA
            $detalles = $nea->detalles()->pluck('id');
            
            if ($detalles->count() > 0) {
                // Obtener todos los movimientos asociados a estos detalles
                $movimientos = Movimiento::whereIn('nea_detalle_id', $detalles)->get();
                
                // Crear movimientos inversos (revertir entrada)
                foreach ($movimientos as $movimiento) {
                    Movimiento::create([
                        'material_id' => $movimiento->material_id,
                        'tipo_movimiento' => $movimiento->tipo_movimiento === 'entrada' ? 'salida' : 'entrada',
                        'nea_detalle_id' => $movimiento->nea_detalle_id,
                        'cantidad' => $movimiento->cantidad,
                        'precio_unitario' => $movimiento->precio_unitario,
                        'incluye_igv' => $movimiento->incluye_igv,
                        'fecha' => $this->nowPeru()->toDateString(),
                        'observaciones' => "Reversión por anulación de NEA #{$nea->nro_documento}",
                        'estado' => true,
                        'usuario_creacion_id' => Auth::id()
                    ]);
                }
            }

            DB::commit();

            $nea->load(['proveedor', 'tipoComprobante', 'usuarioAnulacion', 'detalles.material']);

            return response()->json([
                'success' => true,
                'message' => 'NEA anulada correctamente',
                'data' => $nea
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al anular la NEA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF de impresión de NEA
     */
    public function imprimirPDF($id)
    {
        $nea = Nea::with([
            'proveedor',
            'tipoComprobante',
            'detalles.material.unidadMedida',
            'usuarioCreacion',
            'usuarioAnulacion'
        ])->findOrFail($id);

        // Renderizar la vista como HTML
        $html = view('admin.neas.pdf_impresion', compact('nea'))->render();

        // Usar DOMPDF para generar el PDF
        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper('A4');
        $pdf->setOption('dpi', 96);
        $pdf->setOption('defaultFont', 'Arial');
        $pdf->setOption('isRemoteEnabled', true);
        
        // Retornar con inline para mostrar en navegador
        return $pdf->stream('NEA-' . $nea->nro_documento . '.pdf');
    }

    /**
     * Preview en Modal - Retorna PDF en el navegador
     */
    public function previsualizarPDF($id)
    {
        $nea = Nea::with([
            'proveedor',
            'tipoComprobante',
            'detalles.material.unidadMedida',
            'usuarioCreacion',
            'usuarioAnulacion'
        ])->findOrFail($id);

        // Renderizar la vista como HTML
        $html = view('admin.neas.pdf_impresion', compact('nea'))->render();

        // Usar DOMPDF para generar el PDF
        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper('A4');
        $pdf->setOption('dpi', 96);
        $pdf->setOption('defaultFont', 'Arial');
        $pdf->setOption('isRemoteEnabled', true);
        
        // Retornar con stream para mostrar en navegador
        return $pdf->stream('NEA-' . $nea->nro_documento . '.pdf');
    }
}

