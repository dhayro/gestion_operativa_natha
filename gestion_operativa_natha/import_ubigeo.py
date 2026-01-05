# -*- coding: utf-8 -*-
"""
Script para importar datos de UBIGEO desde Excel a la base de datos
Estructura jerarquica: Departamento -> Provincia -> Distrito
IDDIST es el codigo postal unico del distrito
"""
import pandas as pd
import mysql.connector
from mysql.connector import Error
import os
import sys
from dotenv import load_dotenv

# Cargar variables de entorno
env_path = os.path.join(os.path.dirname(__file__), 'gestion_operativa', '.env')
load_dotenv(env_path)

# Configuracion de conexion
config = {
    'host': os.getenv('DB_HOST', 'localhost'),
    'user': os.getenv('DB_USERNAME', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'database': os.getenv('DB_DATABASE', 'gestion_operativa'),
}

def conectar_db():
    """Conectar a la base de datos"""
    try:
        connection = mysql.connector.connect(**config)
        if connection.is_connected():
            print("[OK] Conexion exitosa a la base de datos")
            return connection
    except Error as e:
        print(f"[ERROR] Error de conexion: {e}")
        return None

def limpiar_tabla(connection):
    """Limpiar la tabla ubigeos para reimportar"""
    cursor = connection.cursor()
    try:
        cursor.execute("SET FOREIGN_KEY_CHECKS=0")
        cursor.execute("TRUNCATE TABLE ubigeos")
        cursor.execute("SET FOREIGN_KEY_CHECKS=1")
        connection.commit()
        print("[OK] Tabla 'ubigeos' limpiada")
    except Error as e:
        print(f"[WARNING] No se pudo limpiar tabla: {e}")
    finally:
        cursor.close()

def insertar_departamentos(connection, df):
    """Insertar departamentos unicos"""
    cursor = connection.cursor()
    
    # Departamentos unicos
    depts = df[['NOMBDEP']].drop_duplicates().sort_values('NOMBDEP')
    
    print(f"\n[INFO] Insertando departamentos...")
    
    sql_insert = "INSERT IGNORE INTO ubigeos (nombre, codigo_postal, estado) VALUES (%s, %s, 1)"
    
    insertados = 0
    dept_ids = {}
    
    for idx, (_, row) in enumerate(depts.iterrows()):
        try:
            dept_nombre = str(row['NOMBDEP']).strip()
            # Departamentos sin codigo postal
            cursor.execute(sql_insert, (dept_nombre, None))
            insertados += 1
        except Exception as e:
            print(f"[ERROR] Error insertando departamento: {e}")
    
    connection.commit()
    print(f"[OK] {insertados} departamentos insertados")
    
    # Obtener IDs de departamentos por nombre
    cursor.execute("SELECT id, nombre FROM ubigeos WHERE dependencia_id IS NULL AND codigo_postal IS NULL")
    for row in cursor.fetchall():
        dept_ids[row[1]] = row[0]
    
    cursor.close()
    return dept_ids

def insertar_provincias(connection, df, dept_ids):
    """Insertar provincias con referencia a departamento"""
    cursor = connection.cursor()
    
    # Agrupar provincias por departamento
    prov_por_dept = df.groupby('NOMBDEP')['NOMBPROV'].unique()
    
    print(f"\n[INFO] Insertando provincias...")
    
    sql_insert = "INSERT IGNORE INTO ubigeos (nombre, codigo_postal, dependencia_id, estado) VALUES (%s, %s, %s, 1)"
    
    insertados = 0
    prov_ids = {}
    
    for dept_nombre, provincias in prov_por_dept.items():
        dept_id = dept_ids.get(str(dept_nombre).strip())
        if not dept_id:
            print(f"[WARNING] No se encontro ID para departamento '{dept_nombre}'")
            continue
        
        for prov in provincias:
            try:
                prov_nombre = str(prov).strip()
                # Provincias sin codigo postal
                cursor.execute(sql_insert, (prov_nombre, None, dept_id))
                insertados += 1
                prov_ids[f"{dept_nombre}|{prov_nombre}"] = None
            except Exception as e:
                print(f"[ERROR] Error insertando provincia: {e}")
    
    connection.commit()
    print(f"[OK] {insertados} provincias insertadas")
    
    # Obtener IDs de provincias por nombre
    cursor.execute("SELECT id, nombre FROM ubigeos WHERE codigo_postal IS NULL AND dependencia_id IS NOT NULL")
    for row in cursor.fetchall():
        prov_nombre = row[1]
        for key in list(prov_ids.keys()):
            if key.split('|')[1] == prov_nombre:
                prov_ids[key] = row[0]
                break
    
    cursor.close()
    return prov_ids

def insertar_distritos(connection, df, prov_ids):
    """Insertar distritos con referencia a provincia"""
    cursor = connection.cursor()
    
    print(f"\n[INFO] Insertando {len(df)} distritos...")
    
    sql_insert = "INSERT IGNORE INTO ubigeos (nombre, codigo_postal, dependencia_id, estado) VALUES (%s, %s, %s, 1)"
    
    insertados = 0
    errores = 0
    
    for index, row in df.iterrows():
        try:
            dept_nombre = str(row['NOMBDEP']).strip()
            prov_nombre = str(row['NOMBPROV']).strip()
            dist_nombre = str(row['NOMBDIST']).strip()
            iddist = str(row['IDDIST']).strip()
            
            # Buscar ID de la provincia
            prov_key = f"{dept_nombre}|{prov_nombre}"
            prov_id = prov_ids.get(prov_key)
            
            if not prov_id:
                print(f"[WARNING] No se encontro provincia '{prov_nombre}' en dept '{dept_nombre}'")
                continue
            
            cursor.execute(sql_insert, (dist_nombre, iddist, prov_id))
            insertados += 1
            
            # Mostrar progreso cada 100 registros
            if (index + 1) % 100 == 0:
                print(f"[INFO] Procesados: {index + 1}/{len(df)}")
                
        except Exception as e:
            errores += 1
            print(f"[ERROR] Error en fila {index + 1}: {e}")
    
    connection.commit()
    cursor.close()
    
    print(f"[OK] {insertados} distritos insertados")
    return insertados, errores

def importar_ubigeo(archivo_excel):
    """Importar datos del Excel a la BD con estructura jerarquica"""
    try:
        # Leer el Excel
        print(f"\n[INFO] Leyendo archivo: {archivo_excel}")
        df = pd.read_excel(archivo_excel)
        
        print(f"[INFO] Registros encontrados: {len(df)}")
        print(f"[INFO] Columnas: {list(df.columns)}\n")
        
        # Conectar a la BD
        connection = conectar_db()
        if not connection:
            return False
        
        # Limpiar tabla
        limpiar_tabla(connection)
        
        # Insertar en orden: Departamentos -> Provincias -> Distritos
        dept_ids = insertar_departamentos(connection, df)
        prov_ids = insertar_provincias(connection, df, dept_ids)
        dist_insertados, dist_errores = insertar_distritos(connection, df, prov_ids)
        
        connection.close()
        
        # Resumen
        total_registros = len(dept_ids) + len(prov_ids) + dist_insertados
        print(f"\n{'='*50}")
        print(f"[RESULTADO] Importacion completada")
        print(f"  - Departamentos: {len(dept_ids)}")
        print(f"  - Provincias: {len(prov_ids)}")
        print(f"  - Distritos: {dist_insertados}")
        print(f"  - Total registros: {total_registros}")
        if dist_errores:
            print(f"  - Errores: {dist_errores}")
        print(f"{'='*50}\n")
        
        return True
        
    except Exception as e:
        print(f"\n[ERROR] Error general: {e}")
        import traceback
        traceback.print_exc()
        return False

if __name__ == '__main__':
    archivo = 'UBIGEO 2022_1891 distritos.xlsx'
    ruta_archivo = os.path.join(os.path.dirname(__file__), archivo)
    
    if not os.path.exists(ruta_archivo):
        print(f"[ERROR] Archivo no encontrado: {ruta_archivo}")
        sys.exit(1)
    else:
        success = importar_ubigeo(ruta_archivo)
        sys.exit(0 if success else 1)
