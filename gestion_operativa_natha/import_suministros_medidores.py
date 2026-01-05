#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para importar Suministros y Medidores desde Excel
Analiza las columnas del Excel y valida contra la estructura de la BD
"""

import pandas as pd
import openpyxl
from pathlib import Path
from tkinter import Tk, filedialog
import json
from datetime import datetime
import sys


class SuministrosMedidoresAnalyzer:
    """Analizador de archivos Excel para Suministros y Medidores"""
    
    # Estructura esperada para Medidores
    MEDIDORES_CAMPOS = {
        'serie': {'requerido': True, 'tipo': 'string', 'max_length': 50},
        'modelo': {'requerido': True, 'tipo': 'string', 'max_length': 50},
        'capacidad_amperios': {'requerido': False, 'tipo': 'string', 'max_length': 10},
        'año_fabricacion': {'requerido': False, 'tipo': 'integer', 'max_length': 4},
        'marca': {'requerido': False, 'tipo': 'string', 'max_length': 50},
        'numero_hilos': {'requerido': False, 'tipo': 'integer'},
        'material_id': {'requerido': False, 'tipo': 'integer'},
        'fm': {'requerido': False, 'tipo': 'string', 'max_length': 50},
        'estado': {'requerido': False, 'tipo': 'boolean', 'default': True},
    }
    
    # Estructura esperada para Suministros
    SUMINISTROS_CAMPOS = {
        'codigo': {'requerido': True, 'tipo': 'string', 'max_length': 50, 'unico': True},
        'nombre': {'requerido': True, 'tipo': 'string'},
        'ruta': {'requerido': False, 'tipo': 'string', 'max_length': 50},
        'direccion': {'requerido': False, 'tipo': 'string'},
        'ubigeo_id': {'requerido': False, 'tipo': 'integer'},
        'referencia': {'requerido': False, 'tipo': 'string'},
        'caja': {'requerido': False, 'tipo': 'string', 'max_length': 50},
        'tarifa': {'requerido': False, 'tipo': 'string', 'max_length': 50},
        'latitud': {'requerido': False, 'tipo': 'string', 'max_length': 50},
        'longitud': {'requerido': False, 'tipo': 'string', 'max_length': 50},
        'serie': {'requerido': False, 'tipo': 'string', 'max_length': 50},
        'medidor_id': {'requerido': False, 'tipo': 'integer'},
        'estado': {'requerido': False, 'tipo': 'boolean', 'default': True},
    }
    
    # Mapeo de columnas Excel -> campos de BD (flexible)
    MAPEO_MEDIDORES = {
        'serie': ['serie', 'Serie'],
        'modelo': ['nombremodelo', 'NombreModelo', 'modelo', 'Modelo'],
        'capacidad_amperios': ['capacidad_amperios', 'capacidad', 'Capacidad'],
        'año_fabricacion': ['anofabricacion', 'AnoFabricacion', 'año_fabricacion', 'Año Fabricación'],
        'marca': ['nombremarca', 'NombreMarca', 'marca', 'Marca'],
        'numero_hilos': ['hilos', 'Hilos', 'numero_hilos', 'Número Hilos'],
        'fm': ['constanterotacion', 'ConstanteRotacion', 'fm', 'FM'],
        'material_id': ['material_id', 'codigoestadomedidor', 'CodigoEstadoMedidor'],
    }
    
    MAPEO_SUMINISTROS = {
        'codigo': ['codigosuministro', 'CodigoSuministro', 'codigo', 'Código'],
        'nombre': ['nombresuministro', 'NombreSuministro', 'nombre', 'Nombre'],
        'ruta': ['codigorutasuministro', 'CodigoRutaSuministro', 'ruta', 'Ruta'],
        'direccion': ['direccionpredio', 'DireccionPredio', 'dirección', 'Dirección'],
        'ubigeo_id': ['codigoubigeo', 'CodigoUbigeo', 'ubigeo_id', 'UBIGEO'],
        'referencia': ['referenciaubicacionpredio', 'ReferenciaUbicacionPredio', 'referencia', 'Referencia'],
        'tarifa': ['nombretarifa', 'NombreTarifa', 'tarifa', 'Tarifa'],
        'latitud': ['latitud', 'Latitud'],
        'longitud': ['longitud', 'Longitud'],
        'serie': ['serie', 'Serie'],
        'medidor_id': ['codigomedidor', 'CodigoMedidor', 'medidor_id'],
    }
    
    def __init__(self):
        self.archivo_excel = None
        self.df_medidores = None
        self.df_suministros = None
        self.mapeo_columnas_medidores = {}
        self.mapeo_columnas_suministros = {}
        self.analisis_resultado = {
            'medidores': {},
            'suministros': {},
            'errores': [],
            'advertencias': [],
            'resumen': {}
        }
    
    def seleccionar_archivo(self):
        """Permite al usuario seleccionar un archivo Excel"""
        root = Tk()
        root.withdraw()  # Oculta la ventana principal
        
        archivo = filedialog.askopenfilename(
            title="Selecciona el archivo Excel",
            filetypes=[("Excel files", "*.xlsx *.xls"), ("All files", "*.*")]
        )
        
        if not archivo:
            print("❌ No se seleccionó archivo")
            sys.exit(1)
        
        archivo_path = Path(archivo)
        if not archivo_path.exists():
            print(f"❌ El archivo no existe: {archivo}")
            sys.exit(1)
        
        self.archivo_excel = archivo_path
        print(f"✓ Archivo seleccionado: {self.archivo_excel.name}")
        return self.archivo_excel
    
    def cargar_hojas(self):
        """Carga las hojas del Excel"""
        try:
            excel_file = pd.ExcelFile(self.archivo_excel)
            hojas_disponibles = excel_file.sheet_names
            print(f"\n📋 Hojas disponibles en el archivo:")
            for i, hoja in enumerate(hojas_disponibles, 1):
                print(f"  {i}. {hoja}")
            
            # Busca hojas con nombres similares
            hoja_medidores = None
            hoja_suministros = None
            
            for hoja in hojas_disponibles:
                hoja_lower = hoja.lower()
                if 'medidor' in hoja_lower:
                    hoja_medidores = hoja
                if 'suministro' in hoja_lower:
                    hoja_suministros = hoja
            
            if hoja_medidores:
                print(f"\n✓ Hoja de medidores encontrada: {hoja_medidores}")
                self.df_medidores = pd.read_excel(self.archivo_excel, sheet_name=hoja_medidores)
            
            if hoja_suministros:
                print(f"✓ Hoja de suministros encontrada: {hoja_suministros}")
                self.df_suministros = pd.read_excel(self.archivo_excel, sheet_name=hoja_suministros)
            
            if not hoja_medidores and not hoja_suministros:
                print("\n⚠️  No se encontraron hojas con nombres 'medidor' o 'suministro'")
                print("Ingresa manualmente los índices de las hojas:")
                if hojas_disponibles:
                    indice_med = input(f"Índice para medidores (0-{len(hojas_disponibles)-1}): ").strip()
                    indice_sum = input(f"Índice para suministros (0-{len(hojas_disponibles)-1}): ").strip()
                    
                    try:
                        if indice_med.isdigit():
                            self.df_medidores = pd.read_excel(self.archivo_excel, sheet_name=int(indice_med))
                        if indice_sum.isdigit():
                            self.df_suministros = pd.read_excel(self.archivo_excel, sheet_name=int(indice_sum))
                    except Exception as e:
                        print(f"❌ Error al cargar hojas: {e}")
                        return False
            
            return True
        except Exception as e:
            print(f"❌ Error al leer el archivo Excel: {e}")
            return False
    
    def _encontrar_columna(self, columnas_excel, alternativas):
        """Busca una columna en el Excel considerando diferentes variaciones"""
        columnas_lower = {col.lower().replace(' ', '').replace('_', ''): col for col in columnas_excel}
        
        for alternativa in alternativas:
            alternativa_clean = alternativa.lower().replace(' ', '').replace('_', '')
            if alternativa_clean in columnas_lower:
                return columnas_lower[alternativa_clean]
        
        return None
    
    def _crear_mapeo_medidores(self, columnas):
        """Crea un mapeo de columnas Excel -> campos de BD para medidores"""
        mapeo = {}
        campos_encontrados = []
        campos_faltantes = []
        
        for campo_bd, alternativas in self.MAPEO_MEDIDORES.items():
            col_encontrada = self._encontrar_columna(columnas, alternativas)
            if col_encontrada:
                mapeo[campo_bd] = col_encontrada
                campos_encontrados.append((campo_bd, col_encontrada))
            else:
                if self.MEDIDORES_CAMPOS[campo_bd]['requerido']:
                    campos_faltantes.append(campo_bd)
        
        self.mapeo_columnas_medidores = mapeo
        return campos_encontrados, campos_faltantes
    
    def _crear_mapeo_suministros(self, columnas):
        """Crea un mapeo de columnas Excel -> campos de BD para suministros"""
        mapeo = {}
        campos_encontrados = []
        campos_faltantes = []
        
        for campo_bd, alternativas in self.MAPEO_SUMINISTROS.items():
            col_encontrada = self._encontrar_columna(columnas, alternativas)
            if col_encontrada:
                mapeo[campo_bd] = col_encontrada
                campos_encontrados.append((campo_bd, col_encontrada))
            else:
                if self.SUMINISTROS_CAMPOS[campo_bd]['requerido']:
                    campos_faltantes.append(campo_bd)
        
        self.mapeo_columnas_suministros = mapeo
        return campos_encontrados, campos_faltantes
    
    def analizar_columnas(self):
        """Analiza las columnas de cada hoja"""
        print("\n" + "="*70)
        print("ANÁLISIS DE COLUMNAS")
        print("="*70)
        
        if self.df_medidores is not None:
            self._analizar_medidores()
        
        if self.df_suministros is not None:
            self._analizar_suministros()
        
        self._generar_resumen()
    
    def _analizar_medidores(self):
        """Analiza la hoja de medidores"""
        print("\n📊 MEDIDORES")
        print("-" * 70)
        
        columnas = self.df_medidores.columns.tolist()
        print(f"Columnas encontradas en Excel ({len(columnas)}):")
        for i, col in enumerate(columnas, 1):
            print(f"  {i:2d}. {col}")
        
        # Crear mapeo de columnas
        campos_encontrados, campos_faltantes = self._crear_mapeo_medidores(columnas)
        
        self.analisis_resultado['medidores']['columnas_encontradas'] = columnas
        self.analisis_resultado['medidores']['total_filas'] = len(self.df_medidores)
        self.analisis_resultado['medidores']['mapeo'] = self.mapeo_columnas_medidores
        
        print(f"\n✓ Mapeo de columnas identificadas:")
        for campo_bd, col_excel in campos_encontrados:
            print(f"  • {campo_bd:20s} <- {col_excel}")
        
        # Validar campos requeridos
        if campos_faltantes:
            msg = f"❌ Campos requeridos faltantes en mapeo: {', '.join(campos_faltantes)}"
            print(f"\n{msg}")
            self.analisis_resultado['errores'].append(msg)
        else:
            print(f"\n✓ Todos los campos requeridos están mapeados")
            self.analisis_resultado['medidores']['validacion'] = 'PASADA'
        
        # Analizar datos
        print(f"\nMuestra de datos (primeras 3 filas):")
        print(self.df_medidores.head(3).to_string())
    
    
    def _analizar_suministros(self):
        """Analiza la hoja de suministros"""
        print("\n📊 SUMINISTROS")
        print("-" * 70)
        
        columnas = self.df_suministros.columns.tolist()
        print(f"Columnas encontradas en Excel ({len(columnas)}):")
        for i, col in enumerate(columnas, 1):
            print(f"  {i:2d}. {col}")
        
        # Crear mapeo de columnas
        campos_encontrados, campos_faltantes = self._crear_mapeo_suministros(columnas)
        
        self.analisis_resultado['suministros']['columnas_encontradas'] = columnas
        self.analisis_resultado['suministros']['total_filas'] = len(self.df_suministros)
        self.analisis_resultado['suministros']['mapeo'] = self.mapeo_columnas_suministros
        
        print(f"\n✓ Mapeo de columnas identificadas:")
        for campo_bd, col_excel in campos_encontrados:
            print(f"  • {campo_bd:20s} <- {col_excel}")
        
        # Validar campos requeridos
        if campos_faltantes:
            msg = f"❌ Campos requeridos faltantes en mapeo: {', '.join(campos_faltantes)}"
            print(f"\n{msg}")
            self.analisis_resultado['errores'].append(msg)
        else:
            print(f"\n✓ Todos los campos requeridos están mapeados")
            self.analisis_resultado['suministros']['validacion'] = 'PASADA'
        
        # Analizar datos
        print(f"\nMuestra de datos (primeras 3 filas):")
        print(self.df_suministros.head(3).to_string())
    
    def _generar_resumen(self):
        """Genera un resumen del análisis"""
        print("\n" + "="*70)
        print("RESUMEN DEL ANÁLISIS")
        print("="*70)
        
        total_errores = len(self.analisis_resultado['errores'])
        total_advertencias = len(self.analisis_resultado['advertencias'])
        
        print(f"\n📈 Estadísticas:")
        if self.df_medidores is not None:
            print(f"  • Medidores: {self.analisis_resultado['medidores']['total_filas']} registros")
        if self.df_suministros is not None:
            print(f"  • Suministros: {self.analisis_resultado['suministros']['total_filas']} registros")
        
        print(f"\n⚠️  Errores encontrados: {total_errores}")
        for error in self.analisis_resultado['errores']:
            print(f"  • {error}")
        
        print(f"\n💡 Advertencias: {total_advertencias}")
        for advertencia in self.analisis_resultado['advertencias']:
            print(f"  • {advertencia}")
        
        if total_errores == 0:
            print("\n✅ El análisis se completó sin errores críticos")
            print("   Puedes proceder con la importación de datos")
        else:
            print("\n❌ Hay errores que deben resolverse antes de importar")
        
        self.analisis_resultado['resumen'] = {
            'fecha_analisis': datetime.now().isoformat(),
            'archivo': str(self.archivo_excel.name),
            'total_errores': total_errores,
            'total_advertencias': total_advertencias,
            'estado': 'APROBADO' if total_errores == 0 else 'RECHAZADO'
        }
    
    def guardar_analisis(self):
        """Guarda el análisis en un archivo JSON"""
        archivo_salida = self.archivo_excel.parent / f"analisis_{self.archivo_excel.stem}_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
        
        try:
            with open(archivo_salida, 'w', encoding='utf-8') as f:
                json.dump(self.analisis_resultado, f, indent=2, ensure_ascii=False)
            print(f"\n💾 Análisis guardado en: {archivo_salida}")
            return archivo_salida
        except Exception as e:
            print(f"❌ Error al guardar el análisis: {e}")
            return None
    
    def ejecutar(self):
        """Ejecuta el análisis completo"""
        print("\n" + "="*70)
        print("IMPORTADOR DE SUMINISTROS Y MEDIDORES")
        print("="*70)
        
        # Seleccionar archivo
        self.seleccionar_archivo()
        
        # Cargar hojas
        if not self.cargar_hojas():
            return False
        
        # Analizar columnas
        self.analizar_columnas()
        
        # Guardar análisis
        self.guardar_analisis()
        
        return self.analisis_resultado['resumen']['estado'] == 'APROBADO'


def main():
    """Función principal"""
    analizador = SuministrosMedidoresAnalyzer()
    resultado = analizador.ejecutar()
    
    if resultado:
        print("\n✅ El archivo está listo para importación")
    else:
        print("\n⚠️  El archivo tiene problemas pero se generó el mapeo")


if __name__ == '__main__':
    main()
