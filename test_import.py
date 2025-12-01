#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Prueba simple del script de importacion
"""

import sys
import os

# Cambiar al directorio del script
os.chdir(r'd:\gestion_operativa_natha')
sys.path.insert(0, r'd:\gestion_operativa_natha')

# Importar y ejecutar el script principal
if __name__ == '__main__':
    from import_ubigeo import importar_ubigeo
    
    archivo = 'UBIGEO 2022_1891 distritos.xlsx'
    ruta_archivo = os.path.join(os.path.dirname(__file__), archivo)
    
    print(f"Archivo: {ruta_archivo}")
    print(f"Existe: {os.path.exists(ruta_archivo)}")
    
    if os.path.exists(ruta_archivo):
        success = importar_ubigeo(ruta_archivo)
        sys.exit(0 if success else 1)
    else:
        print("Archivo no encontrado")
        sys.exit(1)
