<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CargoController extends Controller
{
    public function index()
    {
        return view('admin.cargos.index', [
            'catName' => 'cargos',
            'title' => 'Gestión de Cargos',
            'breadcrumbs' => ['Configuración', 'Cargos'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $cargos = Cargo::select(['id', 'nombre', 'estado']);
            return DataTables::of($cargos)
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editCargo(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteCargo(' . $row->id . ')">Eliminar</a>
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
            'nombre' => 'required|string|max:100',
            'estado' => 'required|boolean',
        ]);
        $cargo = Cargo::create($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $cargo]);
    }

    public function show($id)
    {
        $cargo = Cargo::findOrFail($id);
        return response()->json($cargo);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'estado' => 'required|boolean',
        ]);
        $cargo = Cargo::findOrFail($id);
        $cargo->update($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $cargo]);
    }

    public function destroy($id)
    {
        $cargo = Cargo::findOrFail($id);
        $cargo->delete();
        return response()->json(['success' => true]);
    }

    public function getCargosForSelect()
    {
        try {
            $cargos = Cargo::select(['id', 'nombre'])
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($cargo) {
                    return [
                        'id' => $cargo->id,
                        'text' => $cargo->nombre
                    ];
                });

            return response()->json($cargos);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los cargos'
            ], 500);
        }
    }
}
