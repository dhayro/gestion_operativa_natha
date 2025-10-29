# ========================================================
# Script para Levantar Gestión Operativa (PowerShell)
# Inicia Laravel Server + Vite Dev Server
# ========================================================

# Cambiar a directorio del proyecto
Set-Location "d:\gestion_operativa_natha\gestion_operativa"

# Verificar que estamos en el lugar correcto
if (-not (Test-Path "artisan")) {
    Write-Host "✗ ERROR: No se encontró artisan en $(Get-Location)" -ForegroundColor Red
    Write-Host "`nAsegúrate de estar en: d:\gestion_operativa_natha\gestion_operativa" -ForegroundColor Yellow
    Read-Host "Presiona Enter para salir"
    exit 1
}

# Mostrar encabezado
Clear-Host
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                                                        ║" -ForegroundColor Cyan
Write-Host "║        GESTIÓN OPERATIVA - LEVANTAMIENTO              ║" -ForegroundColor Cyan
Write-Host "║                                                        ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Iniciar Laravel Server
Write-Host "[1/2] Iniciando Laravel Server (Puerto 8000)..." -ForegroundColor Yellow
Write-Host ""
Start-Process powershell -ArgumentList "-NoExit -Command `"Set-Location 'd:\gestion_operativa_natha\gestion_operativa'; php artisan serve --host=0.0.0.0 --port=8000`"" -WindowStyle Normal

# Esperar a que Laravel inicie
Start-Sleep -Seconds 3

# Iniciar Vite Dev Server
Write-Host "[2/2] Iniciando Vite Dev Server (Puerto 5173)..." -ForegroundColor Yellow
Write-Host ""
Start-Process powershell -ArgumentList "-NoExit -Command `"Set-Location 'd:\gestion_operativa_natha\gestion_operativa'; npm run dev`"" -WindowStyle Normal

# Mensaje final
Start-Sleep -Seconds 2
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║                                                        ║" -ForegroundColor Green
Write-Host "║              ✓ SISTEMA LEVANTADO EXITOSAMENTE          ║" -ForegroundColor Green
Write-Host "║                                                        ║" -ForegroundColor Green
Write-Host "║  URLS DISPONIBLES:                                     ║" -ForegroundColor Green
Write-Host "║  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━    ║" -ForegroundColor Green
Write-Host "║                                                        ║" -ForegroundColor Green
Write-Host "║  Desde esta máquina:                                   ║" -ForegroundColor Green
Write-Host "║    http://localhost:8000                              ║" -ForegroundColor Green
Write-Host "║    http://localhost:8000/admin/fichas-actividad      ║" -ForegroundColor Green
Write-Host "║                                                        ║" -ForegroundColor Green
Write-Host "║  Desde celular/otra máquina:                           ║" -ForegroundColor Green
Write-Host "║    http://172.10.9.11:8000                            ║" -ForegroundColor Green
Write-Host "║    http://172.10.9.11:8000/admin/fichas-actividad    ║" -ForegroundColor Green
Write-Host "║                                                        ║" -ForegroundColor Green
Write-Host "║  OTROS PUERTOS:                                        ║" -ForegroundColor Green
Write-Host "║    Vite Dev Server: http://localhost:5173             ║" -ForegroundColor Green
Write-Host "║                                                        ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
Write-Host "ℹ Se abrirán DOS ventanas de PowerShell:" -ForegroundColor Cyan
Write-Host "  1. Laravel Server (deja corriendo)" -ForegroundColor Cyan
Write-Host "  2. Vite Dev Server (deja corriendo)" -ForegroundColor Cyan
Write-Host ""
Write-Host "⚠ Para DETENER todo: Presiona Ctrl+C en cada ventana" -ForegroundColor Yellow
Write-Host ""

Read-Host "Presiona Enter para cerrar esta ventana"
