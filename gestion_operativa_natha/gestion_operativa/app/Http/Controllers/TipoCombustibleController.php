<?php

namespace App\Http\Controllers;

use App\Models\TipoCombustible;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TipoCombustibleController extends Controller
{
    public function index()
    {
        return view('admin.tipo_combustibles.index', [
            'catName' => 'maestros',
            'title' => 'Gestión de Tipos de Combustible',
            'breadcrumbs' => ['Configuración', 'Tipos de Combustible'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $tiposCombustible = TipoCombustible::select(['id', 'nombre', 'estado']);
            return DataTables::of($tiposCombustible)
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editTipoCombustible(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteTipoCombustible(' . $row->id . ')">Eliminar</a>
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
        $tipoCombustible = TipoCombustible::create($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $tipoCombustible]);
    }

    public function show($id)
    {
        $tipoCombustible = TipoCombustible::findOrFail($id);
        return response()->json($tipoCombustible);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'estado' => 'required|boolean',
        ]);
        $tipoCombustible = TipoCombustible::findOrFail($id);
        $tipoCombustible->update($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $tipoCombustible]);
    }

    public function destroy($id)
    {
        $tipoCombustible = TipoCombustible::findOrFail($id);
        $tipoCombustible->delete();
        return response()->json(['success' => true]);
    }

    public function getTiposCombustibleForSelect()
    {
        try {
            $tiposCombustible = TipoCombustible::select(['id', 'nombre'])
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($tipoCombustible) {
                    return [
                        'id' => $tipoCombustible->id,
                        'text' => $tipoCombustible->nombre
                    ];
                });

            return response()->json($tiposCombustible);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de combustible'
            ], 500);
        }
    }
}
