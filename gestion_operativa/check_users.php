<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "USUARIOS EN EL SISTEMA (Ordenados por fecha de creación)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$users = User::with('roles')->orderBy('created_at')->get();

foreach($users as $user) {
    $roles = $user->roles->pluck('nombre')->join(', ');
    $estado = $user->estado ? '✓ Activo' : '✗ Inactivo';
    
    echo sprintf(
        "ID: %d | %s\nEmail: %s\nRol: %s\nCreado: %s | %s\n",
        $user->id,
        $user->name,
        $user->email,
        $roles ?: 'Sin rol asignado',
        $user->created_at,
        $estado
    );
    echo "────────────────────────────────────────────────────\n\n";
}

echo "Total de usuarios: " . $users->count() . "\n";
