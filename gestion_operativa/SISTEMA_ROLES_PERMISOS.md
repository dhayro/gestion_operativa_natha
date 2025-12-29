# SISTEMA DE ROLES Y PERMISOS - GUÍA DE IMPLEMENTACIÓN

## ✅ Completado

Se ha implementado un **sistema completo de roles y permisos** que controla:
- Acceso a menús según el rol del usuario
- Permisos granulares por módulo
- Control de vista en la interfaz Blade
- Middleware para proteger rutas

---

## 📋 ROLES CREADOS

### 1. **ADMIN** 👑
- **Descripción**: Administrador del sistema con acceso total
- **Permisos**: Todos (100%)
- **Usuario de prueba**: `nathalyvr25@gmail.com` / `password`

### 2. **TÉCNICO** 🔧
- **Descripción**: Técnico - Acceso a operaciones técnicas
- **Menús disponibles**:
  - Dashboard
  - Maestros de Negocio (Ubigeo, Categorías, Unidades, Materiales, Stock, Proveedores)
  - Maestros de Activos (Vehículos, Combustible, SOATs, Medidores)
  - Procesos (Tipos de Actividad, Comprobantes, NEAs)
  - Propiedades e Infraestructura
  - Gestión Operativa (Cuadrillas, Papeletas, Fichas de Actividad)
  - Consultas e Informes
- **Usuario de prueba**: `tecnico@example.com` / `password`

### 3. **OPERARIO** 👷
- **Descripción**: Operario - Acceso limitado a operaciones
- **Menús disponibles**:
  - Dashboard
  - Ver Materiales
  - Stock de Materiales
  - Ver Medidores
  - Gestión Operativa (ver fichas y papeletas)
  - Consultas e Informes
- **Usuario de prueba**: `operario@example.com` / `password`

### 4. **SUPERVISOR** 👨‍💼
- **Descripción**: Supervisor - Supervisión de operaciones
- **Menús disponibles**:
  - Dashboard
  - Maestros de Sistema
  - Maestros de Negocio
  - Maestros de Activos
  - Procesos y Servicios (completo)
  - Propiedades e Infraestructura
  - Gestión Operativa (completo)
  - Consultas e Informes
- **Usuario de prueba**: `supervisor@example.com` / `password`

---

## 📁 ARCHIVOS CREADOS/MODIFICADOS

### Migraciones
- `database/migrations/2025_12_28_000001_create_roles_table.php`
- `database/migrations/2025_12_28_000002_create_permissions_table.php`
- `database/migrations/2025_12_28_000003_create_role_permissions_table.php`
- `database/migrations/2025_12_28_000004_create_user_roles_table.php`

### Modelos
- `app/Models/Role.php` (Nueva)
- `app/Models/Permission.php` (Nueva)
- `app/Models/User.php` (Actualizado con relaciones)

### Seeders
- `database/seeders/PermissionSeeder.php` (Nueva - 96 permisos)
- `database/seeders/RoleSeeder.php` (Nueva - 4 roles)
- `database/seeders/UserSeeder.php` (Actualizado)
- `database/seeders/DatabaseSeeder.php` (Actualizado)

### Middleware
- `app/Http/Middleware/CheckPermission.php` (Nueva)
- `app/Http/Middleware/CheckRole.php` (Nueva)

### Vistas
- `resources/views/layouts/sidebar.blade.php` (Actualizado con @if directivas)

---

## 🔐 CÓMO USAR EL SISTEMA

### 1. Verificar Permisos en Blade
```blade
@if(auth()->user()->hasPermission('ver_materiales'))
    <a href="{{ route('materiales.index') }}">Ver Materiales</a>
@endif
```

### 2. Verificar Rol en Blade
```blade
@if(auth()->user()->hasRole('tecnico'))
    <!-- Contenido solo para técnicos -->
@endif
```

### 3. Verificar Múltiples Roles
```blade
@if(auth()->user()->hasAnyRole(['admin', 'supervisor']))
    <!-- Contenido para admin o supervisor -->
@endif
```

### 4. Proteger Rutas con Middleware
```php
Route::middleware(['auth', 'checkPermission:ver_materiales'])->group(function () {
    Route::get('/materiales', [MaterialController::class, 'index']);
});
```

### 5. En el Controlador
```php
// Verificar permiso
if (!auth()->user()->hasPermission('editar_material')) {
    abort(403, 'No tienes permiso');
}

// O en el constructor
$this->middleware(function ($request, $next) {
    if (!auth()->user()->hasPermission('crear_material')) {
        abort(403);
    }
    return $next($request);
});
```

---

## 🗂️ TABLAS CREADAS

