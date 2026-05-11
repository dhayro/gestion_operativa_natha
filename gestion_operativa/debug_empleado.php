<?php

// Script para debuguear la búsqueda de empleados

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Empleado;
use App\Models\CuadrillaEmpleado;

$cuadrillaId = 13;
$search = 'd';
$fichaId = null;

echo "=== DEBUGUEO DE BÚSQUEDA ===\n\n";

// 1. Buscar empleados con la letra 'd'
echo "1. Empleados con nombre/apellido/dni contiendo '$search':\n";
$todoEmpleados = Empleado::where(function($q) use ($search) {
    $q->where('nombre', 'LIKE', "%{$search}%")
      ->orWhere('apellido', 'LIKE', "%{$search}%")
      ->orWhere('dni', 'LIKE', "%{$search}%");
})->get();

foreach ($todoEmpleados as $e) {
    echo "  - ID: {$e->id}, Nombre: {$e->nombre} {$e->apellido}, DNI: {$e->dni}, Estado: " . ($e->estado ? 'ACTIVO' : 'INACTIVO') . "\n";
}

echo "\n2. Empleados ACTIVOS con '$search':\n";
$empleadosActivos = Empleado::where('estado', true)
    ->where(function($q) use ($search) {
        $q->where('nombre', 'LIKE', "%{$search}%")
          ->orWhere('apellido', 'LIKE', "%{$search}%")
          ->orWhere('dni', 'LIKE', "%{$search}%");
    })->get();

foreach ($empleadosActivos as $e) {
    echo "  - ID: {$e->id}, Nombre: {$e->nombre} {$e->apellido}, DNI: {$e->dni}\n";
}

echo "\n3. Empleados asignados a la cuadrilla $cuadrillaId (activos):\n";
$empleadosEnCuadrilla = CuadrillaEmpleado::where('cuadrilla_id', $cuadrillaId)
    ->where('estado', 1)
    ->with('empleado')
    ->get();

foreach ($empleadosEnCuadrilla as $ce) {
    $e = $ce->empleado;
    if ($e) {
        echo "  - ID: {$e->id}, Nombre: {$e->nombre} {$e->apellido}, DNI: {$e->dni}, Estado: " . ($e->estado ? 'ACTIVO' : 'INACTIVO') . "\n";
    }
}

echo "\n4. Empleados QUE COINCIDEN CON '$search' Y ESTÁN EN CUADRILLA $cuadrillaId:\n";
$empleadosEnCuadrillaIds = CuadrillaEmpleado::where('cuadrilla_id', $cuadrillaId)
    ->where('estado', 1)
    ->pluck('empleado_id')
    ->toArray();

$empleadosDisponibles = Empleado::whereIn('id', $empleadosEnCuadrillaIds)
    ->where('estado', true)
    ->where(function($q) use ($search) {
        $q->where('nombre', 'LIKE', "%{$search}%")
          ->orWhere('apellido', 'LIKE', "%{$search}%")
          ->orWhere('dni', 'LIKE', "%{$search}%");
    })->get();

foreach ($empleadosDisponibles as $e) {
    echo "  - ID: {$e->id}, Nombre: {$e->nombre} {$e->apellido}, DNI: {$e->dni}\n";
}

echo "\n5. IDs de empleados en cuadrilla $cuadrillaId: " . json_encode($empleadosEnCuadrillaIds) . "\n";

echo "\n=== FIN DEL DEBUGUEO ===\n";
?>
