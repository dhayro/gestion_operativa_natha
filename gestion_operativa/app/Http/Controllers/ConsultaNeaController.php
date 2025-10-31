<?php

namespace App\Http\Controllers;

use App\Models\Nea;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ConsultaNeaController extends Controller
{
    /**
     * Mostrar página de consulta de NEAs
     */
    public function index()
    {
        $neas = Nea::with(['proveedor', 'tipoComprobante', 'detalles'])
            ->where('estado', true)
            ->latest('fecha')
            ->get();

        return view('admin.consultas.neas.index', [
            'catName' => 'consultas',
            'title' => 'Consulta de NEAs y Movimientos',
            'breadcrumbs' => ['Consultas', 'NEAs y Movimientos'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'neas' => $neas
        ]);
    }

    /**
     * Obtener datos de una NEA específica con todos sus movimientos
     */
    public function obtenerNea($id)
    {
        $nea = Nea::with([
            'proveedor',
            'tipoComprobante',
            'detalles.material.unidadMedida',
            'usuarioCreacion'
        ])->findOrFail($id);

        return response()->json([
            'nea' => $nea,
            'resumen' => [
                'total_sin_igv' => $nea->total_sin_igv,
                'igv_total' => $nea->igv_total,
                'total_con_igv' => $nea->total_con_igv,
                'cantidad_detalles' => $nea->detalles()->count()
            ]
        ]);
    }

    /**
     * Obtener movimientos para DataTable de una NEA
     */
    public function getMovimientos(Request $request, $neaId)
    {
        $nea = Nea::findOrFail($neaId);
        $tipo = $request->get('tipo', 'todos'); // entrada, salida, todos

        $detallesIds = $nea->detalles()->pluck('id')->toArray();

        $query = Movimiento::with('material', 'usuarioCreacion')
            ->where('estado', true); // Solo movimientos activos

        if ($tipo === 'entrada') {
            // Solo movimientos de entrada de NEA - filtrar por nea_detalle_id
            $query->where('tipo_movimiento', 'entrada')
                ->whereIn('nea_detalle_id', $detallesIds);
        } elseif ($tipo === 'salida') {
            // Solo movimientos de salida - filtrar por nea_detalle_id para trazabilidad
            $query->where('tipo_movimiento', 'salida')
                ->whereIn('nea_detalle_id', $detallesIds);
        } else {
            // Todos los movimientos (entrada y salida) relacionados a esta NEA
            // Usar nea_detalle_id para trazabilidad correcta
            $query->whereIn('nea_detalle_id', $detallesIds);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('material_nombre', function ($mov) {
                return $mov->material ? $mov->material->nombre : 'N/A';
            })
            ->addColumn('material_codigo', function ($mov) {
                return $mov->material ? $mov->material->codigo_material : 'N/A';
            })
            ->addColumn('tipo_badge', function ($mov) {
                $color = $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger';
                $texto = $mov->tipo_movimiento === 'entrada' ? 'ENTRADA' : 'SALIDA';
                return '<span class="badge bg-' . $color . '">' . $texto . '</span>';
            })
            ->addColumn('cantidad_formateada', function ($mov) {
                return number_format($mov->cantidad, 3, ',', '.');
            })
            ->addColumn('precio_formateado', function ($mov) {
                return 'S/ ' . number_format($mov->precio_unitario ?? 0, 2, ',', '.');
            })
            ->addColumn('subtotal', function ($mov) {
                $subtotal = ($mov->cantidad ?? 0) * ($mov->precio_unitario ?? 0);
                return 'S/ ' . number_format($subtotal, 2, ',', '.');
            })
            ->addColumn('igv_badge', function ($mov) {
                return $mov->incluye_igv ? '<span class="badge bg-info">Sí</span>' : '<span class="badge bg-secondary">No</span>';
            })
            ->addColumn('fecha_formateada', function ($mov) {
                return $mov->fecha ? $mov->fecha->format('d/m/Y') : '';
            })
            ->addColumn('usuario', function ($mov) {
                return $mov->usuarioCreacion ? $mov->usuarioCreacion->name : 'Sistema';
            })
            ->rawColumns(['tipo_badge', 'igv_badge'])
            ->make(true);
    }

    /**
     * Obtener resumen de stock por material en una NEA
     */
    public function getResumenStock($neaId)
    {
        $nea = Nea::with('detalles.material.unidadMedida')->findOrFail($neaId);

        $resumen = [];
        $detallesIds = $nea->detalles()->pluck('id')->toArray();
        
        foreach ($nea->detalles as $detalle) {
            $material = $detalle->material;
            
            // Calcular entrada total del material en esta NEA (solo este detalle)
            $cantidadEntrada = $detalle->cantidad;
            
            // Calcular salidas totales de este material EN ESTA NEA (filtrando por nea_detalle_id)
            $cantidadSalida = Movimiento::where('tipo_movimiento', 'salida')
                ->where('nea_detalle_id', $detalle->id) // Filtrar por THIS nea_detalle_id
                ->where('estado', true)
                ->sum('cantidad');
            
            // Stock disponible
            $stockDisponible = $cantidadEntrada - $cantidadSalida;
            
            // Obtener unidad de medida
            $unidadMedida = '-';
            if ($material->unidadMedida) {
                $unidadMedida = $material->unidadMedida->abreviatura ?? $material->unidadMedida->nombre;
            }
            
            $resumen[] = [
                'material_id' => $material->id,
                'codigo' => $material->codigo_material,
                'nombre' => $material->nombre,
                'unidad_medida' => $unidadMedida,
                'cantidad_entrada' => $cantidadEntrada,
                'cantidad_salida' => $cantidadSalida,
                'stock_disponible' => $stockDisponible,
                'precio_unitario' => $detalle->precio_unitario,
                'incluye_igv' => $detalle->incluye_igv
            ];
        }

        return response()->json([
            'nea' => $nea,
            'resumen' => $resumen
        ]);
    }

    /**
     * Exportar reporte de NEA con movimientos
     */
    public function exportarReporte($neaId)
    {
        $nea = Nea::with([
            'proveedor',
            'tipoComprobante',
            'detalles.material',
            'usuarioCreacion'
        ])->findOrFail($neaId);

        $detallesIds = $nea->detalles()->pluck('id')->toArray();
        $materialesIds = $nea->detalles()->pluck('material_id')->toArray();

        // Movimientos entrada
        $movimientosEntrada = Movimiento::where('tipo_movimiento', 'entrada')
            ->whereIn('nea_detalle_id', $detallesIds)
            ->with('material', 'usuarioCreacion')
            ->get();

        // Movimientos salida
        $movimientosSalida = Movimiento::where('tipo_movimiento', 'salida')
            ->whereIn('material_id', $materialesIds)
            ->with('material', 'usuarioCreacion')
            ->get();

        $html = view('admin.consultas.neas.reporte', [
            'nea' => $nea,
            'movimientosEntrada' => $movimientosEntrada,
            'movimientosSalida' => $movimientosSalida
        ])->render();

        return response()->json([
            'html' => $html,
            'filename' => 'NEA_' . $nea->nro_documento . '_' . now()->format('Y-m-d-His') . '.pdf'
        ]);
    }
}
