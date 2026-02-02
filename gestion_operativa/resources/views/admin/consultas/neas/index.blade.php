@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ $title }}</div>
                                <p class="card-category">Consulta detallada de NEAs con todos los movimientos</p>
                            </div>
                            <div class="card-body">
                                <!-- Selector de NEA -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="neaSelect" class="form-label">
                                            <i class="fas fa-search"></i> Seleccionar NEA
                                        </label>
                                        <select id="neaSelect" class="form-control select2" style="width: 100%;">
                                            <option value="">-- Seleccione una NEA --</option>
                                            @foreach($neas as $nea)
                                                <option value="{{ $nea->id }}">
                                                    {{ $nea->nro_documento }} - 
                                                    {{ $nea->proveedor ? $nea->proveedor->razon_social : 'N/A' }} 
                                                    ({{ $nea->fecha->format('d/m/Y') }}) - 
                                                    S/ {{ number_format($nea->total_con_igv, 2, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="tipoMovimiento" class="form-label">
                                            <i class="fas fa-filter"></i> Tipo de Movimiento
                                        </label>
                                        <select id="tipoMovimiento" class="form-control">
                                            <option value="todos">Todos</option>
                                            <option value="entrada">Solo Entradas</option>
                                            <option value="salida">Solo Salidas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button id="btnBuscar" class="btn btn-primary btn-block" style="width: 100%;">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>

                                <!-- Contenedor de resultados (inicialmente oculto) -->
                                <div id="resultadosContainer" style="display: none;">
                                    <!-- Información de la NEA -->
                                    <div id="neaInfo" style="display: none;">
                                        <div class="alert alert-primary border-2 border-primary" role="alert">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <strong style="font-size: 1.1rem;">NEA:</strong> 
                                                        <span id="neaNro" style="font-size: 1.3rem; font-weight: bold; color: #0c63e4;"></span>
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Proveedor:</strong> <span id="neaProveedor"></span>
                                                    </div>
                                                    <div>
                                                        <strong>Fecha:</strong> <span id="neaFecha"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div style="background-color: #f0f0f0; padding: 10px; border-radius: 5px; text-align: center;">
                                                                <strong style="font-size: 0.9rem;">Total sin IGV</strong>
                                                                <div style="font-size: 1.2rem; font-weight: bold; color: #333;">S/ <span id="neaTotalSinIgv">0.00</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div style="background-color: #fff3cd; padding: 10px; border-radius: 5px; text-align: center;">
                                                                <strong style="font-size: 0.9rem;">IGV</strong>
                                                                <div style="font-size: 1.2rem; font-weight: bold; color: #ff6b6b;">S/ <span id="neaIgv">0.00</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div style="background-color: #d4edda; padding: 10px; border-radius: 5px; text-align: center;">
                                                                <strong style="font-size: 0.9rem;">Total con IGV</strong>
                                                                <div style="font-size: 1.2rem; font-weight: bold; color: #28a745;">S/ <span id="neaTotalConIgv">0.00</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nav Tabs para Movimientos, Stock, etc -->
                                    <ul class="nav nav-tabs mb-4" id="resultadoTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="movimientos-tab" data-bs-toggle="tab" 
                                                    data-bs-target="#movimientos" type="button" role="tab" aria-controls="movimientos" aria-selected="true">
                                                <i class="fas fa-exchange-alt"></i> Movimientos
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="stock-tab" data-bs-toggle="tab" 
                                                    data-bs-target="#stock" type="button" role="tab" aria-controls="stock" aria-selected="false">
                                                <i class="fas fa-box"></i> Resumen Stock
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="detalles-tab" data-bs-toggle="tab" 
                                                    data-bs-target="#detalles" type="button" role="tab" aria-controls="detalles" aria-selected="false">
                                                <i class="fas fa-list"></i> Detalles NEA
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Content Tabs -->
                                    <div class="tab-content" id="resultadoTabContent">
                                        <!-- Tab Movimientos -->
                                        <div class="tab-pane fade show active" id="movimientos" role="tabpanel" aria-labelledby="movimientos-tab">
                                            <div class="table-responsive">
                                                <table id="movimientosTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Tipo</th>
                                                            <th>Material</th>
                                                            <th>Código</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio Unit.</th>
                                                            <th>Subtotal</th>
                                                            <th>IGV</th>
                                                            <th>Fecha</th>
                                                            <th>Usuario</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab Stock -->
                                        <div class="tab-pane fade" id="stock" role="tabpanel" aria-labelledby="stock-tab">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" id="stockTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Código Material</th>
                                                            <th>Nombre Material</th>
                                                            <th>Unidad</th>
                                                            <th class="text-success">Entrada</th>
                                                            <th class="text-danger">Salida</th>
                                                            <th class="text-info"><strong>Stock Disponible</strong></th>
                                                            <th>Precio Unit.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="stockTableBody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tab Detalles NEA -->
                                        <div class="tab-pane fade" id="detalles" role="tabpanel" aria-labelledby="detalles-tab">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" id="detallesTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Material</th>
                                                            <th>Código</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio Unit.</th>
                                                            <th>Subtotal</th>
                                                            <th>IGV</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="detallesTableBody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botones de Acción -->
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <button id="btnExportar" class="btn btn-success">
                                                <i class="fas fa-file-pdf"></i> Exportar a PDF
                                            </button>
                                            <button id="btnImprimir" class="btn btn-secondary">
                                                <i class="fas fa-print"></i> Imprimir
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sin resultados -->
                                <div id="sinResultados" class="alert alert-warning text-center" style="display: none;">
                                    <i class="fas fa-info-circle"></i> Seleccione una NEA para ver sus movimientos
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.select2.org/downloads/select2-4.0.13/select2.min.js"></script>

<script>
$(document).ready(function() {
    let movimientosTable = null;

    // Inicializar Select2
    $('#neaSelect').select2({
        width: '100%',
        placeholder: '-- Seleccione una NEA --',
        allowClear: true,
        language: 'es'
    });

    // Evento al hacer clic en Buscar
    $('#btnBuscar').on('click', function() {
        let neaId = $('#neaSelect').val();
        
        if (!neaId) {
            Swal.fire('Aviso', 'Debe seleccionar una NEA', 'warning');
            return;
        }

        cargarNea(neaId);
    });

    // Evento al cambiar tipo de movimiento
    $('#tipoMovimiento').on('change', function() {
        let neaId = $('#neaSelect').val();
        if (neaId) {
            recargarMovimientos(neaId);
        }
    });

    // Función para cargar datos de la NEA
    function cargarNea(neaId) {
        $.ajax({
            url: '{{ route("consulta_nea.obtener", ":id") }}'.replace(':id', neaId),
            method: 'GET',
            success: function(response) {
                let nea = response.nea;
                
                // Mostrar información de la NEA
                $('#neaNro').text(nea.nro_documento);
                $('#neaProveedor').text(nea.proveedor ? nea.proveedor.razon_social : 'N/A');
                $('#neaFecha').text(new Date(nea.fecha).toLocaleDateString('es-ES'));
                $('#neaTotalSinIgv').text((parseFloat(nea.total_sin_igv) || 0).toFixed(2));
                $('#neaIgv').text((parseFloat(nea.igv_total) || 0).toFixed(2));
                $('#neaTotalConIgv').text((parseFloat(nea.total_con_igv) || 0).toFixed(2));
                
                $('#neaInfo').show();
                $('#resultadosContainer').show();
                $('#sinResultados').hide();

                // Cargar movimientos
                recargarMovimientos(neaId);

                // Cargar resumen de stock
                cargarResumenStock(neaId);

                // Cargar detalles
                cargarDetalles(nea.detalles);
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudo cargar la NEA', 'error');
                console.error(xhr);
            }
        });
    }

    // Función para recargar movimientos según filtro
    function recargarMovimientos(neaId) {
        let tipo = $('#tipoMovimiento').val();
        
        if (movimientosTable) {
            movimientosTable.destroy();
        }

        movimientosTable = $('#movimientosTable').DataTable({
            serverSide: true,
            ajax: {
                url: '{{ route("consulta_nea.movimientos", ":id") }}'.replace(':id', neaId),
                data: {
                    tipo: tipo
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'tipo_badge', name: 'tipo_movimiento', orderable: false, searchable: false },
                { data: 'material_nombre', name: 'material_nombre' },
                { data: 'material_codigo', name: 'material_codigo' },
                { data: 'cantidad_formateada', name: 'cantidad', className: 'text-end' },
                { data: 'precio_formateado', name: 'precio_unitario', className: 'text-end' },
                { data: 'subtotal', name: 'subtotal', orderable: false, className: 'text-end' },
                { data: 'igv_badge', name: 'incluye_igv', orderable: false, searchable: false },
                { data: 'fecha_formateada', name: 'fecha' },
                { data: 'usuario', name: 'usuario' }
            ],
            language: {
                url: '/static/Spanish.json'
            },
            order: [[8, 'desc']],
            pageLength: 10
        });
    }

    // Función para cargar resumen de stock
    function cargarResumenStock(neaId) {
        $.ajax({
            url: '{{ route("consulta_nea.resumen_stock", ":id") }}'.replace(':id', neaId),
            method: 'GET',
            success: function(response) {
                let stockTableBody = $('#stockTableBody');
                stockTableBody.empty();

                response.resumen.forEach(function(item) {
                    let row = `
                        <tr>
                            <td>${item.codigo}</td>
                            <td>${item.nombre}</td>
                            <td>${item.unidad_medida}</td>
                            <td class="text-success text-end"><strong>${parseFloat(item.cantidad_entrada).toFixed(3)}</strong></td>
                            <td class="text-danger text-end">${parseFloat(item.cantidad_salida).toFixed(3)}</td>
                            <td class="text-info text-end"><strong>${parseFloat(item.stock_disponible).toFixed(3)}</strong></td>
                            <td class="text-end">S/ ${parseFloat(item.precio_unitario).toFixed(2)}</td>
                        </tr>
                    `;
                    stockTableBody.append(row);
                });
            },
            error: function(xhr) {
                console.error('Error al cargar resumen de stock:', xhr);
            }
        });
    }

    // Función para cargar detalles de la NEA
    function cargarDetalles(detalles) {
        let detallesTableBody = $('#detallesTableBody');
        detallesTableBody.empty();

        detalles.forEach(function(detalle) {
            // Obtener nombre y código del material
            let materialNombre = detalle.material ? detalle.material.nombre : 'N/A';
            let materialCodigo = detalle.material ? detalle.material.codigo_material : 'N/A';
            
            let igv = detalle.incluye_igv ? 'Sí' : 'No';
            let totalDetalle = parseFloat(detalle.cantidad) * parseFloat(detalle.precio_unitario);
            
            // Calcular IGV correctamente
            let igvDetalle = 0;
            if (detalle.incluye_igv) {
                // El precio ya incluye IGV, extraemos el IGV
                igvDetalle = totalDetalle - (totalDetalle / 1.18);
            }
            
            let row = `
                <tr>
                    <td>${materialNombre}</td>
                    <td>${materialCodigo}</td>
                    <td class="text-end">${parseFloat(detalle.cantidad).toFixed(3)}</td>
                    <td class="text-end">S/ ${parseFloat(detalle.precio_unitario).toFixed(2)}</td>
                    <td class="text-end">S/ ${totalDetalle.toFixed(2)}</td>
                    <td class="text-center">
                        <span class="badge ${detalle.incluye_igv ? 'bg-info' : 'bg-secondary'}">${igv}</span>
                    </td>
                    <td class="text-end">
                        <strong>S/ ${(totalDetalle + igvDetalle).toFixed(2)}</strong>
                    </td>
                </tr>
            `;
            detallesTableBody.append(row);
        });
    }

    // Botón Exportar
    $('#btnExportar').on('click', function() {
        let neaId = $('#neaSelect').val();
        if (!neaId) {
            Swal.fire('Aviso', 'Debe seleccionar una NEA', 'warning');
            return;
        }

        Swal.fire('Procesando', 'Generando PDF...', 'info');
        Swal.showLoading();

        $.ajax({
            url: '{{ route("consulta_nea.exportar", ":id") }}'.replace(':id', neaId),
            method: 'GET',
            success: function(response) {
                // Aquí iría la generación del PDF
                Swal.fire('Éxito', 'Archivo descargado', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudo exportar', 'error');
            }
        });
    });

    // Botón Imprimir
    $('#btnImprimir').on('click', function() {
        window.print();
    });
});
</script>
@endsection
