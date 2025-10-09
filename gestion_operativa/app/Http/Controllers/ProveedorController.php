<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Ubigeo;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProveedorController extends Controller
{
    public function index()
    {
        return view('admin.proveedores.index', [
            'catName' => 'configuracion',
            'submenu' => 'proveedores',
            'title' => 'Gestión de Proveedores',
            'breadcrumbs' => ['Configuración', 'Proveedores'],
            'scrollspy' => 0,
            'simplePage' => 0,
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $proveedores = Proveedor::select(
                    'proveedors.id',
                    'proveedors.razon_social',
                    'proveedors.ruc',
                    'proveedors.contacto',
                    'proveedors.email',
                    'proveedors.telefono',
                    'proveedors.direccion',
                    'proveedors.ubigeo_id',
                    'proveedors.estado',
                    'proveedors.created_at',
                    'proveedors.updated_at',
                    'ubigeos.nombre as ubigeo_nombre'
                )
                ->leftJoin('ubigeos', 'proveedors.ubigeo_id', '=', 'ubigeos.id');
            
            return DataTables::of($proveedores)
                ->addIndexColumn()
                ->addColumn('ubigeo', function ($row) {
                    return $row->ubigeo_nombre ?? '';
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editProveedor(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteProveedor(' . $row->id . ')">Eliminar</a>
                                </div>
                            </div>';
                })
                ->filterColumn('ubigeo', function($query, $keyword) {
                    $query->whereRaw("LOWER(ubigeos.nombre) LIKE ?", ["%{$keyword}%"]);
                })
                ->orderColumn('ubigeo', 'ubigeos.nombre $1')
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:100',
            'ruc' => 'required|string|size:11|unique:proveedors,ruc',
            'contacto' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:200',
            'ubigeo_id' => 'nullable|exists:ubigeos,id',
            'estado' => 'nullable|boolean',
        ]);
        
        $data = $request->all();
        $data['estado'] = $request->has('estado') ? 1 : 0;
        
        $proveedor = Proveedor::create($data);
        return response()->json(['success' => true, 'data' => $proveedor]);
    }

    public function show($id)
    {
        $proveedor = Proveedor::with(['ubigeo'])->findOrFail($id);
        return response()->json($proveedor);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'razon_social' => 'required|string|max:100',
            'ruc' => 'required|string|size:11|unique:proveedors,ruc,' . $id,
            'contacto' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:200',
            'ubigeo_id' => 'nullable|exists:ubigeos,id',
            'estado' => 'nullable|boolean',
        ]);
        
        $data = $request->all();
        $data['estado'] = $request->has('estado') ? 1 : 0;
        
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update($data);
        return response()->json(['success' => true, 'data' => $proveedor]);
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();
        return response()->json(['success' => true, 'message' => 'Proveedor eliminado correctamente']);
    }

    /**
     * Obtener proveedores activos para Select2
     */
    public function getProveedoresForSelect(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = Proveedor::where('estado', true);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('razon_social', 'LIKE', '%' . $search . '%')
                  ->orWhere('ruc', 'LIKE', '%' . $search . '%');
            });
        }

        $proveedores = $query->select('id', 'razon_social', 'ruc')
            ->orderBy('razon_social')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $results = $proveedores->map(function ($proveedor) {
            return [
                'id' => $proveedor->id,
                'text' => $proveedor->razon_social . ' - ' . $proveedor->ruc
            ];
        });

        return response()->json($results);
    }
}