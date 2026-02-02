<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Cargo;
use App\Models\Area;
use App\Models\Ubigeo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class EmpleadoController extends Controller
{
    public function index()
    {
    $cargos = Cargo::all(); // Mostrar todos para que las opciones inactivas aparezcan en editar
    $areas = Area::all(); // Mostrar todos para que las opciones inactivas aparezcan en editar
    $ubigeos = Ubigeo::where('estado', 1)->get(); // Ubigeo se carga dinámicamente, no afecta
        return view('admin.empleados.index', [
            'catName' => 'empleados',
            'title' => 'Gestión de Empleados',
            'breadcrumbs' => ['Configuración', 'Empleados'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'cargos' => $cargos,
            'areas' => $areas,
            'ubigeos' => $ubigeos,
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $empleados = Empleado::with(['cargo', 'area', 'ubigeo', 'usuario']);
            return DataTables::of($empleados)
                ->addIndexColumn()
                ->addColumn('cargo', function ($row) {
                    return $row->cargo ? $row->cargo->nombre : '';
                })
                ->addColumn('area', function ($row) {
                    return $row->area ? $row->area->nombre : '';
                })
                ->addColumn('ubigeo', function ($row) {
                    return $row->ubigeo ? $row->ubigeo->nombre : '';
                })
                ->addColumn('usuario_estado', function ($row) {
                    if ($row->usuario) {
                        $estado = $row->usuario->estado ? 'Activo' : 'Inactivo';
                        $badgeClass = $row->usuario->estado ? 'badge-success' : 'badge-danger';
                        return '<span class="badge ' . $badgeClass . '">' . $estado . '</span>';
                    } else {
                        return '<span class="badge badge-warning">Sin Usuario</span>';
                    }
                })
                ->addColumn('estado_badge', function ($row) {
                    return $row->estado
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="dropdown">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">';
                    
                    $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="editEmpleado(' . $row->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Editar
                                </a>';
                    
                    // Opción de gestión de usuario
                    if ($row->usuario) {
                        $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="gestionarUsuario(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user me-2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        Gestionar Usuario
                                    </a>';
                    } else {
                        $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="crearUsuario(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus me-2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="8.5" cy="7" r="4"></circle>
                                            <line x1="20" y1="8" x2="20" y2="14"></line>
                                            <line x1="23" y1="11" x2="17" y2="11"></line>
                                        </svg>
                                        Crear Usuario
                                    </a>';
                    }
                    
                    $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="deleteEmpleado(' . $row->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 me-2">
                                        <polyline points="3,6 5,6 21,6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                    Eliminar
                                </a>
                            </div>
                        </div>';
                    
                    return $actions;
                })
                ->rawColumns(['estado_badge', 'usuario_estado', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
                'nombre' => 'required|string|max:50',
                'apellido' => 'required|string|max:100',
                'dni' => 'required|string|max:8|unique:empleados,dni',
                'telefono' => 'nullable|string|max:15',
                'email' => 'required|email|max:100|unique:empleados,email',
                'direccion' => 'nullable|string|max:200',
                'estado' => 'required|boolean',
                'cargo_id' => 'required|exists:cargos,id',
                'area_id' => 'required|exists:areas,id',
                'ubigeo_id' => 'nullable|exists:ubigeos,id',
            ]);
            $empleado = Empleado::create($request->all());
            return response()->json(['success' => true, 'data' => $empleado]);
    }

    public function show($id)
    {
        $empleado = Empleado::with(['cargo', 'area', 'ubigeo'])->findOrFail($id);
        return response()->json($empleado);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                'nombre' => 'required|string|max:50',
                'apellido' => 'required|string|max:100',
                'dni' => 'required|string|max:8|unique:empleados,dni,' . $id,
                'telefono' => 'nullable|string|max:15',
                'email' => 'required|email|max:100|unique:empleados,email,' . $id,
                'direccion' => 'nullable|string|max:200',
                'estado' => 'required|boolean',
                'cargo_id' => 'required|exists:cargos,id',
                'area_id' => 'required|exists:areas,id',
                'ubigeo_id' => 'nullable|exists:ubigeos,id',
            ]);
            $empleado = Empleado::findOrFail($id);
            $empleado->update($request->all());
            return response()->json(['success' => true, 'data' => $empleado]);
    }

    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Crear usuario para un empleado
     */
    public function crearUsuario(Request $request, $empleadoId)
    {
        $empleado = Empleado::findOrFail($empleadoId);
        
        if ($empleado->usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Este empleado ya tiene un usuario asociado.'
            ], 422);
        }

        $request->validate([
            'perfil' => 'required|in:admin,supervisor,tecnico,operario',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'empleado_id' => $empleado->id,
            'name' => $empleado->nombre . ' ' . $empleado->apellido,
            'email' => $empleado->email,
            'password' => Hash::make($request->password),
            'perfil' => $request->perfil,
            'estado' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente.',
            'data' => $user
        ]);
    }

    /**
     * Obtener información del usuario de un empleado
     */
    public function getUsuario($empleadoId)
    {
        $empleado = Empleado::with('usuario')->findOrFail($empleadoId);
        
        return response()->json([
            'success' => true,
            'empleado' => $empleado,
            'usuario' => $empleado->usuario
        ]);
    }

    /**
     * Actualizar usuario de un empleado
     */
    public function actualizarUsuario(Request $request, $empleadoId)
    {
        $empleado = Empleado::with('usuario')->findOrFail($empleadoId);
        
        if (!$empleado->usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Este empleado no tiene usuario asociado.'
            ], 422);
        }

        $request->validate([
            'perfil' => 'required|in:admin,supervisor,tecnico,operario',
            'estado' => 'required|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'perfil' => $request->perfil,
            'estado' => $request->estado,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $empleado->usuario->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente.',
            'data' => $empleado->usuario
        ]);
    }

    /**
     * Eliminar usuario de un empleado
     */
    public function eliminarUsuario($empleadoId)
    {
        $empleado = Empleado::with('usuario')->findOrFail($empleadoId);
        
        if (!$empleado->usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Este empleado no tiene usuario asociado.'
            ], 422);
        }

        $empleado->usuario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente.'
        ]);
    }

    // Obtener Departamentos (Ubigeos padres)
    public function getDepartamentos()
    {
        $departamentos = Ubigeo::whereNull('dependencia_id')
            ->where('estado', true)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nombre
                ];
            });

        return response()->json($departamentos);
    }

    // Obtener Provincias (Hijos del Departamento)
    public function getProvincias($departamento_id)
    {
        $provincias = Ubigeo::where('dependencia_id', $departamento_id)
            ->where('estado', true)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nombre
                ];
            });

        return response()->json($provincias);
    }

    // Obtener Distritos (Hijos de la Provincia)
    public function getDistritos($provincia_id)
    {
        $distritos = Ubigeo::where('dependencia_id', $provincia_id)
            ->where('estado', true)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nombre
                ];
            });

        return response()->json($distritos);
    }

    // Obtener la jerarquía completa de ubigeo (Departamento, Provincia, Distrito) a partir del ID del distrito
    public function getUbigeoHierarquia($ubigeo_id)
    {
        try {
            $ubigeo = Ubigeo::findOrFail($ubigeo_id);
            
            // Inicializamos los niveles
            $distrito = $ubigeo;
            $provincia = null;
            $departamento = null;
            
            // Recorremos hacia arriba en la jerarquía
            if ($distrito->dependencia) {
                $provincia = $distrito->dependencia;
                
                if ($provincia->dependencia) {
                    $departamento = $provincia->dependencia;
                }
            }
            
            return response()->json([
                'success' => true,
                'departamento_id' => $departamento ? $departamento->id : null,
                'provincia_id' => $provincia ? $provincia->id : null,
                'distrito_id' => $distrito->id,
                'departamento_nombre' => $departamento ? $departamento->nombre : null,
                'provincia_nombre' => $provincia ? $provincia->nombre : null,
                'distrito_nombre' => $distrito->nombre
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la jerarquía del ubigeo: ' . $e->getMessage()
            ], 404);
        }
    }
}
