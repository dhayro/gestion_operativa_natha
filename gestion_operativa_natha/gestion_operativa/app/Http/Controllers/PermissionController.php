<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index()
    {
        return view('admin.permissions.index', [
            'catName' => 'admin',
            'title' => 'Gestión de Permisos',
            'breadcrumbs' => ['Configuración', 'Permisos'],
            'scrollspy' => 0,
            'simplePage' => 0,
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $permissions = Permission::orderBy('modulo')->orderBy('nombre');
            
            return DataTables::of($permissions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actions = '<div class="dropdown">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">';
                    
                    $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="editPermission(' . $row->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Editar
                                </a>';
                    
                    $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="deletePermission(' . $row->id . ')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 me-2">
                                        <polyline points="3,6 5,6 21,6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                    Eliminar
                                </a>';
                    
                    $actions .= '</div>
                                </div>';
                    
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }

    public function filterByModule(Request $request)
    {
        $modulo = $request->get('modulo');
        
        if ($modulo) {
            $permissions = Permission::where('modulo', $modulo)
                ->orderBy('nombre')
                ->get();
        } else {
            $permissions = Permission::orderBy('modulo')
                ->orderBy('nombre')
                ->get();
        }

        return response()->json($permissions);
    }

    public function matrix()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('modulo')->orderBy('nombre')->get();
        
        return view('admin.permissions.matrix', [
            'roles' => $roles,
            'permissions' => $permissions,
            'catName' => 'admin',
            'title' => 'Matriz de Permisos por Rol',
            'breadcrumbs' => ['Configuración', 'Matriz de Permisos'],
            'scrollspy' => 0,
            'simplePage' => 0,
        ]);
    }

    public function updateMatrix(Request $request)
    {
        $roleId = $request->get('role_id');
        $permissionIds = $request->get('permissions', []);

        $role = Role::find($roleId);
        if (!$role) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        $role->permissions()->sync($permissionIds);

        return response()->json(['success' => true, 'message' => 'Permisos actualizados']);
    }

    public function create()
    {
        return view('admin.permissions.create', [
            'catName' => 'admin',
            'title' => 'Crear Permiso',
            'breadcrumbs' => ['Configuración', 'Permisos', 'Crear'],
            'scrollspy' => 0,
            'simplePage' => 0,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:permissions|max:255',
            'descripcion' => 'nullable|max:500',
            'modulo' => 'required|max:100'
        ]);

        $permission = Permission::create($validated);

        return response()->json(['success' => true, 'data' => $permission]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:permissions,nombre,' . $id . '|max:255',
            'descripcion' => 'nullable|max:500',
            'modulo' => 'required|max:100'
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update($validated);

        return response()->json(['success' => true, 'data' => $permission]);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json(['success' => true]);
    }
}
