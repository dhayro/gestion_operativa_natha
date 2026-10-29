<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Empleado;
use App\Models\FichaActividad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteActividadController extends Controller
{
    /**
     * Reporte de actividades (fichas de actividad) realizadas en un rango de fechas.
     * Se puede filtrar por cuadrilla o por empleado; sin filtros muestra el general.
     */
    public function index(Request $request)
    {
        $this->verificarAcceso();

        $filtros = $this->validarFiltros($request);
        $fichas = $this->consultarFichas($filtros);

        $fichas->each(function ($ficha) {
            $ficha->setAttribute('cuadrilla_nombre', $this->nombreCuadrilla($ficha));
            $ficha->setAttribute('participantes_nombres', $this->participantes($ficha)->implode(', '));
        });

        return view('admin.consultas.reporte_actividades.index', [
            'catName' => 'consultas',
            'title' => 'Reporte de Actividades Realizadas',
            'breadcrumbs' => ['Consultas', 'Reporte de Actividades'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'filtros' => $filtros,
            'cuadrillas' => Cuadrilla::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']),
            'empleados' => Empleado::where('estado', true)->orderBy('nombre')->get(['id', 'nombre', 'apellido']),
            'totales' => $this->resumenDe($fichas),
            'resumenCuadrillas' => $this->resumenPorCuadrilla($fichas),
            'resumenTipos' => $fichas
                ->groupBy(fn ($ficha) => $ficha->tipoActividad?->nombre ?? 'Sin tipo')
                ->map(fn ($grupo) => $grupo->count())
                ->sortDesc(),
            'fichas' => $fichas,
        ]);
    }

    /**
     * Exporta el mismo reporte a Excel: hoja "Resumen" y hoja "Detalle".
     */
    public function exportar(Request $request)
    {
        $this->verificarAcceso();

        $filtros = $this->validarFiltros($request);
        $fichas = $this->consultarFichas($filtros);

        $spreadsheet = new Spreadsheet();
        $this->llenarHojaResumen($spreadsheet->getActiveSheet(), $fichas);
        $this->llenarHojaDetalle($spreadsheet->createSheet(), $fichas);
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'reporte_actividades_' . $filtros['fecha_desde'] . '_' . $filtros['fecha_hasta'] . '.xlsx';
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

    /**
     * Solo usuarios con el permiso de consultas (mismo criterio que el menú).
     */
    private function verificarAcceso()
    {
        abort_unless(auth()->user()?->hasPermission('ver_consultas'), 403);
    }

    private function validarFiltros(Request $request): array
    {
        $validados = $request->validate([
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date',
            'cuadrilla_id' => 'nullable|exists:cuadrillas,id',
            'empleado_id' => 'nullable|exists:empleados,id',
            'vista' => 'nullable|in:resumen,detalle',
        ]);

        $desde = $validados['fecha_desde'] ?? now()->startOfMonth()->toDateString();
        $hasta = $validados['fecha_hasta'] ?? now()->toDateString();

        if (Carbon::parse($hasta)->lt(Carbon::parse($desde))) {
            throw ValidationException::withMessages([
                'fecha_hasta' => 'La fecha final no puede ser menor que la fecha inicial.',
            ]);
        }

        return [
            'fecha_desde' => $desde,
            'fecha_hasta' => $hasta,
            'cuadrilla_id' => $validados['cuadrilla_id'] ?? null,
            'empleado_id' => $validados['empleado_id'] ?? null,
            'vista' => $validados['vista'] ?? 'resumen',
        ];
    }

    /**
     * Fichas del rango con sus relaciones.
     *
     * La cuadrilla de una ficha se toma de sus participantes o, si no hay,
     * de la PECOSA. Por eso el filtro por cuadrilla revisa ambas vías.
     */
    private function consultarFichas(array $filtros): Collection
    {
        $desde = Carbon::parse($filtros['fecha_desde'])->startOfDay();
        $hasta = Carbon::parse($filtros['fecha_hasta'])->endOfDay();

        return FichaActividad::with([
                'tipoActividad',
                'suministro',
                'pecosa.cuadrillaEmpleado.cuadrilla',
                'fichaEmpleados.cuadrillaEmpleado.cuadrilla',
                'fichaEmpleados.cuadrillaEmpleado.empleado',
                'medidorFichas',
                'materialFichas',
                'precintos',
                'fotos',
                'usuarioCreacion',
            ])
            ->where('estado', true)
            ->whereBetween('fecha', [$desde, $hasta])
            ->when($filtros['cuadrilla_id'], function ($query, $cuadrillaId) {
                $query->where(function ($sub) use ($cuadrillaId) {
                    $sub->whereHas('fichaEmpleados.cuadrillaEmpleado', fn ($c) => $c->where('cuadrilla_id', $cuadrillaId))
                        ->orWhereHas('pecosa.cuadrillaEmpleado', fn ($c) => $c->where('cuadrilla_id', $cuadrillaId));
                });
            })
            ->when($filtros['empleado_id'], function ($query, $empleadoId) {
                $query->whereHas('fichaEmpleados.cuadrillaEmpleado', fn ($c) => $c->where('empleado_id', $empleadoId));
            })
            ->orderBy('fecha')
            ->get();
    }

    private function cuadrillaDeFicha(FichaActividad $ficha): ?Cuadrilla
    {
        return $ficha->fichaEmpleados
                ->map(fn ($fe) => $fe->cuadrillaEmpleado?->cuadrilla)
                ->filter()
                ->first()
            ?? $ficha->pecosa?->cuadrillaEmpleado?->cuadrilla;
    }

    private function nombreCuadrilla(FichaActividad $ficha): string
    {
        return $this->cuadrillaDeFicha($ficha)?->nombre ?? 'Sin cuadrilla';
    }

    private function participantes(FichaActividad $ficha): Collection
    {
        return $ficha->fichaEmpleados
            ->map(function ($fe) {
                $empleado = $fe->cuadrillaEmpleado?->empleado;
                return trim(($empleado?->nombre ?? '') . ' ' . ($empleado?->apellido ?? ''));
            })
            ->filter()
            ->values();
    }

    /**
     * Totales de un conjunto de fichas. Se usa para el total general y para cada grupo.
     */
    private function resumenDe(Collection $fichas): array
    {
        return [
            'actividades' => $fichas->count(),
            'empleados' => $fichas
                ->flatMap(fn ($ficha) => $ficha->fichaEmpleados->pluck('cuadrillaEmpleado.empleado_id'))
                ->filter()
                ->unique()
                ->count(),
            'medidores' => $fichas->sum(fn ($ficha) => $ficha->medidorFichas->count()),
            'materiales' => $fichas->sum(fn ($ficha) => $ficha->materialFichas->count()),
            'precintos' => $fichas->sum(fn ($ficha) => $ficha->precintos->count()),
            'fotos' => $fichas->sum(fn ($ficha) => $ficha->fotos->count()),
        ];
    }

    private function resumenPorCuadrilla(Collection $fichas): Collection
    {
        return $fichas
            ->groupBy(fn ($ficha) => $this->nombreCuadrilla($ficha))
            ->map(fn ($grupo, $nombre) => ['cuadrilla' => $nombre] + $this->resumenDe($grupo))
            ->sortByDesc('actividades')
            ->values();
    }

    private function estiloEncabezado(Worksheet $hoja, string $rango): void
    {
        $hoja->getStyle($rango)->getFont()->setBold(true);
        $hoja->getStyle($rango)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9E1F2');
    }

    private function llenarHojaResumen(Worksheet $hoja, Collection $fichas): void
    {
        $hoja->setTitle('Resumen');

        $hoja->fromArray(
            ['Cuadrilla', 'Actividades', 'Empleados', 'Medidores', 'Materiales (ítems)', 'Precintos', 'Fotos'],
            null,
            'A1'
        );
        $this->estiloEncabezado($hoja, 'A1:G1');

        $fila = 2;
        foreach ($this->resumenPorCuadrilla($fichas) as $resumen) {
            $hoja->fromArray(array_values($resumen), null, 'A' . $fila);
            $fila++;
        }

        foreach (range('A', 'G') as $columna) {
            $hoja->getColumnDimension($columna)->setAutoSize(true);
        }
    }

    private function llenarHojaDetalle(Worksheet $hoja, Collection $fichas): void
    {
        $hoja->setTitle('Detalle');

        $hoja->fromArray(
            ['Fecha', 'Cuadrilla', 'Tipo de actividad', 'Suministro', 'Dirección', 'Empleados',
             'Medidores', 'Materiales (ítems)', 'Precintos', 'Fotos', 'Creado por', 'Observación'],
            null,
            'A1'
        );
        $this->estiloEncabezado($hoja, 'A1:L1');

        $fila = 2;
        foreach ($fichas as $ficha) {
            $hoja->fromArray([
                $ficha->fecha?->format('d/m/Y H:i'),
                $this->nombreCuadrilla($ficha),
                $ficha->tipoActividad?->nombre,
                trim(($ficha->suministro?->codigo ?? '') . ' ' . ($ficha->suministro?->nombre ?? '')),
                $ficha->direccion,
                $this->participantes($ficha)->implode(', '),
                $ficha->medidorFichas->count(),
                $ficha->materialFichas->count(),
                $ficha->precintos->count(),
                $ficha->fotos->count(),
                $ficha->usuarioCreacion?->name,
                $ficha->observacion,
            ], null, 'A' . $fila);
            $fila++;
        }

        foreach (range('A', 'L') as $columna) {
            $hoja->getColumnDimension($columna)->setAutoSize(true);
        }
    }
}
