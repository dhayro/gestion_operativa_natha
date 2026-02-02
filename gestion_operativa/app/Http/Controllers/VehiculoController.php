<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehiculoController extends Controller
{
    public function index()
    {
        return view('admin.vehiculos.index', [
            'catName' => 'maestros',
            'title' => 'Gestión de Vehículos',
            'breadcrumbs' => ['Configuración', 'Vehículos'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $vehiculos = Vehiculo::with(['tipoCombustible', 'soatActivo'])
                ->select(['id', 'marca', 'nombre', 'year', 'modelo', 'color', 'placa', 'tipo_combustible_id', 'estado']);
            
            return DataTables::of($vehiculos)
                ->addIndexColumn()
                ->addColumn('tipo_combustible_nombre', function ($row) {
                    return $row->tipoCombustible ? $row->tipoCombustible->nombre : 'N/A';
                })
                ->addColumn('soat_vencimiento', function ($row) {
                    if ($row->soatActivo && $row->soatActivo->fecha_vencimiento) {
                        $vencimiento = Carbon::parse($row->soatActivo->fecha_vencimiento);
                        $diasRestantes = Carbon::now('America/Lima')->diffInDays($vencimiento, false);
                        
                        if ($diasRestantes < 0) {
                            return '<span class="badge badge-danger">' . $vencimiento->format('d/m/Y') . ' (Vencido)</span>';
                        } elseif ($diasRestantes <= 30) {
                            return '<span class="badge badge-warning">' . $vencimiento->format('d/m/Y') . ' (Por vencer)</span>';
                        } else {
                            return '<span class="badge badge-success">' . $vencimiento->format('d/m/Y') . '</span>';
                        }
                    }
                    return '<span class="badge badge-secondary">Sin SOAT</span>';
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="gestionarSoat(' . $row->id . ', \'' . addslashes($row->marca . ' ' . $row->nombre . ' - ' . $row->placa) . '\')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield me-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                        Gestionar SOAT
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editVehiculo(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        Editar
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteVehiculo(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 me-2"><polyline points="3,6 5,6 21,6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                        Eliminar
                                    </a>
                                </div>
                            </div>';
                })
                ->rawColumns(['soat_vencimiento', 'estado_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'marca' => 'required|string|max:100',
            'nombre' => 'required|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'modelo' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'placa' => 'required|string|max:20|unique:vehiculos,placa',
            'tipo_combustible_id' => 'required|exists:tipo_combustibles,id',
            'estado' => 'required|boolean',
        ]);
        
        $vehiculo = Vehiculo::create($request->only([
            'marca', 'nombre', 'year', 'modelo', 'color', 'placa', 'tipo_combustible_id', 'estado'
        ]));
        
        return response()->json(['success' => true, 'data' => $vehiculo]);
    }

    public function show($id)
    {
        $vehiculo = Vehiculo::with(['tipoCombustible', 'soatActivo'])->findOrFail($id);
        return response()->json($vehiculo);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'marca' => 'required|string|max:100',
            'nombre' => 'required|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'modelo' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'placa' => 'required|string|max:20|unique:vehiculos,placa,' . $id,
            'tipo_combustible_id' => 'required|exists:tipo_combustibles,id',
            'estado' => 'required|boolean',
        ]);
        
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->update($request->only([
            'marca', 'nombre', 'year', 'modelo', 'color', 'placa', 'tipo_combustible_id', 'estado'
        ]));
        
        return response()->json(['success' => true, 'data' => $vehiculo]);
    }

    public function destroy($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->delete();
        return response()->json(['success' => true]);
    }

    public function getVehiculosForSelect()
    {
        try {
            $vehiculos = Vehiculo::select(['id', 'marca', 'nombre', 'placa'])
                ->where('estado', true)
                ->orderBy('marca')
                ->orderBy('nombre')
                ->get()
                ->map(function ($vehiculo) {
                    return [
                        'id' => $vehiculo->id,
                        'text' => $vehiculo->marca . ' ' . $vehiculo->nombre . ' - ' . $vehiculo->placa
                    ];
                });

            return response()->json($vehiculos);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los vehículos'
            ], 500);
        }
    }

    /**
     * Obtener SOATs de un vehículo específico
     */
    public function getSoats($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            $soats = $vehiculo->soats()
                ->with('proveedor')
                ->orderBy('fecha_vencimiento', 'desc')
                ->get()
                ->map(function ($soat) {
                    return [
                        'id' => $soat->id,
                        'numero_soat' => $soat->numero_soat,
                        'fecha_emision' => $soat->fecha_emision,
                        'fecha_vencimiento' => $soat->fecha_vencimiento,
                        'estado' => $soat->estado,
                        'proveedor' => $soat->proveedor ? [
                            'id' => $soat->proveedor->id,
                            'nombre' => $soat->proveedor->razon_social
                        ] : null
                    ];
                });

            return response()->json($soats);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los SOATs del vehículo'
            ], 500);
        }
    }

    /**
     * Obtener SOATs de un vehículo específico formateado para DataTables
     */
    public function getSoatsData($id, Request $request)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            
            if ($request->ajax()) {
                $soats = $vehiculo->soats()
                    ->with('proveedor')
                    ->select(['id', 'numero_soat', 'fecha_emision', 'fecha_vencimiento', 'estado', 'proveedor_id']);
                
                return DataTables::of($soats)
                    ->addIndexColumn()
                    ->addColumn('proveedor_nombre', function ($row) {
                        return $row->proveedor ? $row->proveedor->razon_social : 'N/A';
                    })
                    ->addColumn('fecha_emision_formatted', function ($row) {
                        return $row->fecha_emision ? Carbon::parse($row->fecha_emision)->format('d/m/Y') : '';
                    })
                    ->addColumn('fecha_vencimiento_formatted', function ($row) {
                        if ($row->fecha_vencimiento) {
                            $vencimiento = Carbon::parse($row->fecha_vencimiento);
                            $diasRestantes = Carbon::now('America/Lima')->diffInDays($vencimiento, false);
                            
                            if ($diasRestantes < 0) {
                                return '<span class="badge badge-danger">' . $vencimiento->format('d/m/Y') . ' (Vencido)</span>';
                            } elseif ($diasRestantes <= 30) {
                                return '<span class="badge badge-warning">' . $vencimiento->format('d/m/Y') . ' (Por vencer)</span>';
                            } else {
                                return '<span class="badge badge-success">' . $vencimiento->format('d/m/Y') . '</span>';
                            }
                        }
                        return '';
                    })
                    ->addColumn('estado_badge', function ($row) {
                        return $row->estado
                            ? '<span class="badge badge-success">Activo</span>'
                            : '<span class="badge badge-secondary">Inactivo</span>';
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="dropdown">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="editSoatVehiculo(' . $row->id . ')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            Editar
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="deleteSoatVehiculo(' . $row->id . ')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 me-2"><polyline points="3,6 5,6 21,6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2 2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            Eliminar
                                        </a>
                                    </div>
                                </div>';
                    })
                    ->rawColumns(['fecha_vencimiento_formatted', 'estado_badge', 'action'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los SOATs del vehículo'
            ], 500);
        }
    }
}
