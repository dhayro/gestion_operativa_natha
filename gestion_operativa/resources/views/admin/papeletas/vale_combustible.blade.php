<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vale de Combustible - {{ $dotacion->numero_vale }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.2;
            color: #333;
            background: #fff;
            padding: 8px;
            margin: 0;
        }
        
        .vale-container {
            max-width: 580px;
            margin: 0 auto;
            border: 2px solid #333;
            background: white;
            position: relative;
            page-break-after: avoid;
        }
        
        .vale-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(200, 200, 200, 0.1) 35px, rgba(200, 200, 200, 0.1) 70px);
            pointer-events: none;
            z-index: 0;
        }
        
        .vale-content {
            position: relative;
            z-index: 1;
            padding: 10px;
        }
        
        .vale-header {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .logo-section {
            flex-shrink: 0;
        }
        
        .logo-section img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .logo-placeholder {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 8px;
            text-align: center;
        }
        
        .title-section {
            flex: 1;
            text-align: center;
        }
        
        .company-name {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #2c3e50;
            margin: 0;
        }
        
        .document-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #e74c3c;
            margin: 1px 0 0 0;
        }
        
        .document-subtitle {
            font-size: 8px;
            color: #666;
            font-style: italic;
            margin: 1px 0 0 0;
        }
        
        .vale-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 4px;
            background: #f8f9fa;
            border-left: 2px solid #3498db;
            font-size: 9px;
        }
        
        .vale-info-item {
            flex: 1;
        }
        
        .vale-info-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 8px;
            text-transform: uppercase;
        }
        
        .vale-info-value {
            font-size: 10px;
            color: #333;
            font-weight: 600;
        }
        
        .main-data {
            margin: 6px 0;
            padding: 8px;
            background: linear-gradient(135deg, #ecf0f1 0%, #f8f9fa 100%);
            border-radius: 3px;
            border: 1px solid #bdc3c7;
            font-size: 10px;
        }
        
        .data-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding-bottom: 5px;
            border-bottom: 1px solid #bdc3c7;
        }
        
        .data-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .data-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 8px;
            text-transform: uppercase;
            width: 45%;
        }
        
        .data-value {
            font-size: 10px;
            color: #333;
            font-weight: 600;
            width: 55%;
            text-align: right;
        }
        
        .fuel-summary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 8px;
            margin: 6px 0;
            border-radius: 3px;
            text-align: center;
            font-size: 9px;
        }
        
        .fuel-summary-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        
        .fuel-summary-row {
            display: flex;
            justify-content: space-around;
            margin-bottom: 4px;
        }
        
        .fuel-summary-item {
            flex: 1;
            text-align: center;
        }
        
        .fuel-summary-label {
            font-size: 7px;
            opacity: 0.9;
            margin-bottom: 2px;
        }
        
        .fuel-summary-value {
            font-size: 12px;
            font-weight: bold;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 3px;
            border-radius: 2px;
            background: rgba(0,0,0,0.1);
        }
        
        .totals-section {
            background: #ecf0f1;
            padding: 8px;
            border-radius: 3px;
            margin: 6px 0;
            border-left: 3px solid #e74c3c;
            font-size: 9px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .total-row:last-child {
            margin-bottom: 0;
        }
        
        .total-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 8px;
        }
        
        .total-value {
            font-weight: bold;
            color: #e74c3c;
        }
        
        .grand-total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px;
            background: #e74c3c;
            color: white;
            border-radius: 2px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 3px;
        }
        
        .info-section {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 3px;
            padding: 8px;
            margin: 6px 0;
            font-size: 8px;
            line-height: 1.3;
        }
        
        .info-section-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 3px;
            text-transform: uppercase;
            font-size: 8px;
        }
        
        .signature-section {
            margin-top: 12px;
            display: flex;
            justify-content: space-around;
            text-align: center;
            font-size: 9px;
        }
        
        .signature-box {
            width: 45%;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 20px;
            padding-top: 3px;
            font-size: 9px;
            color: #666;
        }
        
        .vale-footer {
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #7f8c8d;
            line-height: 1.2;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .vale-container {
                border: 1px solid #333;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="vale-container">
        <div class="vale-content">
            <!-- Header con Logo -->
            <div class="vale-header">
                <div class="logo-section">
                    @php
                        $logoPath = public_path('img/logo.png');
                        $logoExists = file_exists($logoPath);
                    @endphp
                    @if($logoExists)
                        @php
                            $imageContent = file_get_contents($logoPath);
                            $base64 = base64_encode($imageContent);
                            $dataUri = 'data:image/png;base64,' . $base64;
                        @endphp
                        <img src="{{ $dataUri }}" alt="Logo Empresa">
                    @else
                        <div class="logo-placeholder">LOGO</div>
                    @endif
                </div>
                <div class="title-section">
                    <div class="company-name">Gestión Operativa</div>
                    <div class="document-title">Vale de Combustible</div>
                    <div class="document-subtitle">Autorización de suministro</div>
                </div>
            </div>
            
            <!-- Información del Vale -->
            <div class="vale-info-row">
                <div class="vale-info-item">
                    <div class="vale-info-label">Nº Vale</div>
                    <div class="vale-info-value">{{ $dotacion->numero_vale }}</div>
                </div>
                <div class="vale-info-item">
                    <div class="vale-info-label">Fecha</div>
                    <div class="vale-info-value">{{ \Carbon\Carbon::parse($dotacion->fecha_carga)->format('d/m/Y') }}</div>
                </div>
                <div class="vale-info-item">
                    <div class="vale-info-label">Papeleta</div>
                    <div class="vale-info-value">{{ $papeleta->correlativo }}</div>
                </div>
            </div>
            
            <!-- Datos Principales -->
            <div class="main-data">
                <div class="data-row">
                    <div class="data-label">Tipo Combustible</div>
                    <div class="data-value">{{ $dotacion->tipoCombustible->nombre }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">Cantidad</div>
                    <div class="data-value">{{ number_format($dotacion->cantidad_gl, 2, ',', '.') }} GL</div>
                </div>
                <div class="data-row">
                    <div class="data-label">Precio Unit.</div>
                    <div class="data-value">S/. {{ number_format($dotacion->precio_unitario, 2, ',', '.') }}</div>
                </div>
                <div class="data-row">
                    <div class="data-label">Costo Total</div>
                    <div class="data-value" style="font-size: 11px; color: #e74c3c; font-weight: bold;">
                        S/. {{ number_format($dotacion->cantidad_gl * $dotacion->precio_unitario, 2, ',', '.') }}
                    </div>
                </div>
            </div>
            
            <!-- Resumen de Combustible -->
            <div class="fuel-summary">
                <div class="fuel-summary-title">Resumen</div>
                <div class="fuel-summary-row">
                    <div class="fuel-summary-item">
                        <div class="fuel-summary-label">Tipo</div>
                        <div class="fuel-summary-value">{{ strtoupper(substr($dotacion->tipoCombustible->nombre, 0, 3)) }}</div>
                    </div>
                    <div class="fuel-summary-item">
                        <div class="fuel-summary-label">Cant (GL)</div>
                        <div class="fuel-summary-value">{{ number_format($dotacion->cantidad_gl, 1, ',', '.') }}</div>
                    </div>
                    <div class="fuel-summary-item">
                        <div class="fuel-summary-label">Total S/.</div>
                        <div class="fuel-summary-value">{{ number_format($dotacion->cantidad_gl * $dotacion->precio_unitario, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Totales -->
            <div class="totals-section">
                <div class="total-row">
                    <div class="total-label">Cantidad:</div>
                    <div class="total-value">{{ number_format($dotacion->cantidad_gl, 2, ',', '.') }} GL</div>
                </div>
                <div class="total-row">
                    <div class="total-label">P. Unitario:</div>
                    <div class="total-value">S/. {{ number_format($dotacion->precio_unitario, 2, ',', '.') }}</div>
                </div>
                <div class="grand-total-row">
                    <div>COSTO TOTAL:</div>
                    <div>S/. {{ number_format($dotacion->cantidad_gl * $dotacion->precio_unitario, 2, ',', '.') }}</div>
                </div>
            </div>
            
            <!-- Información -->
            <div class="info-section">
                <div class="info-section-title">Información</div>
                Este vale autoriza el suministro de la cantidad especificada. No se debe exceder lo autorizado.
            </div>
            
            <!-- Firma -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">Solicitante</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Autorizado</div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="vale-footer">
                <div>Vale {{ $dotacion->numero_vale }} | {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
