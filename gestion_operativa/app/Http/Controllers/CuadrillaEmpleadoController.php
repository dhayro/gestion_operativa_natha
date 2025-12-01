<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Empleado;
use App\Models\CuadrillaEmpleado;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CuadrillaEmpleadoController extends Controller
{
    /**
     * Get empleados asignados a una cuadrilla para DataTables
     */
    public function getEmpleadosAsignados(Request $request, $cuadrillaId)
    {
        if ($request->ajax()) {
            $asignaciones = CuadrillaEmpleado::with(['empleado.cargo', 'empleado.area'])
                ->where('cuadrilla_id', $cuadrillaId)
                ->select(['id', 'empleado_id', 'fecha_asignacion', 'estado']);

            return DataTables::of($asignaciones)
                ->addIndexColumn()
                ->addColumn('empleado_nombre', function ($row) {
                    return $row->empleado ? $row->empleado->nombre . ' ' . $row->empleado->apellido : '';
                })
                ->addColumn('empleado_dni', function ($row) {
                    return $row->empleado ? $row->empleado->dni : '';
                })
                ->addColumn('empleado_cargo', function ($row) {
                    return $row->empleado && $row->empleado->cargo ? $row->empleado->cargo->nombre : '';
                })
                ->addColumn('empleado_area', function ($row) {
                    return $row->empleado && $row->empleado->area ? $row->empleado->area->nombre : '';
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
                    
                    return '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="toggleEstadoAsignacion(' . $row->id . ', \'' . $nuevoEstado . '\')">' . $textoAccion . '</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="removeEmpleadoFromCuadrilla(' . $row->id . ')">Remover</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
    }

    /**
     * Asignar empleado a cuadrilla
     */
    public function asignarEmpleado(Request $request)
    {
        $request->validate([
            'cuadrilla_id' => 'required|exists:cuadrillas,id',
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_asignacion' => 'nullable|date',
        ]);

        try {
            // Verificar si el empleado ya está asignado a esta cuadrilla (incluyendo inactivos)
            $existeAsignacion = CuadrillaEmpleado::where('cuadrilla_id', $request->cuadrilla_id)
                ->where('empleado_id', $request->empleado_id)
                ->first();

            if ($existeAsignacion) {
                if ($existeAsignacion->estado) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El empleado ya está asignado activamente a esta cuadrilla.'
                    ], 422);
                } else {
                    // Si existe pero está inactivo, reactivarlo
                    $existeAsignacion->estado = true;
                    $existeAsignacion->fecha_asignacion = $request->fecha_asignacion ?: now('America/Lima');
                    $existeAsignacion->save();
                    
                    return response()->json([
                        'success' => true, 
                        'message' => 'Empleado reactivado en la cuadrilla correctamente.',
                        'data' => $existeAsignacion
                    ]);
                }
            }

            // Crear nueva asignación
            $asignacion = CuadrillaEmpleado::create([
                'cuadrilla_id' => $request->cuadrilla_id,
                'empleado_id' => $request->empleado_id,
                'fecha_asignacion' => $request->fecha_asignacion ?: now('America/Lima'),
                'estado' => true
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Empleado asignado correctamente a la cuadrilla.',
                'data' => $asignacion
            ]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar error de constraint de unicidad
            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'message' => 'El empleado ya está asignado a esta cuadrilla.'
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar empleado: ' . $e->getMessage()
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
            $asignacion = CuadrillaEmpleado::findOrFail($id);
            
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
     * Remover empleado de cuadrilla
     */
    public function removeEmpleado($id)
    {
        $asignacion = CuadrillaEmpleado::findOrFail($id);
        $asignacion->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get empleados disponibles para asignar (no están en la cuadrilla actualmente)
     */
    public function getEmpleadosDisponibles(Request $request, $cuadrillaId)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $fichaId = $request->get('ficha_id', null);
        $perPage = 10;

        // Solo excluir empleados ya agregados a ESTA FICHA (no a la cuadrilla en general)
        $empleadosEnFicha = [];
        if ($fichaId) {
            // Obtener los CuadrillaEmpleado IDs agregados a la ficha
            $fichaEmpleados = \App\Models\FichaActividadEmpleado::where('ficha_actividad_id', $fichaId)
                ->get()
                ->pluck('cuadrilla_empleado_id')
                ->toArray();
            
            // Ahora obtener los empleado_id de esos cuadrilla_empleado_id
            if (!empty($fichaEmpleados)) {
                $empleadosEnFicha = CuadrillaEmpleado::whereIn('id', $fichaEmpleados)
                    ->pluck('empleado_id')
                    ->toArray();
            }
        }

        // Obtener empleados disponibles para esta cuadrilla
        // (todos los empleados activos excepto los que ya están asignados activamente a esta cuadrilla)
        $query = Empleado::with(['cargo', 'area'])
            ->where('empleados.estado', true) // Solo empleados activos
            ->whereNotIn('empleados.id', function($q) use ($cuadrillaId, $empleadosEnFicha) {
                // Excluir empleados ya asignados activamente a esta cuadrilla
                $q->select('empleado_id')
                  ->from('cuadrillas_empleados')
                  ->where('cuadrilla_id', $cuadrillaId)
                  ->where('estado', 1);
            })
            ->whereNotIn('empleados.id', $empleadosEnFicha) // Excluir también los ya en la ficha
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
                'text' => $empleado->nombre . ' ' . $empleado->apellido . ' (' . $empleado->dni . ')',
                'cargo' => $empleado->cargo ? $empleado->cargo->nombre : '',
                'area' => $empleado->area ? $empleado->area->nombre : ''
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $perPage) < $total
            ]
        ]);
    }

    public function select()
    {
        $cuadrillaEmpleados = CuadrillaEmpleado::with(['cuadrilla', 'empleado'])
            ->where('estado', 1)
            ->orderBy('cuadrilla_id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'cuadrilla_nombre' => $item->cuadrilla ? $item->cuadrilla->nombre : 'S/C',
                    'empleado_nombre' => $item->empleado ? $item->empleado->nombre . ' ' . $item->empleado->apellido : 'N/A'
                ];
            });

        return response()->json($cuadrillaEmpleados);
    }
}