### roles
```sql
- id (PK)
- nombre (UNIQUE) - admin, tecnico, operario, supervisor
- descripcion
- estado (boolean)
- timestamps
```

### permissions
```sql
- id (PK)
- nombre (UNIQUE) - ver_materiales, crear_material, etc
- descripcion
- modulo - dashboard, empleados, materiales, etc
- estado (boolean)
- timestamps
```

### role_permissions
```sql
- id (PK)
- role_id (FK) → roles
- permission_id (FK) → permissions
- timestamps
- UNIQUE(role_id, permission_id)
```

### user_roles
```sql
- id (PK)
- user_id (FK) → users
- role_id (FK) → roles
- timestamps
- UNIQUE(user_id, role_id)
```

---

## 📊 PERMISOS POR MÓDULO (96 total)

### Dashboard (1)
- ver_dashboard

### Cargos (4)
- ver_cargos, crear_cargo, editar_cargo, eliminar_cargo

### Áreas (4)
- ver_areas, crear_area, editar_area, eliminar_area

### Empleados (4)
- ver_empleados, crear_empleado, editar_empleado, eliminar_empleado

### Ubigeo (4)
- ver_ubigeo, crear_ubigeo, editar_ubigeo, eliminar_ubigeo

### Categorías (4)
- ver_categorias, crear_categoria, editar_categoria, eliminar_categoria

### Unidades de Medida (4)
- ver_unidades_medida, crear_unidad_medida, editar_unidad_medida, eliminar_unidad_medida

### Materiales (4)
- ver_materiales, crear_material, editar_material, eliminar_material

### Stock (1)
- ver_stock_materiales

### Proveedores (4)
- ver_proveedores, crear_proveedor, editar_proveedor, eliminar_proveedor

### Vehículos (4)
- ver_vehiculos, crear_vehiculo, editar_vehiculo, eliminar_vehiculo

### Combustibles (4)
- ver_combustibles, crear_combustible, editar_combustible, eliminar_combustible

### SOATs (4)
- ver_soats, crear_soat, editar_soat, eliminar_soat

### Medidores (4)
- ver_medidores, crear_medidor, editar_medidor, eliminar_medidor

### Tipos de Actividad (4)
- ver_tipos_actividad, crear_tipo_actividad, editar_tipo_actividad, eliminar_tipo_actividad

### Comprobantes (1)
- ver_comprobantes

### NEAs (3)
- ver_neas, crear_nea, editar_nea

### PECOSAs (2)
- ver_pecosas, crear_pecosa, editar_pecosa

### Propiedades (6)
- ver_tipos_propiedad, ver_construcciones, ver_usos, ver_situaciones, ver_servicios_electricos, ver_suministros

### Gestión Operativa (7)
- ver_cuadrillas, crear_cuadrilla, editar_cuadrilla, eliminar_cuadrilla, ver_papeletas, crear_papeleta, editar_papeleta, ver_fichas_actividad, crear_ficha_actividad, editar_ficha_actividad

### Consultas (1)
- ver_consultas

---

## 🚀 PRÓXIMOS PASOS

1. **Ejecutar la migración y seeder**:
```bash
php artisan migrate:fresh --seed
```

2. **Probar con los usuarios de ejemplo**:
   - Admin: `nathalyvr25@gmail.com`
   - Técnico: `tecnico@example.com`
   - Operario: `operario@example.com`
   - Supervisor: `supervisor@example.com`
   - Contraseña: `password` para todos

3. **Observar cambios**:
   - El sidebar se mostrará diferente para cada rol
   - Los menús se filtrarán automáticamente
   - Los permisos se validarán en las vistas Blade

4. **Personalizar permisos**: Editar `RoleSeeder.php` para ajustar permisos por rol

---

## 🔧 MÉTODOS DISPONIBLES EN USER

```php
// Verificar permiso específico
auth()->user()->hasPermission('ver_materiales') // bool

// Verificar rol específico
auth()->user()->hasRole('admin') // bool

// Verificar múltiples roles
auth()->user()->hasAnyRole(['admin', 'supervisor']) // bool

// Obtener todos los roles del usuario
auth()->user()->roles // Collection

// Obtener todos los permisos a través de roles
auth()->user()->permissions() // Query Builder
```

---

## 📝 NOTAS IMPORTANTES

1. **Relación Many-to-Many**: Un usuario puede tener múltiples roles
2. **Permisos por Rol**: Un rol puede tener múltiples permisos
3. **Control Dinámico**: El sidebar se actualiza automáticamente según los permisos
4. **Seguridad**: Implementar middleware en rutas críticas
5. **Cache**: Considerar cachear permisos en producción

---

**Sistema implementado: 28 de Diciembre, 2025**
**Versión: 1.0**
