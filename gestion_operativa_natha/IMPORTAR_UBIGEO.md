# Importar datos UBIGEO desde Excel

Tienes dos opciones para importar los datos UBIGEO desde el archivo Excel a tu base de datos:

## Opción 1: Usar el Script Python (Recomendado para la primera importación)

### Requisitos previos

```bash
pip install pandas mysql-connector-python openpyxl
```

### Uso básico

```bash
# Navega a la carpeta raíz del proyecto
cd d:/gestion_operativa_natha

# Ejecuta el script
python import_ubigeo.py "UBIGEO 2022_1891 distritos.xlsx"
```

### Opciones avanzadas

```bash
# Limpiar la tabla antes de importar
python import_ubigeo.py "UBIGEO 2022_1891 distritos.xlsx" --limpiar

# Especificar credenciales de base de datos personalizadas
python import_ubigeo.py "UBIGEO 2022_1891 distritos.xlsx" \
    --host localhost \
    --user root \
    --password "" \
    --database gestion_operativa \
    --limpiar

# Desde otra máquina o servidor
python import_ubigeo.py "datos.xlsx" \
    --host 192.168.1.100 \
    --user admin \
    --password micontraseña
```

### Ejemplo completo

```bash
python import_ubigeo.py "UBIGEO 2022_1891 distritos.xlsx" --limpiar
```

Este comando:
1. ✓ Lee el archivo Excel
2. ✓ Limpia la tabla `ubigeos` (borra datos anteriores)
3. ✓ Inserta automáticamente departamentos, provincias y distritos
4. ✓ Establece las relaciones de dependencia (jerarquía)
5. ✓ Muestra un reporte detallado

## Opción 2: Usar el Comando Artisan (Desde Laravel)

### Requisitos previos

Asegúrate de tener instalada la dependencia `phpoffice/phpspreadsheet`:

```bash
cd gestion_operativa
composer require phpoffice/phpspreadsheet
```

### Uso básico

```bash
cd gestion_operativa

# Importar datos sin limpiar
php artisan ubigeo:import "../UBIGEO 2022_1891 distritos.xlsx"

# Importar y limpiar la tabla primero
php artisan ubigeo:import "../UBIGEO 2022_1891 distritos.xlsx" --limpiar
```

### Ejemplo con ruta absoluta

```bash
php artisan ubigeo:import "d:/gestion_operativa_natha/UBIGEO 2022_1891 distritos.xlsx" --limpiar
```

## Formato esperado del Excel

El archivo Excel debe tener las siguientes columnas:

| Columna | Descripción |
|---------|-------------|
| IDDIST | Código UBIGEO (6 dígitos, ej: 010101) |
| NOMBDEP | Nombre del departamento |
| NOMBPROV | Nombre de la provincia |
| NOMBDIST | Nombre del distrito |
| NOM_CAPITAL | Nombre de la capital (opcional) |
| COD_REG_NAT | Código región natural (opcional) |
| REGION NATURAL | Región natural (opcional) |

### Ejemplo de datos:

```
IDDIST      NOMBDEP    NOMBPROV      NOMBDIST       NOM_CAPITAL
010101      AMAZONAS   CHACHAPOYAS   CHACHAPOYAS    CHACHAPOYAS
010102      AMAZONAS   CHACHAPOYAS   ASUNCION       ASUNCION
010103      AMAZONAS   CHACHAPOYAS   BALSAS         BALSAS
```

## ¿Qué sucede durante la importación?

1. **Lectura del Excel**: Lee todos los registros del archivo
2. **Validación**: Verifica que los datos esenciales estén presentes
3. **Inserción jerárquica**:
   - Primero inserta departamentos (6 primeros dígitos del IDDIST + 0000)
   - Luego inserta provincias (primeros 4 dígitos del IDDIST + 00)
   - Finalmente inserta distritos (código UBIGEO completo)
4. **Relaciones**: Establece la dependencia de cada nivel (distrito → provincia → departamento)
5. **Reporte**: Muestra un resumen de lo importado

## Verificar que los datos se importaron correctamente

### Desde MySQL

```sql
-- Ver total de registros
SELECT COUNT(*) as total FROM ubigeos;

-- Ver estructura jerárquica
SELECT 
    CONCAT(REPEAT('  ', CASE 
        WHEN LENGTH(codigo_postal) = 6 THEN 2
        WHEN LENGTH(codigo_postal) = 5 THEN 1
        ELSE 0
    END), nombre) as nombre,
    codigo_postal,
    estado
FROM ubigeos
ORDER BY codigo_postal
LIMIT 50;

-- Ver un departamento con sus provincias y distritos
SELECT 
    u1.nombre as departamento,
    u2.nombre as provincia,
    u3.nombre as distrito
FROM ubigeos u1
LEFT JOIN ubigeos u2 ON u2.dependencia_id = u1.id
LEFT JOIN ubigeos u3 ON u3.dependencia_id = u2.id
WHERE u1.codigo_postal LIKE '%0000'
LIMIT 30;
```

### Desde Laravel

```php
// En una ruta o tinker
php artisan tinker

// Ver total
Ubigeo::count();

// Ver departamentos
Ubigeo::where('codigo_postal', 'like', '%0000')->get();

// Ver provincias de AMAZONAS (010000)
$amazonas = Ubigeo::where('codigo_postal', '010000')->first();
$amazonas->dependientes()->get();

// Ver distritos de una provincia
$chachapoyas = Ubigeo::where('codigo_postal', '010100')->first();
$chachapoyas->dependientes()->get();
```

## Solución de problemas

### Error: "Archivo no encontrado"
- Verifica que la ruta del archivo es correcta
- Usa la ruta completa: `d:/gestion_operativa_natha/UBIGEO 2022_1891 distritos.xlsx`

### Error: "Conexión rechazada"
- Verifica que MySQL está corriendo
- Verifica las credenciales en `.env` (DB_HOST, DB_USER, DB_PASSWORD)

### Error: "Access denied for user"
- Verifica el usuario y contraseña en `.env`
- Asegúrate de que el usuario tiene permisos en la base de datos

### Algunos registros no se importan
- Verifica que el archivo Excel no tiene filas vacías
- Comprueba que todas las filas tienen valores en IDDIST, NOMBDEP, NOMBPROV y NOMBDIST
- Revisa el reporte de errores que muestra el script

## Performance

- **Python**: ~2000-5000 registros por minuto
- **Artisan**: ~1000-3000 registros por minuto

Para archivos grandes (>10,000 registros), se recomienda usar la versión en Python.

## Rollback

Si algo sale mal y quieres revertir:

```sql
-- Eliminar todos los UBIGEO
TRUNCATE TABLE ubigeos;

-- O si solo quieres eliminar los nuevos
DELETE FROM ubigeos WHERE codigo_postal LIKE '0%';
```

## Automatizar la importación

### En Windows (Tarea Programada)

1. Crea un archivo `import.bat`:
```batch
@echo off
cd D:\gestion_operativa_natha
python import_ubigeo.py "UBIGEO 2022_1891 distritos.xlsx" --limpiar
pause
```

2. Programa una tarea en "Tareas Programadas" de Windows que ejecute este archivo

### En Linux/Mac (Cron)

```bash
# Editar crontab
crontab -e

# Agregar línea para ejecutar cada domingo a las 2 AM
0 2 * * 0 cd /ruta/proyecto && python import_ubigeo.py "archivo.xlsx" --limpiar
```

---

¿Preguntas? Revisa las opciones con `--help`:

```bash
python import_ubigeo.py --help
php artisan ubigeo:import --help
```
