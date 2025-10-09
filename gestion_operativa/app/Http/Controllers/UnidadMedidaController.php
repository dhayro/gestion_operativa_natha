<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        return view('admin.unidad_medidas.index', [
            'catName' => 'unidad_medidas',
            'title' => 'Gestión de Unidades de Medida',
            'breadcrumbs' => ['Configuración', 'Unidades de Medida'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $unidades = UnidadMedida::select(['id', 'nombre', 'estado']);
            return DataTables::of($unidades)
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editUnidadMedida(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteUnidadMedida(' . $row->id . ')">Eliminar</a>
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
        $unidad = UnidadMedida::create($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $unidad]);
    }

    public function show($id)
    {
        $unidad = UnidadMedida::findOrFail($id);
        return response()->json($unidad);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'estado' => 'required|boolean',
        ]);
        $unidad = UnidadMedida::findOrFail($id);
        $unidad->update($request->only('nombre', 'estado'));
        return response()->json(['success' => true, 'data' => $unidad]);
    }

    public function destroy($id)
    {
        $unidad = UnidadMedida::findOrFail($id);
        $unidad->delete();
        return response()->json(['success' => true]);
    }

    public function getUnidadesForSelect()
    {
        try {
            $unidades = UnidadMedida::select(['id', 'nombre'])
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($unidad) {
                    return [
                        'id' => $unidad->id,
                        'text' => $unidad->nombre
                    ];
                });

            return response()->json($unidades);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las unidades de medida'
            ], 500);
        }
    }
}
