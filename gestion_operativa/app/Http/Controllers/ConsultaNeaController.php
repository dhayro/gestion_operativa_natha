<?php

namespace App\Http\Controllers;

use App\Models\Nea;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
     * Incluye movimientos de la tabla 'movimientos' y 'material_pecosa_movimientos'
     */
    public function getMovimientos(Request $request, $neaId)
    {
        $nea = Nea::findOrFail($neaId);
        $tipo = $request->get('tipo', 'todos'); // entrada, salida, todos

        $detallesIds = $nea->detalles()->pluck('id')->toArray();

        // 1. Movimientos de la tabla 'movimientos' (NEA directa)
        $query1 = DB::table('movimientos as m')
            ->join('materials as mat', 'm.material_id', '=', 'mat.id')
            ->join('users as u', 'm.usuario_creacion_id', '=', 'u.id', 'left')
            ->select(
                'm.id',
                'mat.id as material_id',
                'mat.nombre as material_nombre',
                'mat.codigo_material',
                'm.tipo_movimiento',
                'm.cantidad',
                'm.precio_unitario',
                'm.incluye_igv',
                'm.fecha',
                'm.usuario_creacion_id',
                DB::raw("u.name as usuario_name"),
                'm.nea_detalle_id as referencia_id',
                DB::raw("'movimientos' as origen"),
                'm.created_at'
            )
            ->where('m.estado', true);

        if ($tipo === 'entrada') {
            $query1->where('m.tipo_movimiento', 'entrada')
                ->whereIn('m.nea_detalle_id', $detallesIds);
        } elseif ($tipo === 'salida') {
            $query1->where('m.tipo_movimiento', 'salida')
                ->whereIn('m.nea_detalle_id', $detallesIds);
        } else {
            $query1->whereIn('m.nea_detalle_id', $detallesIds);
        }

        // 2. Movimientos de la tabla 'material_pecosa_movimientos' (a través de PECOSA)
        $query2 = DB::table('material_pecosa_movimientos as mpm')
            ->join('materials as mat', 'mpm.material_id', '=', 'mat.id')
            ->join('users as u', 'mpm.usuario_creacion_id', '=', 'u.id', 'left')
            ->join('pecosas as p', 'mpm.pecosa_id', '=', 'p.id')
            ->join('pecosa_detalles as pd', 'pd.pecosa_id', '=', 'p.id')
            ->join('nea_detalles as nd', 'nd.id', '=', 'pd.nea_detalle_id')
            ->select(
                'mpm.id',
                'mat.id as material_id',
                'mat.nombre as material_nombre',
                'mat.codigo_material',
                'mpm.tipo_movimiento',
                'mpm.cantidad',
                DB::raw('0 as precio_unitario'),
                DB::raw('0 as incluye_igv'),
                'mpm.created_at as fecha',
                'mpm.usuario_creacion_id',
                DB::raw("u.name as usuario_name"),
                'mpm.id as referencia_id',
                DB::raw("'material_pecosa_movimientos' as origen"),
                'mpm.created_at'
            )
            ->where('mpm.estado', true);

        if ($tipo === 'entrada') {
            $query2->where('mpm.tipo_movimiento', 'entrada');
        } elseif ($tipo === 'salida') {
            $query2->where('mpm.tipo_movimiento', 'salida');
        }

        // Filtrar por nea_detalles de esta NEA
        $query2->whereIn('nd.id', $detallesIds);

        // Combinar queries
        $query = $query1->unionAll($query2);
        
        // Envolver en subquery para ordenar
        $allMovimientos = DB::table(DB::raw("({$query->toSql()}) as mov"))
            ->mergeBindings($query)
            ->orderBy('created_at', 'desc');

        return DataTables::of($allMovimientos)
            ->addIndexColumn()
            ->addColumn('material_nombre', function ($mov) {
                return $mov->material_nombre ?? 'N/A';
            })
            ->addColumn('material_codigo', function ($mov) {
                return $mov->codigo_material ?? 'N/A';
            })
            ->addColumn('tipo_badge', function ($mov) {
                $color = $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger';
                $icono = $mov->tipo_movimiento === 'entrada' ? '⬆️' : '⬇️';
                $texto = $mov->tipo_movimiento === 'entrada' ? 'ENTRADA' : 'SALIDA';
                $origen = $mov->origen === 'movimientos' ? '(NEA)' : '(PECOSA)';
                return '<span class="badge bg-' . $color . '">' . $icono . ' ' . $texto . ' ' . $origen . '</span>';
            })
            ->addColumn('cantidad_formateada', function ($mov) {
                return number_format($mov->cantidad ?? 0, 3, ',', '.');
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
                return $mov->fecha ? (new \DateTime($mov->fecha))->format('d/m/Y') : '';
            })
            ->addColumn('usuario', function ($mov) {
                return $mov->usuario_name ?? 'Sistema';
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
            
            // Calcular salidas totales de este material en TODAS las formas:
            // 1. Salidas desde tabla 'movimientos' (NEA directa)
            $cantidadSalidaMovimientos = Movimiento::where('tipo_movimiento', 'salida')
                ->where('nea_detalle_id', $detalle->id) // Filtrar por THIS nea_detalle_id
                ->where('estado', true)
                ->sum('cantidad');
            
            // 2. Salidas desde tabla 'material_pecosa_movimientos' (a través de PECOSA)
            // Se obtienen las PECOSAS que vienen de esta NEA, luego sus salidas
            $cantidadSalidaPecosa = \DB::table('material_pecosa_movimientos as mpm')
                ->join('pecosas as p', 'mpm.pecosa_id', '=', 'p.id')
                ->join('pecosa_detalles as pd', function ($join) {
                    $join->on('pd.pecosa_id', '=', 'p.id');
                })
                ->join('nea_detalles as nd', function ($join) {
                    $join->on('nd.id', '=', 'pd.nea_detalle_id');
                })
                ->where('mpm.tipo_movimiento', 'salida')
                ->where('mpm.estado', true)
                ->where('nd.id', $detalle->id)  // Del mismo nea_detalle
                ->where('mpm.material_id', $material->id)  // Del mismo material
                ->sum('mpm.cantidad');
            
            // Total de salidas (movimientos + pecosa)
            $cantidadSalida = $cantidadSalidaMovimientos + $cantidadSalidaPecosa;
            
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

        // Movimientos entrada (NEA directa)
        $movimientosEntrada = Movimiento::where('tipo_movimiento', 'entrada')
            ->whereIn('nea_detalle_id', $detallesIds)
            ->with('material', 'usuarioCreacion')
            ->get();

        // Movimientos salida desde tabla 'movimientos' (NEA directa)
        $movimientosSalidaDirecta = Movimiento::where('tipo_movimiento', 'salida')
            ->whereIn('nea_detalle_id', $detallesIds)
            ->with('material', 'usuarioCreacion')
            ->get();

        // Movimientos salida desde tabla 'material_pecosa_movimientos' (a través de PECOSA)
        $movimientosSalidaPecosa = DB::table('material_pecosa_movimientos as mpm')
            ->join('materials as m', 'mpm.material_id', '=', 'm.id')
            ->join('pecosas as p', 'mpm.pecosa_id', '=', 'p.id')
            ->join('pecosa_detalles as pd', 'pd.pecosa_id', '=', 'p.id')
            ->join('nea_detalles as nd', 'nd.id', '=', 'pd.nea_detalle_id')
            ->join('users as u', 'mpm.usuario_creacion_id', '=', 'u.id', 'left')
            ->select('mpm.*', 'm.nombre as material_nombre', 'm.codigo_material', 'u.name as usuario_name', 'nd.precio_unitario', 'nd.incluye_igv', 'mpm.created_at as fecha_mov')
            ->where('mpm.tipo_movimiento', 'salida')
            ->where('mpm.estado', true)
            ->whereIn('nd.id', $detallesIds)
            ->get();

        // Combinar ambas salidas
        $movimientosSalida = collect()
            ->merge($movimientosSalidaDirecta->map(function ($m) {
                $m->origen = 'movimientos';
                return $m;
            }))
            ->merge($movimientosSalidaPecosa->map(function ($m) {
                $m->origen = 'material_pecosa_movimientos';
                // Crear objeto material compatible con la vista
                $m->material = (object) [
                    'nombre' => $m->material_nombre,
                    'codigo_material' => $m->codigo_material
                ];
                // Crear objeto usuarioCreacion compatible con la vista
                $m->usuarioCreacion = (object) [
                    'name' => $m->usuario_name
                ];
                // Convertir fecha a Carbon
                $m->fecha = Carbon::parse($m->fecha_mov);
                $m->created_at = $m->fecha_mov;
                return $m;
            }))
            ->sortByDesc('created_at');

        $pdf = Pdf::loadView('admin.consultas.neas.reporte', [
            'nea' => $nea,
            'movimientosEntrada' => $movimientosEntrada,
            'movimientosSalida' => $movimientosSalida
        ])->setPaper('a4', 'landscape')->setOption('margin-top', 10)->setOption('margin-bottom', 10)->setOption('margin-left', 10)->setOption('margin-right', 10);

        $filename = 'NEA_' . $nea->nro_documento . '_' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportarExcel($neaId)
    {
        $nea = Nea::with([
            'proveedor',
            'tipoComprobante',
            'detalles.material',
            'usuarioCreacion'
        ])->findOrFail($neaId);

        $detallesIds = $nea->detalles()->pluck('id')->toArray();

        $movimientosEntrada = Movimiento::where('tipo_movimiento', 'entrada')
            ->whereIn('nea_detalle_id', $detallesIds)
            ->with('material', 'usuarioCreacion')
            ->get();

        $movimientosSalidaDirecta = Movimiento::where('tipo_movimiento', 'salida')
            ->whereIn('nea_detalle_id', $detallesIds)
            ->with('material', 'usuarioCreacion')
            ->get();

        $movimientosSalidaPecosa = DB::table('material_pecosa_movimientos as mpm')
            ->join('materials as m', 'mpm.material_id', '=', 'm.id')
            ->join('pecosas as p', 'mpm.pecosa_id', '=', 'p.id')
            ->join('pecosa_detalles as pd', 'pd.pecosa_id', '=', 'p.id')
            ->join('nea_detalles as nd', 'nd.id', '=', 'pd.nea_detalle_id')
            ->join('users as u', 'mpm.usuario_creacion_id', '=', 'u.id', 'left')
            ->select('mpm.*', 'm.nombre as material_nombre', 'm.codigo_material', 'u.name as usuario_name', 'nd.precio_unitario', 'nd.incluye_igv', 'mpm.created_at as fecha_mov')
            ->where('mpm.tipo_movimiento', 'salida')
            ->where('mpm.estado', true)
            ->whereIn('nd.id', $detallesIds)
            ->get();

        $movimientosSalida = collect()
            ->merge($movimientosSalidaDirecta->map(function ($m) {
                $m->origen = 'movimientos';
                return $m;
            }))
            ->merge($movimientosSalidaPecosa->map(function ($m) {
                $m->origen = 'material_pecosa_movimientos';
                $m->material = (object) [
                    'nombre' => $m->material_nombre,
                    'codigo_material' => $m->codigo_material
                ];
                $m->usuarioCreacion = (object) [
                    'name' => $m->usuario_name
                ];
                $m->fecha = Carbon::parse($m->fecha_mov);
                $m->created_at = $m->fecha_mov;
                return $m;
            }))
            ->sortByDesc('created_at');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Título
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'REPORTE DE ENTRADA DE ALMACÉN (NEA) - ' . $nea->nro_documento);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Información general
        $row = 3;
        $sheet->setCellValue('A' . $row, 'NEA Nº:');
        $sheet->setCellValue('B' . $row, $nea->nro_documento);
        $sheet->setCellValue('D' . $row, 'Proveedor:');
        $sheet->setCellValue('E' . $row, $nea->proveedor->razon_social ?? 'N/A');

        $row++;
        $sheet->setCellValue('A' . $row, 'Fecha:');
        $sheet->setCellValue('B' . $row, $nea->fecha->format('d/m/Y'));
        $sheet->setCellValue('D' . $row, 'Usuario:');
        $sheet->setCellValue('E' . $row, $nea->usuarioCreacion->name ?? 'Sistema');

        // Movimientos de Entrada
        $row = 6;
        $sheet->setCellValue('A' . $row, 'MOVIMIENTOS DE ENTRADA');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11);

        $row++;
        $headers = ['Material', 'Código', 'Cantidad', 'Precio Unit.', 'Subtotal', 'IGV', 'Total', 'Fecha', 'Usuario'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');
            $col++;
        }

        $row++;
        $startRow = $row;
        foreach ($movimientosEntrada as $mov) {
            $subtotal = $mov->cantidad * $mov->precio_unitario;
            $igv = $mov->incluye_igv ? $subtotal * 0.18 : 0;

            $sheet->setCellValue('A' . $row, $mov->material->nombre ?? 'N/A');
            $sheet->setCellValue('B' . $row, $mov->material->codigo_material ?? 'N/A');
            $sheet->setCellValue('C' . $row, $mov->cantidad);
            $sheet->setCellValue('D' . $row, $mov->precio_unitario);
            $sheet->setCellValue('E' . $row, $subtotal);
            $sheet->setCellValue('F' . $row, $igv);
            $sheet->setCellValue('G' . $row, $subtotal + $igv);
            $sheet->setCellValue('H' . $row, $mov->fecha->format('d/m/Y'));
            $sheet->setCellValue('I' . $row, $mov->usuarioCreacion->name ?? 'Sistema');
            $row++;
        }

        // Movimientos de Salida
        $row += 2;
        $sheet->setCellValue('A' . $row, 'MOVIMIENTOS DE SALIDA');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(11);

        $row++;
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');
            $col++;
        }

        $row++;
        foreach ($movimientosSalida as $mov) {
            $subtotal = $mov->cantidad * $mov->precio_unitario;
            $igv = $mov->incluye_igv ? $subtotal * 0.18 : 0;

            $sheet->setCellValue('A' . $row, $mov->material->nombre ?? 'N/A');
            $sheet->setCellValue('B' . $row, $mov->material->codigo_material ?? 'N/A');
            $sheet->setCellValue('C' . $row, $mov->cantidad);
            $sheet->setCellValue('D' . $row, $mov->precio_unitario);
            $sheet->setCellValue('E' . $row, $subtotal);
            $sheet->setCellValue('F' . $row, $igv);
            $sheet->setCellValue('G' . $row, $subtotal + $igv);
            $sheet->setCellValue('H' . $row, $mov->fecha->format('d/m/Y'));
            $sheet->setCellValue('I' . $row, $mov->usuarioCreacion->name ?? 'Sistema');
            $row++;
        }

        // Ajustar ancho de columnas
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'NEA_' . $nea->nro_documento . '_' . now()->format('Y-m-d-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}
