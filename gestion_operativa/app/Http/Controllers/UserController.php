<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'catName' => 'admin',
            'title' => 'Gestión de Usuarios',
            'breadcrumbs' => ['Administración', 'Usuarios'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with(['roles', 'empleado'])->select(['id', 'name', 'email', 'empleado_id', 'perfil', 'estado']);

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('empleado_nombre', function ($row) {
                    return $row->empleado ? $row->empleado->nombre : 'N/A';
                })
                ->addColumn('roles', function ($row) {
                    $roles = $row->roles->pluck('nombre')->toArray();
                    $html = '';
                    if (empty($roles)) {
                        $html .= '<span class="badge bg-warning">Sin roles</span>';
                    } else {
                        foreach ($roles as $role) {
                            $html .= '<span class="badge bg-info">' . ucfirst($role) . '</span> ';
                        }
                    }
                    return $html;
                })
                ->addColumn('estado', function ($row) {
                    $badge = $row->estado ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';
                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="asignarRoles(' . $row->id . ')">Asignar Roles</a>';
                    
                    $html .= '</div>
                            </div>';
                    return $html;
                })
                ->rawColumns(['roles', 'estado', 'action'])
                ->make(true);
        }
    }

    public function getRolesModal($userId)
    {
        $user = User::with('roles')->findOrFail($userId);
        $allRoles = Role::all();

        return response()->json([
            'user' => $user,
            'userRoles' => $user->roles->pluck('id')->toArray(),
            'allRoles' => $allRoles
        ]);
    }

    public function asignarRoles(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $rolesIds = $request->input('roles', []);

        // Sincronizar roles (actualizar relación)
        $user->roles()->sync($rolesIds);

        return response()->json([
            'success' => true,
            'message' => 'Roles asignados correctamente'
        ]);
    }
}
