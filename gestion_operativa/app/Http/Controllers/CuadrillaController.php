<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CuadrillaController extends Controller
{
    public function index()
    {
        return view('admin.cuadrillas.index', [
            'catName' => 'cuadrillas',
            'title' => 'Gestión de Cuadrillas',
            'breadcrumbs' => ['Configuración', 'Cuadrillas'],
            'scrollspy' => 0,
            'simplePage' => 0,
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $cuadrillas = Cuadrilla::with([
                    'cuadrillaEmpleados' => function($query) {
                        $query->where('estado', true);
                    },
                    'asignacionesVehiculos' => function($query) {
                        $query->where('estado', true);
                    }
                ])
                ->select(['id', 'nombre', 'fecha_inicio', 'fecha_fin', 'estado', 'created_at']);
            
            return DataTables::of($cuadrillas)
                ->addIndexColumn()
                ->addColumn('fecha_inicio_formatted', function ($row) {
                    return $row->fecha_inicio ? $row->fecha_inicio->format('d/m/Y') : '-';
                })
                ->addColumn('fecha_fin_formatted', function ($row) {
                    return $row->fecha_fin ? $row->fecha_fin->format('d/m/Y') : '-';
                })
                ->addColumn('empleados_count_formatted', function ($row) {
                    // Debug temporal
                    $empleados = $row->cuadrillaEmpleados;
                    \Log::info('Cuadrilla ID: ' . $row->id . ' - Empleados cargados: ' . ($empleados ? $empleados->count() : 'NULL'));
                    
                    // Contar manualmente los empleados activos
                    $count = $empleados ? $empleados->count() : 0;
                    $badgeClass = $count > 0 ? 'badge-success' : 'badge-secondary';
                    return '<span class="badge ' . $badgeClass . ' px-3 py-2" style="font-size: 0.9em; font-weight: 600;">' . $count . '</span>';
                })
                ->addColumn('vehiculos_count_formatted', function ($row) {
                    // Contar vehículos asignados activos
                    $vehiculos = $row->asignacionesVehiculos;
                    $count = $vehiculos ? $vehiculos->count() : 0;
                    $badgeClass = $count > 0 ? 'badge-info' : 'badge-secondary';
                    return '<span class="badge ' . $badgeClass . ' px-3 py-2" style="font-size: 0.9em; font-weight: 600;">' . $count . '</span>';
                })
                ->addColumn('estado_badge', function ($row) {
                    return $row->estado
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="gestionarEmpleados(' . $row->id . ', \'' . addslashes($row->nombre) . '\')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users me-2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                        Asignar Personal
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="gestionarVehiculos(' . $row->id . ', \'' . addslashes($row->nombre) . '\')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck me-2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16,8 20,8 23,11 23,16 16,16 16,8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                        Asignar Vehículos
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editCuadrilla(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Editar
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteCuadrilla(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 me-2"><polyline points="3,6 5,6 21,6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2 2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        Eliminar
                                    </a>
                                </div>
                            </div>';
                })
                ->rawColumns(['empleados_count_formatted', 'vehiculos_count_formatted', 'estado_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:100|unique:cuadrillas,nombre',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|boolean',
        ]);

        // Autogenerar nombre si no se proporciona
        $data = $request->all();
        if (empty($data['nombre'])) {
            $ultimaCuadrilla = Cuadrilla::orderBy('id', 'desc')->first();
            $numero = $ultimaCuadrilla ? $ultimaCuadrilla->id + 1 : 1;
            $nombre = 'Cuadrilla ' . str_pad($numero, 3, '0', STR_PAD_LEFT);
            
            // Verificar que el nombre autogenerado no exista
            while (Cuadrilla::where('nombre', $nombre)->exists()) {
                $numero++;
                $nombre = 'Cuadrilla ' . str_pad($numero, 3, '0', STR_PAD_LEFT);
            }
            $data['nombre'] = $nombre;
        }

        $cuadrilla = Cuadrilla::create($data);
        return response()->json(['success' => true, 'data' => $cuadrilla]);
    }

    public function show($id)
    {
        $cuadrilla = Cuadrilla::findOrFail($id);
        return response()->json($cuadrilla);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:100|unique:cuadrillas,nombre,' . $id,
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|boolean',
        ]);

        $cuadrilla = Cuadrilla::findOrFail($id);
        
        // Manejar autogeneración de nombre si está vacío
        $data = $request->all();
        if (empty($data['nombre']) && empty($cuadrilla->nombre)) {
            $numero = $cuadrilla->id;
            $nombre = 'Cuadrilla ' . str_pad($numero, 3, '0', STR_PAD_LEFT);
            
            // Verificar que el nombre autogenerado no exista
            while (Cuadrilla::where('nombre', $nombre)->where('id', '!=', $id)->exists()) {
                $numero++;
                $nombre = 'Cuadrilla ' . str_pad($numero, 3, '0', STR_PAD_LEFT);
            }
            $data['nombre'] = $nombre;
        } elseif (empty($data['nombre'])) {
            $data['nombre'] = $cuadrilla->nombre; // Mantener el nombre actual
        }

        $cuadrilla->update($data);
        return response()->json(['success' => true, 'data' => $cuadrilla]);
    }

    public function destroy($id)
    {
        $cuadrilla = Cuadrilla::findOrFail($id);
        $cuadrilla->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Get cuadrillas for select dropdown
     */
    public function select(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 10;

        $query = Cuadrilla::select('id', 'nombre', 'estado')
            ->orderBy('nombre');

        if (!empty($search)) {
            $query->where('nombre', 'LIKE', "%{$search}%");
        }

        // Solo activos por defecto, a menos que se especifique incluir inactivos
        if (!$request->has('incluir_inactivos')) {
            $query->where('estado', true);
        }

        $total = $query->count();
        $cuadrillas = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $results = $cuadrillas->map(function ($cuadrilla) {
            return [
                'id' => $cuadrilla->id,
                'text' => $cuadrilla->nombre,
                'disabled' => !$cuadrilla->estado
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }
}