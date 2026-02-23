# 📊 OPTIMIZACIÓN DE MEDIDORES - RESUMEN DE CAMBIOS

## Fecha: 23 de Febrero, 2026

---

## 🎯 OBJETIVOS CUMPLIDOS

1. ✅ **Cambiar estados de medidores**: De boolean (1/0) a integer (1=Disponible, 2=Asignado)
2. ✅ **Sincronizar automáticamente**: Cuando se asigna/desasigna un medidor a un suministro
3. ✅ **Optimizar búsquedas**: LEFT JOIN en lugar de subqueries y filtrado en memoria
4. ✅ **Agregar búsqueda en UI**: Select2 con AJAX en formulario de ficha_actividad

---

## 📁 ARCHIVOS MODIFICADOS

### 1. **Models/Medidor.php**
```php
// Cambios realizados:
- Actualizar $casts: 'estado' => 'integer' (antes: boolean)
- Agregar scopes:
  - scopeDisponibles(): where medidors.estado = 1
  - scopeAsignados(): where medidors.estado = 2
- Agregar métodos:
  - marcarAsignado(): Cambiar estado a 2
  - marcarDisponible(): Cambiar estado a 1
- Actualizar accessor getEstadoTextoAttribute()
```

**Ubicación**: `app/Models/Medidor.php` (líneas 25-70)

### 2. **Models/Suministro.php**
```php
// Cambios realizados:
- Agregar eventos en boot():
  - creating: Al crear, marcar medidor como asignado (estado=2)
  - updating: Al cambiar medidor, liberar anterior y asignar nuevo
  - deleting: Al eliminar, liberar el medidor (estado=1)
```

**Ubicación**: `app/Models/Suministro.php` (líneas 35-70)

### 3. **Controllers/SuministroController.php**
```php
// Cambios realizados:
- Actualizar getMedidores():
  - Usar scope disponibles() en lugar de where('estado', true)
  - Especificar tabla 'medidors' en where clauses
  - Agregar lógica para incluir medidor actual en edición
  
- Método mejorado de 11 líneas a 30 líneas (con mejor lógica)
```

**Ubicación**: `app/Http/Controllers/SuministroController.php` (líneas 489-524)

### 4. **Controllers/MedidorController.php**
```php
// Cambios realizados:
- Actualizar select():
  - Usar scope disponibles()
  - Agregar búsqueda por serie, modelo (LIKE "%query%")
  - LEFT JOIN con medidor_ficha_actividades
  - Limitar a 100 resultados para rendimiento
  - Incluir medidor actual del suministro con flag 'actual: true'
  
- Agregar parámetro 'q' para búsqueda (AJAX)
```

**Ubicación**: `app/Http/Controllers/MedidorController.php` (líneas 247-310)

### 5. **resources/views/admin/ficha_actividad/index.blade.php**
```html
<!-- Cambios realizados:
1. Cambiar select medidor de plain HTML a Select2
   - Línea 492: Agregar clase 'select2'
   
2. Agregar configuración Select2 con AJAX
   - URL: /medidor/select?ficha_id=${fichaId}&suministro_id=${suministroId}
   - Búsqueda en tiempo real
   - Mismo estilo que suministro
   
3. Actualizar función cargarMedidoresDisponibles()
   - Renombrar a cargarMedidoresSelect2()
   - Usar trigger('change') en lugar de llenar HTML
   
4. Actualizar showMedidorForm()
   - Llamar a cargarMedidoresSelect2() en lugar de cargarMedidoresDisponibles()
-->
```

**Ubicación**: `resources/views/admin/ficha_actividad/index.blade.php`
- Línea 492: HTML del select
- Líneas 815-853: Configuración Select2
- Líneas 1902-1905: Nueva función
- Línea 1645: showMedidorForm()

### 6. **database/migrations/2026_02_23_change_medidor_estado_to_integer.php**
```php
// Migración que:
- Convierte campo 'estado' de boolean a integer
- Convierte TRUE -> 1 (Disponible)
- Convierte FALSE -> 2 (Asignado)
- Asigna default = 1
```

**Ubicación**: `database/migrations/`

### 7. **database/data/sincronizar_estado_medidores.sql**
```sql
-- Archivo con:
- UPDATE para marcar todos como disponibles (estado=1)
- UPDATE para marcar asignados según suministros (estado=2)
- SELECT queries de verificación
```

**Ubicación**: `database/data/sincronizar_estado_medidores.sql`

### 8. **app/Console/Commands/SincronizarEstadoMedidores.php**
```php
// Comando Artisan:
- Nombre: medidores:sincronizar-estado
- Sincroniza estados basado en suministros actuales
- Muestra resumen: X disponibles, Y asignados

// Uso:
php artisan medidores:sincronizar-estado
```

**Ubicación**: `app/Console/Commands/SincronizarEstadoMedidores.php`

---

## 🔄 FLUJO DE FUNCIONAMIENTO

### **Asignación de Medidor a Suministro**
```
1. Usuario guarda Suministro con medidor_id
   ↓
2. Evento Suministro::creating() se ejecuta
   ↓
3. Medidor::marcarAsignado() cambia estado a 2
   ↓
4. Medidor ya no aparece en dropdown "Disponibles"
   ↓
5. Aparece en dropdown "Medidores de Suministro"
```

### **Cambio de Medidor en Suministro**
```
1. Usuario edita Suministro, cambia medidor_id
   ↓
2. Evento Suministro::updating() se ejecuta
   ↓
3. Medidor anterior: marcarDisponible() → estado = 1
   ↓
4. Medidor nuevo: marcarAsignado() → estado = 2
   ↓
5. Dropdown se actualiza automáticamente
```

