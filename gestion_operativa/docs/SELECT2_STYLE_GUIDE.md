# Estilo Estándar Select2 para la Aplicación

## Estilo Consistente de Select2

Todos los selects de la aplicación deben usar el siguiente estilo estándar de Select2 para mantener consistencia visual:

```css
/* Select2 Bootstrap 5 integration */
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #bfc9d4 !important;
    border-radius: 6px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 12px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}
.select2-dropdown {
    border: 1px solid #bfc9d4 !important;
    border-radius: 6px !important;
}
```

## Archivos Actualizados

Los siguientes archivos ya implementan este estilo estándar:

- `/resources/views/admin/cuadrillas/index.blade.php` ✅
- `/resources/views/admin/vehiculos/index.blade.php` ✅
- `/resources/views/admin/empleados/index.blade.php` ✅
- `/resources/views/admin/papeletas/index.blade.php` ✅
- `/resources/views/admin/soats/index.blade.php` ✅
- `/resources/views/admin/materiales/index.blade.php` ✅
- `/resources/views/admin/proveedores/index.blade.php` ✅

## Implementación para Nuevos Archivos

Cuando crees nuevos archivos que usen Select2, incluye:

### 1. CSS Link
```html
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
```

### 2. Estilos CSS
Incluye el bloque de estilos CSS estándar mostrado arriba.

### 3. JavaScript Link
```html
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
```

### 4. Inicialización JavaScript
```javascript
$('#mi-select').select2({
    dropdownParent: $('#mi-modal'), // Si está en un modal
    placeholder: 'Seleccione una opción',
    allowClear: true,
    language: {
        noResults: function() { return 'No hay resultados'; }
    }
});
```

## CSS Centralizado

También está disponible un archivo CSS centralizado en:
`/public/css/select2-custom.css`

Este archivo contiene todos los estilos estándar y puede ser incluido en lugar de definir los estilos inline.

## Archivo de Referencia

Usa `/resources/views/admin/cuadrillas/index.blade.php` como archivo de referencia para implementar Select2 con el estilo estándar.