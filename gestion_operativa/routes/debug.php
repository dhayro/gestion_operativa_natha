<?php

use App\Models\Cuadrilla;
use App\Models\CuadrillaEmpleado;
use Illuminate\Support\Facades\Route;

Route::get('/debug/cuadrillas', function () {
    echo "<h2>Debug Cuadrillas</h2>";
    
    // 1. Verificar cuadrillas
    $cuadrillas = Cuadrilla::all();
    echo "<h3>Total cuadrillas: " . $cuadrillas->count() . "</h3>";
    
    // 2. Verificar asignaciones
    $asignaciones = CuadrillaEmpleado::all();
    echo "<h3>Total asignaciones: " . $asignaciones->count() . "</h3>";
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr><th>Cuadrilla ID</th><th>Nombre</th><th>Asignaciones Totales</th><th>Asignaciones Activas</th><th>WithCount Result</th></tr>";
    
    foreach ($cuadrillas as $cuadrilla) {
        // Contar asignaciones totales
        $totalAsignaciones = $cuadrilla->cuadrillaEmpleados()->count();
        
        // Contar asignaciones activas
        $asignacionesActivas = $cuadrilla->cuadrillaEmpleados()->where('estado', true)->count();
        
        // Probar withCount
        $cuadrillaWithCount = Cuadrilla::withCount([
            'cuadrillaEmpleados as empleados_count' => function ($query) {
                $query->where('estado', true);
            }
        ])->find($cuadrilla->id);
        
        echo "<tr>";
        echo "<td>" . $cuadrilla->id . "</td>";
        echo "<td>" . $cuadrilla->nombre . "</td>";
        echo "<td>" . $totalAsignaciones . "</td>";
        echo "<td>" . $asignacionesActivas . "</td>";
        echo "<td>" . ($cuadrillaWithCount->empleados_count ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 3. Mostrar datos de la tabla cuadrillas_empleados
    echo "<h3>Datos de cuadrillas_empleados:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Cuadrilla ID</th><th>Empleado ID</th><th>Fecha Asignación</th><th>Estado</th></tr>";
    
    foreach ($asignaciones as $asignacion) {
        echo "<tr>";
        echo "<td>" . $asignacion->id . "</td>";
        echo "<td>" . $asignacion->cuadrilla_id . "</td>";
        echo "<td>" . $asignacion->empleado_id . "</td>";
        echo "<td>" . $asignacion->fecha_asignacion . "</td>";
        echo "<td>" . ($asignacion->estado ? 'Activo' : 'Inactivo') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
});