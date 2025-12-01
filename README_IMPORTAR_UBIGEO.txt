# ⚡ INICIO RÁPIDO - Importar UBIGEO

## 🚀 Opción más fácil (Windows)

1. **Abre la carpeta `d:\gestion_operativa_natha` en el Explorador**

2. **Haz doble clic en el archivo `import_ubigeo.bat`**

3. **Listo!** Los datos se importarán automáticamente

> El script verificará que Python esté instalado e instalará las dependencias necesarias

---

## 📋 Instrucciones por Sistema Operativo

### Windows (Opción 1 - Más fácil)

```bash
# Simplemente ejecuta el batch
Double-click: import_ubigeo.bat
```

### Windows (Opción 2 - PowerShell)

```powershell
# Abre PowerShell y ejecuta:
cd D:\gestion_operativa_natha
.\import_ubigeo.ps1
```

### Mac / Linux

```bash
cd /ruta/a/gestion_operativa_natha
python3 import_ubigeo.py "UBIGEO 2022_1891 distritos.xlsx"
```

### Desde Laravel (Cualquier SO)

```bash
cd gestion_operativa
php artisan ubigeo:import "../UBIGEO 2022_1891 distritos.xlsx" --limpiar
```

---

## 📊 Verificar que funcionó

### Opción 1: Script Python

```bash
# Ver resumen
python check_ubigeo.py resumen

# Ver departamentos
python check_ubigeo.py departamentos

# Ver provincias de AMAZONAS
python check_ubigeo.py provincias --dep 010000

# Ver jerarquía completa de AMAZONAS
python check_ubigeo.py jerarquia 010000
```

### Opción 2: MySQL directo

```sql
-- Conecta a tu base de datos y ejecuta:
SELECT COUNT(*) FROM ubigeos;
```

### Opción 3: Laravel Tinker

```bash
cd gestion_operativa
php artisan tinker
>>> Ubigeo::count()
```

---

## 🔧 Requisitos

- **Python 3.7+** instalado ([Descargar](https://www.python.org/))
- **MySQL** corriendo localmente
- **Archivo Excel** con la estructura correcta

### Instalar dependencias (si es necesario)

```bash
pip install pandas mysql-connector-python openpyxl tabulate
```

---

## ❓ Preguntas frecuentes

**P: ¿Qué pasa si ejecuto el script varias veces?**
R: Solo la primera vez insertará los datos. Las siguientes veces, al no tener datos duplicados, no insertar nada (usa `--limpiar` para forzar reiniciar)

**P: ¿Cuánto tarda la importación?**
R: Depende del número de registros. Aproximadamente 2-5 segundos para 2500+ registros.

**P: ¿Puedo importar datos personalizados?**
R: Sí, solo asegúrate de que tu Excel tenga las columnas: IDDIST, NOMBDEP, NOMBPROV, NOMBDIST

**P: ¿Qué hago si hay error de conexión?**
R: Verifica que:
- MySQL está corriendo
- El usuario es `root`
- La base de datos es `gestion_operativa`
- Estos valores están en `gestion_operativa/.env`

---

## 📝 Archivos creados

| Archivo | Descripción |
|---------|------------|
| `import_ubigeo.py` | Script principal en Python |
| `import_ubigeo.bat` | Ejecutable para Windows |
| `import_ubigeo.ps1` | Script PowerShell |
| `check_ubigeo.py` | Verificar datos importados |
| `ubigeo_import_config.json` | Configuración (opcional) |
| `IMPORTAR_UBIGEO.md` | Documentación completa |
| `gestion_operativa/app/Console/Commands/ImportUbigeo.php` | Comando Artisan |

---

## 🎯 Próximos pasos

1. ✅ Ejecuta la importación
2. ✅ Verifica que los datos se importaron
3. ✅ Usa los datos en tu aplicación

---

**¿Necesitas ayuda?** Lee `IMPORTAR_UBIGEO.md` para documentación completa.
