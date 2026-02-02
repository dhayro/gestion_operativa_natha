<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papeleta de Salida - {{ $papeleta->correlativo }}</title>
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
        
        .papeleta-container {
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
        
        .value-large {
            min-height: 40px;
            vertical-align: top;
        }
        
        /* Vehicle Section */
        .vehicle-section {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 19px;
            padding: 15px;
        }
        
        /* Kilometraje Section */
        .km-section {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 17px;
        }
        
        .km-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .km-table td {
            border: 2px solid white;
            padding: 10px;
            text-align: center;
            background: rgba(255,255,255,0.9);
            color: #333;
            font-weight: bold;
            font-size: 15px;
        }
        
        .km-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 3px;
        }
        
        /* Signatures Section */
        .signatures-header {
            background: #8e44ad;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 17px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
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
            font-size: 17px;
        }
        
        /* Footer */
        .footer {
            background: #95a5a6;
            color: white;
            text-align: center;
            padding: 6px;
            font-size: 12px;
        }
        
        /* Status indicators */
        .status-programado { color: #f39c12; font-weight: bold; }
        .status-transito { color: #e67e22; font-weight: bold; }
        .status-completado { color: #27ae60; font-weight: bold; }
        
        .highlight {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 8px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
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
                                // Usar file_get_contents que no requiere GD
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
                    <div class="document-title">Papeleta de Salida Vehicular</div>
                </td>
                <td class="correlativo-cell">
                    <div class="correlativo-box">
                        <div class="correlativo-label">DOCUMENTO N°</div>
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
                    FECHA DE EMISIÓN: {{ \Carbon\Carbon::parse($papeleta->fecha)->format('d/m/Y') }}
                </td>
            </tr>
            
            <!-- Vehículo -->
            <tr>
                <td colspan="4" class="vehicle-section">
                    VEHICULO ASIGNADO
                </td>
            </tr>
            @if($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->vehiculo)
                @php
                    $vehiculo = $papeleta->asignacionVehiculo->vehiculo;
                    // Cargar tipo de combustible si no está cargado
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
            
            <!-- Información del Conductor -->
            <tr>
                <td colspan="4" class="section-header">INFORMACION DEL CONDUCTOR Y PERSONAL</td>
            </tr>
            
            <!-- Conductor Permanente del Vehículo -->
            @if($papeleta->asignacionVehiculo && $papeleta->asignacionVehiculo->empleado)
            <tr>
                <td class="label">Conductor Asignado:</td>
                <td class="value">
                    {{ $papeleta->asignacionVehiculo->empleado->nombre }} {{ $papeleta->asignacionVehiculo->empleado->apellido }}
                </td>
                <td class="label">Cargo:</td>
                <td class="value">
                    {{ $papeleta->asignacionVehiculo->empleado->cargo->nombre ?? 'Sin especificar' }}
                </td>
            </tr>
            @else
            <tr>
                <td class="label">Conductor:</td>
                <td class="value" colspan="3">
                    <strong style="color: #e74c3c;">⚠ Sin conductor asignado</strong>
                </td>
            </tr>
            @endif
            
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
            
            <!-- Personal de la Comisión -->
            @if($papeleta->miembros_cuadrilla || $papeleta->personal_adicional)
            <tr>
                <td colspan="4" class="section-header">PERSONAL DE LA COMISION</td>
            </tr>
            @if($papeleta->miembros_cuadrilla && count($papeleta->miembros_cuadrilla) > 0)
            <tr>
                <td class="label">Miembros de Cuadrilla:</td>
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
                <td colspan="4" class="section-header">INFORMACION DEL VIAJE</td>
            </tr>
            <tr>
                <td class="label">Destino:</td>
                <td class="value" colspan="3">{{ $papeleta->destino }}</td>
            </tr>
            <tr>
                <td class="label">Motivo del Viaje:</td>
                <td class="value value-large" colspan="3">{{ $papeleta->motivo }}</td>
            </tr>
            <tr>
                <td class="label">Fecha y Hora de Salida:</td>
                <td class="value">
                    {{ $papeleta->fecha_hora_salida ? \Carbon\Carbon::parse($papeleta->fecha_hora_salida)->format('d/m/Y H:i') : 'Por registrar' }}
                </td>
                <td class="label">Estado:</td>
                <td class="value">
                    @if($papeleta->fecha_hora_llegada)
                        <span class="status-completado">COMPLETADO</span>
                    @elseif($papeleta->fecha_hora_salida)
                        <span class="status-transito">EN TRANSITO</span>
                    @else
                        <span class="status-programado">PROGRAMADO</span>
                    @endif
                </td>
            </tr>
            
            @if($papeleta->fecha_hora_llegada)
            <tr>
                <td class="label">Fecha y Hora de Llegada:</td>
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
                                <div class="km-label">KILOMETRAJE DE SALIDA</div>
                                <div>{{ $papeleta->km_salida ? number_format($papeleta->km_salida, 0) . ' KM' : '___________________' }}</div>
                            </td>
                            <td>
                                <div class="km-label">KILOMETRAJE DE LLEGADA</div>
                                <div>{{ $papeleta->km_llegada ? number_format($papeleta->km_llegada, 0) . ' KM' : '___________________' }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- OBSERVACIONES -->
        @if($papeleta->observaciones)
        <table class="content-table">
            <tr>
                <td class="section-header">OBSERVACIONES</td>
            </tr>
            <tr>
                <td style="padding: 15px; border: 1px solid #ddd; min-height: 60px; vertical-align: top; background: #fafafa;">
                    {{ $papeleta->observaciones }}
                </td>
            </tr>
        </table>
        @endif

        <!-- FIRMAS Y AUTORIZACIONES -->
        <table class="content-table">
            <tr>
                <td colspan="3" class="signatures-header">FIRMAS Y AUTORIZACIONES</td>
            </tr>
            <tr>
                <td style="height: 80px; border: 1px solid #ddd; padding: 25px 10px 10px; text-align: center; background: #fafafa; position: relative; width: 33.33%;">
                    <div style="position: absolute; bottom: 15px; left: 15px; right: 15px; border-bottom: 1px solid #333;"></div>
                    <div class="signature-title">CONDUCTOR</div>
                </td>
                <td style="height: 80px; border: 1px solid #ddd; padding: 25px 10px 10px; text-align: center; background: #fafafa; position: relative; width: 33.33%;">
                    <div style="position: absolute; bottom: 15px; left: 15px; right: 15px; border-bottom: 1px solid #333;"></div>
                    <div class="signature-title">JEFE INMEDIATO</div>
                </td>
                <td style="height: 80px; border: 1px solid #ddd; padding: 25px 10px 10px; text-align: center; background: #fafafa; position: relative; width: 33.33%;">
                    <div style="position: absolute; bottom: 15px; left: 15px; right: 15px; border-bottom: 1px solid #333;"></div>
                    <div class="signature-title">CONTROL VEHICULAR</div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <div class="footer">
            <strong>Documento generado:</strong> {{ \Carbon\Carbon::now('America/Lima')->format('d/m/Y H:i:s') }} | 
            <strong>Usuario:</strong> {{ $papeleta->usuarioCreacion->name ?? 'Sistema' }} |
            <strong>Sistema de Gestión Operativa</strong>
            <br>
            Este documento es de carácter oficial y debe conservarse según las políticas internas de la empresa
        </div>
    </div>
</body>
</html>