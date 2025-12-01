#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para verificar y consultar datos UBIGEO importados
"""

import mysql.connector
from mysql.connector import Error
import argparse
from tabulate import tabulate
from typing import List, Dict

class UbigeoChecker:
    """Clase para verificar y consultar datos UBIGEO"""
    
    def __init__(self, host='localhost', user='root', password='', database='gestion_operativa'):
        self.host = host
        self.user = user
        self.password = password
        self.database = database
        self.connection = None
        self.cursor = None
    
    def conectar(self) -> bool:
        """Conecta a la base de datos"""
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password,
                database=self.database
            )
            self.cursor = self.connection.cursor(dictionary=True)
            return True
        except Error as e:
            print(f"❌ Error de conexión: {e}")
            return False
    
    def desconectar(self):
        """Desconecta de la base de datos"""
        if self.connection and self.connection.is_connected():
            self.cursor.close()
            self.connection.close()
    
    def contar_registros(self) -> Dict[str, int]:
        """Cuenta registros por tipo"""
        try:
            # Total
            self.cursor.execute("SELECT COUNT(*) as total FROM ubigeos")
            total = self.cursor.fetchone()['total']
            
            # Departamentos (6 últimos dígitos son 0000)
            self.cursor.execute("SELECT COUNT(*) as total FROM ubigeos WHERE codigo_postal LIKE '%0000'")
            departamentos = self.cursor.fetchone()['total']
            
            # Provincias (últimos 2 dígitos son 00, pero no 0000)
            self.cursor.execute("""
                SELECT COUNT(*) as total FROM ubigeos 
                WHERE codigo_postal LIKE '%00' AND codigo_postal NOT LIKE '%0000'
            """)
            provincias = self.cursor.fetchone()['total']
            
            # Distritos (últimos 2 dígitos no son 00)
            self.cursor.execute("""
                SELECT COUNT(*) as total FROM ubigeos 
                WHERE codigo_postal NOT LIKE '%00'
            """)
            distritos = self.cursor.fetchone()['total']
            
            return {
                'total': total,
                'departamentos': departamentos,
                'provincias': provincias,
                'distritos': distritos
            }
        except Error as e:
            print(f"❌ Error: {e}")
            return {}
    
    def listar_departamentos(self) -> List[Dict]:
        """Lista todos los departamentos"""
        try:
            self.cursor.execute("""
                SELECT id, nombre, codigo_postal, estado 
                FROM ubigeos 
                WHERE codigo_postal LIKE '%0000'
                ORDER BY nombre
            """)
            return self.cursor.fetchall()
        except Error as e:
            print(f"❌ Error: {e}")
            return []
    
    def listar_provincias(self, codigo_dep=None) -> List[Dict]:
        """Lista provincias de un departamento"""
        try:
            if codigo_dep:
                self.cursor.execute("""
                    SELECT u.id, u.nombre, u.codigo_postal, d.nombre as departamento
                    FROM ubigeos u
                    LEFT JOIN ubigeos d ON u.dependencia_id = d.id
                    WHERE u.codigo_postal LIKE %s AND u.codigo_postal NOT LIKE '%0000'
                    ORDER BY u.nombre
                """, (codigo_dep[:2] + '%',))
            else:
                self.cursor.execute("""
                    SELECT u.id, u.nombre, u.codigo_postal, d.nombre as departamento
                    FROM ubigeos u
                    LEFT JOIN ubigeos d ON u.dependencia_id = d.id
                    WHERE u.codigo_postal LIKE '%00' AND u.codigo_postal NOT LIKE '%0000'
                    ORDER BY d.nombre, u.nombre
                """)
            return self.cursor.fetchall()
        except Error as e:
            print(f"❌ Error: {e}")
            return []
    
    def listar_distritos(self, codigo_prov=None) -> List[Dict]:
        """Lista distritos de una provincia"""
        try:
            if codigo_prov:
                self.cursor.execute("""
                    SELECT u.id, u.nombre, u.codigo_postal, p.nombre as provincia
                    FROM ubigeos u
                    LEFT JOIN ubigeos p ON u.dependencia_id = p.id
                    WHERE u.codigo_postal LIKE %s AND u.codigo_postal NOT LIKE '%00'
                    ORDER BY u.nombre
                """, (codigo_prov[:4] + '%',))
            else:
                self.cursor.execute("""
                    SELECT u.id, u.nombre, u.codigo_postal, p.nombre as provincia
                    FROM ubigeos u
                    LEFT JOIN ubigeos p ON u.dependencia_id = p.id
                    WHERE u.codigo_postal NOT LIKE '%00'
                    ORDER BY p.nombre, u.nombre
                    LIMIT 100
                """)
            return self.cursor.fetchall()
        except Error as e:
            print(f"❌ Error: {e}")
            return []
    
    def mostrar_jerarquia(self, codigo_dep: str):
        """Muestra la jerarquía de un departamento"""
        try:
            print(f"\n📊 Jerarquía del Departamento: {codigo_dep}")
            print("=" * 100)
            
            self.cursor.execute("""
                SELECT nombre FROM ubigeos WHERE codigo_postal = %s
            """, (codigo_dep,))
            result = self.cursor.fetchone()
            if not result:
                print("❌ Departamento no encontrado")
                return
            
            print(f"\n🏛️  DEPARTAMENTO: {result['nombre']}")
            
            # Provincias
            self.cursor.execute("""
                SELECT nombre, codigo_postal FROM ubigeos 
                WHERE dependencia_id = (SELECT id FROM ubigeos WHERE codigo_postal = %s)
                ORDER BY nombre
            """, (codigo_dep,))
            provincias = self.cursor.fetchall()
            
            for prov in provincias:
                print(f"\n  📍 PROVINCIA: {prov['nombre']} ({prov['codigo_postal']})")
                
                # Distritos
                self.cursor.execute("""
                    SELECT nombre, codigo_postal FROM ubigeos 
                    WHERE dependencia_id = (SELECT id FROM ubigeos WHERE codigo_postal = %s)
                    ORDER BY nombre
                """, (prov['codigo_postal'],))
                distritos = self.cursor.fetchall()
                
                for dist in distritos[:10]:  # Mostrar solo los primeros 10
                    print(f"    🏘️  {dist['nombre']} ({dist['codigo_postal']})")
                
                if len(distritos) > 10:
                    print(f"    ... y {len(distritos) - 10} distritos más")
        
        except Error as e:
            print(f"❌ Error: {e}")


def main():
    parser = argparse.ArgumentParser(description='Verificar datos UBIGEO importados')
    parser.add_argument('--host', default='localhost', help='Host de MySQL')
    parser.add_argument('--user', default='root', help='Usuario de MySQL')
    parser.add_argument('--password', default='', help='Contraseña de MySQL')
    parser.add_argument('--database', default='gestion_operativa', help='Base de datos')
    
    subparsers = parser.add_subparsers(dest='comando', help='Comando a ejecutar')
    
    # Comando: resumen
    subparsers.add_parser('resumen', help='Mostrar resumen de datos')
    
    # Comando: departamentos
    subparsers.add_parser('departamentos', help='Listar departamentos')
    
    # Comando: provincias
    prov_parser = subparsers.add_parser('provincias', help='Listar provincias')
    prov_parser.add_argument('--dep', help='Código de departamento (ej: 010000)')
    
    # Comando: distritos
    dist_parser = subparsers.add_parser('distritos', help='Listar distritos')
    dist_parser.add_argument('--prov', help='Código de provincia (ej: 010100)')
    
    # Comando: jerarquia
    jer_parser = subparsers.add_parser('jerarquia', help='Mostrar jerarquía completa')
    jer_parser.add_argument('departamento', help='Código de departamento (ej: 010000)')
    
    args = parser.parse_args()
    
    if not args.comando:
        parser.print_help()
        return
    
    # Crear instancia y conectar
    checker = UbigeoChecker(args.host, args.user, args.password, args.database)
    if not checker.conectar():
        return
    
    try:
        if args.comando == 'resumen':
            print("\n📊 RESUMEN DE DATOS UBIGEO")
            print("=" * 50)
            stats = checker.contar_registros()
            print(f"✓ Total de registros: {stats['total']}")
            print(f"  • Departamentos: {stats['departamentos']}")
            print(f"  • Provincias: {stats['provincias']}")
            print(f"  • Distritos: {stats['distritos']}")
            
        elif args.comando == 'departamentos':
            deps = checker.listar_departamentos()
            if deps:
                print("\n🏛️  DEPARTAMENTOS")
                print("=" * 80)
                tabla = [(d['codigo_postal'], d['nombre'], '✓' if d['estado'] else '✗') for d in deps]
                print(tabulate(tabla, headers=['Código', 'Departamento', 'Estado'], tablefmt='grid'))
            else:
                print("❌ No se encontraron departamentos")
        
        elif args.comando == 'provincias':
            provs = checker.listar_provincias(args.dep)
            if provs:
                print("\n📍 PROVINCIAS")
                print("=" * 80)
                tabla = [(p['codigo_postal'], p['nombre'], p.get('departamento', 'N/A')) for p in provs]
                print(tabulate(tabla, headers=['Código', 'Provincia', 'Departamento'], tablefmt='grid'))
            else:
                print("❌ No se encontraron provincias")
        
        elif args.comando == 'distritos':
            dists = checker.listar_distritos(args.prov)
            if dists:
                print("\n🏘️  DISTRITOS")
                print("=" * 80)
                tabla = [(d['codigo_postal'], d['nombre'], d.get('provincia', 'N/A')) for d in dists]
                print(tabulate(tabla, headers=['Código', 'Distrito', 'Provincia'], tablefmt='grid'))
            else:
                print("❌ No se encontraron distritos")
        
        elif args.comando == 'jerarquia':
            checker.mostrar_jerarquia(args.departamento)
    
    finally:
        checker.desconectar()


if __name__ == '__main__':
    main()
