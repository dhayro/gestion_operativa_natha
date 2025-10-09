<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Vehiculo;
use App\Models\AsignacionVehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AsignacionVehiculoController extends Controller
{
    /**
     * Get vehículos asignados a una cuadrilla para DataTables
     */
    public function getVehiculosAsignados(Request $request, $cuadrillaId)
    {
        if ($request->ajax()) {
            $asignaciones = AsignacionVehiculo::with(['vehiculo.tipoCombustible', 'empleado'])
                ->where('cuadrilla_id', $cuadrillaId)
                ->select(['id', 'vehiculo_id', 'empleado_id', 'fecha_asignacion', 'estado']);

            return DataTables::of($asignaciones)
                ->addIndexColumn()
                ->addColumn('vehiculo_nombre', function ($row) {
                    if ($row->vehiculo) {
                        return $row->vehiculo->nombre ?: ($row->vehiculo->marca . ' ' . $row->vehiculo->modelo);
                    }
                    return '';
                })
                ->addColumn('vehiculo_placa', function ($row) {
                    return $row->vehiculo ? $row->vehiculo->placa : '';
                })
                ->addColumn('vehiculo_tipo', function ($row) {
                    return $row->vehiculo && $row->vehiculo->tipoCombustible ? $row->vehiculo->tipoCombustible->nombre : '';
                })
                ->addColumn('vehiculo_marca_modelo', function ($row) {
                    if ($row->vehiculo) {
                        return $row->vehiculo->marca . ' ' . $row->vehiculo->modelo . ' (' . $row->vehiculo->year . ')';
                    }
                    return '';
                })
                ->addColumn('chofer_nombre', function ($row) {
                    if ($row->empleado) {
                        return $row->empleado->nombre . ' ' . $row->empleado->apellido;
                    }
                    return '<span class="text-muted">Sin asignar</span>';
                })
                ->addColumn('vehiculo_info', function ($row) {
                    if ($row->vehiculo) {
                        return $row->vehiculo->marca . ' ' . $row->vehiculo->nombre . ' - ' . $row->vehiculo->placa;
                    }
                    return '';
                })
                ->addColumn('vehiculo_color', function ($row) {
                    return $row->vehiculo ? $row->vehiculo->color : '';
                })
                ->addColumn('fecha_asignacion_formatted', function ($row) {
                    return $row->fecha_asignacion ? $row->fecha_asignacion->format('d/m/Y H:i') : '';
                })
                ->addColumn('estado_badge', function ($row) {
                    return $row->estado
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                })
                ->addColumn('action', function ($row) {
                    $nuevoEstado = $row->estado ? 'false' : 'true';
                    $textoAccion = $row->estado ? 'Desactivar' : 'Activar';
                    $iconoAccion = $row->estado ? 'eye-off' : 'eye';
                    
                    return '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="toggleEstadoAsignacionVehiculo(' . $row->id . ', \'' . $nuevoEstado . '\')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-' . $iconoAccion . ' me-2">
                                            ' . ($row->estado ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>' : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>') . '
                                        </svg>
                                        ' . $textoAccion . '
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="removeVehiculoFromCuadrilla(' . $row->id . ')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 me-2">
                                            <polyline points="3,6 5,6 21,6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2-2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                        Remover
                                    </a>
                                </div>
                            </div>';
                })
                ->rawColumns(['estado_badge', 'action', 'chofer_nombre'])
                ->make(true);
        }
    }

    /**
     * Asignar vehículo a cuadrilla
     */
    public function asignarVehiculo(Request $request)
    {
        $request->validate([
            'cuadrilla_id' => 'required|exists:cuadrillas,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'empleado_id' => 'nullable|exists:empleados,id',
            'fecha_asignacion' => 'nullable|date',
        ]);

        try {
            // Verificar si ya existe esta combinación exacta (cuadrilla + vehículo + empleado)
            $existeAsignacion = AsignacionVehiculo::where('cuadrilla_id', $request->cuadrilla_id)
                ->where('vehiculo_id', $request->vehiculo_id)
                ->where('empleado_id', $request->empleado_id)
                ->first();

            if ($existeAsignacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta combinación de cuadrilla, vehículo y chofer ya existe.'
                ], 422);
            }

            // Crear nueva asignación
            $asignacion = AsignacionVehiculo::create([
                'cuadrilla_id' => $request->cuadrilla_id,
                'vehiculo_id' => $request->vehiculo_id,
                'empleado_id' => $request->empleado_id, // Chofer asignado
                'fecha_asignacion' => $request->fecha_asignacion ?: Carbon::now('America/Lima'),
                'estado' => true
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Vehículo asignado correctamente a la cuadrilla.',
                'data' => $asignacion
            ]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar error de constraint de unicidad
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta combinación de cuadrilla, vehículo y chofer ya existe.'
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar vehículo: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado de asignación
     */
    public function toggleEstado(Request $request, $id)
    {
        try {
            $asignacion = AsignacionVehiculo::findOrFail($id);
            
            // Manejar conversión de estado (puede venir como string o boolean)
            $estadoValue = $request->input('estado');
            
            if (is_string($estadoValue)) {
                // Convertir string a boolean
                $nuevoEstado = $estadoValue === 'true' || $estadoValue === '1';
            } else {
                // Ya es boolean
                $nuevoEstado = (bool) $estadoValue;
            }
            
            $asignacion->estado = $nuevoEstado;
            $asignacion->save();

            return response()->json([
                'success' => true, 
                'message' => 'Estado actualizado correctamente',
                'data' => $asignacion,
                'nuevo_estado' => $asignacion->estado
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover vehículo de cuadrilla
     */
    public function removeVehiculo($id)
    {
        $asignacion = AsignacionVehiculo::findOrFail($id);
        $asignacion->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get vehículos disponibles para asignar (no están en la cuadrilla actualmente)
     */
    public function getVehiculosDisponibles(Request $request, $cuadrillaId)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 10;

        // Ya no excluimos vehículos asignados porque ahora pueden tener diferentes choferes
        // IDs de vehículos ya asignados a esta cuadrilla - YA NO SE USA
        // $vehiculosAsignados = AsignacionVehiculo::where('cuadrilla_id', $cuadrillaId)
        //     ->pluck('vehiculo_id')
        //     ->toArray();

        $query = Vehiculo::with(['tipoCombustible'])
            ->where('estado', true) // Solo vehículos activos
            // ->whereNotIn('id', $vehiculosAsignados) // REMOVIDO: Ya no excluir los ya asignados
            ->orderBy('marca');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('marca', 'LIKE', "%{$search}%")
                  ->orWhere('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('placa', 'LIKE', "%{$search}%")
                  ->orWhere('modelo', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $vehiculos = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $results = $vehiculos->map(function ($vehiculo) {
            return [
                'id' => $vehiculo->id,
                'text' => $vehiculo->marca . ' ' . $vehiculo->nombre . ' - ' . $vehiculo->placa,
                'modelo' => $vehiculo->modelo,
                'year' => $vehiculo->year,
                'color' => $vehiculo->color,
                'tipo_combustible' => $vehiculo->tipoCombustible ? $vehiculo->tipoCombustible->nombre : ''
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    /**
     * Get empleados disponibles para ser chofer
     */
    public function getEmpleadosChofer(Request $request, $cuadrillaId)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 10;

        // Obtener empleados de la cuadrilla
        $empleadosCuadrilla = \App\Models\CuadrillaEmpleado::where('cuadrilla_id', $cuadrillaId)
            ->where('estado', true)
            ->pluck('empleado_id')
            ->toArray();

        $query = \App\Models\Empleado::where('estado', true)
            ->whereIn('id', $empleadosCuadrilla) // Solo empleados de la cuadrilla
            ->orderBy('nombre');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('apellido', 'LIKE', "%{$search}%")
                  ->orWhere('dni', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $empleados = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $results = $empleados->map(function ($empleado) {
            return [
                'id' => $empleado->id,
                'text' => $empleado->nombre . ' ' . $empleado->apellido . ' - ' . $empleado->dni,
                'nombre' => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'dni' => $empleado->dni
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }
}