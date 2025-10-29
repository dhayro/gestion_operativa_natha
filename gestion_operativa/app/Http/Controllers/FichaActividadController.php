<?php

namespace App\Http\Controllers;

use App\Models\FichaActividad;
use App\Models\Suministro;
use App\Models\TiposActividad;
use App\Models\TipoPropiedad;
use App\Models\Construccion;
use App\Models\ServicioElectrico;
use App\Models\Uso;
use App\Models\Situacion;
use App\Repositories\FichaActividadRepository;
use App\Repositories\Contracts\FichaActividadRepositoryContract;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FichaActividadController extends Controller
{
    protected $fichaRepository;

    public function __construct(FichaActividadRepositoryContract $fichaRepository)
    {
        $this->fichaRepository = $fichaRepository;
    }

    /**
     * Mostrar lista de fichas de actividad
     */
    public function index()
    {
        return view('admin.ficha_actividad.index', [
            'catName' => 'ficha_actividad',
            'title' => 'Gestión de Fichas de Actividad',
            'breadcrumbs' => ['Operaciones', 'Fichas de Actividad'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    /**
     * Obtener datos para DataTable
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $fichas = FichaActividad::select([
                'id',
                'suministro_id',
                'tipo_actividad_id',
                'numero_piso',
                'fecha',
                'estado'
            ])->orderBy('fecha', 'desc');

            return DataTables::of($fichas)
                ->addIndexColumn()
                ->addColumn('suministro_codigo', function ($row) {
                    return $row->suministro ? $row->suministro->codigo : '-';
                })
                ->addColumn('suministro_nombre', function ($row) {
                    return $row->suministro ? $row->suministro->nombre : '-';
                })
                ->addColumn('tipo_actividad', function ($row) {
                    return $row->tipoActividad ? $row->tipoActividad->nombre : '-';
                })
                ->addColumn('fecha_formateada', function ($row) {
                    return $row->fecha ? $row->fecha->format('d/m/Y H:i') : '-';
                })
                ->addColumn('estado_badge', function ($row) {
                    return $row->estado
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="verFicha(' . $row->id . ')">Ver</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editarFicha(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="eliminarFicha(' . $row->id . ')">Eliminar</a>
                                </div>
                            </div>';
                    return $html;
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
    }

    /**
     * Guardar ficha de actividad
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipo_actividad_id' => 'required|exists:tipos_actividads,id',
            'suministro_id' => 'required|exists:suministros,id',
            'tipo_propiedad_id' => 'nullable|exists:tipos_propiedads,id',
            'construccion_id' => 'nullable|exists:construccions,id',
            'servicio_electrico_id' => 'nullable|exists:servicio_electricos,id',
            'uso_id' => 'nullable|exists:usos,id',
            'numero_piso' => 'nullable|string|max:10',
            'situacion_id' => 'nullable|exists:situacions,id',
            'situacion_detalle' => 'nullable|string|max:100',
            'suministro_derecho' => 'nullable|string|max:50',
            'suministro_izquierdo' => 'nullable|string|max:50',
            'latitud' => 'nullable|string|max:50',
            'longitud' => 'nullable|string|max:50',
            'observacion' => 'nullable|string',
            'documento' => 'nullable|string|max:100',
            'fecha' => 'nullable|date',
            'estado' => 'required|boolean'
        ], [
            'tipo_actividad_id.required' => 'El tipo de actividad es obligatorio.',
            'suministro_id.required' => 'El suministro es obligatorio.',
            'tipo_actividad_id.exists' => 'El tipo de actividad seleccionado no existe.',
            'suministro_id.exists' => 'El suministro seleccionado no existe.'
        ]);

        try {
            $ficha = $this->fichaRepository->crear($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Ficha de Actividad creada exitosamente.',
                'data' => $ficha
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la ficha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar detalle de ficha (JSON para modal)
     */
    public function show($id)
    {
        try {
            $ficha = $this->fichaRepository->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $ficha
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ficha no encontrada: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        // Este método ya no se usa - el edit se hace en modal
        return abort(404);
    }

    /**
     * Actualizar ficha de actividad
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'tipo_actividad_id' => 'required|exists:tipos_actividads,id',
            'suministro_id' => 'required|exists:suministros,id',
            'tipo_propiedad_id' => 'nullable|exists:tipos_propiedads,id',
            'construccion_id' => 'nullable|exists:construccions,id',
            'servicio_electrico_id' => 'nullable|exists:servicio_electricos,id',
            'uso_id' => 'nullable|exists:usos,id',
            'numero_piso' => 'nullable|string|max:10',
            'situacion_id' => 'nullable|exists:situacions,id',
            'situacion_detalle' => 'nullable|string|max:100',
            'suministro_derecho' => 'nullable|string|max:50',
            'suministro_izquierdo' => 'nullable|string|max:50',
            'latitud' => 'nullable|string|max:50',
            'longitud' => 'nullable|string|max:50',
            'observacion' => 'nullable|string',
            'documento' => 'nullable|string|max:100',
            'fecha' => 'nullable|date',
            'estado' => 'required|boolean'
        ]);

        try {
            $ficha = $this->fichaRepository->actualizar($id, $validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Ficha de Actividad actualizada exitosamente.',
                'data' => $ficha
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la ficha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar ficha de actividad
     */
    public function destroy($id)
    {
        try {
            $this->fichaRepository->eliminar($id);

            return response()->json([
                'success' => true,
                'message' => 'Ficha de Actividad eliminada exitosamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la ficha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado de ficha
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $request->validate([
                'estado' => 'required|boolean'
            ]);

            $ficha = $this->fichaRepository->cambiarEstado($id, $request->estado);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente.',
                'data' => $ficha
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar fichas por término
     */
    public function buscar(Request $request)
    {
        try {
            $termino = $request->input('termino', '');
            $fichas = $this->fichaRepository->buscar($termino);

            return response()->json([
                'success' => true,
                'data' => $fichas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener fichas por suministro
     */
    public function porSuministro($suministroId)
    {
        try {
            $fichas = $this->fichaRepository->obtenerPorSuministro($suministroId);

            return response()->json([
                'success' => true,
                'data' => $fichas
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
