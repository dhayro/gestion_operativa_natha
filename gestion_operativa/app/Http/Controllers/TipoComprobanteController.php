<?php

namespace App\Http\Controllers;

use App\Models\TipoComprobante;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TipoComprobanteController extends Controller
{
    public function index()
    {
        return view('admin.tipo_comprobantes.index', [
            'catName' => 'maestros',
            'title' => 'Gestión de Tipos de Comprobante',
            'breadcrumbs' => ['Configuración', 'Tipos de Comprobante'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $comprobantes = TipoComprobante::select(['id', 'nombre', 'estado']);
            return DataTables::of($comprobantes)
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editTipoComprobante(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteTipoComprobante(' . $row->id . ')">Eliminar</a>
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
            'nombre' => 'required|string|max:20|unique:tipo_comprobantes',
            'estado' => 'required|in:0,1',
        ]);
        $comprobante = TipoComprobante::create([
            'nombre' => $request->nombre,
            'estado' => (bool)$request->estado
        ]);
        return response()->json(['success' => true, 'data' => $comprobante]);
    }

    public function show($id)
    {
        $comprobante = TipoComprobante::findOrFail($id);
        return response()->json($comprobante);
    }

    public function update(Request $request, $id)
    {
        $comprobante = TipoComprobante::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:20|unique:tipo_comprobantes,nombre,' . $id,
            'estado' => 'required|in:0,1',
        ]);

        $comprobante->update([
            'nombre' => $request->nombre,
            'estado' => (bool)$request->estado
        ]);

        return response()->json(['success' => true, 'data' => $comprobante]);
    }

    public function destroy($id)
    {
        $comprobante = TipoComprobante::findOrFail($id);
        $comprobante->delete();
        return response()->json(['success' => true]);
    }

    public function select()
    {
        $comprobantes = TipoComprobante::where('estado', true)
            ->get(['id', 'nombre as text'])
            ->toArray();
        return response()->json($comprobantes);
    }
}
