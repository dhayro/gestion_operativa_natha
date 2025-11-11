<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pecosa;
use App\Models\Material;
use App\Models\MaterialPecosaMovimiento;
use App\Models\FichaActividad;
use Illuminate\Http\Request;
use Auth;

class PecosaController extends Controller
{
    /**
     * Listar materiales disponibles de una pecosa
     * GET /pecosa/{id}/materiales
     */
    public function getMaterialesPecosa($pecosaId)
    {
        try {
            $pecosa = Pecosa::with([
                'detalles.neaDetalle.material.unidadMedida',
                'detalles.neaDetalle.material.categoria'
            ])->findOrFail($pecosaId);

            // Obtener materiales desde pecosa_detalles
            $materiales = $pecosa->detalles
                ->map(function ($detalle) use ($pecosaId) {
                    $material = $detalle->neaDetalle->material;
                    
                    // Calcular saldo usando tabla 'material_pecosa_movimientos'
                    $entradas = \DB::table('material_pecosa_movimientos')
                        ->where('pecosa_id', $pecosaId)
                        ->where('material_id', $material->id)
                        ->where('tipo_movimiento', 'entrada')
                        ->sum('cantidad');
                    
                    $salidas = \DB::table('material_pecosa_movimientos')
                        ->where('pecosa_id', $pecosaId)
                        ->where('material_id', $material->id)
                        ->where('tipo_movimiento', 'salida')
                        ->sum('cantidad');
                    
                    $saldo = $entradas - $salidas;
                    
                    return [
                        'id' => $material->id,
                        'nombre' => $material->nombre,
                        'codigo_material' => $material->codigo_material,
                        'descripcion' => $material->descripcion,
                        'categoria_id' => $material->categoria_id,
                        'unidad_medida_id' => $material->unidad_medida_id,
                        'precio_unitario' => $material->precio_unitario,
                        'stock_minimo' => $material->stock_minimo,
                        'estado' => $material->estado,
                        'saldo' => $saldo,
                        'saldo_disponible' => $saldo > 0 ? $saldo : 0,
                        'unidad_medida' => $material->unidadMedida,
                        'categoria' => $material->categoria
                    ];
                })
                ->values()
                ->all();

            return response()->json([
                'success' => true,
                'data' => $materiales,
                'pecosa_nro' => $pecosa->nro_documento,
                'mensaje' => empty($materiales) ? 'Esta PECOSA no tiene materiales asignados.' : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener pecosas de una cuadrilla_empleado específica
     * GET /pecosa/cuadrilla/{cuadrillaEmpleadoId}/pecosas
     */
    public function getPecosasPorCuadrilla($cuadrillaEmpleadoId)
    {
        try {
            // Buscar pecosas ACTIVAS primero
            $pecosas = Pecosa::where('cuadrilla_empleado_id', $cuadrillaEmpleadoId)
                ->where('estado', true)
                ->with(['cuadrillaEmpleado.cuadrilla', 'cuadrillaEmpleado.empleado'])
                ->orderBy('nro_documento', 'desc')
                ->get();

            // Si no hay activas, buscar TODAS (incluso inactivas)
            if ($pecosas->isEmpty()) {
                $pecosas = Pecosa::where('cuadrilla_empleado_id', $cuadrillaEmpleadoId)
                    ->with(['cuadrillaEmpleado.cuadrilla', 'cuadrillaEmpleado.empleado'])
                    ->orderBy('estado', 'desc')
                    ->orderBy('nro_documento', 'desc')
                    ->get();
            }

            $result = $pecosas->map(function ($pecosa) {
                return [
                    'id' => $pecosa->id,
                    'nro_documento' => $pecosa->nro_documento,
                    'fecha' => $pecosa->fecha->format('d/m/Y'),
                    'estado' => $pecosa->estado ? 'activa' : 'inactiva',
                    'cuadrilla' => $pecosa->cuadrillaEmpleado?->cuadrilla?->nombre,
                    'empleado' => $pecosa->cuadrillaEmpleado?->empleado?->nombre
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $result,
                'cuadrilla_empleado_id' => $cuadrillaEmpleadoId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener TODAS las pecosas disponibles (sin filtrar por cuadrilla)
     * GET /pecosas/todas
     */
    public function getPecosasDisponibles()
    {
        try {
            // Buscar pecosas ACTIVAS
            $pecosas = Pecosa::where('estado', true)
                ->with(['cuadrillaEmpleado.cuadrilla', 'cuadrillaEmpleado.empleado'])
                ->orderBy('nro_documento', 'desc')
                ->get();

            // Si no hay activas, retornar inactivas también
            if ($pecosas->isEmpty()) {
                $pecosas = Pecosa::with(['cuadrillaEmpleado.cuadrilla', 'cuadrillaEmpleado.empleado'])
                    ->orderBy('estado', 'desc')
                    ->orderBy('nro_documento', 'desc')
                    ->get();
            }

            $result = $pecosas->map(function ($pecosa) {
                return [
                    'id' => $pecosa->id,
                    'nro_documento' => $pecosa->nro_documento,
                    'fecha' => $pecosa->fecha->format('d/m/Y'),
                    'estado' => $pecosa->estado ? 'activa' : 'inactiva',
                    'cuadrilla_empleado_id' => $pecosa->cuadrilla_empleado_id,
                    'cuadrilla' => $pecosa->cuadrillaEmpleado?->cuadrilla?->nombre,
                    'empleado' => $pecosa->cuadrillaEmpleado?->empleado?->nombre
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalles de saldo de un material en una pecosa
     * GET /pecosa/{pecosaId}/material/{materialId}/saldo
     */
    public function getSaldoMaterial($pecosaId, $materialId)
    {
        try {
            $saldo = MaterialPecosaMovimiento::getSaldoMaterial($pecosaId, $materialId);

            $entradas = MaterialPecosaMovimiento::porPecosa($pecosaId)
                ->porMaterial($materialId)
                ->entradas()
                ->sum('cantidad');

            $salidas = MaterialPecosaMovimiento::porPecosa($pecosaId)
                ->porMaterial($materialId)
                ->salidas()
                ->sum('cantidad');

            return response()->json([
                'success' => true,
                'data' => [
                    'saldo' => $saldo,
                    'entradas' => $entradas,
                    'salidas' => $salidas,
                    'disponible' => max(0, $saldo)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar salida de material de pecosa a ficha
     * POST /pecosa/registrar-salida
     */
    public function registrarSalida(Request $request)
    {
        try {
            $request->validate([
                'pecosa_id' => 'required|exists:pecosas,id',
                'material_id' => 'required|exists:materials,id',
                'cantidad' => 'required|numeric|min:0.01',
                'ficha_actividad_id' => 'required|exists:ficha_actividads,id',
                'observaciones' => 'nullable|string'
            ]);

            // Obtener el PECOSA y calcular saldo
            $pecosa = Pecosa::findOrFail($request->pecosa_id);
            
            // Calcular saldo desde material_pecosa_movimientos
            $entradas = \DB::table('material_pecosa_movimientos')
                ->where('pecosa_id', $request->pecosa_id)
                ->where('material_id', $request->material_id)
                ->where('tipo_movimiento', 'entrada')
                ->sum('cantidad');
            
            $salidas = \DB::table('material_pecosa_movimientos')
                ->where('pecosa_id', $request->pecosa_id)
                ->where('material_id', $request->material_id)
                ->where('tipo_movimiento', 'salida')
                ->sum('cantidad');
            
            $saldoDisponible = $entradas - $salidas;

            if ($saldoDisponible < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => "Saldo insuficiente. Disponible: {$saldoDisponible}, Solicitado: {$request->cantidad}"
                ], 422);
            }

            // Registrar la salida en material_pecosa_movimientos
            $movimiento = \App\Models\MaterialPecosaMovimiento::create([
                'pecosa_id' => $request->pecosa_id,
                'material_id' => $request->material_id,
                'cantidad' => $request->cantidad,
                'tipo_movimiento' => 'salida',
                'ficha_actividad_id' => $request->ficha_actividad_id,
                'observaciones' => $request->observaciones,
                'estado' => true,
                'usuario_creacion_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Material registrado como salida correctamente',
                'data' => $movimiento
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Registrar entrada de material a pecosa
     * POST /pecosa/registrar-entrada
     */
    public function registrarEntrada(Request $request)
    {
        try {
            $request->validate([
                'pecosa_id' => 'required|exists:pecosas,id',
                'material_id' => 'required|exists:materials,id',
                'cantidad' => 'required|numeric|min:0.01',
                'observaciones' => 'nullable|string'
            ]);

            $movimiento = MaterialPecosaMovimiento::registrarEntrada(
                $request->pecosa_id,
                $request->material_id,
                $request->cantidad,
                Auth::id(),
                $request->observaciones
            );

            return response()->json([
                'success' => true,
                'message' => 'Material registrado como entrada',
                'data' => $movimiento->load(['material', 'pecosa'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Obtener historial de movimientos de una pecosa
     * GET /pecosa/{id}/historial
     */
    public function getHistorial($pecosaId)
    {
        try {
            $pecosa = Pecosa::with([
                'materialMovimientos.material',
                'materialMovimientos.fichaActividad',
                'materialMovimientos.usuarioCreacion'
            ])->findOrFail($pecosaId);

            $movimientos = $pecosa->materialMovimientos()
                ->with(['material', 'fichaActividad', 'usuarioCreacion'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($mov) {
                    return [
                        'id' => $mov->id,
                        'material' => $mov->material->nombre,
                        'cantidad' => $mov->cantidad,
                        'tipo' => $mov->tipo_movimiento,
                        'tipo_texto' => ucfirst($mov->tipo_movimiento),
                        'ficha' => $mov->fichaActividad?->id,
                        'usuario' => $mov->usuarioCreacion?->name,
                        'observaciones' => $mov->observaciones,
                        'fecha' => $mov->created_at->format('d/m/Y H:i'),
                        'fecha_raw' => $mov->created_at
                    ];
                });

            // Calcular inventario actual
            $inventario = [];
            $pecosa->materialMovimientos()
                ->with('material')
                ->get()
                ->groupBy('material_id')
                ->each(function ($movs, $materialId) use (&$inventario) {
                    $material = $movs->first()->material;
                    $saldo = MaterialPecosaMovimiento::getSaldoMaterial($movs->first()->pecosa_id, $materialId);
                    if ($saldo > 0) {
                        $inventario[] = [
                            'material_id' => $materialId,
                            'material_nombre' => $material->nombre,
                            'unidad' => $material->unidadMedida->simbolo ?? 'un',
                            'saldo' => $saldo
                        ];
                    }
                });

            return response()->json([
                'success' => true,
                'pecosa' => [
                    'id' => $pecosa->id,
                    'nro_documento' => $pecosa->nro_documento,
                    'fecha' => $pecosa->fecha->format('d/m/Y'),
                    'cuadrilla' => $pecosa->cuadrillaEmpleado?->cuadrilla?->nombre
                ],
                'movimientos' => $movimientos,
                'inventario' => $inventario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver historial de una pecosa (vista)
     * GET /pecosa/{id}/ver-historial
     */
    public function verHistorial($pecosaId)
    {
        try {
            $pecosa = Pecosa::findOrFail($pecosaId);
            return view('admin.pecosa.historial', ['pecosaId' => $pecosaId]);
        } catch (\Exception $e) {
            abort(404, 'Pecosa no encontrada');
        }
    }

    /**
     * Obtener movimientos de una ficha (qué materiales se sacaron)
     * GET /pecosa/ficha/{fichaId}/movimientos
     */
    public function getMovimientosFicha($fichaId)
    {
        try {
            $ficha = FichaActividad::findOrFail($fichaId);

            $movimientos = MaterialPecosaMovimiento::getMovimientosFicha($fichaId);

            return response()->json([
                'success' => true,
                'ficha' => [
                    'id' => $ficha->id,
                    'suministro' => $ficha->suministro?->nombre,
                    'tipo_actividad' => $ficha->tipoActividad?->nombre,
                    'fecha' => $ficha->fecha->format('d/m/Y')
                ],
                'movimientos' => $movimientos->map(function ($mov) {
                    return [
                        'id' => $mov->id,
                        'material' => $mov->material->nombre,
                        'cantidad' => $mov->cantidad,
                        'unidad' => $mov->material->unidadMedida->simbolo ?? 'un',
                        'pecosa' => $mov->pecosa->nro_documento,
                        'usuario' => $mov->usuarioCreacion?->name,
                        'fecha' => $mov->created_at->format('d/m/Y H:i')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
