<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papeleta de Salida - {{ $papeleta->correlativo }} (Doble)</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4 landscape;
            margin: 5mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 6px;
            line-height: 1.0;
            color: #333;
            background: #fff;
            padding: 2px;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: row;
            gap: 5px;
        }
        
        .page-copy {
            border: 1px solid #333;
            background: white;
            position: relative;
            page-break-inside: avoid;
            flex: 1;
            width: 49.5%;
            height: 100%;
            overflow: hidden;
        }
        
        .copy-label {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 7px;
            z-index: 10;
        }
        
        .papeleta-container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        
        .header-table td {
            padding: 2px;
            vertical-align: middle;
        }
        
        .logo-cell {
            width: 30px;
            text-align: center;
        }
        
        .logo-container {
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        .logo-container img {
            max-width: 16px;
            max-height: 16px;
            object-fit: contain;
        }
        
        .title-cell {
            text-align: center;
        }
        
        .company-name {
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 1px;
        }
        
        .document-title {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #f39c12;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .correlativo-cell {
            width: 50px;
            text-align: center;
        }
        
        .correlativo-box {
            background: #e74c3c;
            border: 1px solid white;
            border-radius: 3px;
            padding: 2px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        .correlativo-label {
            font-size: 5px;
            font-weight: bold;
            margin-bottom: 1px;
        }
        
        .correlativo-number {
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        
        /* Content Table */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            flex: 1;
        }
        
        .content-table td,
        .content-table th {
            border: 1px solid #ddd;
            padding: 1px;
            vertical-align: top;
            font-size: 5px;
        }
        
        .section-header {
            background: #34495e;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            font-size: 6px;
            padding: 2px;
        }
        
        .label {
            background: #ecf0f1;
            font-weight: bold;
            color: #2c3e50;
            width: 25%;
            text-transform: uppercase;
            font-size: 6px;
            padding: 1px 2px;
        }
        
        .value {
            background: white;
            color: #333;
            width: 75%;
            font-size: 7px;
            padding: 1px 2px;
        }
        
        .value-large {
            min-height: 8px;
            vertical-align: top;
        }
        
        /* Vehicle Section */
        .vehicle-section {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 6px;
            padding: 2px;
        }
        
        /* Date Section */
        .date-section {
            background: #3498db;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 6px;
            padding: 1px;
        }
        
        /* Kilometraje Section */
        .km-section {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 6px;
            padding: 2px;
        }
        
        .km-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        
        .km-table td {
            border: 1px solid white;
            padding: 2px;
            text-align: center;
            background: rgba(255,255,255,0.9);
            color: #333;
            font-weight: bold;
            font-size: 5px;
        }
        
        .km-label {
            font-size: 4px;
            color: #666;
            margin-bottom: 1px;
        }
        
        /* Signatures Section */
        .signatures-header {
            background: #8e44ad;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 1px;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        
        .signature-table td {
            border: 1px solid #ddd;
            padding: 8px 3px 3px;
            text-align: center;
            background: #fafafa;
            position: relative;
            width: 33.33%;
            height: 18px;
        }
        
        .signature-line {
            position: absolute;
            bottom: 4px;
            left: 5px;
            right: 5px;
            border-bottom: 1px solid #333;
        }
        
        .signature-title {
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            font-size: 5px;
            letter-spacing: 0.3px;
        }
        
        /* Footer */
        .footer {
            background: #95a5a6;
            color: white;
            text-align: center;
            padding: 1px;
            font-size: 5px;
        }
        
        /* Status indicators */
        .status-programado { color: #f39c12; font-weight: bold; }
        .status-transito { color: #e67e22; font-weight: bold; }
        .status-completado { color: #27ae60; font-weight: bold; }
        
        /* Vehicle compact display */
        .vehicle-compact {
            font-size: 7px;
            line-height: 1.2;
        }
        
        .vehicle-row {
            margin: 1px 0;
        }
    </style>
</head>
<body>
    <!-- ORIGINAL -->
    <div class="page-copy">
        <div class="copy-label">ORIGINAL</div>
        <div class="papeleta-container">
            
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
                                <img src="{{ $dataUri }}" alt="Logo Empresa" style="width: 16px; height: 16px; object-fit: contain;">
                            @else
                                <div style="width: 16px; height: 16px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 5px; text-align: center; line-height: 1.1;">
                                    LOGO
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="title-cell">
                        <div class="company-name">Gestión Operativa</div>
                        <div class="document-title">Papeleta de Salida Vehicular</div>
                    </td>
                    <td class="correlativo-cell">
                        <div class="correlativo-box">
                            <div class="correlativo-label">DOC N°</div>
                            <div class="correlativo-number">{{ $papeleta->correlativo }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- INFORMACIÓN PRINCIPAL -->
            <table class="content-table">
                <!-- Fecha -->
                <tr>
                    <td colspan="4" class="date-section">
                        FECHA: {{ \Carbon\Carbon::parse($papeleta->fecha)->format('d/m/Y') }}
                    </td>
                </tr>
                
                <!-- Vehículo -->
                <tr>
                    <td colspan="4" class="vehicle-section">
                        VEHÍCULO ASIGNADO
                    </td>
                </tr>
                @if($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->vehiculo)
                    @php
                        $vehiculo = $papeleta->asignacionVehiculo->vehiculo;
                        if (!$vehiculo->relationLoaded('tipoCombustible')) {
                            $vehiculo->load('tipoCombustible');
                        }
                    @endphp
                    <tr>
                        <td class="label">Marca/Modelo:</td>
                        <td class="value">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td>
                        <td class="label">Placa:</td>
                        <td class="value"><strong>{{ $vehiculo->placa }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Año:</td>
                        <td class="value">{{ $vehiculo->year }}</td>
                        <td class="label">Color:</td>
                        <td class="value">{{ $vehiculo->color }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nombre:</td>
                        <td class="value">{{ $vehiculo->nombre ?? '-' }}</td>
                        <td class="label">Combustible:</td>
                        <td class="value">{{ $vehiculo->tipoCombustible->nombre ?? 'No especificado' }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="4" class="value" style="text-align: center; color: #e74c3c;">
                            <strong>⚠ SIN INFORMACIÓN DE VEHÍCULO</strong>
                        </td>
                    </tr>
                @endif
                
                <!-- Conductor -->
                <tr>
                    <td colspan="4" class="section-header">CONDUCTOR Y PERSONAL</td>
                </tr>
                <tr>
                    <td class="label">Conductor:</td>
                    <td class="value">
                        @if($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->empleado)
                            {{ $papeleta->asignacionVehiculo->empleado->nombre }} {{ $papeleta->asignacionVehiculo->empleado->apellido }}
                        @else
                            Sin asignar
                        @endif
                    </td>
                    <td class="label">Cargo:</td>
                    <td class="value">
                        {{ $papeleta->asignacionVehiculo->empleado->cargo->nombre ?? 'Sin especificar' }}
                    </td>
                </tr>
                
                <tr>
                    <td class="label">Cuadrilla:</td>
                    <td class="value" colspan="3">
                        @if($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->cuadrilla)
                            {{ $papeleta->asignacionVehiculo->cuadrilla->nombre }}
                        @else
                            Sin cuadrilla asignada
                        @endif
                    </td>
                </tr>
                
                <!-- Personal de la Comisión (solo si hay) -->
                @if($papeleta->miembros_cuadrilla || $papeleta->personal_adicional)
                    @if($papeleta->miembros_cuadrilla && count($papeleta->miembros_cuadrilla) > 0)
                    <tr>
                        <td class="label">Miembros:</td>
                        <td class="value" colspan="3">
                            @php
                                $miembros = $papeleta->miembrosCuadrillaEmpleados();
                                $nombresMiembros = $miembros->map(function($empleado) {
                                    return $empleado->nombre . ' ' . $empleado->apellido;
                                })->join(', ');
                            @endphp
                            {{ $nombresMiembros ?: 'No hay miembros seleccionados' }}
                        </td>
                    </tr>
                    @endif
                    @if($papeleta->personal_adicional)
                    <tr>
                        <td class="label">Personal Adicional:</td>
                        <td class="value value-large" colspan="3">
                            {{ $papeleta->personal_adicional }}
                        </td>
                    </tr>
                    @endif
                @endif
                
                <!-- Información del Viaje -->
                <tr>
                    <td colspan="4" class="section-header">INFORMACIÓN DEL VIAJE</td>
                </tr>
                <tr>
                    <td class="label">Destino:</td>
                    <td class="value" colspan="3">{{ $papeleta->destino }}</td>
                </tr>
                <tr>
                    <td class="label">Motivo:</td>
                    <td class="value value-large" colspan="3">{{ $papeleta->motivo }}</td>
                </tr>
                <tr>
                    <td class="label">Salida:</td>
                    <td class="value">
                        {{ $papeleta->fecha_hora_salida ? \Carbon\Carbon::parse($papeleta->fecha_hora_salida)->format('d/m/Y H:i') : 'Por registrar' }}
                    </td>
                    <td class="label">Estado:</td>
                    <td class="value">
                        @if($papeleta->fecha_hora_llegada)
                            <span class="status-completado">COMPLETADO</span>
                        @elseif($papeleta->fecha_hora_salida)
                            <span class="status-transito">EN TRÁNSITO</span>
                        @else
                            <span class="status-programado">PROGRAMADO</span>
                        @endif
                    </td>
                </tr>
                
                @if($papeleta->fecha_hora_llegada)
                <tr>
                    <td class="label">Llegada:</td>
                    <td class="value" colspan="3">
                        {{ \Carbon\Carbon::parse($papeleta->fecha_hora_llegada)->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endif
            </table>

            <!-- CONTROL DE KILOMETRAJE -->
            <table class="content-table">
                <tr>
                    <td colspan="2" class="km-section">
                        CONTROL DE KILOMETRAJE
                        <table class="km-table">
                            <tr>
                                <td>
                                    <div class="km-label">KM SALIDA</div>
                                    <div>{{ $papeleta->km_salida ? number_format($papeleta->km_salida, 0) . ' KM' : '_____________' }}</div>
                                </td>
                                <td>
                                    <div class="km-label">KM LLEGADA</div>
                                    <div>{{ $papeleta->km_llegada ? number_format($papeleta->km_llegada, 0) . ' KM' : '_____________' }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- FIRMAS -->
            <table class="content-table">
                <tr>
                    <td colspan="3" class="signatures-header">FIRMAS Y AUTORIZACIONES</td>
                </tr>
                <tr>
                    <table class="signature-table">
                        <tr>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-title">CONDUCTOR</div>
                            </td>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-title">JEFE INMEDIATO</div>
                            </td>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-title">CONTROL VEHICULAR</div>
                            </td>
                        </tr>
                    </table>
                </tr>
            </table>

            <!-- FOOTER -->
            <div class="footer">
                {{ now('America/Lima')->format('d/m/Y H:i:s') }} | {{ $papeleta->usuarioCreacion->name ?? 'Sistema' }} | Sistema de Gestión Operativa
            </div>
        </div>
    </div>

    <!-- COPIA -->
    <div class="page-copy">
        <div class="copy-label">COPIA</div>
        <div class="papeleta-container">
            
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
                                <img src="{{ $dataUri }}" alt="Logo Empresa" style="width: 16px; height: 16px; object-fit: contain;">
                            @else
                                <div style="width: 16px; height: 16px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 5px; text-align: center; line-height: 1.1;">
                                    LOGO
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="title-cell">
                        <div class="company-name">Gestión Operativa</div>
                        <div class="document-title">Papeleta de Salida Vehicular</div>
                    </td>
                    <td class="correlativo-cell">
                        <div class="correlativo-box">
                            <div class="correlativo-label">DOC N°</div>
                            <div class="correlativo-number">{{ $papeleta->correlativo }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- INFORMACIÓN PRINCIPAL -->
            <table class="content-table">
                <!-- Fecha -->
                <tr>
                    <td colspan="4" class="date-section">
                        FECHA: {{ \Carbon\Carbon::parse($papeleta->fecha)->format('d/m/Y') }}
                    </td>
                </tr>
                
                <!-- Vehículo -->
                <tr>
                    <td colspan="4" class="vehicle-section">
                        VEHÍCULO ASIGNADO
                    </td>
                </tr>
                @if($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->vehiculo)
                    @php
                        $vehiculo = $papeleta->asignacionVehiculo->vehiculo;
                        if (!$vehiculo->relationLoaded('tipoCombustible')) {
                            $vehiculo->load('tipoCombustible');
                        }
                    @endphp
                    <tr>
                        <td class="label">Marca/Modelo:</td>
                        <td class="value">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td>
                        <td class="label">Placa:</td>
                        <td class="value"><strong>{{ $vehiculo->placa }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Año:</td>
                        <td class="value">{{ $vehiculo->year }}</td>
                        <td class="label">Color:</td>
                        <td class="value">{{ $vehiculo->color }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nombre:</td>
                        <td class="value">{{ $vehiculo->nombre ?? '-' }}</td>
                        <td class="label">Combustible:</td>
                        <td class="value">{{ $vehiculo->tipoCombustible->nombre ?? 'No especificado' }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="4" class="value" style="text-align: center; color: #e74c3c;">
                            <strong>⚠ SIN INFORMACIÓN DE VEHÍCULO</strong>
                        </td>
                    </tr>
                @endif
                
                <!-- Conductor -->
                <tr>
                    <td colspan="4" class="section-header">CONDUCTOR Y PERSONAL</td>
                </tr>
                <tr>
                    <td class="label">Conductor:</td>
                    <td class="value">
                        @if($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->empleado)
                            {{ $papeleta->asignacionVehiculo->empleado->nombre }} {{ $papeleta->asignacionVehiculo->empleado->apellido }}
                        @else
                            Sin asignar
                        @endif
                    </td>
                    <td class="label">Cargo:</td>
                    <td class="value">
                        {{ $papeleta->asignacionVehiculo->empleado->cargo->nombre ?? 'Sin especificar' }}
                    </td>
                </tr>
                
                <tr>
                    <td class="label">Cuadrilla:</td>
                    <td class="value" colspan="3">
                        @if($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->cuadrilla)
                            {{ $papeleta->asignacionVehiculo->cuadrilla->nombre }}
                        @else
                            Sin cuadrilla asignada
                        @endif
                    </td>
                </tr>
                
                <!-- Personal de la Comisión (solo si hay) -->
                @if($papeleta->miembros_cuadrilla || $papeleta->personal_adicional)
                    @if($papeleta->miembros_cuadrilla && count($papeleta->miembros_cuadrilla) > 0)
                    <tr>
                        <td class="label">Miembros:</td>
                        <td class="value" colspan="3">
                            @php
                                $miembros = $papeleta->miembrosCuadrillaEmpleados();
                                $nombresMiembros = $miembros->map(function($empleado) {
                                    return $empleado->nombre . ' ' . $empleado->apellido;
                                })->join(', ');
                            @endphp
                            {{ $nombresMiembros ?: 'No hay miembros seleccionados' }}
                        </td>
                    </tr>
                    @endif
                    @if($papeleta->personal_adicional)
                    <tr>
                        <td class="label">Personal Adicional:</td>
                        <td class="value value-large" colspan="3">
                            {{ $papeleta->personal_adicional }}
                        </td>
                    </tr>
                    @endif
                @endif
                
                <!-- Información del Viaje -->
                <tr>
                    <td colspan="4" class="section-header">INFORMACIÓN DEL VIAJE</td>
                </tr>
                <tr>
                    <td class="label">Destino:</td>
                    <td class="value" colspan="3">{{ $papeleta->destino }}</td>
                </tr>
                <tr>
                    <td class="label">Motivo:</td>
                    <td class="value value-large" colspan="3">{{ $papeleta->motivo }}</td>
                </tr>
                <tr>
                    <td class="label">Salida:</td>
                    <td class="value">
                        {{ $papeleta->fecha_hora_salida ? \Carbon\Carbon::parse($papeleta->fecha_hora_salida)->format('d/m/Y H:i') : 'Por registrar' }}
                    </td>
                    <td class="label">Estado:</td>
                    <td class="value">
                        @if($papeleta->fecha_hora_llegada)
                            <span class="status-completado">COMPLETADO</span>
                        @elseif($papeleta->fecha_hora_salida)
                            <span class="status-transito">EN TRÁNSITO</span>
                        @else
                            <span class="status-programado">PROGRAMADO</span>
                        @endif
                    </td>
                </tr>
                
                @if($papeleta->fecha_hora_llegada)
                <tr>
                    <td class="label">Llegada:</td>
                    <td class="value" colspan="3">
                        {{ \Carbon\Carbon::parse($papeleta->fecha_hora_llegada)->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endif
            </table>

            <!-- CONTROL DE KILOMETRAJE -->
            <table class="content-table">
                <tr>
                    <td colspan="2" class="km-section">
                        CONTROL DE KILOMETRAJE
                        <table class="km-table">
                            <tr>
                                <td>
                                    <div class="km-label">KM SALIDA</div>
                                    <div>{{ $papeleta->km_salida ? number_format($papeleta->km_salida, 0) . ' KM' : '_____________' }}</div>
                                </td>
                                <td>
                                    <div class="km-label">KM LLEGADA</div>
                                    <div>{{ $papeleta->km_llegada ? number_format($papeleta->km_llegada, 0) . ' KM' : '_____________' }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- FIRMAS -->
            <table class="content-table">
                <tr>
                    <td colspan="3" class="signatures-header">FIRMAS Y AUTORIZACIONES</td>
                </tr>
                <tr>
                    <table class="signature-table">
                        <tr>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-title">CONDUCTOR</div>
                            </td>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-title">JEFE INMEDIATO</div>
                            </td>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-title">CONTROL VEHICULAR</div>
                            </td>
                        </tr>
                    </table>
                </tr>
            </table>

            <!-- FOOTER -->
            <div class="footer">
                {{ now('America/Lima')->format('d/m/Y H:i:s') }} | {{ $papeleta->usuarioCreacion->name ?? 'Sistema' }} | Sistema de Gestión Operativa
            </div>
        </div>
    </div>
</body>
</html>