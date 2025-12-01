# Script para importar datos UBIGEO desde Excel
# Uso: .\import_ubigeo.ps1 -Archivo "UBIGEO 2022_1891 distritos.xlsx" -Limpiar

param(
    [string]$Archivo = "UBIGEO 2022_1891 distritos.xlsx",
    [switch]$Limpiar,
    [string]$Host = "localhost",
    [string]$Usuario = "root",
    [string]$Password = "",
    [string]$Database = "gestion_operativa"
)

Write-Host "`n" -ForegroundColor Green
Write-Host "╔════════════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║          IMPORTADOR DE DATOS UBIGEO - Gestión Operativa           ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host "`n"

# Verificar si Python está instalado
try {
    $pythonVersion = python --version 2>&1
    Write-Host "✓ Python encontrado: $pythonVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Error: Python no está instalado o no está en el PATH" -ForegroundColor Red
    Write-Host "`nDescarga Python desde: https://www.python.org/downloads/" -ForegroundColor Yellow
    Write-Host "Asegúrate de marcar 'Add Python to PATH' durante la instalación`n" -ForegroundColor Yellow
    exit 1
}

# Verificar si el archivo existe
if (-not (Test-Path $Archivo)) {
    Write-Host "❌ Error: Archivo no encontrado: $Archivo" -ForegroundColor Red
    Write-Host "`nVerifica la ruta y try again.`n" -ForegroundColor Yellow
    exit 1
}

# Verificar dependencias
Write-Host "`n📦 Verificando dependencias de Python..." -ForegroundColor Cyan
try {
    python -c "import pandas, mysql" 2>&1 | Out-Null
} catch {
    Write-Host "⚠️  Instalando dependencias necesarias..." -ForegroundColor Yellow
    & python -m pip install pandas mysql-connector-python openpyxl --quiet
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Error al instalar dependencias" -ForegroundColor Red
        exit 1
    }
}

# Mostrar información
Write-Host "`n📂 Información de importación:" -ForegroundColor Cyan
Write-Host "  • Archivo: $Archivo" -ForegroundColor White
Write-Host "  • Host: $Host" -ForegroundColor White
Write-Host "  • Database: $Database" -ForegroundColor White
if ($Limpiar) {
    Write-Host "  • Limpiar tabla: SÍ" -ForegroundColor Yellow
} else {
    Write-Host "  • Limpiar tabla: NO" -ForegroundColor White
}

# Construir comando
$cmd = "python import_ubigeo.py `"$Archivo`" --host $Host --user $Usuario --password `"$Password`" --database $Database"
if ($Limpiar) {
    $cmd += " --limpiar"
}

Write-Host "`n⏳ Iniciando importación..." -ForegroundColor Cyan
Write-Host "-" * 80 -ForegroundColor Gray

# Ejecutar
Invoke-Expression $cmd
$exitCode = $LASTEXITCODE

Write-Host "-" * 80 -ForegroundColor Gray

if ($exitCode -eq 0) {
    Write-Host "`n✅ Importación exitosa" -ForegroundColor Green
} else {
    Write-Host "`n❌ La importación falló" -ForegroundColor Red
}

Write-Host "`n"
