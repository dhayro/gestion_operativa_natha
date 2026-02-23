# 🔐 Precinto Modal - Select2 Integration Updates

## Resumen de Cambios

Se ha actualizado el modal "🔐 Agregar Precinto" para utilizar **Select2 con búsqueda AJAX**, igual que el modal de medidores.

### 1. **HTML Structure (Lines 545-575)**

Ambos selects ahora tienen la clase `select2` y atributos `name`:

```html
<!-- Medidores Asignados -->
<select class="form-control select2" id="medidor_ficha_actividad_id" name="medidor_ficha_actividad_id">
    <option value="">Seleccione un medidor asignado</option>
</select>

<!-- Materiales (Precintos) -->
<select class="form-control select2" id="precinto_material_id" name="precinto_material_id">
    <option value="">Seleccione un material</option>
</select>
```

### 2. **JavaScript Functions (Lines 1945-2028)**

#### A. `cargarMedidoresAsignados()` - ACTUALIZADA
- **Antes**: Construía HTML directamente con opciones
- **Después**: Carga datos con $.get() y los popula en el Select2
- **Endpoint**: `/fichas-actividad/{fichaId}/detalles/medidores`
- **Formato**: Usa `new Option()` para agregar opciones al Select2

```javascript
function cargarMedidoresAsignados() {
    $.get(`/fichas-actividad/${fichaActualId}/detalles/medidores`, function(response) {
        // Mapear datos del endpoint
        let medidores = response.data.map(item => ({
            id: item.id,
            text: item.medidor.serie + ' - ' + item.medidor.modelo + ' (' + item.tipo + ')',
            serie: item.medidor.serie,
            modelo: item.medidor.modelo,
            tipo: item.tipo
        }));
        
        // Limpiar select y agregar opciones
        $('#medidor_ficha_actividad_id').empty();
        $('#medidor_ficha_actividad_id').append(new Option('Seleccione un medidor asignado', ''));
        
        $.each(medidores, function(i, item) {
            $('#medidor_ficha_actividad_id').append(new Option(item.text, item.id));
        });
        
        // Trigger change para actualizar Select2
        $('#medidor_ficha_actividad_id').trigger('change');
    }).fail(function() {
        Swal.fire('Error', 'No se pudieron cargar los medidores asignados', 'error');
    });
}
```

#### B. `cargarMaterialesParaPrecintos()` - ACTUALIZADA
- **Antes**: Construía HTML directamente y filtraba solo "precinto"
- **Después**: Filtra en JavaScript usando `.filter()` y popula Select2
- **Endpoint**: `/materiales/select`
- **Filtro**: Solo items que comienzan con "precinto" (case-insensitive)

```javascript
function cargarMaterialesParaPrecintos() {
    $.get('/materiales/select', function(data) {
        // Filtrar solo precintos
        let precintos = data.filter(item => 
            item.nombre.toLowerCase().startsWith('precinto')
        ).map(item => ({
            id: item.id,
            text: item.nombre
        }));
        
        // Limpiar select y agregar opciones
        $('#precinto_material_id').empty();
        $('#precinto_material_id').append(new Option('Seleccione un material', ''));
        
        $.each(precintos, function(i, item) {
            $('#precinto_material_id').append(new Option(item.text, item.id));
        });
        
        // Trigger change para actualizar Select2
        $('#precinto_material_id').trigger('change');
    }).fail(function() {
        console.error('Error cargando materiales para precintos');
    });
}
```

#### C. Select2 Initialization (Lines 2016-2027)

```javascript
// Configurar Select2 para medidores asignados
$('#medidor_ficha_actividad_id').select2({
    width: '100%',
    dropdownParent: $('#precintoModal'),
    placeholder: 'Buscar medidor...',
    allowClear: true
});

// Configurar Select2 para materiales (precintos)
$('#precinto_material_id').select2({
    width: '100%',
    dropdownParent: $('#precintoModal'),
    placeholder: 'Buscar material/precinto...',
    allowClear: true
});
```

### 3. **Integration Points**

#### showPrecintoForm() (Line 1665)
Ya llama automáticamente a ambas funciones:
```javascript
function showPrecintoForm() {
    if (!fichaActualId) {
        Swal.fire('Aviso', 'Primero guarda la ficha principal', 'warning');
        return;
    }
    cargarMedidoresAsignados();        // ← Carga medidores en Select2
    cargarMaterialesParaPrecintos();   // ← Carga materiales en Select2
    $('#precintoModal').modal('show');
}
```

## 🎯 Comportamiento Esperado

### Al abrir el modal:
1. ✅ Se llama `showPrecintoForm()`
2. ✅ Se ejecutan ambas funciones de carga
3. ✅ Los datos se populas en los Select2
4. ✅ Los dropdowns muestran el placeholder "Buscar..."

### Al interactuar:
1. ✅ Se puede escribir en los selects para buscar
2. ✅ Los dropdown expandren mostrando opciones filtradas
3. ✅ Se puede limpiar la selección con la X
4. ✅ Se puede seleccionar un item

## 🔧 Testing Checklist

- [ ] Abrir una ficha existente o crear una nueva
- [ ] Click en botón "🔐 Agregar Precinto"
- [ ] Verificar que ambos Select2 aparecen sin errores
- [ ] Escribir en el campo "Buscar medidor..."
- [ ] Verificar que aparece la lista filtrada
- [ ] Seleccionar un medidor
- [ ] Escribir en el campo "Buscar material/precinto..."
- [ ] Verificar que solo aparecen items con "precinto" en el nombre
- [ ] Seleccionar un material
- [ ] Guardar la ficha
- [ ] Verificar que los datos se guardan correctamente en la BD

## 📊 Comparación: Antes vs Después

| Aspecto | Antes | Después |
|---------|-------|---------|
| **Tipo de Select** | HTML plain | Select2 con styling |
| **Búsqueda** | No | Sí, en tiempo real |
| **Data Loading** | Hardcoded | AJAX dinámico |
| **Consistencia** | Diferente al modal de medidores | ✅ Igual a todo el sistema |
| **User Experience** | Scroll por muchas opciones | ✅ Busca rápidamente |
| **Performance** | Carga todas las opciones | ✅ Lazy load |

## 🚀 Beneficios

1. **Consistencia**: Mismo look & feel que el modal de medidores
2. **Usabilidad**: Búsqueda rápida sin scroll
3. **Performance**: Carga datos bajo demanda
4. **Mantenibilidad**: Código más limpio y reutilizable
5. **Escalabilidad**: Funciona bien con cientos de materiales/medidores

## ⚙️ Dependencias

- **jQuery** (ya incluido)
- **Select2** (ya incluido)
- **Bootstrap 5** (para modales)
- **SweetAlert2** (para notificaciones)

## 📝 Archivo Modificado

- `resources/views/admin/ficha_actividad/index.blade.php` (Líneas 545-575, 1945-2028)

