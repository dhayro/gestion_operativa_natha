@echo off
REM ========================================================
REM  Script para Levantar Gestión Operativa
REM  Inicia Laravel Server + Vite Dev Server
REM ========================================================

REM Cambiar a directorio del proyecto
cd /d "d:\gestion_operativa_natha\gestion_operativa"

REM Limpiar pantalla
cls

REM Mostrar encabezado
echo.
echo ╔════════════════════════════════════════════════════════╗
echo ║                                                        ║
echo ║        GESTIÓN OPERATIVA - LEVANTAMIENTO              ║
echo ║                                                        ║
echo ╚════════════════════════════════════════════════════════╝
echo.

REM Verificar que estamos en el directorio correcto
if not exist "artisan" (
    echo ✗ ERROR: No se encontró artisan en %cd%
    echo. 
    echo Asegúrate de estar en: d:\gestion_operativa_natha\gestion_operativa
    pause
    exit /b 1
)

echo [1/2] Iniciando Laravel Server (Puerto 8000)...
echo.
start "Laravel - Gestión Operativa" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

REM Esperar a que Laravel inicie
timeout /t 3 /nobreak

echo.
echo [2/2] Iniciando Vite Dev Server (Puerto 5173)...
echo.
start "Vite - Compilador Assets" cmd /k "npm run dev"

echo.
echo ╔════════════════════════════════════════════════════════╗
echo ║                                                        ║
echo ║              ✓ SISTEMA LEVANTADO EXITOSAMENTE          ║
echo ║                                                        ║
echo ║  URLS DISPONIBLES:                                     ║
echo ║  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━    ║
echo ║                                                        ║
echo ║  Desde esta máquina:                                   ║
echo ║    http://localhost:8000                              ║
echo ║    http://localhost:8000/admin/fichas-actividad      ║
echo ║                                                        ║
echo ║  Desde celular/otra máquina:                           ║
echo ║    http://172.10.9.11:8000                            ║
echo ║    http://172.10.9.11:8000/admin/fichas-actividad    ║
echo ║                                                        ║
echo ║  OTROS PUERTOS:                                        ║
echo ║    Vite Dev Server: http://localhost:5173             ║
echo ║                                                        ║
echo ╚════════════════════════════════════════════════════════╝
echo.
echo ATENCION: Se abrirán DOS ventanas.
echo   1. Laravel Server (deja corriendo)
echo   2. Vite Dev Server (deja corriendo)
echo.
echo Para DETENER todo: Presiona Ctrl+C en cada ventana
echo.
pause
