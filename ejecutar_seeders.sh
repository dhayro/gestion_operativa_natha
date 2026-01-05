#!/bin/bash
# Script para ejecutar los seeders de Medidores y Suministros en Laravel

echo "======================================================================"
echo "EJECUTOR DE SEEDERS - Medidores y Suministros"
echo "======================================================================"

cd "$(dirname "$0")/gestion_operativa"

if [ ! -f "artisan" ]; then
    echo "ERROR: No se encontró artisan. Asegúrate de estar en la raíz del proyecto."
    exit 1
fi

echo ""
echo "1. Ejecutando MaterialSeeder..."
php artisan db:seed --class=MaterialSeeder

echo ""
echo "2. Ejecutando SuministrosMedidoresSeeder..."
echo "   [ADVERTENCIA] Este proceso puede tardar algunos minutos (~5-10 min)"
echo "   Tiene 121,184 medidores + 115,736 suministros"
php artisan db:seed --class=SuministrosMedidoresSeeder

echo ""
echo "======================================================================"
echo "Seeders ejecutados correctamente"
echo "======================================================================"
echo ""
echo "Verificar datos en BD:"
echo "  SELECT COUNT(*) FROM medidors;    -- debe mostrar 121,184"
echo "  SELECT COUNT(*) FROM suministros; -- debe mostrar 115,736"
echo "  SELECT COUNT(*) FROM materials;   -- debe mostrar 44 (incluye MEDIDOR-001)"
