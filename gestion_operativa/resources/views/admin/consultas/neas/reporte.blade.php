<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte NEA - {{ $nea->nro_documento }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #007bff;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .info-section {
            background-color: #f8f9fa;
            padding: 12px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }

        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 8px;
        }

        .info-item {
            font-size: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #333;
        }

        .info-value {
            color: #666;
        }

        .section-title {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        thead {
            background-color: #e9ecef;
        }

        th {
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #007bff;
            font-size: 9px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        .totales {
            margin-top: 15px;
            padding: 12px;
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
        }

        .total-row {
            display: grid;
            grid-template-columns: auto auto;
            gap: 50px;
            margin-bottom: 8px;
            justify-content: flex-end;
        }

        .total-label {
            font-weight: bold;
        }

        .total-value {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #999;
        }

        .resumen-stock {
            background-color: #f0f8ff;
            padding: 10px;
            margin: 15px 0;
            border-radius: 4px;
        }

        .resumen-titulo {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 8px;
        }

        .stock-item {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 8px;
            padding: 8px;
            background-color: white;
            border-radius: 3px;
        }

        .stock-label {
            font-size: 9px;
            color: #666;
        }

        .stock-value {
            font-size: 10px;
            font-weight: bold;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📋 REPORTE DE ENTRADA DE ALMACÉN (NEA)</h1>
            <p>Consulta de Movimientos de Materiales</p>
        </div>

        <!-- Información General -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">NEA Nº:</span>
                    <span class="info-value">{{ $nea->nro_documento }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Proveedor:</span>
                    <span class="info-value">{{ $nea->proveedor ? $nea->proveedor->razon_social : 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha:</span>
                    <span class="info-value">{{ $nea->fecha->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Tipo de Comprobante:</span>
                    <span class="info-value">{{ $nea->tipoComprobante ? $nea->tipoComprobante->nombre : 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Usuario de Creación:</span>
                    <span class="info-value">{{ $nea->usuarioCreacion ? $nea->usuarioCreacion->name : 'Sistema' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha de Creación:</span>
                    <span class="info-value">{{ $nea->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Movimientos de Entrada -->
        @if($movimientosEntrada->count() > 0)
            <div class="section-title">✅ MOVIMIENTOS DE ENTRADA</div>
            <table>
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Código</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Precio Unit.</th>
                        <th class="text-right">Subtotal</th>
                        <th>IGV</th>
                        <th class="text-right">Total</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalEntrada = 0;
                        $totalEntradaIgv = 0;
                    @endphp
                    @foreach($movimientosEntrada as $mov)
                        @php
                            $subtotal = $mov->cantidad * $mov->precio_unitario;
                            $igv = $mov->incluye_igv ? $subtotal * 0.18 : 0;
                            $totalEntrada += $subtotal;
                            $totalEntradaIgv += $igv;
                        @endphp
                        <tr>
                            <td>{{ $mov->material ? $mov->material->nombre : 'N/A' }}</td>
                            <td>{{ $mov->material ? $mov->material->codigo_material : 'N/A' }}</td>
                            <td class="text-right">{{ number_format($mov->cantidad, 3, ',', '.') }}</td>
                            <td class="text-right">S/ {{ number_format($mov->precio_unitario ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">S/ {{ number_format($subtotal, 2, ',', '.') }}</td>
                            <td class="text-center">
                                @if($mov->incluye_igv)
                                    <span class="badge badge-info">Sí</span>
                                @else
                                    <span>No</span>
                                @endif
                            </td>
                            <td class="text-right">S/ {{ number_format($subtotal + $igv, 2, ',', '.') }}</td>
                            <td>{{ $mov->fecha ? $mov->fecha->format('d/m/Y') : '' }}</td>
                            <td>{{ $mov->usuarioCreacion ? $mov->usuarioCreacion->name : 'Sistema' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Movimientos de Salida -->
        @if($movimientosSalida->count() > 0)
            <div class="section-title">❌ MOVIMIENTOS DE SALIDA</div>
            <table>
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Código</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Precio Unit.</th>
                        <th class="text-right">Subtotal</th>
                        <th>IGV</th>
                        <th class="text-right">Total</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalSalida = 0;
                        $totalSalidaIgv = 0;
                    @endphp
                    @foreach($movimientosSalida as $mov)
                        @php
                            $subtotal = $mov->cantidad * $mov->precio_unitario;
                            $igv = $mov->incluye_igv ? $subtotal * 0.18 : 0;
                            $totalSalida += $subtotal;
                            $totalSalidaIgv += $igv;
                        @endphp
                        <tr>
                            <td>{{ $mov->material ? $mov->material->nombre : 'N/A' }}</td>
                            <td>{{ $mov->material ? $mov->material->codigo_material : 'N/A' }}</td>
                            <td class="text-right">{{ number_format($mov->cantidad, 3, ',', '.') }}</td>
                            <td class="text-right">S/ {{ number_format($mov->precio_unitario ?? 0, 2, ',', '.') }}</td>
                            <td class="text-right">S/ {{ number_format($subtotal, 2, ',', '.') }}</td>
                            <td class="text-center">
                                @if($mov->incluye_igv)
                                    <span class="badge badge-info">Sí</span>
                                @else
                                    <span>No</span>
                                @endif
                            </td>
                            <td class="text-right">S/ {{ number_format($subtotal + $igv, 2, ',', '.') }}</td>
                            <td>{{ $mov->fecha ? $mov->fecha->format('d/m/Y') : '' }}</td>
                            <td>{{ $mov->usuarioCreacion ? $mov->usuarioCreacion->name : 'Sistema' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Resumen de Stock -->
        <div class="resumen-stock">
            <div class="resumen-titulo">📦 RESUMEN DE STOCK</div>
            @php
                $totalStock = 0;
                $totalValor = 0;
            @endphp
            @foreach($nea->detalles as $detalle)
                @php
                    $cantidadSalida = $movimientosSalida->where('material_id', $detalle->material_id)->sum('cantidad');
                    $stock = $detalle->cantidad - $cantidadSalida;
                    $valor = $stock * $detalle->precio_unitario;
                    $totalStock += $stock;
                    $totalValor += $valor;
                @endphp
                <div class="stock-item">
                    <div>
                        <div class="stock-label">Material</div>
                        <div class="stock-value">{{ $detalle->material_nombre }}</div>
                    </div>
                    <div>
                        <div class="stock-label">Entrada: {{ number_format($detalle->cantidad, 3, ',', '.') }} - Salida: {{ number_format($cantidadSalida, 3, ',', '.') }}</div>
                        <div class="stock-value text-success">Stock: {{ number_format($stock, 3, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="stock-label">Precio Unit.</div>
                        <div class="stock-value">S/ {{ number_format($detalle->precio_unitario, 2, ',', '.') }}</div>
                    </div>
                    <div>
                        <div class="stock-label">Valor Stock</div>
                        <div class="stock-value">S/ {{ number_format($valor, 2, ',', '.') }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Totales -->
        <div class="totales">
            <div class="total-row">
                <div class="total-label">Total sin IGV (Entrada):</div>
                <div class="total-value">S/ {{ number_format($totalEntrada ?? 0, 2, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">IGV Total (Entrada):</div>
                <div class="total-value">S/ {{ number_format($totalEntradaIgv ?? 0, 2, ',', '.') }}</div>
            </div>
            <div class="total-row">
                <div class="total-label"><strong>Total con IGV (Entrada):</strong></div>
                <div class="total-value">S/ {{ number_format(($totalEntrada ?? 0) + ($totalEntradaIgv ?? 0), 2, ',', '.') }}</div>
            </div>
            @if($movimientosSalida->count() > 0)
                <hr style="margin: 10px 0;">
                <div class="total-row">
                    <div class="total-label">Total Salida:</div>
                    <div class="total-value">S/ {{ number_format($totalSalida ?? 0, 2, ',', '.') }}</div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Este documento fue generado automáticamente por el Sistema de Gestión Operativa</p>
        </div>
    </div>
</body>
</html>
