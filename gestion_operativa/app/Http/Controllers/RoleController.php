<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index', [
            'catName' => 'admin',
            'title' => 'Gestión de Roles',
            'breadcrumbs' => ['Administración', 'Roles'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::with('permissions')->select(['id', 'nombre', 'descripcion']);

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('permisos_count', function ($row) {
                    return '<span class="badge badge-info">' . $row->permissions->count() . ' permisos</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editRole(' . $row->id . ')">Editar</a>';
                    
                    if (!in_array($row->nombre, ['admin', 'supervisor', 'tecnico', 'operario'])) {
                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="deleteRole(' . $row->id . ')">Eliminar</a>';
                    }
                    
                    $html .= '</div>
                            </div>';
                    return $html;
                })
                ->rawColumns(['permisos_count', 'action'])
                ->make(true);
        }
    }

    public function show(Role $role)
    {
        if (request()->ajax()) {
            return response()->json([
                'id' => $role->id,
                'nombre' => $role->nombre,
                'descripcion' => $role->descripcion,
                'permissions' => $role->permissions
            ]);
        }
        abort(404);
    }

    public function create()
    {
        $permissions = Permission::orderBy('modulo')->get();
        
        return view('admin.roles.create', [
            'permissions' => $permissions,
            'catName' => 'admin'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:roles|max:255',
            'descripcion' => 'nullable|max:500',
            'permissions' => 'array'
        ]);

        $role = Role::create($validated);
        
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $role]);
        }

        return redirect()->route('roles.index')
            ->with('success', "Rol '{$role->nombre}' creado exitosamente");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('modulo')->get();
        
        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'catName' => 'admin'
        ]);
    }

    public function update(Request $request, Role $role)
    {
        // Proteger roles del sistema
        if (in_array($role->nombre, ['admin', 'supervisor', 'tecnico', 'operario'])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No puedes editar roles del sistema'], 422);
            }
            return redirect()->back()->with('error', 'No puedes editar roles del sistema');
        }

        $validated = $request->validate([
            'nombre' => "required|unique:roles,nombre,{$role->id}|max:255",
            'descripcion' => 'nullable|max:500',
            'permissions' => 'array'
        ]);

        $role->update($validated);
        
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $role]);
        }

        return redirect()->route('roles.index')
            ->with('success', "Rol '{$role->nombre}' actualizado exitosamente");
    }

    public function destroy(Role $role)
    {
        // Proteger roles del sistema
        if (in_array($role->nombre, ['admin', 'supervisor', 'tecnico', 'operario'])) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'No puedes eliminar roles del sistema'], 422);
            }
            return redirect()->back()->with('error', 'No puedes eliminar roles del sistema');
        }

        $role->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Rol eliminado exitosamente']);
        }

        return redirect()->route('roles.index')
            ->with('success', "Rol eliminado exitosamente");
    }
}
