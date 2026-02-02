<?php

namespace App\Http\Controllers;

use App\Models\Construccion;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ConstruccionController extends Controller
{
    public function index()
    {
        return view('admin.construcciones.index', [
            'catName' => 'maestros',
            'title' => 'Gestión de Construcciones',
            'breadcrumbs' => ['Configuración', 'Construcciones'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $construcciones = Construccion::select(['id', 'nombre', 'estado']);
            return DataTables::of($construcciones)
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editConstruccion(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteConstruccion(' . $row->id . ')">Eliminar</a>
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
        $construccion = Construccion::create($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $construccion]);
    }

    public function show($id)
    {
        $construccion = Construccion::findOrFail($id);
        return response()->json($construccion);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'estado' => 'required|boolean',
        ]);
        $construccion = Construccion::findOrFail($id);
        $construccion->update($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $construccion]);
    }

    public function destroy($id)
    {
        $construccion = Construccion::findOrFail($id);
        $construccion->delete();
        return response()->json(['success' => true]);
    }

    // Para selects dinámicos (opcional)
    public function select()
    {
        $construcciones = Construccion::where('estado', true)
            ->get(['id', 'nombre as text'])
            ->toArray();
        return response()->json($construcciones);
    }
}
