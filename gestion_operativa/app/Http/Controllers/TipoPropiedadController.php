<?php

namespace App\Http\Controllers;

use App\Models\TipoPropiedad;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TipoPropiedadController extends Controller
{
    public function index()
    {
        return view('admin.tipo_propiedades.index', [
            'catName' => 'maestros',
            'title' => 'Gestión de Tipos de Propiedad',
            'breadcrumbs' => ['Configuración', 'Tipos de Propiedad'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $tiposPropiedades = TipoPropiedad::select(['id', 'nombre', 'estado']);
            return DataTables::of($tiposPropiedades)
                ->addIndexColumn()
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editTipoPropiedad(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteTipoPropiedad(' . $row->id . ')">Eliminar</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'estado' => 'required|boolean',
        ]);
        $tipoPropiedad = TipoPropiedad::create($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $tipoPropiedad]);
    }

    public function show($id)
    {
        $tipoPropiedad = TipoPropiedad::findOrFail($id);
        return response()->json($tipoPropiedad);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'estado' => 'required|boolean',
        ]);
        $tipoPropiedad = TipoPropiedad::findOrFail($id);
        $tipoPropiedad->update($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $tipoPropiedad]);
    }

    public function destroy($id)
    {
        $tipoPropiedad = TipoPropiedad::findOrFail($id);
        $tipoPropiedad->delete();
        return response()->json(['success' => true]);
    }

    // Para selects dinámicos (opcional)
    public function select()
    {
        $tiposPropiedades = TipoPropiedad::where('estado', true)
            ->get(['id', 'nombre as text'])
            ->toArray();
        return response()->json($tiposPropiedades);
    }
}
