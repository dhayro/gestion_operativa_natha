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
        
        /* SweetAlert2 Mejorado */
        .swal2-popup {
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        }
        .swal2-title {
            font-size: 20px !important;
            font-weight: 600 !important;
            color: #2c3e50 !important;
            margin-bottom: 15px !important;
        }
        .swal2-html-container {
            font-size: 14px !important;
            color: #555 !important;
            line-height: 1.6 !important;
        }
        .swal2-confirm {
            border-radius: 6px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }
        .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2) !important;
        }
        .swal2-cancel {
            border-radius: 6px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
        }
        .swal2-textarea {
            border: 2px solid #bfc9d4 !important;
            border-radius: 6px !important;
            padding: 10px !important;
            font-family: inherit !important;
            transition: border-color 0.3s !important;
        }
        .swal2-textarea:focus {
            border-color: #ff9800 !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.1) !important;
        }
        
        /* Select2 Bootstrap 5 integration */
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
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #7c3aed !important;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1) !important;
        }
        .select2-results__option {
            padding: 10px 12px !important;
            font-size: 14px !important;
        }
        .select2-results__option--highlighted {
            background-color: #007bff !important;
            color: white !important;
        }
        .select2-results__option--selected {
            background-color: #f0f0f0 !important;
            color: #333 !important;
        }
        .select2-selection__clear {
            margin-right: 5px !important;
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

        /* Tabla de detalles */
        .detalle-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .detalle-field {
            flex: 1;
            min-width: 150px;
        }

        .detalle-total {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .btn-remove-detalle {
            padding: 6px 12px;
            height: 38px;
        }

        .resumen-totales {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            margin-top: 20px;
        }

        .resumen-total {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .total-item {
            display: flex;
            flex-direction: column;
        }

        .total-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .total-valor {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .detalle-row {
                flex-direction: column;
            }
            .resumen-total {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de NEAs (Notas de Entrada de Almacén)</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#neaModal" id="btnNuevaNea">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nueva NEA
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="neasTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nro. Documento</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Tipo Comprobante</th>
                                <th>Ítems</th>
                                <th>Total S/.</th>
                                <th>Estado</th>
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


<!-- Modal para ver detalles -->
<div class="modal fade" id="neaDetallesModal" tabindex="-1" aria-labelledby="neaDetallesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="neaDetallesModalLabel">Detalles de NEA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="neaDetallesContent">
                <!-- Se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista previa de PDF -->
<div class="modal fade" id="neaPDFModal" tabindex="-1" aria-labelledby="neaPDFModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="neaPDFModalLabel">Vista Previa - NEA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="neaPDFFrame" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar NEA con detalles -->
<div class="modal fade" id="neaModal" tabindex="-1" aria-labelledby="neaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="neaForm" action="" method="POST">
                @csrf
                <input type="hidden" id="nea_id" name="nea_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="neaModalLabel">Nueva NEA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Sección de datos principales -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Datos de la NEA</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="proveedor_id" name="proveedor_id" required>
                                        <option value="">Seleccione un proveedor</option>
                                        @foreach($proveedores as $id => $nombre)
                                            <option value="{{ $id }}">{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nro_documento" class="form-label">Nro. Documento <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nro_documento" name="nro_documento" maxlength="50" placeholder="Auto-generado" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="btnGenerarNumero" title="Generar número automático">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36M20.49 15a9 9 0 0 1-14.85 3.36"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_comprobante_id" class="form-label">Tipo Comprobante <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="tipo_comprobante_id" name="tipo_comprobante_id" required>
                                        <option value="">Seleccione un tipo</option>
                                        @foreach($tiposComprobantes as $id => $nombre)
                                            <option value="{{ $id }}">{{ $nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="numero_comprobante" class="form-label">Número Comprobante <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numero_comprobante" name="numero_comprobante" placeholder="Ej: 001-0000001" maxlength="50" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Ingrese observaciones (opcional)"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="estado" name="estado" value="1" checked>
                                        <label class="form-check-label" for="estado">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de detalles de materiales -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Detalles de Materiales</h6>
                        </div>
                        <div class="card-body">
                            <!-- Formulario para agregar detalle -->
                            <div class="form-agregardetalle mb-4 p-4" style="background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0;">
                                <h6 class="mb-3">Agregar Material</h6>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="formMaterial" class="form-label">Material <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="formMaterial">
                                            <option value="">Seleccione un material</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="formCantidad" class="form-label">Cantidad <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="formCantidad" placeholder="0.000" step="0.001" min="0">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="formPrecio" class="form-label">Precio Unit. <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="formPrecio" placeholder="0.00" step="0.01" min="0">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="formIncluye" class="form-label">&nbsp;</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="formIncluye">
                                            <label class="form-check-label" for="formIncluye">¿Incluye IGV?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-success w-100" id="btnAgregarDetalle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-1">
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                            Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de detalles agregados -->
                            <div class="table-responsive" id="detallesTablaContainer" style="display:none;">
                                <table class="table table-striped table-bordered table-hover table-sm" id="detallesTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%">Material</th>
                                            <th style="width: 10%">Unidad</th>
                                            <th style="width: 10%">Cantidad</th>
                                            <th style="width: 12%">Precio Unit.</th>
                                            <th style="width: 10%">IGV</th>
                                            <th style="width: 15%">Subtotal</th>
                                            <th style="width: 8%; text-align: center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detallesTbody">
                                    </tbody>
                                </table>
                            </div>

                            <!-- Resumen de totales -->
                            <div class="resumen-totales" id="resumenTotales" style="display:none;">
                                <div class="resumen-total">
                                    <div class="total-item">
                                        <span class="total-label">Subtotal</span>
                                        <span class="total-valor" id="totalSinIgv">S/ 0.00</span>
                                    </div>
                                    <div class="total-item" id="igvRow" style="display:none;">
                                        <span class="total-label">IGV (18%)</span>
                                        <span class="total-valor text-warning" id="totalIgv">S/ 0.00</span>
                                    </div>
                                    <div class="total-item">
                                        <span class="total-label">Total a Pagar</span>
                                        <span class="total-valor text-success" id="totalConIgv">S/ 0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar NEA</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('scripts')
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
    // Variables globales
    let detalleCounter = 0;
    let detalleEditado = {};

    $(document).ready(function() {
        initializeDataTable();
        setupEventListeners();
        setFechaHoy();
    });

    function initializeDataTable() {
        var table = $('#neasTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('neas.data') }}',
                data: function(d) {
                    d.order = d.order || [];
                    if (d.order.length === 0) {
                        d.order = [{ column: 0, dir: 'desc' }]; // Ordenar por columna índice (ID) descendente
                    }
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nro_documento', name: 'nro_documento' },
                { data: 'fecha_formatted', name: 'fecha' },
                { data: 'proveedor_nombre', name: 'proveedor_nombre', searchable: true },
                { data: 'tipo_comprobante_nombre', name: 'tipo_comprobante_nombre', searchable: true },
                { data: 'cantidad_detalles', name: 'detalles', orderable: false, searchable: false },
                { data: 'total_con_igv', name: 'total_con_igv', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "desc"]], // Ordenar por columna nro_documento descendente
            columnDefs: [
                { targets: [7, 8], className: "text-center" }
            ],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });
    }

    function setupEventListeners() {
        // Botón agregar detalle
        $('#btnAgregarDetalle').on('click', agregarFilaDetalle);

        // Botón generar número automático
        $('#btnGenerarNumero').on('click', generarNumeroNea);

        // Formulario
        $('#neaForm').on('submit', guardarNea);

        // Modal nueva
        $('#btnNuevaNea').on('click', function() {
            resetForm();
            $('#neaModalLabel').text('Nueva NEA');
        });

        // Mostrar modal - Inicializar Select2 aquí
        $('#neaModal').on('shown.bs.modal', function() {
            initializeSelect2();
        });

        // Inicializar Select2 la primera vez
        initializeSelect2();
    }

    function initializeSelect2() {
        // Select2 para proveedor
        if ($('#proveedor_id').data('select2')) {
            $('#proveedor_id').select2('destroy');
        }
        $('#proveedor_id').select2({
            dropdownParent: $('#neaModal'),
            width: '100%',
            allowClear: true,
            placeholder: 'Seleccione un proveedor',
            language: {
                noResults: function() {
                    return 'No se encontraron resultados';
                }
            }
        });

        // Select2 para tipo comprobante
        if ($('#tipo_comprobante_id').data('select2')) {
            $('#tipo_comprobante_id').select2('destroy');
        }
        $('#tipo_comprobante_id').select2({
            dropdownParent: $('#neaModal'),
            width: '100%',
            allowClear: true,
            placeholder: 'Seleccione un tipo de comprobante',
            language: {
                noResults: function() {
                    return 'No se encontraron resultados';
                }
            }
        });

        // Select2 para material en formulario de agregar
        if ($('#formMaterial').data('select2')) {
            $('#formMaterial').select2('destroy');
        }
        $('#formMaterial').select2({
            dropdownParent: $('#neaModal'),
            width: '100%',
            allowClear: true,
            placeholder: 'Seleccione un material',
            language: {
                noResults: function() {
                    return 'No se encontraron resultados';
                }
            },
            ajax: {
                url: '{{ route('neas.getMateriales') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.map(item => ({
                            id: item.id,
                            text: `${item.codigo_material} - ${item.nombre}`
                        })),
                        pagination: {
                            more: data.current_page < data.last_page
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        });

        // Cargar precio automático cuando se selecciona un material
        $('#formMaterial').on('change', function() {
            const materialId = $(this).val();
            if (materialId) {
                $.get('{{ route("neas.getDetallesMaterial", ":id") }}'.replace(':id', materialId), function(data) {
                    $('#formPrecio').val(data.precio_unitario || 0);
                    // Guardar unidad de medida en un atributo data
                    $('#formMaterial').data('unidad_medida', data.unidad_medida || '');
                    $('#formMaterial').data('unidad_medida_nombre', data.unidad_medida_nombre || '');
                });
            }
        });
    }

    function setFechaHoy() {
        const hoy = new Date().toISOString().split('T')[0];
        $('#fecha').val(hoy);
    }

    function generarNumeroNea() {
        $.ajax({
            url: '{{ route('neas.proximoNumero') }}',
            method: 'GET',
            success: function(res) {
                if (res.success) {
                    $('#nro_documento').val(res.numero_nea);
                }
            },
            error: function() {
                Swal.fire('Error', 'No se pudo generar el número de NEA', 'error');
            }
        });
    }

    function agregarFilaDetalle() {
        // Validar campos
        const materialId = $('#formMaterial').val();
        const cantidad = parseFloat($('#formCantidad').val());
        const precio = parseFloat($('#formPrecio').val());
        const incluye = $('#formIncluye').is(':checked');

        if (!materialId) {
            Swal.fire('Error', 'Debe seleccionar un material', 'error');
            return;
        }
        if (isNaN(cantidad) || cantidad <= 0) {
            Swal.fire('Error', 'La cantidad debe ser mayor a 0', 'error');
            return;
        }
        if (isNaN(precio) || precio < 0) {
            Swal.fire('Error', 'El precio debe ser válido', 'error');
            return;
        }

        // Obtener texto del material seleccionado y unidad de medida
        const materialText = $('#formMaterial').find('option:selected').text();
        const unidadMedida = $('#formMaterial').data('unidad_medida_nombre') || '';
        const subtotal = cantidad * precio;
        const igv = incluye ? subtotal - (subtotal / 1.18) : 0;

        // Crear fila en la tabla
        detalleCounter++;
        const html = `
            <tr data-detalle-id="detalle_${detalleCounter}">
                <td>
                    <small>${materialText}</small>
                    <input type="hidden" class="form-detalle-material-id" value="${materialId}">
                </td>
                <td>
                    <small><strong>${unidadMedida}</strong></small>
                    <input type="hidden" class="form-detalle-unidad" value="${unidadMedida}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm form-detalle-cantidad" value="${cantidad.toFixed(3)}" step="0.001" min="0" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm form-detalle-precio" value="${precio.toFixed(2)}" step="0.01" min="0" required>
                </td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input form-detalle-igv" type="checkbox" ${incluye ? 'checked' : ''}>
                    </div>
                </td>
                <td>
                    <strong class="form-detalle-subtotal">S/ ${subtotal.toFixed(2)}</strong>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-detalle" onclick="removerDetalle(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </td>
            </tr>
        `;

        // Mostrar tabla si no está visible
        $('#detallesTablaContainer').show();
        $('#detallesTbody').append(html);

        // Agregar event listeners a los inputs
        const $fila = $(`tr[data-detalle-id="detalle_${detalleCounter}"]`);
        $fila.find('.form-detalle-cantidad, .form-detalle-precio, .form-detalle-igv').on('change keyup', function() {
            calcularSubtotalFila($fila);
            calcularTotales();
        });

        // Limpiar formulario
        $('#formMaterial').val(null).trigger('change.select2');
        $('#formCantidad').val('');
        $('#formPrecio').val('');
        $('#formIncluye').prop('checked', false);

        // Enfocar en el campo de material
        $('#formMaterial').focus();

        calcularTotales();
    }

    function removerDetalle(btn) {
        $(btn).closest('tr').remove();
        
        // Ocultar tabla si no hay detalles
        if ($('#detallesTbody tr').length === 0) {
            $('#detallesTablaContainer').hide();
        }
        
        calcularTotales();
    }

    function calcularSubtotalFila($fila) {
        const cantidad = parseFloat($fila.find('.form-detalle-cantidad').val()) || 0;
        const precio = parseFloat($fila.find('.form-detalle-precio').val()) || 0;
        const subtotal = cantidad * precio;
        $fila.find('.form-detalle-subtotal').text('S/ ' + subtotal.toFixed(2));
    }

    function calcularTotales() {
        let totalSinIgv = 0;
        let totalIgv = 0;
        let hayIgv = false;

        $('#detallesTbody tr').each(function() {
            const $this = $(this);
            const cantidad = parseFloat($this.find('.form-detalle-cantidad').val()) || 0;
            const precio = parseFloat($this.find('.form-detalle-precio').val()) || 0;
            const incluye = $this.find('.form-detalle-igv').is(':checked');
            
            const subtotal = cantidad * precio;
            
            if (incluye) {
                hayIgv = true;
                const sinIgv = subtotal / 1.18;
                totalSinIgv += sinIgv;
                totalIgv += subtotal - sinIgv;
            } else {
                totalSinIgv += subtotal;
            }
        });

        const totalConIgv = totalSinIgv + totalIgv;

        // Actualizar label del subtotal
        if (hayIgv) {
            $('.total-item:first .total-label').text('Subtotal (Sin IGV)');
            $('#igvRow').show();
        } else {
            $('.total-item:first .total-label').text('Subtotal');
            $('#igvRow').hide();
        }
        
        // Actualizar valores
        $('#totalSinIgv').text('S/ ' + totalSinIgv.toFixed(2));
        $('#totalIgv').text('S/ ' + totalIgv.toFixed(2));
        $('#totalConIgv').text('S/ ' + totalConIgv.toFixed(2));
        
        if ($('#detallesTbody tr').length > 0) {
            $('#resumenTotales').show();
        } else {
            $('#resumenTotales').hide();
        }
    }

    function resetForm() {
        $('#neaForm')[0].reset();
        $('#nea_id').val('');
        $('#detallesTbody').html('');
        $('#detallesTablaContainer').hide();
        detalleCounter = 0;
        $('#resumenTotales').hide();
        setFechaHoy();
        
        // Resetear select2
        $('#proveedor_id').val(null).trigger('change');
        $('#tipo_comprobante_id').val(null).trigger('change');
        
        // Resetear formulario de agregar detalle
        $('#formMaterial').val('').trigger('change');
        $('#formCantidad').val('');
        $('#formPrecio').val('');
        $('#formIncluye').prop('checked', false);
        
        generarNumeroNea();
    }

    function guardarNea(e) {
        e.preventDefault();

        // Validar campos principales PRIMERO
        const proveedor_id = $('#proveedor_id').val();
        const fecha = $('#fecha').val();
        const tipo_comprobante_id = $('#tipo_comprobante_id').val();
        const numero_comprobante = $('#numero_comprobante').val();

        if (!proveedor_id) {
            Swal.fire('Error', 'Debe seleccionar un proveedor', 'error');
            return;
        }
        if (!fecha) {
            Swal.fire('Error', 'Debe ingresar una fecha', 'error');
            return;
        }
        if (!tipo_comprobante_id) {
            Swal.fire('Error', 'Debe seleccionar un tipo de comprobante', 'error');
            return;
        }
        if (!numero_comprobante) {
            Swal.fire('Error', 'Debe ingresar el número de comprobante', 'error');
            return;
        }

        // Validar detalles
        const detalles = [];
        $('#detallesTbody tr').each(function() {
            const $this = $(this);
            detalles.push({
                material_id: $this.find('.form-detalle-material-id').val(),
                cantidad: parseFloat($this.find('.form-detalle-cantidad').val()),
                precio_unitario: parseFloat($this.find('.form-detalle-precio').val()),
                incluye_igv: $this.find('.form-detalle-igv').is(':checked')
            });
        });

        if (detalles.length === 0) {
            Swal.fire('Error', 'Debe agregar al menos un material', 'error');
            return;
        }

        const estado = $('#estado').is(':checked') ? 1 : 0;
        const neaId = $('#nea_id').val();
        const url = neaId ? `/neas/${neaId}` : '/neas';
        const method = neaId ? 'PUT' : 'POST';

        const formData = {
            proveedor_id: $('#proveedor_id').val(),
            fecha: $('#fecha').val(),
            nro_documento: $('#nro_documento').val(),
            tipo_comprobante_id: $('#tipo_comprobante_id').val(),
            numero_comprobante: $('#numero_comprobante').val(),
            observaciones: $('#observaciones').val(),
            estado: estado,
            detalles: detalles,
            _token: '{{ csrf_token() }}'
        };

        Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(res) {
                Swal.fire('¡Guardado!', res.message, 'success').then(() => {
                    $('#neaModal').modal('hide');
                    $('#neasTable').DataTable().ajax.reload();
                });
            },
            error: function(xhr) {
                let mensaje = 'Error al guardar la NEA';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    window.verNea = function(id) {
        $.get(`/neas/${id}`, function(nea) {
            let detallesHtml = '<div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Material</th><th>Código</th><th>Unidad</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th><th>IGV</th><th>Total</th></tr></thead><tbody>';
            
            nea.detalles.forEach(d => {
                const materialNombre = d.material ? d.material.nombre : 'N/A';
                const materialCodigo = d.material ? d.material.codigo_material : 'N/A';
                const unidadMedida = d.material && d.material.unidad_medida ? d.material.unidad_medida.nombre : 'N/A';
                const subtotal = parseFloat(d.cantidad) * parseFloat(d.precio_unitario);
                
                // Calcular IGV correctamente
                let igv = 0;
                if (d.incluye_igv) {
                    // El precio ya incluye IGV, extraemos el IGV
                    igv = subtotal - (subtotal / 1.18);
                }
                
                const total = subtotal + igv;
                
                detallesHtml += `<tr>
                    <td>${materialNombre}</td>
                    <td>${materialCodigo}</td>
                    <td>${unidadMedida}</td>
                    <td class="text-end">${parseFloat(d.cantidad).toFixed(3)}</td>
                    <td class="text-end">S/ ${parseFloat(d.precio_unitario).toFixed(2)}</td>
                    <td class="text-end">S/ ${subtotal.toFixed(2)}</td>
                    <td class="text-end"><span class="badge ${d.incluye_igv ? 'bg-info' : 'bg-secondary'}">S/ ${igv.toFixed(2)}</span></td>
                    <td class="text-end"><strong>S/ ${total.toFixed(2)}</strong></td>
                </tr>`;
            });

            detallesHtml += '</tbody></table></div>';
            detallesHtml += `<div class="alert alert-info mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Total Sin IGV:</strong> S/ ${(parseFloat(nea.total_sin_igv) || 0).toFixed(2)}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>IGV Total:</strong> S/ ${(parseFloat(nea.igv_total) || 0).toFixed(2)}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Total Con IGV:</strong> S/ ${(parseFloat(nea.total_con_igv) || 0).toFixed(2)}</p>
                    </div>
                </div>
            </div>`;

            $('#neaDetallesContent').html(detallesHtml);
            $('#neaDetallesModal').modal('show');
        });
    };

    window.editNea = function(id) {
        $.get(`/neas/${id}`, function(nea) {
            $('#neaModalLabel').text('Editar NEA');
            $('#nea_id').val(nea.id);
            
            // Actualizar select2 de proveedor
            $('#proveedor_id').val(nea.proveedor_id).trigger('change');
            
            // Formatear fecha para el input type="date"
            let fecha = nea.fecha;
            if (fecha.includes('T')) {
                fecha = fecha.split('T')[0];
            }
            $('#fecha').val(fecha);
            
            $('#nro_documento').val(nea.nro_documento);
            
            // Actualizar select2 de tipo comprobante
            $('#tipo_comprobante_id').val(nea.tipo_comprobante_id).trigger('change');
            
            $('#numero_comprobante').val(nea.numero_comprobante || '');
            $('#observaciones').val(nea.observaciones || '');
            $('#estado').prop('checked', nea.estado);

            // Limpiar tabla y agregar detalles
            $('#detallesTbody').html('');
            detalleCounter = 0;

            nea.detalles.forEach(d => {
                detalleCounter++;
                const materialNombre = d.material ? d.material.nombre : 'N/A';
                const unidadMedida = d.material && d.material.unidad_medida ? d.material.unidad_medida.nombre : 'N/A';
                const subtotal = parseFloat(d.cantidad) * parseFloat(d.precio_unitario);
                const html = `
                    <tr data-detalle-id="detalle_${detalleCounter}">
                        <td>
                            <small>${materialNombre}</small>
                            <input type="hidden" class="form-detalle-material-id" value="${d.material_id}">
                        </td>
                        <td>
                            <small><strong>${unidadMedida}</strong></small>
                            <input type="hidden" class="form-detalle-unidad" value="${unidadMedida}">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm form-detalle-cantidad" value="${parseFloat(d.cantidad).toFixed(3)}" step="0.001" min="0" required>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm form-detalle-precio" value="${parseFloat(d.precio_unitario).toFixed(2)}" step="0.01" min="0" required>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input form-detalle-igv" type="checkbox" ${d.incluye_igv ? 'checked' : ''}>
                            </div>
                        </td>
                        <td>
                            <strong class="form-detalle-subtotal">S/ ${subtotal.toFixed(2)}</strong>
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-detalle" onclick="removerDetalle(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;

                $('#detallesTbody').append(html);
                
                // Agregar event listeners
                const $fila = $(`tr[data-detalle-id="detalle_${detalleCounter}"]`);
                $fila.find('.form-detalle-cantidad, .form-detalle-precio, .form-detalle-igv').on('change keyup', function() {
                    calcularSubtotalFila($fila);
                    calcularTotales();
                });
            });

            // Mostrar tabla si hay detalles
            if (nea.detalles.length > 0) {
                $('#detallesTablaContainer').show();
            }

            calcularTotales();
            $('#neaModal').modal('show');
        });
    };

    window.deleteNea = function(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/neas/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado', res.message, 'success');
                        $('#neasTable').DataTable().ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar la NEA', 'error');
                    }
                });
            }
        });
    };

    window.previsualizarPdf = function(id) {
        const iframeUrl = '/neas/' + id + '/preview';
        $('#neaPDFFrame').attr('src', iframeUrl);
        
        // Usar Bootstrap Modal API estándar
        const pdfModal = new bootstrap.Modal(document.getElementById('neaPDFModal'), {
            backdrop: true,
            keyboard: true
        });
        pdfModal.show();
    };

    // Limpiar iframe cuando se cierra el modal
    document.getElementById('neaPDFModal').addEventListener('hidden.bs.modal', function() {
        $('#neaPDFFrame').attr('src', '');
    });

    window.anualarNea = function(id) {
        Swal.fire({
            title: '<div style="display: flex; align-items: center; justify-content: center; gap: 12px;"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ff9800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg><span>Anular NEA</span></div>',
            html: `
                <div style="text-align: left; margin: 20px 0;">
                    <div style="background: #fff3cd; border-left: 4px solid #ff9800; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                        <p style="margin: 0; color: #856404; font-size: 14px; font-weight: 500;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; margin-right: 8px; vertical-align: middle;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Esta acción será registrada en auditoría y no podrá ser revertida.
                        </p>
                    </div>
                    
                    <label style="display: block; font-weight: 600; color: #2c3e50; margin-bottom: 8px; font-size: 14px;">
                        Motivo de la anulación <span style="color: #e74c3c;">*</span>
                    </label>
                    <textarea id="motivoAnulacion" class="form-control" rows="5" 
                        placeholder="Explique brevemente el motivo por el cual desea anular esta NEA..."
                        style="resize: vertical; border: 2px solid #bfc9d4; border-radius: 6px; font-size: 13px; padding: 10px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"></textarea>
                    
                    <div style="margin-top: 12px; font-size: 12px; color: #7f8c8d;">
                        <strong>Cambios que ocurrirán:</strong>
                        <ul style="margin: 8px 0; padding-left: 20px;">
                            <li>NEA marcada como anulada</li>
                            <li>Stock revertido automáticamente</li>
                            <li>Registro en auditoría con usuario y fecha</li>
                        </ul>
                    </div>
                </div>
            `,
            icon: false,
            showCancelButton: true,
            confirmButtonColor: '#ff9800',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline; margin-right: 6px; vertical-align: middle;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Sí, anular NEA',
            cancelButtonText: 'Cancelar',
            width: '500px',
            didOpen: () => {
                $('#motivoAnulacion').focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const motivo = $('#motivoAnulacion').val().trim();
                
                if (!motivo) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campo requerido',
                        text: 'Debe ingresar un motivo para anular la NEA',
                        confirmButtonColor: '#e74c3c'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Anulando NEA...',
                    html: '<div class="spinner-border text-warning" role="status" style="margin-top: 10px;"><span class="visually-hidden">Cargando...</span></div>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });

                $.ajax({
                    url: `/neas/${id}/anular`,
                    type: 'POST',
                    data: JSON.stringify({
                        motivo_anulacion: motivo,
                        _token: '{{ csrf_token() }}'
                    }),
                    contentType: 'application/json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡NEA Anulada!',
                                html: '<p style="color: #7f8c8d; margin: 10px 0;">La NEA ha sido anulada correctamente y el stock ha sido revertido.</p>',
                                confirmButtonColor: '#27ae60',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                $('#neasTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message || 'No se pudo anular la NEA',
                                confirmButtonColor: '#e74c3c'
                            });
                        }
                    },
                    error: function(xhr) {
                        let mensaje = 'Error al anular la NEA';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            mensaje = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en el servidor',
                            text: mensaje,
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                });
            }
        });
    };

    // Función para mostrar alerta cuando no se puede editar
    function alertaSinEdicion() {
        Swal.fire({
            icon: 'warning',
            title: 'Edición no permitida',
            text: 'No se puede editar esta NEA porque ya tiene movimientos de salida (PECOSA) activos. Para editarla, primero debe anular las PECOSAs relacionadas.',
            confirmButtonColor: '#ff9800',
            confirmButtonText: 'Entendido'
        });
    }

    // Función para mostrar alerta cuando no se puede anular
    function alertaSinAnular() {
        Swal.fire({
            icon: 'warning',
            title: 'Anulación no permitida',
            text: 'No se puede anular esta NEA porque tiene movimientos de salida (PECOSA) activos. Para anularla, primero debe anular las PECOSAs relacionadas.',
            confirmButtonColor: '#ff9800',
            confirmButtonText: 'Entendido'
        });
    }
    </script>
@endsection

