# Archivos Grandes - Pasar Manualmente

Los siguientes archivos están excluidos del repositorio Git por su tamaño (>100 MB):

## Archivos a pasar manualmente:

- `gestion_operativa/database/seeders/data/seeder_datos.json` (106+ MB)
- `seeder_datos_*.json` (archivos de seeders con datos)

## Instrucciones:

1. **Para pasar estos archivos:**
   - Copiar manualmente desde tu máquina local al servidor
   - Usar FTP, SFTP, rsync o similar

2. **Ubicación en el servidor:**
   - Colocar en: `/ruta/del/proyecto/gestion_operativa/database/seeders/data/`

3. **Información de archivos:**
   - Estos archivos no son críticos para el funcionamiento base
   - Se usan para cargar datos de prueba o seeders
   - Si necesitas actualizar, descarga, modifica y sube nuevamente

## Ignorar en Git:

Estos archivos están agregados a `.gitignore`:
```gitignore
# Large seed files (para pasar manualmente)
gestion_operativa/database/seeders/data/seeder_datos.json
seeder_datos_*.json
```

---

**Última actualización:** 28 de diciembre de 2025
