<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Movimiento;
use App\Models\NeaDetalle;
use App\Models\PecosaDetalle;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Mostrar página principal de consulta de stock
     */
    public function index()
    {
        $categorias = Material::select('categoria_id')
            ->distinct()
            ->with('categoria')
            ->get()
            ->pluck('categoria.nombre', 'categoria_id')
            ->toArray();

        return view('admin.stock.index', [
            'catName' => 'stock',
            'title' => 'Consulta de Stock en Almacén',
            'breadcrumbs' => ['Almacén', 'Stock'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'categorias' => $categorias
        ]);
    }

    /**
     * Obtener datos de stock para DataTable
     */
    public function getData(Request $request)
    {
        $query = Material::query()
            ->select([
                'materials.id',
                'materials.codigo_material',
                'materials.nombre',
                'categorias.nombre as categoria_nombre',
                'unidad_medidas.nombre as unidad_nombre',
                DB::raw('COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "entrada" AND estado = true), 0) as total_entrada'),
                DB::raw('COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "salida" AND estado = true), 0) as total_salida'),
                DB::raw('COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "entrada" AND estado = true), 0) - COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "salida" AND estado = true), 0) as stock_actual'),
                'materials.precio_unitario'
            ])
            ->leftJoin('categorias', 'materials.categoria_id', '=', 'categorias.id')
            ->leftJoin('unidad_medidas', 'materials.unidad_medida_id', '=', 'unidad_medidas.id')
            ->where('materials.estado', true);

        // Filtro por categoría
        if ($request->has('categoria_id') && $request->categoria_id) {
            $query->where('materials.categoria_id', $request->categoria_id);
        }

        // Filtro por búsqueda
        if ($request->has('search') && $request->search) {
            $search = $request->search['value'] ?? '';
            $query->where(function ($q) use ($search) {
                $q->where('materials.codigo_material', 'like', "%{$search}%")
                  ->orWhere('materials.nombre', 'like', "%{$search}%")
                  ->orWhere('categorias.nombre', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('stock_color', function ($row) {
                $stock = $row->stock_actual;
                if ($stock == 0) {
                    return '<span class="badge bg-danger">Sin Stock</span>';
                } elseif ($stock < 10) {
                    return '<span class="badge bg-warning">Bajo Stock</span>';
                } else {
                    return '<span class="badge bg-success">Disponible</span>';
                }
            })
            ->addColumn('valor_inventario', function ($row) {
                $valor = $row->stock_actual * $row->precio_unitario;
                return 'S/ ' . number_format($valor, 2, '.', ',');
            })
            ->addColumn('total_entrada_fmt', function ($row) {
                return number_format($row->total_entrada, 3, '.', ',');
            })
            ->addColumn('total_salida_fmt', function ($row) {
                return number_format($row->total_salida, 3, '.', ',');
            })
            ->addColumn('stock_actual_fmt', function ($row) {
                return number_format($row->stock_actual, 3, '.', ',');
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-info" onclick="verMovimientos(' . $row->id . ')" title="Ver movimientos">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>';
            })
            ->rawColumns(['stock_color', 'action'])
            ->make(true);
    }

    /**
     * Obtener movimientos de un material
     */
    public function getMovimientos($materialId)
    {
        $movimientos = Movimiento::query()
            ->select([
                'movimientos.id',
                'movimientos.tipo_movimiento',
                'movimientos.cantidad',
                'movimientos.precio_unitario',
                'movimientos.incluye_igv',
                'movimientos.fecha',
                'movimientos.created_at',
                DB::raw('CASE 
                    WHEN nea_detalle_id IS NOT NULL THEN (SELECT CONCAT("NEA: ", neas.nro_documento) FROM nea_detalles JOIN neas ON nea_detalles.nea_id = neas.id WHERE nea_detalles.id = movimientos.nea_detalle_id)
                    WHEN pecosa_detalle_id IS NOT NULL THEN (SELECT CONCAT("PECOSA: ", pecosas.nro_documento) FROM pecosa_detalles JOIN pecosas ON pecosa_detalles.pecosa_id = pecosas.id WHERE pecosa_detalles.id = movimientos.pecosa_detalle_id)
                    ELSE "Manual"
                END as referencia'),
                'usuarios_creacion.name as usuario_nombre'
            ])
            ->leftJoin('users as usuarios_creacion', 'movimientos.usuario_creacion_id', '=', 'usuarios_creacion.id')
            ->where('movimientos.material_id', $materialId)
            ->where('movimientos.estado', true)
            ->orderByDesc('movimientos.fecha')
            ->orderByDesc('movimientos.id')
            ->get()
            ->map(function ($movimiento) {
                return [
                    'id' => $movimiento->id,
                    'tipo' => $movimiento->tipo_movimiento === 'entrada' ? 'ENTRADA' : 'SALIDA',
                    'tipo_color' => $movimiento->tipo_movimiento === 'entrada' ? 'success' : 'danger',
                    'cantidad' => number_format($movimiento->cantidad, 3, '.', ','),
                    'precio' => $movimiento->precio_unitario ? 'S/ ' . number_format($movimiento->precio_unitario, 2, '.', ',') : 'N/A',
                    'igv' => $movimiento->incluye_igv ? 'Sí' : 'No',
                    'fecha' => $movimiento->fecha,
                    'referencia' => $movimiento->referencia,
                    'usuario' => $movimiento->usuario_nombre
                ];
            });

        return response()->json($movimientos);
    }

    /**
     * Obtener resumen de stock por categoría
     */
    public function getResumenPorCategoria()
    {
        $resumen = DB::table('materials')
            ->select([
                'categorias.nombre as categoria',
                DB::raw('COUNT(materials.id) as cantidad_materiales'),
                DB::raw('SUM(COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "entrada" AND estado = true), 0)) as total_entrada'),
                DB::raw('SUM(COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "salida" AND estado = true), 0)) as total_salida'),
                DB::raw('SUM(COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "entrada" AND estado = true), 0) - COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "salida" AND estado = true), 0)) as stock_total')
            ])
            ->leftJoin('categorias', 'materials.categoria_id', '=', 'categorias.id')
            ->where('materials.estado', true)
            ->groupBy('materials.categoria_id', 'categorias.nombre')
            ->get();

        return response()->json($resumen);
    }

    /**
     * Exportar reporte de stock a Excel
     */
    public function exportarStock()
    {
        // Aquí se puede implementar export a Excel con Laravel Excel si lo necesitas
        $stock = Material::query()
            ->select([
                'materials.id',
                'materials.codigo_material',
                'materials.nombre',
                'categorias.nombre as categoria_nombre',
                'unidad_medidas.nombre as unidad_nombre',
                DB::raw('COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "entrada" AND estado = true), 0) as total_entrada'),
                DB::raw('COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "salida" AND estado = true), 0) as total_salida'),
                DB::raw('COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "entrada" AND estado = true), 0) - COALESCE((SELECT SUM(cantidad) FROM movimientos WHERE material_id = materials.id AND tipo_movimiento = "salida" AND estado = true), 0) as stock_actual'),
                'materials.precio_unitario'
            ])
            ->leftJoin('categorias', 'materials.categoria_id', '=', 'categorias.id')
            ->leftJoin('unidad_medidas', 'materials.unidad_medida_id', '=', 'unidad_medidas.id')
            ->where('materials.estado', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stock,
            'message' => 'Reporte generado exitosamente'
        ]);
    }
}