### **Eliminación de Suministro**
```
1. Usuario elimina Suministro
   ↓
2. Evento Suministro::deleting() se ejecuta
   ↓
3. Medidor liberado: marcarDisponible() → estado = 1
   ↓
4. Medidor nuevamente disponible para usar
```

---

## 📊 OPTIMIZACIONES REALIZADAS

### **SuministroController::getMedidores()**

| Aspecto | Antes | Después |
|---------|-------|---------|
| Queries | 2 (subquery + main) | 1 |
| Datos traídos | Todos los medidores | Solo disponibles |
| Filtrado | PHP (RAM) | MySQL (índices) |
| Rendimiento | O(n) | O(log n) |

### **MedidorController::select()**

| Aspecto | Antes | Después |
|---------|-------|---------|
| Búsqueda | No | Sí (AJAX) |
| Queries | 2-3 (subqueries) | 1 (LEFT JOIN) |
| Usuarios pueden | Scroll 1000+ | Buscar dinámicamente |
| Rendimiento | Lento con muchos | Rápido |

### **Frontend - Select2**

| Aspecto | Antes | Después |
|---------|-------|---------|
| Interfaz | HTML plain | Select2 con estilo |
| Búsqueda | No | Sí, en tiempo real |
| UX | Scroll incómodo | Búsqueda intuitiva |
| Consistencia | Diferente a suministro | Idéntico a suministro |

---

## 🚀 EJECUCIÓN DE CAMBIOS

### **Paso 1: Ejecutar Migración**
```bash
php artisan migrate --force
```
Esto convierte el campo `estado` de boolean a integer.

### **Paso 2: Sincronizar Estados (Opción A: Comando Artisan)**
```bash
php artisan medidores:sincronizar-estado
```
Output esperado:
```
✓ Se marcaron X medidores como disponibles
✓ Se marcaron Y medidores como asignados
===════════════════════════════════════
Estado Final:
  • Medidores Disponibles (estado=1): X
  • Medidores Asignados (estado=2): Y
===════════════════════════════════════
✓ Sincronización completada exitosamente
```

### **Paso 2: Sincronizar Estados (Opción B: SQL directo)**
```bash
mysql -u root -p tu_bd < database/data/sincronizar_estado_medidores.sql
```

### **Paso 3: Verificar en la UI**
1. Ir a Ficha Actividad
2. Crear nueva ficha
3. Seleccionar suministro
4. Hacer clic en "Agregar Medidor"
5. Verificar que Select2 funciona con búsqueda

---

## ✅ PRUEBAS RECOMENDADAS

### **1. Búsqueda de Medidores**
```
GET /medidor/select?ficha_id=1&q=DDS
Esperado: Lista de medidores con "DDS" en serie o modelo
```

### **2. Búsqueda con Suministro**
```
GET /medidor/select?ficha_id=1&suministro_id=80156&q=08
Esperado: Medidores disponibles + medidor actual del suministro
```

### **3. Búsqueda en UI**
```
1. Abrir ficha actividad
2. Seleccionar suministro
3. Click en "Agregar Medidor"
4. Escribir en búsqueda "DDS"
Esperado: Dropdown se actualiza con resultados
```

### **4. Estados Sincronizados**
```
Base de datos:
- SELECT COUNT(*) FROM medidors WHERE estado = 1; // Disponibles
- SELECT COUNT(*) FROM medidors WHERE estado = 2; // Asignados
Verificar que suma = total de medidores
```

### **5. Asignación Automática**
```
1. Crear suministro con medidor X
2. Verificar que medidor X tiene estado = 2
3. Editar suministro, cambiar a medidor Y
4. Verificar que X ahora tiene estado = 1
5. Verificar que Y ahora tiene estado = 2
```

---

## 📝 NOTAS IMPORTANTES

### **Para Desarrolladores:**
- Los scopes `disponibles()` y `asignados()` especifican la tabla para evitar ambigüedad
- El comando Artisan reconstruye los estados desde la fuente de verdad (suministros)
- El campo `q` en AJAX es igual al de suministro por consistencia

### **Para DevOps:**
- Ejecutar migración ANTES de sincronizar estados
- Hacer backup antes de ejecutar las operaciones
- Verificar que no haya suministros sin medidor_id asignado

### **Para QA:**
- Probar búsqueda con caracteres especiales
- Verificar con muchos medidores (5000+)
- Validar que medidor actual siempre aparece en dropdown

---

## 🎯 RESULTADOS FINALES

✅ **Estados de medidores automáticos**
- Al asignar → estado 2
- Al desasignar → estado 1
- Sincronización manual posible

✅ **Búsqueda optimizada**
- LEFT JOIN en BD (no en PHP)
- AJAX en tiempo real
- Consistencia con otros selects

✅ **UX mejorada**
- Select2 estilo profesional
- Búsqueda intuitiva
- Interfaz consistente

✅ **Performance**
- Reducción de queries: 3 → 1
- Filtrado en BD (más rápido)
- Limit 100 previene lag

---

## 📞 SOPORTE

Si encuentras problemas:
1. Ejecuta `php artisan medidores:sincronizar-estado`
2. Verifica estados en BD: `SELECT estado, COUNT(*) FROM medidors GROUP BY estado;`
3. Revisa logs: `storage/logs/laravel.log`
4. Prueba con curl: `curl "http://127.0.0.1:8000/medidor/select?ficha_id=1&q=test"`
