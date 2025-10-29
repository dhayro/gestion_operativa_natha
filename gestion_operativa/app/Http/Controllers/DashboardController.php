<?php

namespace App\Http\Controllers;

use App\Models\Cuadrilla;
use App\Models\Papeleta;
use App\Models\FichaActividad;
use App\Models\Empleado;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener cuadrillas activas con sus empleados y vehículos
        $cuadrillas = Cuadrilla::where('estado', true)
            ->with([
                'empleadosActivos',
                'vehiculosActivos'
            ])
            ->get();

        // Obtener estadísticas generales
        $totalEmpleados = Empleado::count();
        $totalCuadrillas = Cuadrilla::where('estado', true)->count();
        $totalVehiculos = \App\Models\Vehiculo::count();
        
        // Papeletas pendientes del día
        $papeletasHoy = Papeleta::whereDate('created_at', today())->count();
        // Papeletas activas (no anuladas y no completadas)
        $papeletasActivas = Papeleta::where('estado', true)
            ->where(function($query) {
                $query->whereNull('fecha_hora_llegada')
                    ->orWhere('fecha_hora_salida', null);
            })
            ->count();
        
        // Fichas de actividad pendientes
        $fichasHoy = FichaActividad::whereDate('created_at', today())->count();
        $fichasActivas = FichaActividad::where('estado', true)->count();

        return view('admin/dashboard/index', [
            'catName' => 'dashboard',
            'title' => 'Dashboard',
            'breadcrumbs' => ['Dashboard'],
            'scrollspy' => 0,
            'simplePage' => 0,
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
