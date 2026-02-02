@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
    <style>
        .badge-entrada { background-color: #28a745 !important; }
        .badge-salida { background-color: #dc3545 !important; }
        .inventory-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .inventory-stat {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
        }
        .inventory-stat .number {
            font-size: 24px;
            font-weight: bold;
        }
        .inventory-stat .label {
            font-size: 14px;
            opacity: 0.9;
        }
        .material-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        .material-row:hover {
            background-color: #f5f5f5;
        }
        .saldo-positive {
            color: #28a745;
            font-weight: bold;
        }
        .saldo-negative {
            color: #dc3545;
            font-weight: bold;
        }
        .movimiento-timeline {
            position: relative;
            padding-left: 30px;
        }
        .movimiento-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #ddd;
        }
        .movimiento-item {
            position: relative;
            margin-bottom: 20px;
        }
        .movimiento-item::before {
            content: '';
            position: absolute;
            left: -37px;
            top: 3px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: white;
            border: 2px solid #ddd;
        }
        .movimiento-item.entrada::before {
            background: #28a745;
            border-color: #28a745;
        }
        .movimiento-item.salida::before {
            background: #dc3545;
            border-color: #dc3545;
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4>Historial de Pecosa</h4>
            <p class="text-muted mb-0">ID <span id="pecosa-id" class="badge bg-primary"></span> | <span id="pecosa-fecha" class="text-muted"></span></p>
        </div>
        <button type="button" class="btn btn-secondary" onclick="history.back()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Atrás
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <!-- INVENTARIO ACTUAL -->
    <div class="col-lg-4">
        <div class="inventory-card">
            <h5 class="mb-3">📦 Inventario Actual</h5>
            <div id="inventario-content">
                <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
            </div>
        </div>
    </div>

    <!-- HISTORIAL DE MOVIMIENTOS -->
    <div class="col-lg-8">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <h5 class="widget-title">📊 Historial de Movimientos</h5>
            </div>
            <div class="widget-content widget-content-area">
                <div id="movimientos-timeline">
                    <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let pecosaId = {{ $pecosaId ?? 'null' }};

    $(document).ready(function() {
        if (!pecosaId) {
            pecosaId = new URLSearchParams(window.location.search).get('pecosa_id');
        }
        
        if (pecosaId) {
            cargarHistorial();
        } else {
            Swal.fire('Error', 'Pecosa no especificada', 'error');
        }
    });

    function cargarHistorial() {
        $.ajax({
            url: `/pecosas/${pecosaId}/historial`,
            type: 'GET',
            success: function(response) {
                console.log('✓ Historial cargado:', response);
                
                // Actualizar datos de pecosa
                $('#pecosa-id').text(response.pecosa.nro_documento);
                $('#pecosa-fecha').text(response.pecosa.fecha);
                
                // Mostrar inventario actual
                mostrarInventario(response.inventario);
                
                // Mostrar timeline de movimientos
                mostrarMovimientos(response.movimientos);
            },
            error: function(xhr) {
                console.error('✗ Error cargando historial:', xhr);
                Swal.fire('Error', 'No se pudo cargar el historial', 'error');
            }
        });
    }

    function mostrarInventario(inventario) {
        let html = '';
        
        if (inventario.length === 0) {
            html = '<p class="text-center text-warning">No hay materiales en inventario</p>';
        } else {
            html = '<div>';
            inventario.forEach(item => {
                const saldoClass = item.saldo > 0 ? 'saldo-positive' : 'saldo-negative';
                html += `
                    <div class="material-row">
                        <div>
                            <strong>${item.material_nombre}</strong><br>
                            <small>${item.unidad}</small>
                        </div>
                        <div>
                            <span class="${saldoClass}">${item.saldo.toFixed(2)}</span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        $('#inventario-content').html(html);
    }

    function mostrarMovimientos(movimientos) {
        let html = '';
        
        if (movimientos.length === 0) {
            html = '<p class="text-center">No hay movimientos registrados</p>';
        } else {
            html = '<div class="movimiento-timeline">';
            movimientos.forEach(mov => {
                const esEntrada = mov.tipo === 'entrada';
                const badgeClass = esEntrada ? 'badge-entrada' : 'badge-salida';
                const icono = esEntrada ? '📥' : '📤';
                const detalleExtra = mov.ficha ? `<br><small class="text-muted">Ficha: ${mov.ficha}</small>` : '';
                
                html += `
                    <div class="movimiento-item ${mov.tipo}">
                        <div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge ${badgeClass}">${icono} ${mov.tipo_texto}</span>
                                    <strong class="ms-2">${mov.material}</strong>
                                </div>
                                <span class="text-muted">${mov.fecha}</span>
                            </div>
                            <div class="mt-2">
                                <p class="mb-1"><strong>Cantidad:</strong> ${mov.cantidad}</p>
                                ${detalleExtra}
                                ${mov.observaciones ? `<p class="mb-0"><small><strong>Observación:</strong> ${mov.observaciones}</small></p>` : ''}
                                <small class="text-muted">Por: ${mov.usuario || 'N/A'}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        $('#movimientos-timeline').html(html);
    }
</script>
@endsection
