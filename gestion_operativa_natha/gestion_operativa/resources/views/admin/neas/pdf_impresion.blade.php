<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEA - {{ $nea->nro_documento }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.2;
            color: #333;
            background: #fff;
            padding: 10px;
        }
        
        .nea-container {
            max-width: 750px;
            margin: 0 auto;
            border: 2px solid #333;
            background: white;
        }
        
        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        
        .header-table td {
            padding: 10px;
            vertical-align: middle;
        }
        
        .logo-cell {
            width: 80px;
            text-align: center;
        }
        
        .logo-container {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .logo-container img {
            max-width: 50px;
            max-height: 50px;
            object-fit: contain;
        }
        
        .title-cell {
            text-align: center;
        }
        
        .company-name {
            font-size: 17px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        
        .document-title {
            font-size: 21px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #f39c12;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .correlativo-cell {
            width: 120px;
            text-align: center;
        }
        
        .correlativo-box {
            background: #e74c3c;
            border: 2px solid white;
            border-radius: 8px;
            padding: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            color: white;
        }
        
        .correlativo-label {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .correlativo-number {
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        /* Content Table */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        
        .content-table td,
        .content-table th {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: top;
        }
        
        .section-header {
            background: #34495e;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            font-size: 14px;
            padding: 8px;
        }
        
        .label {
            background: #ecf0f1;
            font-weight: bold;
            color: #2c3e50;
            width: 25%;
            text-transform: uppercase;
            font-size: 12px;
        }
        
        .value {
            background: white;
            color: #333;
            width: 75%;
            font-size: 13px;
        }

        /* TABLA DE DETALLES */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .details-table thead {
            background: #34495e;
            color: white;
        }

        .details-table th {
            padding: 8px 6px;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 12px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .details-table td {
            padding: 6px;
            border: 1px solid #ddd;
            font-size: 12px;
        }

        .details-table .text-center {
            text-align: center;
        }

        .details-table .text-right {
            text-align: right;
        }

        /* TOTALES */
        .totales {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .totales tr {
            border: 1px solid #ddd;
        }

        .totales td {
            padding: 10px 8px;
            background: white;
        }

        .totales .label {
            text-align: right;
            font-weight: bold;
            width: 50%;
            background: #ecf0f1;
            text-transform: uppercase;
            font-size: 12px;
            color: #2c3e50;
        }

        .totales .value {
            text-align: right;
            font-weight: bold;
            font-size: 13px;
        }

        .totales .total-value {
            background: #34495e;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        /* FIRMAS */
        .signatures-header {
            background: #8e44ad;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
        }

        .signature-table td {
            border: 1px solid #ddd;
            padding: 25px 10px 10px;
            text-align: center;
            background: #fafafa;
            position: relative;
            width: 33.33%;
        }

        .signature-line {
            position: absolute;
            bottom: 15px;
            left: 15px;
            right: 15px;
            border-bottom: 1px solid #333;
        }

        .signature-title {
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        /* Date Section */
        .date-section {
            background: #3498db;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            padding: 8px;
        }

        /* Footer */
        .footer {
            background: #95a5a6;
            color: white;
            text-align: center;
            padding: 6px;
            font-size: 11px;
        }

        .anulation-warning {
            background-color: #e74c3c;
            border: 2px solid white;
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
            color: white;
            text-align: center;
            border-radius: 3px;
        }

        /* Watermark for Anulado */
        .anulado-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 90px;
            font-weight: bold;
            color: rgba(231, 76, 60, 0.15);
            text-transform: uppercase;
            pointer-events: none;
            z-index: 1000;
            letter-spacing: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .nea-container {
                margin: 0;
                border: none;
            }
        }
    </style>
</head>
<body>
    @if($nea->anulada)
        <div class="anulado-watermark">ANULADO</div>
    @endif
    <div class="nea-container">
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <div class="logo-container">
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
                            <img src="{{ $dataUri }}" alt="Logo Empresa" style="width: 50px; height: 50px; object-fit: contain;">
                        @else
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 13px; text-align: center; line-height: 1.1;">
                                LOGO
                            </div>
                        @endif
                    </div>
                </td>
                <td class="title-cell">
                    <div class="company-name">Gestión Operativa</div>
                    <div class="document-title">Nota de Entrada de Almacén</div>
                </td>
                <td class="correlativo-cell">
                    <div class="correlativo-box">
                        <div class="correlativo-label">DOCUMENTO N°</div>
                        <div class="correlativo-number">{{ $nea->nro_documento }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- INFORMACIÓN PRINCIPAL -->
        <table class="content-table">
            <!-- Fecha -->
            <tr>
                <td colspan="4" class="date-section">
                    FECHA: {{ $nea->fecha ? $nea->fecha->format('d/m/Y') : '-' }}
                </td>
            </tr>

            <!-- Proveedor -->
            <tr>
                <td colspan="4" class="section-header">
                    INFORMACIÓN DEL PROVEEDOR
                </td>
            </tr>
            <tr>
                <td class="label">Proveedor:</td>
                <td class="value">{{ $nea->proveedor->razon_social ?? '-' }}</td>
                <td class="label">RUC/DNI:</td>
                <td class="value">{{ $nea->proveedor->ruc ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tipo Comprobante:</td>
                <td class="value">{{ $nea->tipoComprobante->nombre ?? '-' }}</td>
                <td class="label">N° Comprobante:</td>
                <td class="value">{{ $nea->numero_comprobante ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Observaciones:</td>
                <td class="value" colspan="3">{{ $nea->observaciones ?? '-' }}</td>
            </tr>
        </table>

        <!-- DETALLE DE MATERIALES -->
        <table class="content-table">
            <tr>
                <td colspan="4" class="section-header">
                    DETALLE DE MATERIALES
                </td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Material</th>
                    <th style="width: 12%; text-align: center;">Cantidad</th>
                    <th style="width: 12%; text-align: center;">Unidad</th>
                    <th style="width: 18%; text-align: right;">P.U.</th>
                    <th style="width: 18%; text-align: right;">IGV?</th>
                    <th style="width: 18%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nea->detalles as $detalle)
                    @php
                        $subtotal = $detalle->cantidad * $detalle->precio_unitario;
                    @endphp
                    <tr>
                        <td><strong>{{ $detalle->material->codigo_material ?? '-' }}</strong><br>{{ $detalle->material->nombre ?? '-' }}</td>
                        <td class="text-center">{{ number_format($detalle->cantidad, 3, ',', '.') }}</td>
                        <td class="text-center">{{ $detalle->material->unidadMedida->abreviatura ?? '-' }}</td>
                        <td class="text-right">S/. {{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $detalle->incluye_igv ? 'Sí' : 'No' }}</td>
                        <td class="text-right"><strong>S/. {{ number_format($subtotal, 2, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
                @if($nea->detalles->isEmpty())
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #999;">
                            Sin detalles de materiales
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTALES -->
        <table class="content-table">
            <tr>
                <td colspan="4" class="section-header">
                    RESUMEN DE TOTALES
                </td>
            </tr>
        </table>

        <table class="totales">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">S/. {{ number_format($nea->total_sin_igv, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">IGV (18%):</td>
                <td class="value">S/. {{ number_format($nea->igv_total, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label total-value">TOTAL:</td>
                <td class="value total-value">S/. {{ number_format($nea->total_con_igv, 2, ',', '.') }}</td>
            </tr>
        </table>

        <!-- INFORMACIÓN DE ANULACIÓN -->
        @if($nea->anulada)
            <div class="anulation-warning">
                ⚠ DOCUMENTO ANULADO
            </div>
            <table class="content-table">
                <tr>
                    <td colspan="4" class="section-header">
                        DATOS DE ANULACIÓN
                    </td>
                </tr>
                <tr>
                    <td class="label">Motivo:</td>
                    <td class="value" colspan="3">{{ $nea->motivo_anulacion ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Anulada por:</td>
                    <td class="value">{{ $nea->usuarioAnulacion->name ?? 'Sistema' }}</td>
                    <td class="label">Fecha:</td>
                    <td class="value">{{ $nea->fecha_anulacion ? $nea->fecha_anulacion->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            </table>
        @endif

        <!-- FIRMAS -->
        <table class="content-table">
            <tr>
                <td colspan="4" class="signatures-header">
                    FIRMAS Y AUTORIZACIONES
                </td>
            </tr>
        </table>

        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-title">Recibido por<br>Almacén</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-title">Autorizado por<br>Jefe</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-title">Proveedor</div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <table class="content-table">
            <tr>
                <td colspan="4" class="footer">
                    <strong>Generado:</strong> {{ \Carbon\Carbon::now('America/Lima')->format('d/m/Y H:i:s') }} | 
                    <strong>Usuario:</strong> {{ $nea->usuarioCreacion->name ?? 'Sistema' }} | 
                    <strong>Sistema de Gestión Operativa</strong>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
