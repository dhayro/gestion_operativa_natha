@extends('layouts.app')

@section('styles')
<style>
    .modal-content { 
        background: #fff !important; 
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">📦 Consulta de Stock de Materiales</div>
                                <p class="card-category">Consulta detallada de stock de materiales por cuadrilla con todas las entradas y salidas</p>
                            </div>
                            <div class="card-body">
                                <!-- Selector de Cuadrilla y Filtros -->
                                <div class="row mb-4">
                                    <div class="col-md-5">
                                        <label for="cuadrillaSelect" class="form-label">
                                            <i class="fas fa-search"></i> Seleccionar Cuadrilla
                                        </label>
                                        <select id="cuadrillaSelect" class="form-control select2" style="width: 100%;">
                                            <option value="">-- Seleccione una Cuadrilla --</option>
                                            @foreach($cuadrillas as $cuadrilla)
                                                <option value="{{ $cuadrilla->id }}">{{ $cuadrilla->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="tipoStock" class="form-label">
                                            <i class="fas fa-filter"></i> Filtrar por Estado
                                        </label>
                                        <select id="tipoStock" class="form-control">
                                            <option value="todos">Todos</option>
                                            <option value="normal">🟢 Normal</option>
                                            <option value="bajo">🟡 Bajo</option>
                                            <option value="critico">🔴 Crítico</option>
                                            <option value="agotado">⚫ Agotado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end gap-2">
                                        <button id="btnBuscar" class="btn btn-primary" style="width: 48%;">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                        <button id="btnExportar" class="btn btn-success" style="width: 48%;" disabled>
                                            <i class="fas fa-download"></i> Exportar CSV
                                        </button>
                                    </div>
                                </div>

                                <!-- Contenedor de resultados (inicialmente oculto) -->
                                <div id="resultadosContainer" style="display: none;">
                                    <!-- Información de la Cuadrilla -->
                                    <div id="cuadrillaInfo" style="display: none;">
                                        <div class="alert alert-primary border-2 border-primary" role="alert">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <strong style="font-size: 1.1rem;">Cuadrilla:</strong> 
                                                        <span id="cuadrillaNombre" style="font-size: 1.3rem; font-weight: bold; color: #0c63e4;"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div style="background-color: #f0f0f0; padding: 10px; border-radius: 5px; text-align: center;">
                                                                <strong style="font-size: 0.9rem;">Total Materiales</strong>
                                                                <div style="font-size: 1.2rem; font-weight: bold; color: #333;"><span id="totalMateriales">0</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div style="background-color: #d4edda; padding: 10px; border-radius: 5px; text-align: center;">
                                                                <strong style="font-size: 0.9rem;">Total en Stock</strong>
                                                                <div style="font-size: 1.2rem; font-weight: bold; color: #28a745;">S/ <span id="totalValorStock">0.00</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div style="background-color: #fff3cd; padding: 10px; border-radius: 5px; text-align: center;">
                                                                <strong style="font-size: 0.9rem;">Críticos/Agotados</strong>
                                                                <div style="font-size: 1.2rem; font-weight: bold; color: #ff6b6b;"><span id="totalCriticos">0</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Indicadores de Estado -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <small class="text-muted">
                                                <span class="badge bg-success">🟢 Normal</span>
                                                <span class="badge bg-warning text-dark">🟡 Bajo</span>
                                                <span class="badge bg-danger">🔴 Crítico</span>
                                                <span class="badge bg-dark">⚫ Agotado</span>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Tabla de Stock -->
                                    <div class="table-responsive">
                                        <table id="stockTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Material</th>
                                                    <th>Código</th>
                                                    <th>Categoría</th>
                                                    <th>Unidad</th>
                                                    <th>Entradas</th>
                                                    <th>Salidas</th>
                                                    <th>Stock Actual</th>
                                                    <th>Stock Mín.</th>
                                                    <th>Última Actualización</th>
                                                    <th>Valor Stock</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Movimientos -->
<div class="modal fade" id="movimientosModal" tabindex="-1" aria-labelledby="movimientosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="movimientosModalLabel">📋 Movimientos de Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="movimientosTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                                <th>Ficha</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
<script>
    let stockTable = null;
    let currentCuadrillaId = null;
    let currentCuadrillaNombre = null;

    $(document).ready(function() {
        // Inicializar Select2
        $('#cuadrillaSelect').select2({
            allowClear: true,
            language: 'es',
            placeholder: '-- Seleccione una Cuadrilla --'
        });

        // Evento del botón Buscar
        $('#btnBuscar').click(function() {
            buscarStock();
        });

        // Evento del botón Exportar
        $('#btnExportar').click(function() {
            exportarCSV();
        });
    });

    function buscarStock() {
        const cuadrillaId = $('#cuadrillaSelect').val();

        if (!cuadrillaId) {
            Swal.fire('Aviso', 'Debes seleccionar una cuadrilla', 'warning');
            return;
        }

        currentCuadrillaId = cuadrillaId;

        // Si ya existe DataTable, destruirlo
        if ($.fn.DataTable.isDataTable('#stockTable')) {
            $('#stockTable').DataTable().destroy();
        }

        // Obtener información de la cuadrilla
        $.ajax({
            url: '{{ route("stock_materiales.obtenerCuadrilla", ["id" => ":id"]) }}'.replace(':id', cuadrillaId),
            type: 'GET',
            success: function(response) {
                $('#resultadosContainer').show();
                $('#cuadrillaInfo').show();
                
                $('#cuadrillaNombre').text(response.cuadrilla.nombre);
                $('#totalMateriales').text(response.resumen.total_materiales);
                $('#totalCriticos').text('0');
                $('#totalValorStock').text('0.00');

                // Crear DataTable con AJAX
                stockTable = $('#stockTable').DataTable({
                    processing: true,
                    serverSide: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                    },
                    ajax: {
                        url: '{{ route("stock_materiales.getData", ["cuadrillaId" => ":cuadrillaId"]) }}'.replace(':cuadrillaId', cuadrillaId),
                        type: 'GET'
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nombre', name: 'nombre' },
                        { data: 'codigo', name: 'codigo' },
                        { data: 'categoria', name: 'categoria' },
                        { data: 'unidad', name: 'unidad' },
                        { data: 'entradas_badge', name: 'entradas_badge', orderable: false },
                        { data: 'salidas_badge', name: 'salidas_badge', orderable: false },
                        { data: 'stock_actual', name: 'stock_actual', orderable: false },
                        { data: 'stock_minimo_formateado', name: 'stock_minimo' },
                        { data: 'precio_unitario_formateado', name: 'precio_unitario' },
                        { data: 'valor_stock', name: 'valor_stock', orderable: false },
                        { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
                    ],
                    columnDefs: [],
                    pageLength: 25,
                    ordering: true,
                    searching: true,
                    info: true,
                    paging: true,
                    drawCallback: function(settings) {
                        // Actualizar totales después de cargar
                        let totalValorStock = 0;
                        let totalCriticos = 0;
                        
                        $('#stockTable tbody tr').each(function() {
                            // Contar filas para calcular totales
                            const valorText = $(this).find('td').eq(10).text().trim();
                            const valorStr = valorText.replace('S/ ', '').replace(/\./g, '').replace(',', '.');
                            totalValorStock += parseFloat(valorStr) || 0;
                        });
                        
                        $('#totalValorStock').text(totalValorStock.toFixed(2));
                        $('#btnExportar').prop('disabled', false);
                    }
                });
            },
            error: function(xhr) {
                Swal.fire('Error', 'Error al cargar cuadrilla', 'error');
            }
        });
    }

    function verMovimientos(materialId, cuadrillaId, materialNombre) {
        // Si ya existe DataTable, destruirlo
        if ($.fn.DataTable.isDataTable('#movimientosTable')) {
            $('#movimientosTable').DataTable().destroy();
        }

        $('#movimientosModalLabel').text(`📋 Movimientos - ${materialNombre}`);

        // Crear DataTable para movimientos
        $('#movimientosTable').DataTable({
            processing: true,
            serverSide: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            ajax: {
                url: '{{ route("stock_materiales.getMovimientos", ["materialId" => ":materialId", "cuadrillaId" => ":cuadrillaId"]) }}'
                    .replace(':materialId', materialId)
                    .replace(':cuadrillaId', cuadrillaId),
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tipo_badge', name: 'tipo_badge', orderable: false },
                { data: 'cantidad_formateada', name: 'cantidad' },
                { data: 'fecha_formateada', name: 'fecha' },
                { data: 'ficha', name: 'ficha' },
                { data: 'usuario', name: 'usuario' }
            ],
            pageLength: 10,
            ordering: true,
            searching: true,
            info: true,
            paging: true
        });

        $('#movimientosModal').modal('show');
    }

    function exportarCSV() {
        if (!currentCuadrillaId) {
            Swal.fire('Aviso', 'Debes seleccionar una cuadrilla', 'warning');
            return;
        }

        window.location.href = `{{ route('stock_materiales.exportCsv') }}?cuadrilla_id=${currentCuadrillaId}`;
    }
</script>

@endsection
