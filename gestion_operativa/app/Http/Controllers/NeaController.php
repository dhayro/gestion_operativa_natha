<?php

namespace App\Http\Controllers;

use App\Models\Nea;
use App\Models\Proveedor;
use App\Models\TipoComprobante;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class NeaController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::where('estado', true)->pluck('nombre', 'id');
        $tiposComprobantes = TipoComprobante::where('estado', true)->pluck('nombre', 'id');
        return view('admin.neas.index', compact('proveedores', 'tiposComprobantes'));
    }

    public function getData(Request $request)
    {
        $neas = Nea::with(['proveedor', 'tipoComprobante'])->latest('fecha');

        return DataTables::of($neas)
            ->addIndexColumn()
            ->addColumn('proveedor_nombre', function ($nea) {
                return $nea->proveedor ? $nea->proveedor->nombre : 'N/A';
            })
            ->addColumn('tipo_comprobante_nombre', function ($nea) {
                return $nea->tipoComprobante ? $nea->tipoComprobante->nombre : 'N/A';
            })
            ->addColumn('estado_badge', function ($nea) {
                $color = $nea->estado ? 'success' : 'danger';
                $texto = $nea->estado ? 'Activo' : 'Inactivo';
                return '<span class="badge bg-' . $color . '">' . $texto . '</span>';
            })
            ->addColumn('action', function ($nea) {
                return '<div class="text-center">
                    <button class="btn btn-sm btn-info me-1" onclick="editNea(' . $nea->id . ')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteNea(' . $nea->id . ')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </div>';
            })
            ->rawColumns(['estado_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'fecha' => 'required|date',
            'nro_documento' => 'required|string|max:50|unique:neas',
            'tipo_comprobante_id' => 'required|exists:tipo_comprobantes,id',
            'observaciones' => 'nullable|string',
            'estado' => 'required|boolean'
        ]);

        $nea = Nea::create([
            'proveedor_id' => $request->proveedor_id,
            'fecha' => $request->fecha,
            'nro_documento' => $request->nro_documento,
            'tipo_comprobante_id' => $request->tipo_comprobante_id,
            'observaciones' => $request->observaciones,
            'estado' => $request->estado,
            'usuario_creacion_id' => Auth::id()
        ]);

        return response()->json(['message' => 'NEA creada correctamente.', 'data' => $nea], 201);
    }

    public function show($id)
    {
        $nea = Nea::with(['proveedor', 'tipoComprobante'])->findOrFail($id);
        return response()->json($nea);
    }

    public function update(Request $request, $id)
    {
        $nea = Nea::findOrFail($id);

        $request->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'fecha' => 'required|date',
            'nro_documento' => 'required|string|max:50|unique:neas,nro_documento,' . $id,
            'tipo_comprobante_id' => 'required|exists:tipo_comprobantes,id',
            'observaciones' => 'nullable|string',
            'estado' => 'required|boolean'
        ]);

        $nea->update([
            'proveedor_id' => $request->proveedor_id,
            'fecha' => $request->fecha,
            'nro_documento' => $request->nro_documento,
            'tipo_comprobante_id' => $request->tipo_comprobante_id,
            'observaciones' => $request->observaciones,
            'estado' => $request->estado,
            'usuario_actualizacion_id' => Auth::id()
        ]);

        return response()->json(['message' => 'NEA actualizada correctamente.', 'data' => $nea]);
    }

    public function destroy($id)
    {
        $nea = Nea::findOrFail($id);
        $nea->delete();

        return response()->json(['message' => 'NEA eliminada correctamente.']);
    }

    public function select()
    {
        $neas = Nea::where('estado', true)->get(['id', 'nro_documento as text']);
        return response()->json($neas);
    }
}
