#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para insertar Suministros y Medidores en la BD desde Excel
Utiliza el archivo de análisis generado previamente
"""

import pandas as pd
import mysql.connector
from mysql.connector import Error
import json
from pathlib import Path
from datetime import datetime
import sys
from dotenv import load_dotenv
import os
from tkinter import Tk, filedialog


class InsertadorSuministrosMedidores:
    """Insertador de datos en BD para Suministros y Medidores"""
    
    def __init__(self, archivo_excel, archivo_analisis):
        self.archivo_excel = Path(archivo_excel)
        self.archivo_analisis = Path(archivo_analisis)
        self.conexion = None
        self.cursor = None
        self.analisis = {}
        self.mapeo_medidores = {}
        self.mapeo_suministros = {}
        self.df_medidores = None
        self.df_suministros = None
        self.resultados = {
            'medidores_insertados': 0,
            'medidores_errores': 0,
            'suministros_insertados': 0,
            'suministros_errores': 0,
            'errores_detalle': []
        }
    
    def cargar_analisis(self):
        """Carga el archivo de análisis JSON"""
        try:
            with open(self.archivo_analisis, 'r', encoding='utf-8') as f:
                self.analisis = json.load(f)
            
            self.mapeo_medidores = self.analisis.get('medidores', {}).get('mapeo', {})
            self.mapeo_suministros = self.analisis.get('suministros', {}).get('mapeo', {})
            
            print(f"✓ Análisis cargado: {self.archivo_analisis.name}")
            return True
        except Exception as e:
            print(f"❌ Error al cargar análisis: {e}")
            return False
    
    def cargar_excel(self):
        """Carga el archivo Excel"""
        try:
            self.df_medidores = pd.read_excel(self.archivo_excel, sheet_name='medidores')
            self.df_suministros = pd.read_excel(self.archivo_excel, sheet_name='suministros')
            
            print(f"✓ Excel cargado: {self.archivo_excel.name}")
            print(f"  - Medidores: {len(self.df_medidores)} registros")
            print(f"  - Suministros: {len(self.df_suministros)} registros")
            return True
        except Exception as e:
            print(f"❌ Error al cargar Excel: {e}")
            return False
    
    def conectar_bd(self, host, usuario, contraseña, base_datos):
        """Conecta a la base de datos MySQL"""
        try:
            self.conexion = mysql.connector.connect(
                host=host,
                user=usuario,
                password=contraseña,
                database=base_datos
            )
            self.cursor = self.conexion.cursor()
            print(f"✓ Conectado a BD: {base_datos}")
            return True
        except Error as e:
            print(f"❌ Error de conexión: {e}")
            return False
    
    def obtener_id_ubigeo(self, codigo_ubigeo):
        """Obtiene el ID del UBIGEO desde el código"""
        if pd.isna(codigo_ubigeo):
            return None
        
        try:
            codigo_str = str(int(codigo_ubigeo)) if not pd.isna(codigo_ubigeo) else None
            if not codigo_str:
                return None
            
            self.cursor.execute(
                "SELECT id FROM ubigeos WHERE codigo = %s LIMIT 1",
                (codigo_str,)
            )
            resultado = self.cursor.fetchone()
            return resultado[0] if resultado else None
        except Exception as e:
            print(f"⚠️  Error al obtener UBIGEO {codigo_ubigeo}: {e}")
            return None
    
    def obtener_id_material(self, codigo_material):
        """Obtiene el ID del material desde el código"""
        if pd.isna(codigo_material):
            return None
        
        try:
            codigo_int = int(codigo_material) if not pd.isna(codigo_material) else None
            if not codigo_int:
                return None
            
            self.cursor.execute(
                "SELECT id FROM materials WHERE id = %s LIMIT 1",
                (codigo_int,)
            )
            resultado = self.cursor.fetchone()
            return resultado[0] if resultado else None
        except Exception as e:
            print(f"⚠️  Error al obtener Material {codigo_material}: {e}")
            return None
    
    def obtener_id_medidor_por_serie(self, serie):
        """Obtiene el ID del medidor por su serie"""
        if pd.isna(serie):
            return None
        
        try:
            self.cursor.execute(
                "SELECT id FROM medidors WHERE serie = %s LIMIT 1",
                (str(serie),)
            )
            resultado = self.cursor.fetchone()
            return resultado[0] if resultado else None
        except Exception as e:
            print(f"⚠️  Error al obtener Medidor {serie}: {e}")
            return None
    
    def insertar_medidores(self, usuario_id=1):
        """Inserta los medidores en la BD"""
        print("\n" + "="*70)
        print("INSERTANDO MEDIDORES")
        print("="*70)
        
        if self.df_medidores is None or self.df_medidores.empty:
            print("⚠️  No hay medidores para insertar")
            return True
        
        total = len(self.df_medidores)
        
        for idx, fila in self.df_medidores.iterrows():
            try:
                # Obtener valores mapeados
                serie = str(fila[self.mapeo_medidores.get('serie', 'Serie')]).strip()
                modelo = str(fila[self.mapeo_medidores.get('modelo', 'NombreModelo')]).strip()
                
                # Campos opcionales
                capacidad_amperios = fila.get(self.mapeo_medidores.get('capacidad_amperios'), None)
                capacidad_amperios = str(capacidad_amperios) if not pd.isna(capacidad_amperios) else None
                
                año_fab = fila.get(self.mapeo_medidores.get('año_fabricacion', 'AnoFabricacion'), None)
                año_fabricacion = None
                if not pd.isna(año_fab):
                    try:
                        # Si es string con formato de fecha, extraer el año
                        if isinstance(año_fab, str) and len(año_fab) >= 4:
                            año_fabricacion = año_fab[:4]
                        else:
                            año_fabricacion = str(int(año_fab))
                    except:
                        año_fabricacion = None
                
                marca = fila.get(self.mapeo_medidores.get('marca', 'NombreMarca'), None)
                marca = str(marca).strip() if not pd.isna(marca) else None
                
                numero_hilos = fila.get(self.mapeo_medidores.get('numero_hilos', 'Hilos'), None)
                numero_hilos = int(numero_hilos) if not pd.isna(numero_hilos) and numero_hilos != '' else None
                
                material_id = self.obtener_id_material(
                    fila.get(self.mapeo_medidores.get('material_id', 'CodigoEstadoMedidor'), None)
                )
                
                fm = fila.get(self.mapeo_medidores.get('fm', 'ConstanteRotacion'), None)
                fm = str(fm).strip() if not pd.isna(fm) and fm != '' else None
                
                # Validar serie única
                self.cursor.execute(
                    "SELECT id FROM medidors WHERE serie = %s",
                    (serie,)
                )
                if self.cursor.fetchone():
                    print(f"⚠️  Serie duplicada (fila {idx+2}): {serie}")
                    self.resultados['medidores_errores'] += 1
                    continue
                
                # Insertar
                sql = """
                    INSERT INTO medidors 
                    (serie, modelo, capacidad_amperios, año_fabricacion, marca, 
                     numero_hilos, material_id, fm, estado, usuario_creacion_id, created_at, updated_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
                """
                
                valores = (
                    serie, modelo, capacidad_amperios, año_fabricacion, marca,
                    numero_hilos, material_id, fm, True, usuario_id
                )
                
                self.cursor.execute(sql, valores)
                self.resultados['medidores_insertados'] += 1
                
                if (self.resultados['medidores_insertados'] + self.resultados['medidores_errores']) % 1000 == 0:
                    print(f"  Procesados {self.resultados['medidores_insertados'] + self.resultados['medidores_errores']}/{total}...")
            
            except Exception as e:
                self.resultados['medidores_errores'] += 1
                error_msg = f"Fila {idx+2}: {str(e)[:100]}"
                self.resultados['errores_detalle'].append(error_msg)
                if self.resultados['medidores_errores'] <= 10:  # Mostrar primeros 10 errores
                    print(f"❌ {error_msg}")
        
        self.conexion.commit()
        print(f"\n✓ Medidores insertados: {self.resultados['medidores_insertados']}")
        print(f"❌ Medidores con error: {self.resultados['medidores_errores']}")
        
        return self.resultados['medidores_errores'] == 0
    
    def insertar_suministros(self, usuario_id=1):
        """Inserta los suministros en la BD"""
        print("\n" + "="*70)
        print("INSERTANDO SUMINISTROS")
        print("="*70)
        
        if self.df_suministros is None or self.df_suministros.empty:
            print("⚠️  No hay suministros para insertar")
            return True
        
        total = len(self.df_suministros)
        
        for idx, fila in self.df_suministros.iterrows():
            try:
                # Campos requeridos
                codigo = str(fila[self.mapeo_suministros.get('codigo', 'CodigoSuministro')]).strip()
                nombre = str(fila[self.mapeo_suministros.get('nombre', 'NombreSuministro')]).strip()
                
                # Campos opcionales
                ruta = fila.get(self.mapeo_suministros.get('ruta', 'CodigoRutaSuministro'), None)
                ruta = str(ruta).strip() if not pd.isna(ruta) and ruta != '' else None
                
                direccion = fila.get(self.mapeo_suministros.get('direccion', 'DireccionPredio'), None)
                direccion = str(direccion).strip() if not pd.isna(direccion) and direccion != '' else None
                
                # UBIGEO - necesita conversión
                ubigeo_id = self.obtener_id_ubigeo(
                    fila.get(self.mapeo_suministros.get('ubigeo_id', 'CodigoUbigeo'), None)
                )
                
                referencia = fila.get(self.mapeo_suministros.get('referencia', 'ReferenciaUbicacionPredio'), None)
                referencia = str(referencia).strip() if not pd.isna(referencia) and referencia != '' else None
                
                # Caja - no está en el mapeo, será None
                caja = None
                
                tarifa = fila.get(self.mapeo_suministros.get('tarifa', 'NombreTarifa'), None)
                tarifa = str(tarifa).strip() if not pd.isna(tarifa) and tarifa != '' else None
                
                latitud = fila.get(self.mapeo_suministros.get('latitud', 'Latitud'), None)
                latitud = str(latitud).strip() if not pd.isna(latitud) and latitud != '' else None
                
                longitud = fila.get(self.mapeo_suministros.get('longitud', 'Longitud'), None)
                longitud = str(longitud).strip() if not pd.isna(longitud) and longitud != '' else None
                
                # Serie opcional
                serie = fila.get(self.mapeo_suministros.get('serie', 'Serie'), None)
                serie = str(serie).strip() if not pd.isna(serie) and serie != '' else None
                
                # Obtener ID del medidor por serie (si existe)
                medidor_id = None
                if serie:
                    medidor_id = self.obtener_id_medidor_por_serie(serie)
                
                # Validar código único
                self.cursor.execute(
                    "SELECT id FROM suministros WHERE codigo = %s",
                    (codigo,)
                )
                if self.cursor.fetchone():
                    print(f"⚠️  Código duplicado (fila {idx+2}): {codigo}")
                    self.resultados['suministros_errores'] += 1
                    continue
                
                # Insertar
                sql = """
                    INSERT INTO suministros 
                    (codigo, nombre, ruta, direccion, ubigeo_id, referencia, 
                     caja, tarifa, latitud, longitud, serie, medidor_id, estado, 
                     usuario_creacion_id, created_at, updated_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
                """
                
                valores = (
                    codigo, nombre, ruta, direccion, ubigeo_id, referencia,
                    caja, tarifa, latitud, longitud, serie, medidor_id, True, usuario_id
                )
                
                self.cursor.execute(sql, valores)
                self.resultados['suministros_insertados'] += 1
                
                if (self.resultados['suministros_insertados'] + self.resultados['suministros_errores']) % 1000 == 0:
                    print(f"  Procesados {self.resultados['suministros_insertados'] + self.resultados['suministros_errores']}/{total}...")
            
            except Exception as e:
                self.resultados['suministros_errores'] += 1
                error_msg = f"Fila {idx+2}: {str(e)[:100]}"
                self.resultados['errores_detalle'].append(error_msg)
                if self.resultados['suministros_errores'] <= 10:  # Mostrar primeros 10 errores
                    print(f"❌ {error_msg}")
        
        self.conexion.commit()
        print(f"\n✓ Suministros insertados: {self.resultados['suministros_insertados']}")
        print(f"❌ Suministros con error: {self.resultados['suministros_errores']}")
        
        return self.resultados['suministros_errores'] == 0
    
    def generar_reporte(self):
        """Genera un reporte de la inserción"""
        print("\n" + "="*70)
        print("REPORTE DE INSERCIÓN")
        print("="*70)
        
        print(f"\n📊 MEDIDORES:")
        print(f"  ✓ Insertados: {self.resultados['medidores_insertados']}")
        print(f"  ❌ Errores: {self.resultados['medidores_errores']}")
        
        print(f"\n📊 SUMINISTROS:")
        print(f"  ✓ Insertados: {self.resultados['suministros_insertados']}")
        print(f"  ❌ Errores: {self.resultados['suministros_errores']}")
        
        total_insertados = self.resultados['medidores_insertados'] + self.resultados['suministros_insertados']
        total_errores = self.resultados['medidores_errores'] + self.resultados['suministros_errores']
        
        print(f"\n📈 TOTALES:")
        print(f"  ✓ Registros insertados: {total_insertados}")
        print(f"  ❌ Registros con error: {total_errores}")
        
        if self.resultados['errores_detalle']:
            print(f"\n⚠️  Primeros errores:")
            for error in self.resultados['errores_detalle'][:10]:
                print(f"  • {error}")
        
        if total_errores == 0:
            print(f"\n✅ ¡INSERCIÓN COMPLETADA SIN ERRORES!")
        else:
            print(f"\n⚠️  Se completó la inserción con algunos errores")
        
        # Guardar reporte
        archivo_reporte = self.archivo_excel.parent / f"reporte_insercion_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
        self.resultados['fecha_insercion'] = datetime.now().isoformat()
        
        with open(archivo_reporte, 'w', encoding='utf-8') as f:
            json.dump(self.resultados, f, indent=2, ensure_ascii=False)
        
        print(f"\n💾 Reporte guardado: {archivo_reporte}")
    
    def cerrar_conexion(self):
        """Cierra la conexión a la BD"""
        if self.cursor:
            self.cursor.close()
        if self.conexion:
            self.conexion.close()
        print("\n✓ Conexión cerrada")
    
    def ejecutar(self, host, usuario, contraseña, base_datos):
        """Ejecuta el proceso completo"""
        print("\n" + "="*70)
        print("INSERTADOR DE SUMINISTROS Y MEDIDORES")
        print("="*70)
        
        # Cargar análisis
        if not self.cargar_analisis():
            return False
        
        # Cargar Excel
        if not self.cargar_excel():
            return False
        
        # Conectar a BD
        if not self.conectar_bd(host, usuario, contraseña, base_datos):
            return False
        
        # Insertar datos
        try:
            self.insertar_medidores()
            self.insertar_suministros()
            
            # Generar reporte
            self.generar_reporte()
            
            return True
        finally:
            self.cerrar_conexion()


def main():
    """Función principal"""
    # Cargar variables de entorno
    env_path = Path(__file__).parent / '.env'
    if env_path.exists():
        load_dotenv(env_path)
    
    # Configuración de BD desde .env
    host = os.getenv('DB_HOST', '127.0.0.1')
    usuario = os.getenv('DB_USERNAME', 'root')
    contraseña = os.getenv('DB_PASSWORD', '')
    base_datos = os.getenv('DB_DATABASE', 'gestion_operativa')
    
    print("\n" + "="*70)
    print("BUSCANDO ARCHIVO DE ANALISIS...")
    print("="*70)
    
    # Archivos
    ruta_proyecto = Path(__file__).parent
    ruta_padre = ruta_proyecto.parent  # Sube un nivel
    
    # Buscar el archivo de análisis más reciente (en Downloads y proyecto)
    archivos_analisis = sorted(
        list(ruta_proyecto.glob('analisis_medidores*.json')) + 
        list(ruta_padre.glob('analisis_medidores*.json')) +
        list(Path.home().glob('Downloads/analisis_medidores*.json')), 
        key=lambda x: x.stat().st_mtime, 
        reverse=True
    )
    
    if not archivos_analisis:
        print("ERROR: No se encontro archivo de analisis")
        print("Ejecuta primero: python import_suministros_medidores.py")
        sys.exit(1)
    
    archivo_analisis = archivos_analisis[0]
    print(f"[OK] Analisis encontrado: {archivo_analisis.name}")
    
    # Seleccionar el archivo Excel
    print("\n" + "="*70)
    print("SELECCIONA EL ARCHIVO EXCEL")
    print("="*70)
    
    root = Tk()
    root.withdraw()
    
    archivo_excel = filedialog.askopenfilename(
        title="Selecciona el archivo Excel de Medidores y Suministros",
        filetypes=[("Excel files", "*.xlsx *.xls"), ("All files", "*.*")]
    )
    
    if not archivo_excel:
        print("ERROR: No se selecciono archivo Excel")
        sys.exit(1)
    
    archivo_excel = Path(archivo_excel)
    if not archivo_excel.exists():
        print(f"ERROR: El archivo no existe: {archivo_excel}")
        sys.exit(1)
    
    print(f"[OK] Excel seleccionado: {archivo_excel.name}")
    
    print("\n" + "="*70)
    
    # Ejecutar
    insertador = InsertadorSuministrosMedidores(archivo_excel, archivo_analisis)
    resultado = insertador.ejecutar(host, usuario, contraseña, base_datos)


if __name__ == '__main__':
    main()
