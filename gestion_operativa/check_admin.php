<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "VERIFICAR PERMISOS DEL USUARIO ADMIN\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$admin = User::find(1);
echo "Usuario: " . $admin->name . " ({$admin->email})\n";
echo "ID: " . $admin->id . "\n";
echo "Estado: " . ($admin->estado ? 'Activo' : 'Inactivo') . "\n\n";

echo "ROLES ASIGNADOS:\n";
$roles = $admin->roles;
foreach($roles as $role) {
    echo "  - {$role->nombre}\n";
}

echo "\nPERMISOS DEL USUARIO (via roles):\n";
$permisos = $admin->getPermissions();
foreach($permisos as $permiso) {
    echo "  - {$permiso->nombre}\n";
}

echo "\nVERIFICAR PERMISOS ESPECÍFICOS:\n";
echo "administrar_roles: " . ($admin->hasPermission('administrar_roles') ? 'SI' : 'NO') . "\n";
echo "administrar_permisos: " . ($admin->hasPermission('administrar_permisos') ? 'SI' : 'NO') . "\n";

echo "\nVERIFICAR ROLES:\n";
echo "Es admin: " . ($admin->hasRole('admin') ? 'SI' : 'NO') . "\n";
