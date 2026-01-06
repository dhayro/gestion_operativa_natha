# LISTADO COMPLETO DE MENÚS DEL SISTEMA - GESTIÓN OPERATIVA

## Estructura General del Sistema de Menús

El sistema tiene una estructura jerárquica de menús basada en permisos de usuario. Cada menú solo se muestra si el usuario tiene los permisos necesarios.

---

## MENÚS PRINCIPALES

### 1. **DASHBOARD** 
- **Ruta**: `/dashboard`
- **Permiso Requerido**: `ver_dashboard`
- **Descripción**: Panel principal del sistema
- **Icono**: Home
- **Acceso**: Todos los usuarios autenticados

---

### 2. **MAESTROS DE SISTEMA** 📋
- **Permiso Requerido**: Roles `admin`, `tecnico`, `supervisor`
- **Icono**: Settings
- **Sub-menús**:
  - **Cargos**
    - Ruta: `/cargos`
    - Permiso: `ver_cargos`
  - **Áreas**
    - Ruta: `/areas`
    - Permiso: `ver_areas`
  - **Empleados**
    - Ruta: `/empleados`
    - Permiso: `ver_empleados`

---

### 3. **MAESTROS DE NEGOCIO** 👥
- **Permiso Requerido**: Roles `admin`, `tecnico`, `supervisor`
- **Icono**: Users
- **Sub-menús**:
  - **Ubigeo**
    - Ruta: `/ubigeo`
    - Permiso: `ver_ubigeo`
    - Descripción: Ubicaciones geográficas
  
  - **Categorías**
    - Ruta: `/categorias`
    - Permiso: `ver_categorias`
  
  - **Unidades de Medida**
    - Ruta: `/unidad_medidas`
    - Permiso: `ver_unidades_medida`
  
  - **Materiales**
    - Ruta: `/materiales`
    - Permiso: `ver_materiales`
  
  - **Tipos de Actividad**
    - Ruta: `/tipos-actividad`
    - Permiso: `ver_tipos_actividad`
  
  - **Tipos de Comprobante**
    - Ruta: `/tipo-comprobantes`
    - Permiso: `ver_comprobantes`

---

### 6. **PROPIEDADES E INFRAESTRUCTURA** 🏠
- **Permisos Requeridos**: 
  - `ver_tipos_propiedad` O
  - `ver_construcciones` O
  - `ver_usos` O
  - `ver_situaciones` O
  - `ver_servicios_electricos` O
  - `ver_suministros`
- **Icono**: Home
- **Sub-menús**:
  - **Tipos de Propiedad**
    - Ruta: `/tipo_propiedades`
    - Permiso: `ver_tipos_propiedad`
  
  - **Construcciones**
    - Ruta: `/construcciones`
    - Permiso: `ver_construcciones`
  
  - **Usos**
    - Ruta: `/usos`
    - Permiso: `ver_usos`
  
  - **Situaciones**
    - Ruta: `/situaciones`
    - Permiso: `ver_situaciones`
  
  - **Servicios Eléctricos**
    - Ruta: `/servicios-electricos`
    - Permiso: `ver_servicios_electricos`
  
  - **Suministros**
    - Ruta: `/suministro`
    - Permiso: `ver_suministros`

---

### 4. **MAESTROS DE ACTIVOS** 🚚
- **Permiso Requerido**: Roles `admin`, `tecnico`, `supervisor`
- **Icono**: Truck
- **Sub-menús**:
  
  - **Tipos de Combustible**
    - Ruta: `/tipo_combustibles`
    - Permiso: `ver_combustibles`

  - **Vehículos**
    - Ruta: `/vehiculos`
    - Permiso: `ver_vehiculos`
  
  - **SOATs**
    - Ruta: `/soats`
    - Permiso: `ver_soats`
    - Descripción: Seguros obligatorios de vehículos
  
  - **Medidores**
    - Ruta: `/medidores`
    - Permiso: `ver_medidores`
  
  - **Proveedores**
    - Ruta: `/proveedores`
    - Permiso: `ver_proveedores`

---

### 5. **PROCESOS Y SERVICIOS** 📊
- **Permiso Requerido**: Roles `admin`, `tecnico`, `supervisor`
- **Icono**: Layers
- **Sub-menús**:
  
  - **NEAs**
    - Ruta: `/neas`
    - Permiso: `ver_neas`
    - Descripción: Notas de Entrega de Almacén
  
  - **PECOSAs**
    - Ruta: `/pecosas`
    - Permiso: `ver_pecosas`
    - Descripción: Planillas de Entrega de Comprobante de Salida
  
  - **Consulta de Stock**
    - Ruta: `/stock`
    - Permiso: Ninguno especificado
    - Descripción: Consultas de disponibilidad de materiales

---

### 7. **GESTIÓN OPERATIVA** 🔧
- **Permiso Requerido**: Roles `admin`, `tecnico`, `supervisor`, `operario`
- **Icono**: Truck
- **Sub-menús**:
  - **Cuadrillas**
    - Ruta: `/cuadrillas`
    - Permiso: `ver_cuadrillas`
    - Descripción: Equipos de trabajo

  - **📦 Stock por Cuadrilla**
    - Ruta: `/stock_materiales`
    - Permiso: `ver_stock_materiales`
    - Descripción: Gestión de inventario por equipos de trabajo
  
  - **Papeletas de Trabajo**
    - Ruta: `/papeletas`
    - Permiso: `ver_papeletas`
  
  - **Fichas de Actividad**
    - Ruta: `/fichas_actividad`
    - Permiso: `ver_fichas_actividad`

