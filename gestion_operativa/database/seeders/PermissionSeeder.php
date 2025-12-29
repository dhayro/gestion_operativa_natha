<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // Dashboard
            ['nombre' => 'ver_dashboard', 'descripcion' => 'Ver dashboard', 'modulo' => 'dashboard'],
            
            // Maestros de Sistema
            ['nombre' => 'ver_cargos', 'descripcion' => 'Ver cargos', 'modulo' => 'cargos'],
            ['nombre' => 'crear_cargo', 'descripcion' => 'Crear cargo', 'modulo' => 'cargos'],
            ['nombre' => 'editar_cargo', 'descripcion' => 'Editar cargo', 'modulo' => 'cargos'],
            ['nombre' => 'eliminar_cargo', 'descripcion' => 'Eliminar cargo', 'modulo' => 'cargos'],
            
            ['nombre' => 'ver_areas', 'descripcion' => 'Ver áreas', 'modulo' => 'areas'],
            ['nombre' => 'crear_area', 'descripcion' => 'Crear área', 'modulo' => 'areas'],
            ['nombre' => 'editar_area', 'descripcion' => 'Editar área', 'modulo' => 'areas'],
            ['nombre' => 'eliminar_area', 'descripcion' => 'Eliminar área', 'modulo' => 'areas'],
            
            ['nombre' => 'ver_empleados', 'descripcion' => 'Ver empleados', 'modulo' => 'empleados'],
            ['nombre' => 'crear_empleado', 'descripcion' => 'Crear empleado', 'modulo' => 'empleados'],
            ['nombre' => 'editar_empleado', 'descripcion' => 'Editar empleado', 'modulo' => 'empleados'],
            ['nombre' => 'eliminar_empleado', 'descripcion' => 'Eliminar empleado', 'modulo' => 'empleados'],
            
            // Maestros de Negocio
            ['nombre' => 'ver_ubigeo', 'descripcion' => 'Ver ubigeo', 'modulo' => 'ubigeo'],
            ['nombre' => 'crear_ubigeo', 'descripcion' => 'Crear ubigeo', 'modulo' => 'ubigeo'],
            ['nombre' => 'editar_ubigeo', 'descripcion' => 'Editar ubigeo', 'modulo' => 'ubigeo'],
            ['nombre' => 'eliminar_ubigeo', 'descripcion' => 'Eliminar ubigeo', 'modulo' => 'ubigeo'],
            
            ['nombre' => 'ver_categorias', 'descripcion' => 'Ver categorías', 'modulo' => 'categorias'],
            ['nombre' => 'crear_categoria', 'descripcion' => 'Crear categoría', 'modulo' => 'categorias'],
            ['nombre' => 'editar_categoria', 'descripcion' => 'Editar categoría', 'modulo' => 'categorias'],
            ['nombre' => 'eliminar_categoria', 'descripcion' => 'Eliminar categoría', 'modulo' => 'categorias'],
            
            ['nombre' => 'ver_unidades_medida', 'descripcion' => 'Ver unidades de medida', 'modulo' => 'unidades'],
            ['nombre' => 'crear_unidad_medida', 'descripcion' => 'Crear unidad de medida', 'modulo' => 'unidades'],
            ['nombre' => 'editar_unidad_medida', 'descripcion' => 'Editar unidad de medida', 'modulo' => 'unidades'],
            ['nombre' => 'eliminar_unidad_medida', 'descripcion' => 'Eliminar unidad de medida', 'modulo' => 'unidades'],
            
            ['nombre' => 'ver_materiales', 'descripcion' => 'Ver materiales', 'modulo' => 'materiales'],
            ['nombre' => 'crear_material', 'descripcion' => 'Crear material', 'modulo' => 'materiales'],
            ['nombre' => 'editar_material', 'descripcion' => 'Editar material', 'modulo' => 'materiales'],
            ['nombre' => 'eliminar_material', 'descripcion' => 'Eliminar material', 'modulo' => 'materiales'],
            
            ['nombre' => 'ver_stock_materiales', 'descripcion' => 'Ver stock de materiales', 'modulo' => 'stock'],
            
            ['nombre' => 'ver_proveedores', 'descripcion' => 'Ver proveedores', 'modulo' => 'proveedores'],
            ['nombre' => 'crear_proveedor', 'descripcion' => 'Crear proveedor', 'modulo' => 'proveedores'],
            ['nombre' => 'editar_proveedor', 'descripcion' => 'Editar proveedor', 'modulo' => 'proveedores'],
            ['nombre' => 'eliminar_proveedor', 'descripcion' => 'Eliminar proveedor', 'modulo' => 'proveedores'],
            
            // Maestros de Activos
            ['nombre' => 'ver_vehiculos', 'descripcion' => 'Ver vehículos', 'modulo' => 'vehiculos'],
            ['nombre' => 'crear_vehiculo', 'descripcion' => 'Crear vehículo', 'modulo' => 'vehiculos'],
            ['nombre' => 'editar_vehiculo', 'descripcion' => 'Editar vehículo', 'modulo' => 'vehiculos'],
            ['nombre' => 'eliminar_vehiculo', 'descripcion' => 'Eliminar vehículo', 'modulo' => 'vehiculos'],
            
            ['nombre' => 'ver_combustibles', 'descripcion' => 'Ver combustibles', 'modulo' => 'combustibles'],
            ['nombre' => 'crear_combustible', 'descripcion' => 'Crear combustible', 'modulo' => 'combustibles'],
            ['nombre' => 'editar_combustible', 'descripcion' => 'Editar combustible', 'modulo' => 'combustibles'],
            ['nombre' => 'eliminar_combustible', 'descripcion' => 'Eliminar combustible', 'modulo' => 'combustibles'],
            
            ['nombre' => 'ver_soats', 'descripcion' => 'Ver SOATs', 'modulo' => 'soats'],
            ['nombre' => 'crear_soat', 'descripcion' => 'Crear SOAT', 'modulo' => 'soats'],
            ['nombre' => 'editar_soat', 'descripcion' => 'Editar SOAT', 'modulo' => 'soats'],
            ['nombre' => 'eliminar_soat', 'descripcion' => 'Eliminar SOAT', 'modulo' => 'soats'],
            
            ['nombre' => 'ver_medidores', 'descripcion' => 'Ver medidores', 'modulo' => 'medidores'],
            ['nombre' => 'crear_medidor', 'descripcion' => 'Crear medidor', 'modulo' => 'medidores'],
            ['nombre' => 'editar_medidor', 'descripcion' => 'Editar medidor', 'modulo' => 'medidores'],
            ['nombre' => 'eliminar_medidor', 'descripcion' => 'Eliminar medidor', 'modulo' => 'medidores'],
            
            // Procesos y Servicios
            ['nombre' => 'ver_tipos_actividad', 'descripcion' => 'Ver tipos de actividad', 'modulo' => 'tipos_actividad'],
            ['nombre' => 'crear_tipo_actividad', 'descripcion' => 'Crear tipo de actividad', 'modulo' => 'tipos_actividad'],
            ['nombre' => 'editar_tipo_actividad', 'descripcion' => 'Editar tipo de actividad', 'modulo' => 'tipos_actividad'],
            ['nombre' => 'eliminar_tipo_actividad', 'descripcion' => 'Eliminar tipo de actividad', 'modulo' => 'tipos_actividad'],
            
            ['nombre' => 'ver_comprobantes', 'descripcion' => 'Ver comprobantes', 'modulo' => 'comprobantes'],
            ['nombre' => 'ver_neas', 'descripcion' => 'Ver NEAs', 'modulo' => 'neas'],
            ['nombre' => 'crear_nea', 'descripcion' => 'Crear NEA', 'modulo' => 'neas'],
            ['nombre' => 'editar_nea', 'descripcion' => 'Editar NEA', 'modulo' => 'neas'],
            
            ['nombre' => 'ver_pecosas', 'descripcion' => 'Ver PECOSAs', 'modulo' => 'pecosas'],
            ['nombre' => 'crear_pecosa', 'descripcion' => 'Crear PECOSA', 'modulo' => 'pecosas'],
            ['nombre' => 'editar_pecosa', 'descripcion' => 'Editar PECOSA', 'modulo' => 'pecosas'],
            
            // Propiedades e Infraestructura
            ['nombre' => 'ver_tipos_propiedad', 'descripcion' => 'Ver tipos de propiedad', 'modulo' => 'propiedades'],
            ['nombre' => 'ver_construcciones', 'descripcion' => 'Ver construcciones', 'modulo' => 'propiedades'],
            ['nombre' => 'ver_usos', 'descripcion' => 'Ver usos', 'modulo' => 'propiedades'],
            ['nombre' => 'ver_situaciones', 'descripcion' => 'Ver situaciones', 'modulo' => 'propiedades'],
            ['nombre' => 'ver_servicios_electricos', 'descripcion' => 'Ver servicios eléctricos', 'modulo' => 'propiedades'],
            ['nombre' => 'ver_suministros', 'descripcion' => 'Ver suministros', 'modulo' => 'propiedades'],
            
            // Gestión Operativa
            ['nombre' => 'ver_cuadrillas', 'descripcion' => 'Ver cuadrillas', 'modulo' => 'operativa'],
            ['nombre' => 'crear_cuadrilla', 'descripcion' => 'Crear cuadrilla', 'modulo' => 'operativa'],
            ['nombre' => 'editar_cuadrilla', 'descripcion' => 'Editar cuadrilla', 'modulo' => 'operativa'],
            ['nombre' => 'eliminar_cuadrilla', 'descripcion' => 'Eliminar cuadrilla', 'modulo' => 'operativa'],
            
            ['nombre' => 'ver_papeletas', 'descripcion' => 'Ver papeletas de trabajo', 'modulo' => 'operativa'],
            ['nombre' => 'crear_papeleta', 'descripcion' => 'Crear papeleta de trabajo', 'modulo' => 'operativa'],
            ['nombre' => 'editar_papeleta', 'descripcion' => 'Editar papeleta de trabajo', 'modulo' => 'operativa'],
            
            ['nombre' => 'ver_fichas_actividad', 'descripcion' => 'Ver fichas de actividad', 'modulo' => 'operativa'],
            ['nombre' => 'crear_ficha_actividad', 'descripcion' => 'Crear ficha de actividad', 'modulo' => 'operativa'],
            ['nombre' => 'editar_ficha_actividad', 'descripcion' => 'Editar ficha de actividad', 'modulo' => 'operativa'],
            
            // Administración del Sistema
            ['nombre' => 'administrar_roles', 'descripcion' => 'Administrar roles y permisos', 'modulo' => 'admin'],
            ['nombre' => 'administrar_permisos', 'descripcion' => 'Gestionar matriz de permisos', 'modulo' => 'admin'],
            // Consultas e Informes
            ['nombre' => 'ver_consultas', 'descripcion' => 'Ver consultas', 'modulo' => 'consultas'],
        ];

        foreach ($permisos as $permiso) {
            Permission::updateOrCreate(
                ['nombre' => $permiso['nombre']],
                $permiso
            );
        }

        $this->command->info('Permisos creados/actualizados exitosamente');
    }
}
