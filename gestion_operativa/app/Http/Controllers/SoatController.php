<?php

namespace App\Http\Controllers;

use App\Models\Soat;
use App\Models\Vehiculo;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class SoatController extends Controller
{
    public function index()
    {
        return view('admin.soats.index', [
            'catName' => 'maestros',
            'title' => 'Gestión de SOATs',
            'breadcrumbs' => ['Configuración', 'SOATs'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $soats = Soat::with(['vehiculo', 'proveedor'])
                ->select(['id', 'vehiculo_id', 'proveedor_id', 'numero_soat', 'fecha_emision', 'fecha_vencimiento', 'estado']);
            
            return DataTables::of($soats)
                ->addIndexColumn()
                ->addColumn('vehiculo_info', function ($row) {
                    return $row->vehiculo ? 
                        $row->vehiculo->marca . ' ' . $row->vehiculo->nombre . ' (' . $row->vehiculo->placa . ')' : 
                        'N/A';
                })
                ->addColumn('proveedor_nombre', function ($row) {
                    return $row->proveedor ? $row->proveedor->nombre : 'N/A';
                })
                ->addColumn('fecha_emision_formatted', function ($row) {
                    return $row->fecha_emision ? $row->fecha_emision->format('d/m/Y') : '';
                })
                ->addColumn('fecha_vencimiento_formatted', function ($row) {
                    return $row->fecha_vencimiento ? $row->fecha_vencimiento->format('d/m/Y') : '';
                })
                ->addColumn('vigencia_badge', function ($row) {
                    if (!$row->fecha_vencimiento) return '<span class="badge badge-secondary">Sin fecha</span>';
                    
                    $hoy = Carbon::now('America/Lima');
                    $vencimiento = Carbon::parse($row->fecha_vencimiento);
                    $diasRestantes = $hoy->diffInDays($vencimiento, false);
                    
                    if ($diasRestantes < 0) {
                        return '<span class="badge badge-danger">Vencido</span>';
                    } elseif ($diasRestantes <= 30) {
                        return '<span class="badge badge-warning">Por vencer</span>';
                    } else {
                        return '<span class="badge badge-success">Vigente</span>';
                    }
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editSoat(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteSoat(' . $row->id . ')">Eliminar</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['vigencia_badge', 'estado_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'proveedor_id' => 'required|exists:proveedors,id',
            'numero_soat' => 'required|string|max:200',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_emision',
            'estado' => 'required|boolean',
        ]);
        
        $soat = Soat::create($request->only([
            'vehiculo_id', 'proveedor_id', 'numero_soat', 'fecha_emision', 'fecha_vencimiento', 'estado'
        ]));
        
        return response()->json(['success' => true, 'data' => $soat]);
    }

    public function show($id)
    {
        $soat = Soat::with(['vehiculo', 'proveedor'])->findOrFail($id);
        
        // Formatear las fechas para los campos de tipo date en HTML
        $soatData = $soat->toArray();
        $soatData['fecha_emision'] = $soat->fecha_emision ? $soat->fecha_emision->format('Y-m-d') : null;
        $soatData['fecha_vencimiento'] = $soat->fecha_vencimiento ? $soat->fecha_vencimiento->format('Y-m-d') : null;
        
        return response()->json($soatData);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'proveedor_id' => 'required|exists:proveedors,id',
            'numero_soat' => 'required|string|max:200',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_emision',
            'estado' => 'required|boolean',
        ]);
        
        $soat = Soat::findOrFail($id);
        $soat->update($request->only([
            'vehiculo_id', 'proveedor_id', 'numero_soat', 'fecha_emision', 'fecha_vencimiento', 'estado'
        ]));
        
        return response()->json(['success' => true, 'data' => $soat]);
    }

    public function destroy($id)
    {
        $soat = Soat::findOrFail($id);
        $soat->delete();
        return response()->json(['success' => true]);
    }
}