---

### 8. **CONSULTAS E INFORMES** 📈
- **Permiso Requerido**: `ver_consultas`
- **Icono**: Bar Chart 2
- **Sub-menús**:
  - **NEAs y Movimientos**
    - Ruta: `/consulta_nea`
    - Permiso: `ver_neas` O `ver_consultas`
    - Descripción: Búsqueda y análisis de NEAs

---

### 9. **ADMINISTRACIÓN** ⚙️
- **Permisos Requeridos**: 
  - `administrar_roles` O
  - `administrar_permisos`
- **Icono**: Settings
- **Sub-menús**:
  - **Gestión de Roles**
    - Ruta: `/roles`
    - Permiso: `administrar_roles`
  
  - **Matriz de Permisos**
    - Ruta: `/permissions/matrix`
    - Permiso: `administrar_permisos`
  
  - **Listado de Permisos**
    - Ruta: `/permissions`
    - Permiso: `administrar_permisos`

---

## RESUMEN DE PERMISOS

### Permisos del Sistema
| Permiso | Módulo |
|---------|--------|
| `ver_dashboard` | Dashboard |
| `ver_cargos` | Maestros Sistema |
| `ver_areas` | Maestros Sistema |
| `ver_empleados` | Maestros Sistema |
| `ver_ubigeo` | Maestros Negocio |
| `ver_categorias` | Maestros Negocio |
| `ver_unidades_medida` | Maestros Negocio |
| `ver_materiales` | Maestros Negocio |
| `ver_stock_materiales` | Maestros Negocio |
| `ver_proveedores` | Maestros Negocio |
| `ver_vehiculos` | Maestros Activos |
| `ver_combustibles` | Maestros Activos |
| `ver_soats` | Maestros Activos |
| `ver_medidores` | Maestros Activos |
| `ver_tipos_actividad` | Procesos Servicios |
| `ver_comprobantes` | Procesos Servicios |
| `ver_neas` | Procesos Servicios |
| `ver_pecosas` | Procesos Servicios |
| `ver_tipos_propiedad` | Propiedades Infraestructura |
| `ver_construcciones` | Propiedades Infraestructura |
| `ver_usos` | Propiedades Infraestructura |
| `ver_situaciones` | Propiedades Infraestructura |
| `ver_servicios_electricos` | Propiedades Infraestructura |
| `ver_suministros` | Propiedades Infraestructura |
| `ver_cuadrillas` | Gestión Operativa |
| `ver_papeletas` | Gestión Operativa |
| `ver_fichas_actividad` | Gestión Operativa |
| `ver_consultas` | Consultas Informes |
| `administrar_roles` | Administración |
| `administrar_permisos` | Administración |

---

## RESUMEN DE ROLES

### Roles del Sistema
| Rol | Menús Accesibles |
|-----|-----------------|
| `admin` | Todos excepto limitados por permisos específicos |
| `tecnico` | Maestros Sistema, Negocio, Activos, Procesos, Propiedades, Gestión Operativa |
| `supervisor` | Maestros Sistema, Negocio, Activos, Procesos, Propiedades, Gestión Operativa |
| `operario` | Solo Gestión Operativa |
| Otros | Basado en permisos específicos |

---

## ESTRUCTURA TÉCNICA

**Archivo**: `resources/views/layouts/sidebar.blade.php`

**Características**:
- Sistema de permisos basado en Laravel Gate
- Menús dinámicos según rol y permisos del usuario
- Colapsables (accordion)
- Iconos SVG para cada menú principal
- Responsive design

**Variables Clave**:
- `auth()->user()->hasPermission()` - Verifica permiso específico
- `auth()->user()->hasAnyRole()` - Verifica si tiene cualquiera de los roles
- `$catName` - Variable de categoría activa
- `Request::routeIs()` - Detecta ruta activa para resaltar

---

## DIAGRAMA DE JERARQUÍA

```
DASHBOARD
│
├─ MAESTROS DE SISTEMA
│  ├─ Cargos
│  ├─ Áreas
│  └─ Empleados
│
├─ MAESTROS DE NEGOCIO
│  ├─ Ubigeo
│  ├─ Categorías
│  ├─ Unidades de Medida
│  ├─ Materiales
│  ├─ Stock por Cuadrilla
│  └─ Proveedores
│
├─ MAESTROS DE ACTIVOS
│  ├─ Vehículos
│  ├─ Tipos de Combustible
│  ├─ SOATs
│  └─ Medidores
│
├─ PROCESOS Y SERVICIOS
│  ├─ Tipos de Actividad
│  ├─ Tipos de Comprobante
│  ├─ NEAs
│  ├─ PECOSAs
│  └─ Consulta de Stock
│
├─ PROPIEDADES E INFRAESTRUCTURA
│  ├─ Tipos de Propiedad
│  ├─ Construcciones
│  ├─ Usos
│  ├─ Situaciones
│  ├─ Servicios Eléctricos
│  └─ Suministros
│
├─ GESTIÓN OPERATIVA
│  ├─ Cuadrillas
│  ├─ Papeletas de Trabajo
│  └─ Fichas de Actividad
│
├─ CONSULTAS E INFORMES
│  └─ NEAs y Movimientos
│
└─ ADMINISTRACIÓN
   ├─ Gestión de Roles
   ├─ Matriz de Permisos
   └─ Listado de Permisos
```

---

*Documento generado el 6 de enero de 2026*
*Sistema: Gestión Operativa Natha*
