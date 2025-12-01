"""
Configuración para la importación de UBIGEO
"""

# Si usas SQLite en lugar de MySQL, descomenta esto:
USAR_SQLITE = False

# Si USAR_SQLITE = True, especifica la ruta de tu BD
SQLITE_DB_PATH = 'gestion_operativa/database/database.sqlite'

# Configuración MySQL
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',  # Cambiar según tu contraseña
    'database': 'gestion_operativa'
}

# Ruta del Excel
EXCEL_FILE = 'UBIGEO 2022_1891 distritos.xlsx'

# Mostrar detalles de importación
VERBOSE = True
