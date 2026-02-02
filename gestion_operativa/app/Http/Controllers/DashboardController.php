<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Papeleta;
use App\Models\FichaActividad;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Verificar si es Admin o Supervisor
        $isAdminOrSupervisor = $user->hasRole('admin') || $user->hasRole('supervisor');
        
        if ($isAdminOrSupervisor) {
            // ADMIN/SUPERVISOR: Ver todas las cuadrillas y estadísticas globales
            $cuadrillas = Cuadrilla::where('estado', true)
                ->with([
                    'empleadosActivos',
                    'vehiculosActivos'
                ])
                ->get();

            $totalEmpleados = Empleado::count();
            $totalCuadrillas = Cuadrilla::where('estado', true)->count();
            $totalVehiculos = \App\Models\Vehiculo::count();
            
            $papeletasHoy = Papeleta::whereDate('created_at', today())->count();
            $papeletasActivas = Papeleta::where('estado', true)
                ->where(function($query) {
                    $query->whereNull('fecha_hora_llegada')
                        ->orWhere('fecha_hora_salida', null);
                })
                ->count();
            
            $fichasHoy = FichaActividad::whereDate('created_at', today())->count();
            $fichasActivas = FichaActividad::where('estado', true)->count();
        } else {
            // OPERARIO/TÉCNICO: Solo ver su cuadrilla y datos personales
            $empleado = Empleado::where('email', $user->email)->first();
            
            if ($empleado) {
                // Obtener solo la cuadrilla del usuario
                $cuadrillasDelUsuario = $empleado->cuadrillaEmpleados()
                    ->where('estado', true)
                    ->with('cuadrilla')
                    ->get()
                    ->pluck('cuadrilla')
                    ->filter(function($cuadrilla) {
                        return $cuadrilla && $cuadrilla->estado;
                    });
                
                $cuadrillas = $cuadrillasDelUsuario;
                
                // Estadísticas personales
                $totalEmpleados = 1; // Solo el usuario
                $totalCuadrillas = count($cuadrillasDelUsuario);
                
                // Contar vehículos de sus cuadrillas
                $cuadrillaIds = $cuadrillasDelUsuario->pluck('id')->toArray();
                $totalVehiculos = \App\Models\AsignacionVehiculo::whereIn('cuadrilla_id', $cuadrillaIds)
                    ->where('estado', true)
                    ->distinct('vehiculo_id')
                    ->count();
                
                // Papeletas del usuario de hoy
                $papeletasHoy = Papeleta::whereDate('created_at', today())
                    ->where('usuario_creacion_id', $user->id)
                    ->count();
                
                // Papeletas activas donde el usuario está asignado
                $papeletasActivas = Papeleta::where('estado', true)
                    ->where(function($query) use ($user, $empleado, $cuadrillaIds) {
                        // Papeletas que creó el usuario
                        $query->where('usuario_creacion_id', $user->id)
                        // O papeletas de sus cuadrillas
                        ->orWhereHas('asignacionVehiculo.cuadrilla', function($q) use ($cuadrillaIds) {
                            $q->whereIn('id', $cuadrillaIds);
                        })
                        // O papeletas donde es el chofer
                        ->orWhere('chofer_id', $empleado->id);
                    })
                    ->where(function($query) {
                        $query->whereNull('fecha_hora_llegada')
                            ->orWhere('fecha_hora_salida', null);
                    })
                    ->count();
                
                // Fichas de actividad del usuario
                $fichasHoy = FichaActividad::whereDate('created_at', today())
                    ->where('usuario_creacion_id', $user->id)
                    ->count();
                    
                $fichasActivas = FichaActividad::where('estado', true)
                    ->where('usuario_creacion_id', $user->id)
                    ->count();
            } else {
                // Si no hay empleado asociado, mostrar vacío
                $cuadrillas = collect();
                $totalEmpleados = 0;
                $totalCuadrillas = 0;
                $totalVehiculos = 0;
                $papeletasHoy = 0;
                $papeletasActivas = 0;
                $fichasHoy = 0;
                $fichasActivas = 0;
            }
        }

        return view('admin/dashboard/index', [
            'catName' => 'dashboard',
            'title' => 'Dashboard',
            'breadcrumbs' => ['Dashboard'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'isAdminOrSupervisor' => $isAdminOrSupervisor,
            'cuadrillas' => $cuadrillas,
            'totalEmpleados' => $totalEmpleados,
            'totalCuadrillas' => $totalCuadrillas,
            'totalVehiculos' => $totalVehiculos,
            'papeletasHoy' => $papeletasHoy,
            'papeletasActivas' => $papeletasActivas,
            'fichasHoy' => $fichasHoy,
            'fichasActivas' => $fichasActivas,
        ]);
    }
}
