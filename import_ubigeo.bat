@echo off
chcp 65001 >nul
REM Script para importar datos UBIGEO desde Excel
REM Uso: import_ubigeo.bat [archivo] [--limpiar]

echo.
echo ╔════════════════════════════════════════════════════════════════════╗
echo ║          IMPORTADOR DE DATOS UBIGEO - Gestión Operativa           ║
echo ╚════════════════════════════════════════════════════════════════════╝
echo.

REM Verificar si Python está instalado
python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Error: Python no está instalado o no está en el PATH
    echo.
    echo Por favor instala Python desde: https://www.python.org/downloads/
    echo Asegúrate de marcar "Add Python to PATH" durante la instalación
    echo.
    pause
    exit /b 1
)

REM Obtener ruta del archivo
if "%~1"=="" (
    set "archivo=UBIGEO 2022_1891 distritos.xlsx"
) else (
    set "archivo=%~1"
)

REM Verificar si el archivo existe
if not exist "%archivo%" (
    echo ❌ Error: Archivo no encontrado: %archivo%
    echo.
    echo Por favor verifica la ruta y try again.
    echo.
    pause
    exit /b 1
)

REM Instalar dependencias si es necesario
echo 📦 Verificando dependencias de Python...
python -c "import pandas, mysql" >nul 2>&1
if %errorlevel% neq 0 (
    echo ⚠️  Instalando dependencias necesarias...
    pip install pandas mysql-connector-python openpyxl
    if %errorlevel% neq 0 (
        echo ❌ Error al instalar dependencias
        pause
        exit /b 1
    )
)

echo.
echo 📂 Archivo: %archivo%
echo.

REM Ejecutar el script Python
if "%~2"=="--limpiar" (
    echo 🗑️  Se limpiarán los datos existentes antes de importar
    echo.
    python import_ubigeo.py "%archivo%" --limpiar
) else (
    python import_ubigeo.py "%archivo%"
)

if %errorlevel% equ 0 (
    echo.
    echo ✅ Importación exitosa
) else (
    echo.
    echo ❌ La importación falló
)

echo.
pause
