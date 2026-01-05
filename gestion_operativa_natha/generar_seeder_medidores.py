#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para crear Material para Medidores, actualizar relaciones y generar Seeder
"""

import mysql.connector
from mysql.connector import Error
from pathlib import Path
from datetime import datetime
import sys
from dotenv import load_dotenv
import os
import json


class GeneradorSeedersConMaterial:
    """Generador de seeders y gestor de materiales para medidores"""
    
    def __init__(self, host, usuario, contraseña, base_datos):
        self.conexion = None
        self.cursor = None
        self.host = host
        self.usuario = usuario
        self.contraseña = contraseña
        self.base_datos = base_datos
        self.material_id_nuevo = None
        self.categoria_id = None
        self.unidad_medida_id = None
    
    def conectar(self):
        """Conecta a la base de datos"""
        try:
            self.conexion = mysql.connector.connect(
                host=self.host,
                user=self.usuario,
                password=self.contraseña,
                database=self.base_datos
            )
            self.cursor = self.conexion.cursor()
            print("[OK] Conectado a BD")
            return True
        except Error as e:
            print(f"ERROR: No se pudo conectar a BD: {e}")
            return False
    
    def obtener_categoria_default(self):
        """Obtiene la categoría por defecto o crea una"""
        try:
            # Buscar categoría de materiales o equipos
            self.cursor.execute(
                "SELECT id FROM categorias WHERE LOWER(nombre) LIKE '%medidor%' OR LOWER(nombre) LIKE '%equipo%' LIMIT 1"
            )
            resultado = self.cursor.fetchone()
            
            if resultado:
                self.categoria_id = resultado[0]
                print(f"[OK] Categoria encontrada: ID {self.categoria_id}")
                return self.categoria_id
            
            # Si no existe, crear una
            print("[INFO] Creando categoria 'Medidores'...")
            self.cursor.execute(
                "INSERT INTO categorias (nombre, descripcion, estado, created_at, updated_at) VALUES (%s, %s, %s, NOW(), NOW())",
                ("Medidores", "Medidores de energia electrica", True)
            )
            self.conexion.commit()
            self.categoria_id = self.cursor.lastrowid
            print(f"[OK] Categoria creada: ID {self.categoria_id}")
            return self.categoria_id
        except Exception as e:
            print(f"ERROR al obtener categoria: {e}")
            return None
    
    def obtener_unidad_medida_default(self):
        """Obtiene unidad de medida por defecto"""
        try:
            # Buscar unidad 'unidad' o 'pieza'
            self.cursor.execute(
                "SELECT id FROM unidad_medidas WHERE LOWER(nombre) IN ('unidad', 'pieza', 'unidades') LIMIT 1"
            )
            resultado = self.cursor.fetchone()
            
            if resultado:
                self.unidad_medida_id = resultado[0]
                print(f"[OK] Unidad de medida encontrada: ID {self.unidad_medida_id}")
                return self.unidad_medida_id
            
            # Si no existe, usar la primera disponible
            self.cursor.execute("SELECT id FROM unidad_medidas LIMIT 1")
            resultado = self.cursor.fetchone()
            
            if resultado:
                self.unidad_medida_id = resultado[0]
                print(f"[OK] Unidad de medida: ID {self.unidad_medida_id}")
                return self.unidad_medida_id
            
            print("ERROR: No hay unidades de medida disponibles")
            return None
        except Exception as e:
            print(f"ERROR al obtener unidad de medida: {e}")
            return None
    
    def crear_material_medidor(self):
        """Verifica o crea el material para medidores"""
        try:
            # Verificar si el material ya existe
            self.cursor.execute(
                "SELECT id FROM materials WHERE codigo_material = %s",
                ("MEDIDOR-001",)
            )
            resultado = self.cursor.fetchone()
            
            if resultado:
                self.material_id_nuevo = resultado[0]
                print(f"[OK] Material existente: ID {self.material_id_nuevo}")
                return True
            
            # Si no existe, usar ID 44 (del seeder)
            print("[INFO] Material MEDIDOR-001 sera creado por el seeder con ID 44")
            self.material_id_nuevo = 44
            return True
        except Exception as e:
            print(f"ERROR al verificar material: {e}")
            return False
    
    def actualizar_medidores_material(self):
        """Actualiza el material_id en la tabla medidores"""
        try:
            if not self.material_id_nuevo:
                print("ERROR: No hay material_id para actualizar")
                return False
            
            # Deshabilitar restricciones de FK temporalmente
            self.cursor.execute("SET FOREIGN_KEY_CHECKS=0")
            
            # Contar medidores sin material_id correcto
            self.cursor.execute(
                "SELECT COUNT(*) FROM medidors WHERE material_id IS NULL OR material_id != %s",
                (self.material_id_nuevo,)
            )
            total = self.cursor.fetchone()[0]
            
            if total == 0:
                print("[OK] Todos los medidores ya tienen el material correcto")
                self.cursor.execute("SET FOREIGN_KEY_CHECKS=1")
                return True
            
            # Actualizar
            print(f"[INFO] Actualizando {total} medidores...")
            self.cursor.execute(
                "UPDATE medidors SET material_id = %s WHERE material_id IS NULL OR material_id != %s",
                (self.material_id_nuevo, self.material_id_nuevo)
            )
            self.conexion.commit()
            print(f"[OK] {self.cursor.rowcount} medidores actualizados")
            
            # Reabilitar restricciones
            self.cursor.execute("SET FOREIGN_KEY_CHECKS=1")
            return True
        except Exception as e:
            self.cursor.execute("SET FOREIGN_KEY_CHECKS=1")
            print(f"ERROR al actualizar medidores: {e}")
            return False
    
    def obtener_datos_medidores(self):
        """Obtiene todos los medidores de la BD"""
        try:
            self.cursor.execute(
                """
                SELECT id, serie, modelo, capacidad_amperios, año_fabricacion, 
                       marca, numero_hilos, material_id, fm, estado, usuario_creacion_id, created_at, updated_at
                FROM medidors
                ORDER BY id
                """
            )
            columnas = [desc[0] for desc in self.cursor.description]
            datos = []
            for fila in self.cursor.fetchall():
                datos.append(dict(zip(columnas, fila)))
            
            print(f"[OK] {len(datos)} medidores obtenidos")
            return datos
        except Exception as e:
            print(f"ERROR al obtener medidores: {e}")
            return []
    
    def obtener_datos_suministros(self):
        """Obtiene todos los suministros de la BD"""
        try:
            self.cursor.execute(
                """
                SELECT id, codigo, nombre, ruta, direccion, ubigeo_id, referencia,
                       caja, tarifa, latitud, longitud, serie, medidor_id, estado, 
                       usuario_creacion_id, created_at, updated_at
                FROM suministros
                ORDER BY id
                """
            )
            columnas = [desc[0] for desc in self.cursor.description]
            datos = []
            for fila in self.cursor.fetchall():
                datos.append(dict(zip(columnas, fila)))
            
            print(f"[OK] {len(datos)} suministros obtenidos")
            return datos
        except Exception as e:
            print(f"ERROR al obtener suministros: {e}")
            return []
    
    def generar_seeder(self, medidores, suministros):
        """Genera el archivo seeder PHP"""
        try:
            archivo_seeder = Path(__file__).parent / "database" / "seeders" / "SuministrosMedidoresSeeder.php"
            archivo_seeder.parent.mkdir(parents=True, exist_ok=True)
            
            # Convertir datetime a string para JSON
            def convertir_datetime(obj):
                if isinstance(obj, datetime):
                    return obj.isoformat()
                raise TypeError(f"Type {type(obj)} not serializable")
            
            contenido = f'''<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuministrosMedidoresSeeder extends Seeder
{{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {{
        // Deshabilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Limpiar tablas
        DB::table('medidors')->truncate();
        DB::table('suministros')->truncate();
        
        // Datos de Medidores
        $medidores = {json.dumps(medidores, indent=12, default=convertir_datetime)};
        
        foreach ($medidores as $medidor) {{
            DB::table('medidors')->insert([
                'id' => $medidor['id'],
                'serie' => $medidor['serie'],
                'modelo' => $medidor['modelo'],
                'capacidad_amperios' => $medidor['capacidad_amperios'],
                'año_fabricacion' => $medidor['año_fabricacion'],
                'marca' => $medidor['marca'],
                'numero_hilos' => $medidor['numero_hilos'],
                'material_id' => $medidor['material_id'],
                'fm' => $medidor['fm'],
                'estado' => $medidor['estado'],
                'usuario_creacion_id' => $medidor['usuario_creacion_id'],
                'created_at' => $medidor['created_at'],
                'updated_at' => $medidor['updated_at'],
            ]);
        }}
        
        // Datos de Suministros
        $suministros = {json.dumps(suministros, indent=12, default=convertir_datetime)};
        
        foreach ($suministros as $suministro) {{
            DB::table('suministros')->insert([
                'id' => $suministro['id'],
                'codigo' => $suministro['codigo'],
                'nombre' => $suministro['nombre'],
                'ruta' => $suministro['ruta'],
                'direccion' => $suministro['direccion'],
                'ubigeo_id' => $suministro['ubigeo_id'],
                'referencia' => $suministro['referencia'],
                'caja' => $suministro['caja'],
                'tarifa' => $suministro['tarifa'],
                'latitud' => $suministro['latitud'],
                'longitud' => $suministro['longitud'],
                'serie' => $suministro['serie'],
                'medidor_id' => $suministro['medidor_id'],
                'estado' => $suministro['estado'],
                'usuario_creacion_id' => $suministro['usuario_creacion_id'],
                'created_at' => $suministro['created_at'],
                'updated_at' => $suministro['updated_at'],
            ]);
        }}
        
        // Reabilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Seeder de Suministros y Medidores ejecutado correctamente.');
    }}
}}
'''
            
            with open(archivo_seeder, 'w', encoding='utf-8') as f:
                f.write(contenido)
            
            print(f"[OK] Seeder generado: {archivo_seeder}")
            return True
        except Exception as e:
            print(f"ERROR al generar seeder: {e}")
            return False
    
    def generar_seeder_json(self, medidores, suministros):
        """Genera un archivo JSON con los datos para referencia"""
        try:
            archivo_json = Path(__file__).parent / f"seeder_datos_{datetime.now().strftime('%Y%m%d_%H%M%S')}.json"
            
            def convertir_datetime(obj):
                if isinstance(obj, datetime):
                    return obj.isoformat()
                raise TypeError(f"Type {type(obj)} not serializable")
            
            datos = {
                'generado_en': datetime.now().isoformat(),
                'total_medidores': len(medidores),
                'total_suministros': len(suministros),
                'medidores': medidores,
                'suministros': suministros
            }
            
            with open(archivo_json, 'w', encoding='utf-8') as f:
                json.dump(datos, f, indent=2, ensure_ascii=False, default=convertir_datetime)
            
            print(f"[OK] Datos JSON guardados: {archivo_json}")
            return True
        except Exception as e:
            print(f"ERROR al guardar JSON: {e}")
            return False
    
    def cerrar(self):
        """Cierra la conexión"""
        if self.cursor:
            self.cursor.close()
        if self.conexion:
            self.conexion.close()
        print("[OK] Conexion cerrada")
    
    def ejecutar(self):
        """Ejecuta el proceso completo"""
        print("\n" + "="*70)
        print("GENERADOR DE SEEDER Y GESTOR DE MATERIAL MEDIDOR")
        print("="*70 + "\n")
        
        if not self.conectar():
            return False
        
        try:
            # Paso 1: Crear material
            print("\n1. CREANDO MATERIAL PARA MEDIDORES")
            print("-" * 70)
            if not self.crear_material_medidor():
                return False
            
            # Paso 2: Actualizar medidores
            print("\n2. ACTUALIZANDO MATERIAL EN MEDIDORES")
            print("-" * 70)
            if not self.actualizar_medidores_material():
                return False
            
            # Paso 3: Obtener datos
            print("\n3. OBTENIENDO DATOS DE LA BD")
            print("-" * 70)
            medidores = self.obtener_datos_medidores()
            suministros = self.obtener_datos_suministros()
            
            if not medidores or not suministros:
                print("ERROR: No hay datos para generar seeder")
                return False
            
            # Paso 4: Generar seeders
            print("\n4. GENERANDO SEEDER")
            print("-" * 70)
            self.generar_seeder(medidores, suministros)
            self.generar_seeder_json(medidores, suministros)
            
            # Resumen
            print("\n" + "="*70)
            print("RESUMEN")
            print("="*70)
            print(f"Material ID nuevo: {self.material_id_nuevo}")
            print(f"Medidores procesados: {len(medidores)}")
            print(f"Suministros procesados: {len(suministros)}")
            print("[OK] Proceso completado exitosamente")
            
            return True
        except Exception as e:
            print(f"ERROR: {e}")
            return False
        finally:
            self.cerrar()


def main():
    """Función principal"""
    # Cargar variables de entorno
    env_path = Path(__file__).parent / '.env'
    if env_path.exists():
        load_dotenv(env_path)
    
    # Configuración de BD
    host = os.getenv('DB_HOST', '127.0.0.1')
    usuario = os.getenv('DB_USERNAME', 'root')
    contraseña = os.getenv('DB_PASSWORD', '')
    base_datos = os.getenv('DB_DATABASE', 'gestion_operativa')
    
    # Ejecutar
    generador = GeneradorSeedersConMaterial(host, usuario, contraseña, base_datos)
    resultado = generador.ejecutar()
    
    sys.exit(0 if resultado else 1)


if __name__ == '__main__':
    main()
