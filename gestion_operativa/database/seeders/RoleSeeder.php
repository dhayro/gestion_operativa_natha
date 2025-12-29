<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::updateOrCreate(
            ['nombre' => 'admin'],
            ['descripcion' => 'Administrador del sistema con acceso total', 'estado' => true]
        );

        $tecnicoRole = Role::updateOrCreate(
            ['nombre' => 'tecnico'],
            ['descripcion' => 'Técnico - Acceso a operaciones técnicas', 'estado' => true]
        );

        $operarioRole = Role::updateOrCreate(
            ['nombre' => 'operario'],
            ['descripcion' => 'Operario - Acceso limitado a operaciones', 'estado' => true]
        );

        $supervisorRole = Role::updateOrCreate(
            ['nombre' => 'supervisor'],
            ['descripcion' => 'Supervisor - Supervisión de operaciones', 'estado' => true]
        );

        // Asignar permisos al ADMIN (todos los permisos)
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Permisos para TÉCNICO
        $tecnicoPermisos = Permission::whereIn('nombre', [
            'ver_dashboard',
            'ver_empleados',
            'ver_materiales',
            'ver_stock_materiales',
            'ver_vehiculos',
            'ver_medidores',
            'ver_cuadrillas',
            'ver_papeletas',
            'crear_papeleta',
            'editar_papeleta',
            'ver_fichas_actividad',
            'crear_ficha_actividad',
            'editar_ficha_actividad',
            'ver_tipos_propiedad',
            'ver_construcciones',
            'ver_usos',
            'ver_situaciones',
            'ver_servicios_electricos',
            'ver_suministros',
            'ver_neas',
            'crear_nea',
            'editar_nea',
            'ver_consultas',
        ])->pluck('id');
        
        $tecnicoRole->permissions()->sync($tecnicoPermisos);

        // Permisos para OPERARIO
        $operarioPermisos = Permission::whereIn('nombre', [
            'ver_dashboard',
            'ver_materiales',
            'ver_stock_materiales',
            'ver_medidores',
            'ver_papeletas',
            'ver_fichas_actividad',
            'ver_tipos_propiedad',
            'ver_construcciones',
            'ver_usos',
            'ver_situaciones',
            'ver_servicios_electricos',
            'ver_suministros',
            'ver_consultas',
        ])->pluck('id');
        
        $operarioRole->permissions()->sync($operarioPermisos);

        // Permisos para SUPERVISOR
        $supervisorPermisos = Permission::whereIn('nombre', [
            'ver_dashboard',
            'ver_empleados',
            'ver_materiales',
            'ver_stock_materiales',
            'ver_vehiculos',
            'ver_medidores',
            'ver_cuadrillas',
            'ver_papeletas',
            'crear_papeleta',
            'editar_papeleta',
            'ver_fichas_actividad',
            'crear_ficha_actividad',
            'editar_ficha_actividad',
            'ver_tipos_propiedad',
            'ver_construcciones',
            'ver_usos',
            'ver_situaciones',
            'ver_servicios_electricos',
            'ver_suministros',
            'ver_neas',
            'ver_pecosas',
            'ver_consultas',
        ])->pluck('id');
        
        $supervisorRole->permissions()->sync($supervisorPermisos);

        $this->command->info('Roles creados/actualizados exitosamente');
        $this->command->info('✓ Admin - Acceso total');
        $this->command->info('✓ Técnico - Acceso a maestros, operaciones y fichas');
        $this->command->info('✓ Operario - Acceso limitado a fichas y consultas');
        $this->command->info('✓ Supervisor - Acceso completo a operaciones');
    }
}
