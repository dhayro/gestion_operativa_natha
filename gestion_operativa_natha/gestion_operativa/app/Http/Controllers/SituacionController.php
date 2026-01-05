<?php

namespace App\Http\Controllers;

use App\Models\Situacion;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SituacionController extends Controller
{
    public function index()
    {
        return view('admin.situaciones.index', [
            'catName' => 'maestros',
            'title' => 'Gestión de Situaciones',
            'breadcrumbs' => ['Configuración', 'Situaciones'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $situaciones = Situacion::select(['id', 'nombre', 'estado']);
            return DataTables::of($situaciones)
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editSituacion(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteSituacion(' . $row->id . ')">Eliminar</a>
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
            'estado' => 'required|in:0,1',
        ]);
        $situacion = Situacion::create([
            'nombre' => $request->nombre,
            'estado' => (bool)$request->estado
        ]);
        return response()->json(['success' => true, 'data' => $situacion]);
    }

    public function show($id)
    {
        $situacion = Situacion::findOrFail($id);
        return response()->json($situacion);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'estado' => 'required|in:0,1',
        ]);
        $situacion = Situacion::findOrFail($id);
        $situacion->update([
            'nombre' => $request->nombre,
            'estado' => (bool)$request->estado
        ]);
        return response()->json(['success' => true, 'data' => $situacion]);
    }

    public function destroy($id)
    {
        $situacion = Situacion::findOrFail($id);
        $situacion->delete();
        return response()->json(['success' => true]);
    }

    public function select()
    {
        $situaciones = Situacion::where('estado', true)
            ->get(['id', 'nombre as text'])
            ->toArray();
        return response()->json($situaciones);
    }
}
