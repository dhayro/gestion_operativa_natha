@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .modal-content { background: #fff !important; }
        .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }

        .form-control:disabled,
        .form-control[readonly] {
            font-weight: bold;
            background-color: #f8f9fa !important;
            color: #212529 !important;
            cursor: not-allowed;
        }

        /* Badge styling */
        .badge-success {
            background-color: #1abc9c !important;
            color: white !important;
        }
        .badge-danger {
            background-color: #e74c3c !important;
            color: white !important;
        }
        .badge-warning {
            background-color: #f39c12 !important;
            color: white !important;
        }

        /* Cards para estadísticas */
        .stock-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stock-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stock-card.danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }

        .stock-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stock-card h5 {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .stock-card .valor {
            font-size: 1.8rem;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Tabla mejorada */
        .table-stock {
            font-size: 0.9rem;
        }

        .table-stock th {
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
        }

        .table-stock tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Modal para movimientos */
        .modal-body {
            overflow-y: auto !important;
            max-height: calc(100vh - 200px);
            scroll-behavior: smooth;
        }

        .modal-dialog.modal-lg {
            max-width: 900px !important;
        }

        /* Timeline de movimientos */
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #dee2e6;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .timeline-item.entrada {
            border-left-color: #28a745;
        }

        .timeline-item.salida {
            border-left-color: #dc3545;
        }

        .timeline-item-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .timeline-item-title {
            font-weight: bold;
            color: #2c3e50;
        }

        .timeline-item-date {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .timeline-item-body {
            font-size: 0.9rem;
            color: #555;
        }

        /* Filtros */
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .filter-section .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        /* Select2 Bootstrap 5 */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #bfc9d4 !important;
            border-radius: 6px !important;
            font-size: 14px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #212529 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            border: 1px solid #bfc9d4 !important;
            border-radius: 6px !important;
            z-index: 10000 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4>Consulta de Stock en Almacén</h4>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary" id="btnExportar" title="Exportar a Excel">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Exportar
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btnRefrescar" title="Refrescar datos">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36M20.49 15a9 9 0 0 1-14.85 3.36"></path>
                </svg>
                Refrescar
            </button>
        </div>
    </div>
</div>

<!-- Cards de estadísticas -->
<div class="row layout-spacing">
    <div class="col-lg-3 col-md-6">
        <div class="stock-card success">
            <h5>Stock Disponible</h5>
            <div class="valor" id="totalStockDisponible">0.00</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stock-card">
            <h5>Materiales Registrados</h5>
            <div class="valor" id="totalMateriales">0</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stock-card warning">
            <h5>Bajo Stock</h5>
            <div class="valor" id="bajoStock">0</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stock-card danger">
            <h5>Sin Stock</h5>
            <div class="valor" id="sinStock">0</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="filter-section">
            <div class="row">
                <div class="col-md-6">
                    <label for="categoriaFilter" class="form-label">Categoría</label>
                    <select class="form-control" id="categoriaFilter">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-primary w-100" id="btnLimpiarFiltros">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter me-2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de stock -->
<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="stockTable" class="table table-striped table-bordered table-hover table-stock" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Material</th>
                                <th>Categoría</th>
                                <th>Unidad</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Stock Actual</th>
                                <th>Estado</th>
                                <th>Valor Inv.</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver movimientos -->
<div class="modal fade" id="movimientosModal" tabindex="-1" aria-labelledby="movimientosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="movimientosModalLabel">Movimientos de Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="movimientosContent">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        let stockTable;

        $(document).ready(function() {
            // Inicializar Select2
            $('#categoriaFilter').select2({
                placeholder: 'Seleccione una categoría',
                allowClear: true
            });

            // Inicializar DataTable
            stockTable = $('#stockTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/stock/data',
                    data: function(d) {
                        d.categoria_id = $('#categoriaFilter').val();
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'codigo_material' },
                    { data: 'nombre' },
                    { data: 'categoria_nombre' },
                    { data: 'unidad_nombre' },
                    { data: 'total_entrada_fmt' },
                    { data: 'total_salida_fmt' },
                    { data: 'stock_actual_fmt' },
                    { data: 'stock_color' },
                    { data: 'valor_inventario' },
                    { data: 'action', orderable: false, searchable: false }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                },
                pageLength: 25,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                createdRow: function(row, data, dataIndex) {
                    // Actualizar estadísticas
                    actualizarEstadisticas();
                }
            });

            // Event listeners
            $('#categoriaFilter').on('change', function() {
                stockTable.draw();
            });

            $('#btnLimpiarFiltros').on('click', function() {
                $('#categoriaFilter').val('').trigger('change');
            });

            $('#btnRefrescar').on('click', function() {
                stockTable.draw();
            });

            $('#btnExportar').on('click', exportarStock);

            // Cargar datos iniciales
            actualizarEstadisticas();
        });

        function actualizarEstadisticas() {
            $.get('/stock/resumen/categoria', function(data) {
                let totalStock = 0;
                let totalMateriales = 0;
                let bajoStock = 0;
                let sinStock = 0;

                // Calcular desde la tabla visible
                const table = $('#stockTable').DataTable();
                table.rows().every(function() {
                    const rowData = this.data();
                    totalMateriales++;
                    const stock = parseFloat(rowData.stock_actual_fmt.replace(/,/g, '.'));
                    totalStock += stock;
                    
                    if (stock == 0) {
                        sinStock++;
                    } else if (stock < 10) {
                        bajoStock++;
                    }
                });

                $('#totalStockDisponible').text(totalStock.toFixed(3));
                $('#totalMateriales').text(totalMateriales);
                $('#bajoStock').text(bajoStock);
                $('#sinStock').text(sinStock);
            });
        }

        window.verMovimientos = function(materialId) {
            $.get(`/stock/${materialId}/movimientos`, function(movimientos) {
                let html = '<div class="timeline">';

                if (movimientos.length > 0) {
                    movimientos.forEach(function(mov) {
                        const tipoClass = mov.tipo_color === 'success' ? 'entrada' : 'salida';
                        const tipoBadge = mov.tipo === 'ENTRADA' 
                            ? '<span class="badge bg-success">ENTRADA</span>'
                            : '<span class="badge bg-danger">SALIDA</span>';

                        html += `
                            <div class="timeline-item ${tipoClass}">
                                <div class="timeline-item-header">
                                    <div class="timeline-item-title">${tipoBadge} - ${mov.referencia}</div>
                                    <div class="timeline-item-date">${mov.fecha}</div>
                                </div>
                                <div class="timeline-item-body">
                                    <strong>Cantidad:</strong> ${mov.cantidad}<br>
                                    <strong>Precio:</strong> ${mov.precio}<br>
                                    <strong>Con IGV:</strong> ${mov.igv}<br>
                                    <strong>Usuario:</strong> ${mov.usuario}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += '<p class="text-muted">No hay movimientos registrados para este material.</p>';
                }

                html += '</div>';

                $('#movimientosContent').html(html);
                const modal = new bootstrap.Modal(document.getElementById('movimientosModal'));
                modal.show();
            }).fail(function() {
                Swal.fire('Error', 'No se pudieron cargar los movimientos', 'error');
            });
        };

        function exportarStock() {
            $.get('/stock/exportar', function(response) {
                Swal.fire('Éxito', 'Reporte exportado correctamente', 'success');
            }).fail(function() {
                Swal.fire('Error', 'No se pudo exportar el reporte', 'error');
            });
        }
    </script>
@endsection
