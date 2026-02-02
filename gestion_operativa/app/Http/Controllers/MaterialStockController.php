<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Material;
use App\Models\MaterialPecosaMovimiento;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class MaterialStockController extends Controller
{
    /**
     * Mostrar página de consulta de stock de materiales
     */
    public function index()
    {
        $cuadrillas = Cuadrilla::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.stock_materiales.index', [
            'catName' => 'stock',
            'title' => 'Consulta de Stock de Materiales',
            'breadcrumbs' => ['Stock', 'Materiales por Cuadrilla'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'cuadrillas' => $cuadrillas
        ]);
    }

    /**
     * Obtener datos de una cuadrilla específica con todos sus materiales
     */
    public function obtenerCuadrilla($id)
    {
        $cuadrilla = Cuadrilla::with([
            'empleados',
            'materialesPecosaMovimientos'
        ])->findOrFail($id);

        return response()->json([
            'cuadrilla' => $cuadrilla,
            'resumen' => [
                'total_empleados' => $cuadrilla->empleados()->count(),
                'total_materiales' => $cuadrilla->materialesPecosaMovimientos()->distinct('material_id')->count('material_id'),
                'fecha_inicio' => $cuadrilla->fecha_inicio?->format('d/m/Y'),
                'fecha_fin' => $cuadrilla->fecha_fin?->format('d/m/Y')
            ]
        ]);
    }

    /**
     * Obtener stock de materiales para DataTable de una cuadrilla
     * Muestra TODOS los materiales con sus movimientos (entradas y salidas)
     * Los materiales se obtienen desde las PECOSAS asignadas a la cuadrilla
     */
    public function getStockData(Request $request, $cuadrillaId)
    {
        $cuadrilla = Cuadrilla::findOrFail($cuadrillaId);

        // Query de TODOS los materiales con sus movimientos para esta cuadrilla
        // La relación es: Cuadrilla → Pecosa (via cuadrilla_empleado) → material_pecosa_movimientos
        $query = Material::with('categoria', 'unidadMedida')
            ->select(
                'materials.id',
                'materials.nombre',
                'materials.codigo_material',
                'materials.categoria_id',
                'materials.unidad_medida_id',
                'materials.stock_minimo',
                'materials.precio_unitario',
                'materials.created_at',
                'materials.updated_at'
            )
            ->selectRaw('
                COALESCE(SUM(CASE WHEN mpm.tipo_movimiento = "entrada" AND mpm.estado = 1 THEN mpm.cantidad ELSE 0 END), 0) as total_entradas,
                COALESCE(SUM(CASE WHEN mpm.tipo_movimiento = "salida" AND mpm.estado = 1 THEN mpm.cantidad ELSE 0 END), 0) as total_salidas
            ')
            ->leftJoin('material_pecosa_movimientos as mpm', function ($join) use ($cuadrillaId) {
                // Join via PECOSA: Cuadrilla → Pecosa → material_pecosa_movimientos
                $join->on('materials.id', '=', 'mpm.material_id')
                    ->whereExists(function ($q) use ($cuadrillaId) {
                        $q->selectRaw('1')
                            ->from('pecosas')
                            ->whereRaw('pecosas.id = mpm.pecosa_id')
                            ->whereExists(function ($q2) use ($cuadrillaId) {
                                $q2->selectRaw('1')
                                    ->from('cuadrillas_empleados')
                                    ->whereRaw('cuadrillas_empleados.id = pecosas.cuadrilla_empleado_id')
                                    ->where('cuadrillas_empleados.cuadrilla_id', $cuadrillaId);
                            });
                    })
                    ->where('mpm.estado', '=', true);
            })
            ->groupBy(
                'materials.id',
                'materials.nombre',
                'materials.codigo_material',
                'materials.categoria_id',
                'materials.unidad_medida_id',
                'materials.stock_minimo',
                'materials.precio_unitario',
                'materials.created_at',
                'materials.updated_at'
            )
            ->having('total_entradas', '>', 0)  // Solo materiales que fueron entregados a la cuadrilla
            ->orderBy('materials.nombre', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('codigo', function ($material) {
                return $material->codigo_material;
            })
            ->addColumn('categoria', function ($material) {
                return $material->categoria?->nombre ?? '-';
            })
            ->addColumn('unidad', function ($material) {
                return $material->unidadMedida?->nombre ?? '-';
            })
            ->addColumn('entradas_badge', function ($material) {
                return '<span class="badge bg-success">' . number_format($material->total_entradas, 2, ',', '.') . '</span>';
            })
            ->addColumn('salidas_badge', function ($material) {
                return '<span class="badge bg-danger">' . number_format($material->total_salidas, 2, ',', '.') . '</span>';
            })
            ->addColumn('stock_actual', function ($material) {
                $stock = $material->total_entradas - $material->total_salidas;
                $stockMinimo = $material->stock_minimo ?? 0;
                
                if ($stock <= 0) {
                    $badge = 'bg-dark';
                    $icon = '⚫';
                } elseif ($stock <= $stockMinimo) {
                    $badge = 'bg-danger';
                    $icon = '🔴';
                } elseif ($stock <= ($stockMinimo * 1.5)) {
                    $badge = 'bg-warning text-dark';
                    $icon = '🟡';
                } else {
                    $badge = 'bg-success';
                    $icon = '🟢';
                }
                
                return '<span class="badge ' . $badge . '">' . $icon . ' ' . number_format($stock, 2, ',', '.') . '</span>';
            })
            ->addColumn('stock_minimo_formateado', function ($material) {
                return number_format($material->stock_minimo ?? 0, 2, ',', '.');
            })
            ->addColumn('precio_unitario_formateado', function ($material) {
                return 'S/ ' . number_format($material->precio_unitario ?? 0, 2, ',', '.');
            })
            ->addColumn('valor_stock', function ($material) {
                $stock = $material->total_entradas - $material->total_salidas;
                $valor = $stock * ($material->precio_unitario ?? 0);
                return 'S/ ' . number_format($valor, 2, ',', '.');
            })
            ->addColumn('acciones', function ($material) use ($cuadrillaId) {
                return '<button class="btn btn-sm btn-outline-info" onclick="verMovimientos(' . $material->id . ', ' . $cuadrillaId . ', \'' . addslashes($material->nombre) . '\')">
                    <i class="fas fa-eye"></i> Ver
                </button>';
            })
            ->rawColumns(['entradas_badge', 'salidas_badge', 'stock_actual', 'acciones'])
            ->with('cuadrilla', [
                'id' => $cuadrilla->id,
                'nombre' => $cuadrilla->nombre
            ])
            ->make(true);
    }

    /**
     * Obtener movimientos detallados de un material en una cuadrilla
     */
    public function getMovimientos($materialId, $cuadrillaId)
    {
        $material = Material::findOrFail($materialId);
        $cuadrilla = Cuadrilla::findOrFail($cuadrillaId);

        // Obtener movimientos del material filtrando por PECOSAS de la cuadrilla
        $movimientos = MaterialPecosaMovimiento::with(['fichaActividad', 'usuarioCreacion', 'pecosa'])
            ->where('material_id', $materialId)
            ->where('estado', true)
            ->whereExists(function ($query) use ($cuadrillaId) {
                // Filtrar por PECOSAS que pertenecen a la cuadrilla
                $query->selectRaw('1')
                    ->from('pecosas')
                    ->whereRaw('pecosas.id = material_pecosa_movimientos.pecosa_id')
                    ->whereExists(function ($q) use ($cuadrillaId) {
                        $q->selectRaw('1')
                            ->from('cuadrillas_empleados')
                            ->whereRaw('cuadrillas_empleados.id = pecosas.cuadrilla_empleado_id')
                            ->where('cuadrillas_empleados.cuadrilla_id', $cuadrillaId);
                    });
            })
            ->orderBy('created_at', 'desc');

        return DataTables::of($movimientos)
            ->addIndexColumn()
            ->addColumn('tipo_badge', function ($mov) {
                $color = $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger';
                $texto = $mov->tipo_movimiento === 'entrada' ? '⬆️ ENTRADA' : '⬇️ SALIDA';
                return '<span class="badge bg-' . $color . '">' . $texto . '</span>';
            })
            ->addColumn('cantidad_formateada', function ($mov) {
                return number_format($mov->cantidad, 2, ',', '.');
            })
            ->addColumn('fecha_formateada', function ($mov) {
                return $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '-';
            })
            ->addColumn('ficha', function ($mov) {
                return $mov->fichaActividad?->numero_ficha ?? '-';
            })
            ->addColumn('usuario', function ($mov) {
                return $mov->usuarioCreacion?->nombre ?? 'Sistema';
            })
            ->rawColumns(['tipo_badge'])
            ->make(true);
    }

    /**
     * Exportar reporte de stock de materiales de una cuadrilla
     */
    public function exportarReporte($cuadrillaId)
    {
        $cuadrilla = Cuadrilla::with(['materialesPecosaMovimientos.material'])
            ->findOrFail($cuadrillaId);

        // Obtener materiales y sus movimientos - Solo los que fueron entregados a la cuadrilla
        $materiales = Material::with('categoria', 'unidadMedida')
            ->select(
                'materials.id',
                'materials.nombre',
                'materials.codigo_material',
                'materials.categoria_id',
                'materials.unidad_medida_id',
                'materials.stock_minimo',
                'materials.precio_unitario',
                'materials.created_at',
                'materials.updated_at'
            )
            ->selectRaw('
                COALESCE(SUM(CASE WHEN mpm.tipo_movimiento = "entrada" AND mpm.estado = 1 THEN mpm.cantidad ELSE 0 END), 0) as total_entradas,
                COALESCE(SUM(CASE WHEN mpm.tipo_movimiento = "salida" AND mpm.estado = 1 THEN mpm.cantidad ELSE 0 END), 0) as total_salidas
            ')
            ->leftJoin('material_pecosa_movimientos as mpm', function ($join) use ($cuadrillaId) {
                // Join via PECOSA
                $join->on('materials.id', '=', 'mpm.material_id')
                    ->whereExists(function ($q) use ($cuadrillaId) {
                        $q->selectRaw('1')
                            ->from('pecosas')
                            ->whereRaw('pecosas.id = mpm.pecosa_id')
                            ->whereExists(function ($q2) use ($cuadrillaId) {
                                $q2->selectRaw('1')
                                    ->from('cuadrillas_empleados')
                                    ->whereRaw('cuadrillas_empleados.id = pecosas.cuadrilla_empleado_id')
                                    ->where('cuadrillas_empleados.cuadrilla_id', $cuadrillaId);
                            });
                    })
                    ->where('mpm.estado', '=', true);
            })
            ->groupBy(
                'materials.id',
                'materials.nombre',
                'materials.codigo_material',
                'materials.categoria_id',
                'materials.unidad_medida_id',
                'materials.stock_minimo',
                'materials.precio_unitario',
                'materials.created_at',
                'materials.updated_at'
            )
            ->having('total_entradas', '>', 0)  // Solo materiales que fueron entregados
            ->orderBy('materials.nombre', 'asc')
            ->get();

        // Obtener movimientos entrada y salida
        $movimientosEntrada = MaterialPecosaMovimiento::with(['material', 'fichaActividad', 'usuarioCreacion'])
            ->where('tipo_movimiento', 'entrada')
            ->where('cuadrilla_id', $cuadrillaId)
            ->where('estado', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $movimientosSalida = MaterialPecosaMovimiento::with(['material', 'fichaActividad', 'usuarioCreacion'])
            ->where('tipo_movimiento', 'salida')
            ->where('cuadrilla_id', $cuadrillaId)
            ->where('estado', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('admin.stock_materiales.reporte', [
            'cuadrilla' => $cuadrilla,
            'materiales' => $materiales,
            'movimientosEntrada' => $movimientosEntrada,
            'movimientosSalida' => $movimientosSalida
        ])->render();

        return response()->json([
            'html' => $html,
            'filename' => 'STOCK_' . $cuadrilla->id . '_' . now()->format('Y-m-d-His') . '.pdf'
        ]);
    }

    /**
     * Exportar CSV
     */
    public function exportCsv(Request $request)
    {
        $cuadrillaId = $request->input('cuadrilla_id');

        if (!$cuadrillaId) {
            return response()->json(['success' => false, 'message' => 'Debe seleccionar una cuadrilla'], 400);
        }

        $cuadrilla = Cuadrilla::findOrFail($cuadrillaId);

        // Obtener datos - Solo materiales entregados a la cuadrilla
        $materiales = Material::with('categoria', 'unidadMedida')
            ->select(
                'materials.id',
                'materials.nombre',
                'materials.codigo_material',
                'materials.categoria_id',
                'materials.unidad_medida_id',
                'materials.stock_minimo',
                'materials.precio_unitario',
                'materials.created_at',
                'materials.updated_at'
            )
            ->selectRaw('
                COALESCE(SUM(CASE WHEN mpm.tipo_movimiento = "entrada" AND mpm.estado = 1 THEN mpm.cantidad ELSE 0 END), 0) as total_entradas,
                COALESCE(SUM(CASE WHEN mpm.tipo_movimiento = "salida" AND mpm.estado = 1 THEN mpm.cantidad ELSE 0 END), 0) as total_salidas
            ')
            ->leftJoin('material_pecosa_movimientos as mpm', function ($join) use ($cuadrillaId) {
                // Join via PECOSA
                $join->on('materials.id', '=', 'mpm.material_id')
                    ->whereExists(function ($q) use ($cuadrillaId) {
                        $q->selectRaw('1')
                            ->from('pecosas')
                            ->whereRaw('pecosas.id = mpm.pecosa_id')
                            ->whereExists(function ($q2) use ($cuadrillaId) {
                                $q2->selectRaw('1')
                                    ->from('cuadrillas_empleados')
                                    ->whereRaw('cuadrillas_empleados.id = pecosas.cuadrilla_empleado_id')
                                    ->where('cuadrillas_empleados.cuadrilla_id', $cuadrillaId);
                            });
                    })
                    ->where('mpm.estado', '=', true);
            })
            ->groupBy(
                'materials.id',
                'materials.nombre',
                'materials.codigo_material',
                'materials.categoria_id',
                'materials.unidad_medida_id',
                'materials.stock_minimo',
                'materials.precio_unitario',
                'materials.created_at',
                'materials.updated_at'
            )
            ->having('total_entradas', '>', 0)  // Solo materiales que fueron entregados
            ->orderBy('materials.nombre', 'asc')
            ->get();

        // Crear CSV
        $csv = "Cuadrilla: " . $cuadrilla->nombre . "\n";
        $csv .= "Fecha de Generación: " . now()->format('d/m/Y H:i:s') . "\n\n";
        $csv .= "Código,Material,Categoría,Unidad,Entradas,Salidas,Stock Actual,Stock Mínimo,Precio Unitario,Valor Stock\n";

        foreach ($materiales as $material) {
            $stockActual = $material->total_entradas - $material->total_salidas;
            $valor = $stockActual * ($material->precio_unitario ?? 0);
            
            $csv .= "\"" . $material->codigo_material . "\",";
            $csv .= "\"" . $material->nombre . "\",";
            $csv .= "\"" . ($material->categoria?->nombre ?? '') . "\",";
            $csv .= "\"" . ($material->unidadMedida?->nombre ?? '') . "\",";
            $csv .= $material->total_entradas . ",";
            $csv .= $material->total_salidas . ",";
            $csv .= $stockActual . ",";
            $csv .= ($material->stock_minimo ?? 0) . ",";
            $csv .= ($material->precio_unitario ?? 0) . ",";
            $csv .= $valor . "\n";
        }

        // Retornar descarga
        $filename = 'stock_materiales_' . $cuadrilla->id . '_' . now()->format('YmdHis') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }
}
