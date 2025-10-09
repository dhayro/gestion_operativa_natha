@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .modal-content { background: #fff !important; }
        .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }
        
        /* Badge styling */
        .badge-success {
            background-color: #1abc9c !important;
            color: white !important;
        }
        .badge-danger {
            background-color: #e74c3c !important;
            color: white !important;
        }

        /* Select2 Bootstrap 5 integration */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #bfc9d4 !important;
            border-radius: 6px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            border: 1px solid #bfc9d4 !important;
            border-radius: 6px !important;
        }

        /* Estilos para el modal de SOATs */
        .modal-xl {
            max-width: 95%;
        }
        
        .soat-management-modal .modal-dialog {
            max-height: 90vh;
        }
        
        .soat-management-modal .modal-content {
            height: 85vh;
            max-height: 85vh;
        }
        
        .soat-management-modal .modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }
        
        /* Estilos para la tabla de historial de SOATs - similar a la tabla principal */
        .soat-history-table {
            font-size: 14px !important;
            width: 100% !important;
            background-color: #fff;
        }
        
        .soat-history-table thead th {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            font-weight: 600;
            color: #495057;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .soat-history-table tbody td {
            vertical-align: middle;
            border-color: #dee2e6;
        }
        
        .soat-history-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Estilos para dropdowns en el modal - idénticos a la tabla principal */
        
        
        /* Hacer la tabla responsive dentro del modal */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        
        /* Estilos específicos para DataTables en el modal */
        .soat-management-modal .dataTables_wrapper {
            width: 100%;
        }
        
        .soat-management-modal .dataTables_scroll {
            width: 100%;
        }
        
        .soat-management-modal .dataTables_scrollBody {
            width: 100%;
        }
        
        .soat-management-modal #soatHistoryTable {
            width: 100% !important;
            table-layout: auto;
        }
        
        .soat-management-modal .dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody > table > thead > tr > th,
        .soat-management-modal .dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody > table > tbody > tr > td {
            width: auto !important;
        }
     
        
        .soat-management-modal .table-responsive {
            overflow: visible;
        }
        
        .soat-management-modal .dataTables_wrapper {
            overflow: visible;
        }
        
        .vehicle-info-card {
            border: 1px solid #e3ebf0;
            border-radius: 8px;
            background-color: #f8f9fa;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .soat-status-vigente {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .soat-status-vencido {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .soat-status-por-vencer {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        .modal-xl {
            max-width: 1200px;
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de Vehículos</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehiculoModal" id="btnNuevoVehiculo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo Vehículo
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="vehiculosTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Marca</th>
                                <th>Nombre</th>
                                <th>Año</th>
                                <th>Modelo</th>
                                <th>Color</th>
                                <th>Placa</th>
                                <th>Tipo Combustible</th>
                                <th>SOAT Vencimiento</th>
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

<!-- Modal para crear/editar vehículo -->
<div class="modal fade" id="vehiculoModal" tabindex="-1" aria-labelledby="vehiculoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="vehiculoForm" action="" method="POST">
                @csrf
                <input type="hidden" id="vehiculo_id" name="vehiculo_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="vehiculoModalLabel">Crear/Editar Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label">Marca <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="marca" name="marca" maxlength="100" placeholder="Ingrese la marca del vehículo" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" placeholder="Ingrese el nombre del vehículo" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="year" class="form-label">Año</label>
                            <input type="number" class="form-control" id="year" name="year" min="1900" max="{{ date('Y') + 1 }}" placeholder="Año del vehículo">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modelo" name="modelo" maxlength="100" placeholder="Modelo del vehículo" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="color" name="color" maxlength="50" placeholder="Color del vehículo" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="placa" name="placa" maxlength="20" placeholder="Placa del vehículo" required style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo_combustible_id" class="form-label">Tipo de Combustible <span class="text-danger">*</span></label>
                            <select class="form-control" id="tipo_combustible_id" name="tipo_combustible_id" required>
                                <option value="">Seleccione un tipo de combustible</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="estado" name="estado" value="1" checked>
                                <label class="form-check-label" for="estado">Activo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para gestionar SOATs de un vehículo -->
<div class="modal fade soat-management-modal" id="soatManagementModal" tabindex="-1" aria-labelledby="soatManagementModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="soatManagementModalLabel">Gestión de SOATs - <span id="vehiculoInfo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información del vehículo -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Información del Vehículo</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Placa:</strong> <span id="vehiculoPlaca"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Marca:</strong> <span id="vehiculoMarca"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Nombre:</strong> <span id="vehiculoNombre"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Modelo:</strong> <span id="vehiculoModelo"></span>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <strong>Año:</strong> <span id="vehiculoYear"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Color:</strong> <span id="vehiculoColor"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Tipo Combustible:</strong> <span id="vehiculoTipoCombustible"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Estado SOAT:</strong> <span id="estadoSoatVehiculo"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para nuevo SOAT -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" id="btnNuevoSoatVehiculo">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Agregar Nuevo SOAT
                        </button>
                    </div>
                </div>

                <!-- Historial de SOATs -->
                <div class="row">
                    <div class="col-md-12">
                        <h6>Historial de SOATs</h6>
                        <div class="table-responsive">
                            <table id="soatHistoryTable" class="table table-striped table-bordered soat-history-table table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Número SOAT</th>
                                        <th>Proveedor</th>
                                        <th>Fecha Emisión</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los datos se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar SOAT específico del vehículo -->
<div class="modal fade" id="soatVehiculoModal" tabindex="-1" aria-labelledby="soatVehiculoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="soatVehiculoForm" action="" method="POST">
                @csrf
                <input type="hidden" id="soat_vehiculo_id" name="soat_id">
                <input type="hidden" id="selected_vehiculo_id" name="vehiculo_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="soatVehiculoModalLabel">Crear/Editar SOAT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="proveedor_vehiculo_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control" id="proveedor_vehiculo_id" name="proveedor_id" required>
                                <option value="">Seleccione un proveedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="numero_soat_vehiculo" class="form-label">Número SOAT <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="numero_soat_vehiculo" name="numero_soat" maxlength="200" placeholder="Ingrese el número del SOAT" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_emision_vehiculo" class="form-label">Fecha de Emisión <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_emision_vehiculo" name="fecha_emision" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_vencimiento_vehiculo" class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_vencimiento_vehiculo" name="fecha_vencimiento" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="estado_vehiculo" name="estado" value="1" checked>
                                <label class="form-check-label" for="estado_vehiculo">Activo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar SOAT</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    // Carga dinámica de tipos de combustible
    function loadTiposCombustible(selectedId = null) {
        return $.get('/api/select/tipo-combustibles')
            .done(function(data) {
                var select = $('#tipo_combustible_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${selectedId == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#vehiculoModal'),
                    placeholder: 'Seleccione un tipo de combustible',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar tipos de combustible');
            });
    }

    // Carga dinámica de proveedores para SOAT
    function loadProveedoresVehiculo(selectedId = null) {
        return $.get('/api/select/proveedores')
            .done(function(data) {
                var select = $('#proveedor_vehiculo_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${selectedId == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#soatVehiculoModal'),
                    placeholder: 'Seleccione un proveedor',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar proveedores');
            });
    }

    // Inicializar Select2 para tipos de combustible
    function initializeSelect2() {
        $('#tipo_combustible_id').select2({
            dropdownParent: $('#vehiculoModal'),
            placeholder: 'Seleccione un tipo de combustible',
            allowClear: true,
            language: {
                noResults: function() { return 'No hay resultados'; }
            }
        });
    }

    // Validación en tiempo real
    function setupFormValidation() {
        // Limpiar validaciones previas
        $('#marca, #nombre, #year, #modelo, #color, #placa, #tipo_combustible_id').off('blur focus change');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        // Validaciones
        $('#marca, #nombre, #modelo, #color').on('blur', function() {
            var value = $(this).val().trim();
            var fieldName = $(this).attr('name');
            var displayName = $(this).prev('label').text().replace(' *', '');
            
            if (value.length === 0) {
                validateField($(this), `${displayName} es obligatorio`, function(value) {
                    return value.length > 0;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        $('#placa').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length === 0) {
                validateField($(this), 'La placa es obligatoria', function(value) {
                    return value.length > 0;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Convertir placa a mayúsculas
        $('#placa').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        // Limpiar error al enfocar
        $('#marca, #nombre, #year, #modelo, #color, #placa, #tipo_combustible_id').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });
    }

    function validateField(field, message, validationFunction) {
        var value = field.val();
        var isValid = validationFunction(value);
        field.removeClass('is-invalid is-valid');
        field.next('.invalid-feedback').remove();
        if (!isValid) {
            field.addClass('is-invalid');
            field.after(`<div class="invalid-feedback">${message}</div>`);
            return false;
        } else {
            field.addClass('is-valid');
            return true;
        }
    }

    function validateForm() {
        var isValid = true;
        var firstErrorField = null;

        // Validar campos requeridos
        var requiredFields = ['marca', 'nombre', 'modelo', 'color', 'placa'];
        requiredFields.forEach(function(fieldName) {
            var field = $('#' + fieldName);
            var displayName = field.prev('label').text().replace(' *', '');
            var value = field.val().trim();
            
            if (value.length === 0) {
                if (!validateField(field, `${displayName} es obligatorio`, function(value) {
                    return value.length > 0;
                })) {
                    isValid = false;
                    if (!firstErrorField) firstErrorField = field;
                }
            }
        });

        // Validar tipo de combustible
        var tipoCombustible = $('#tipo_combustible_id').val();
        if (!tipoCombustible) {
            $('#tipo_combustible_id').next('.select2').find('.select2-selection').addClass('is-invalid');
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#tipo_combustible_id');
        }

        if (firstErrorField) {
            firstErrorField.focus();
        }
        return isValid;
    }

    $(document).ready(function() {
        var table = $('#vehiculosTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('vehiculos.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'marca', name: 'marca' },
                { data: 'nombre', name: 'nombre' },
                { data: 'year', name: 'year' },
                { data: 'modelo', name: 'modelo' },
                { data: 'color', name: 'color' },
                { data: 'placa', name: 'placa' },
                { data: 'tipo_combustible_nombre', name: 'tipoCombustible.nombre' },
                { data: 'soat_vencimiento', name: 'soat_vencimiento', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
            columnDefs: [
                { targets: [8, 9, 10], className: "text-center" }
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

        // Inicializar Select2 con placeholder
        initializeSelect2();
        
        // Cargar todos los selects dinámicamente al inicializar
        loadTiposCombustible();
        setupFormValidation();

        // Si el usuario cambia el select y la opción es inactiva, eliminarla y mostrar placeholder
        $('#tipo_combustible_id').on('change', function() {
            var $opt = $(this).find('option:selected');
            if ($opt.is(':disabled')) {
                $(this).val('').trigger('change.select2');
                $opt.remove();
            }
        });

        $('#btnNuevoVehiculo').click(function() {
            $('#vehiculoForm')[0].reset();
            $('#vehiculo_id').val('');
            $('#vehiculoModalLabel').text('Nuevo Vehículo');
            loadTiposCombustible();
            // Limpiar selects y mostrar placeholder
            setTimeout(function() {
                $('#tipo_combustible_id').val('').trigger('change');
            }, 200);
            setupFormValidation();
        });

        $('#vehiculoForm').on('submit', function(e) {
            // Ajustar valor de estado (checkbox)
            if ($('#estado').is(':checked')) {
                $('#estado').val(1);
            } else {
                // Para enviar 0 si está desmarcado
                if ($('#estado').next('input[type=hidden][name=estado]').length === 0) {
                    $('<input>').attr({type: 'hidden', name: 'estado', value: 0}).insertAfter($('#estado'));
                }
            }
            e.preventDefault();
            var $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true);
            if (!validateForm()) {
                $submitBtn.prop('disabled', false);
                Swal.fire({
                    title: 'Campos Requeridos',
                    text: 'Por favor, complete todos los campos obligatorios correctamente.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            var id = $('#vehiculo_id').val();
            var url = id ? '/vehiculos/' + id : '/vehiculos';
            var method = id ? 'PUT' : 'POST';
            var formData = $(this).serialize();
            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(res) {
                    Swal.fire('Guardado!', res.message || 'El vehículo ha sido guardado correctamente.', 'success');
                    $('#vehiculoModal').modal('hide');
                    table.ajax.reload();
                    $('#vehiculoForm')[0].reset();
                    $submitBtn.prop('disabled', false);
                },
                error: function(xhr) {
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            var input = $('#' + field);
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                        });
                        Swal.fire('Error de Validación', 'Por favor, corrija los errores en el formulario.', 'error');
                    } else {
                        Swal.fire('Error!', 'Hubo un problema al guardar el vehículo.', 'error');
                    }
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        $('#vehiculoModal').on('hidden.bs.modal', function () {
            $('#vehiculoForm')[0].reset();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#tipo_combustible_id').val('').trigger('change');
        });
    });

    window.editVehiculo = function(id) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        $.get('/vehiculos/' + id, function(data) {
            Swal.close();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#vehiculo_id').val(data.id);
            $('#marca').val(data.marca || '');
            $('#nombre').val(data.nombre || '');
            $('#year').val(data.year || '');
            $('#modelo').val(data.modelo || '');
            $('#color').val(data.color || '');
            $('#placa').val(data.placa || '');
            
            // Cargar tipos de combustible y manejar selección
            loadTiposCombustible(data.tipo_combustible_id).done(function() {
                var tipoCombVal = data.tipo_combustible_id ? data.tipo_combustible_id.toString() : '';
                if ($('#tipo_combustible_id option[value="'+tipoCombVal+'"], #tipo_combustible_id option[value='+tipoCombVal+']').length > 0) {
                    $('#tipo_combustible_id').val(tipoCombVal).trigger('change.select2');
                } else if (tipoCombVal) {
                    var nombreTipo = data.tipo_combustible && data.tipo_combustible.nombre ? data.tipo_combustible.nombre + ' (inactivo)' : 'Inactivo';
                    $('#tipo_combustible_id').append('<option value="'+tipoCombVal+'" selected>'+nombreTipo+'</option>');
                    $('#tipo_combustible_id').val(tipoCombVal).trigger('change.select2');
                    setTimeout(function(){
                        $('#tipo_combustible_id option[value="'+tipoCombVal+'"]').prop('disabled', true);
                    }, 0);
                } else {
                    $('#tipo_combustible_id').val('').trigger('change.select2');
                }
            });
            
            if (data.estado == 1) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }
            $('#vehiculoModalLabel').text('Editar Vehículo');
            $('#vehiculoModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.close();
            Swal.fire('Error!', 'No se pudieron cargar los datos del vehículo.', 'error');
        });
    };

    window.gestionarSoat = function(vehiculoId) {
        // Redirigir a la página de gestión de SOATs con el vehículo preseleccionado
        window.location.href = '/soats?vehiculo_id=' + vehiculoId;
    };

    window.deleteVehiculo = function(id) {
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
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                $.ajax({
                    url: '/vehiculos/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', res.message || 'Vehículo eliminado correctamente.', 'success');
                        $('#vehiculosTable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        var message = 'No se pudo eliminar el vehículo';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', message, 'error');
                    }
                });
            }
        });
    };
    // Variables globales para gestión de SOATs
    var soatHistoryTable = null;
    var currentVehiculoId = null;

    // Función para recargar información del vehículo en el modal y tabla principal
    function reloadVehiculoInfo(vehiculoId) {
        if (!vehiculoId && currentVehiculoId) {
            vehiculoId = currentVehiculoId;
        }
        
        if (vehiculoId) {
            // Recargar información del vehículo en el modal
            $.get('/vehiculos/' + vehiculoId, function(vehiculo) {
                $('#vehiculoInfo').text(vehiculo.marca + ' ' + vehiculo.nombre + ' - ' + vehiculo.placa);
                $('#vehiculoPlaca').text(vehiculo.placa);
                $('#vehiculoMarca').text(vehiculo.marca);
                $('#vehiculoNombre').text(vehiculo.nombre);
                $('#vehiculoModelo').text(vehiculo.modelo);
                $('#vehiculoYear').text(vehiculo.year || 'N/A');
                $('#vehiculoColor').text(vehiculo.color);
                $('#vehiculoTipoCombustible').text(vehiculo.tipo_combustible ? vehiculo.tipo_combustible.nombre : 'N/A');
                
                // Determinar estado del SOAT
                if (vehiculo.soat_activo && vehiculo.soat_activo.fecha_vencimiento) {
                    const vencimiento = new Date(vehiculo.soat_activo.fecha_vencimiento);
                    const hoy = new Date();
                    const diasRestantes = Math.ceil((vencimiento - hoy) / (1000 * 60 * 60 * 24));
                    
                    if (diasRestantes < 0) {
                        $('#estadoSoatVehiculo').html('<span class="badge badge-danger">Vencido</span>');
                    } else if (diasRestantes <= 30) {
                        $('#estadoSoatVehiculo').html('<span class="badge badge-warning">Por vencer</span>');
                    } else {
                        $('#estadoSoatVehiculo').html('<span class="badge badge-success">Vigente</span>');
                    }
                } else {
                    $('#estadoSoatVehiculo').html('<span class="badge badge-secondary">Sin SOAT</span>');
                }
            });
            
            // Recargar tabla principal de vehículos
            if (typeof table !== 'undefined' && table) {
                table.ajax.reload(null, false); // false = mantener paginación actual
            }
        }
    }

    // Función para gestionar SOAT (llamada desde la tabla de vehículos)
    window.gestionarSoat = function(vehiculoId) {
        // Guardar el ID del vehículo actual
        currentVehiculoId = vehiculoId;
        
        // Cargar información del vehículo
        $.get('/vehiculos/' + vehiculoId, function(vehiculo) {
            $('#vehiculoInfo').text(vehiculo.marca + ' ' + vehiculo.nombre + ' - ' + vehiculo.placa);
            $('#vehiculoPlaca').text(vehiculo.placa);
            $('#vehiculoMarca').text(vehiculo.marca);
            $('#vehiculoNombre').text(vehiculo.nombre);
            $('#vehiculoModelo').text(vehiculo.modelo);
            $('#vehiculoYear').text(vehiculo.year || 'N/A');
            $('#vehiculoColor').text(vehiculo.color);
            $('#vehiculoTipoCombustible').text(vehiculo.tipo_combustible ? vehiculo.tipo_combustible.nombre : 'N/A');
            $('#selected_vehiculo_id').val(vehiculoId);
            
            // Determinar estado del SOAT
            if (vehiculo.soat_activo && vehiculo.soat_activo.fecha_vencimiento) {
                const vencimiento = new Date(vehiculo.soat_activo.fecha_vencimiento);
                const hoy = new Date();
                const diasRestantes = Math.ceil((vencimiento - hoy) / (1000 * 60 * 60 * 24));
                
                if (diasRestantes < 0) {
                    $('#estadoSoatVehiculo').html('<span class="badge badge-danger">Vencido</span>');
                } else if (diasRestantes <= 30) {
                    $('#estadoSoatVehiculo').html('<span class="badge badge-warning">Por vencer</span>');
                } else {
                    $('#estadoSoatVehiculo').html('<span class="badge badge-success">Vigente</span>');
                }
            } else {
                $('#estadoSoatVehiculo').html('<span class="badge badge-secondary">Sin SOAT</span>');
            }
            
            // Cargar historial de SOATs con DataTables
            initializeSoatHistoryTable(vehiculoId);
            
            // Mostrar modal
            $('#soatManagementModal').modal('show');
            
            // Ajustar columnas cuando el modal esté completamente visible
            $('#soatManagementModal').on('shown.bs.modal', function () {
                setTimeout(function() {
                    if (soatHistoryTable) {
                        soatHistoryTable.columns.adjust();
                        if (soatHistoryTable.responsive) {
                            soatHistoryTable.responsive.recalc();
                        }
                        $('#soatHistoryTable').css('width', '100%');
                    }
                }, 100);
            });
            

        }).fail(function() {
            Swal.fire('Error!', 'No se pudo cargar la información del vehículo.', 'error');
        });
    };

    // Función para inicializar DataTable de historial de SOATs
    function initializeSoatHistoryTable(vehiculoId) {
        if (soatHistoryTable) {
            soatHistoryTable.destroy();
        }
        
        soatHistoryTable = $('#soatHistoryTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: false,
            autoWidth: false,
            width: "100%",
            deferRender: true,
            ajax: {
                url: '/vehiculos/' + vehiculoId + '/soats/data',
                type: 'GET',
                dataSrc: function(json) {
                    // Debug: verificar datos recibidos
                    if (json.data && json.data.length > 0) {
                        console.log('Primera fila de SOATs:', json.data[0]);
                        console.log('HTML de acciones:', json.data[0].action);
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                { data: 'numero_soat', name: 'numero_soat', width: '20%' },
                { data: 'proveedor_nombre', name: 'proveedor.nombre', width: '20%' },
                { data: 'fecha_emision_formatted', name: 'fecha_emision', width: '15%' },
                { 
                    data: 'fecha_vencimiento_formatted', 
                    name: 'fecha_vencimiento', 
                    width: '15%',
                    render: function(data, type, row) {
                        return data;
                    }
                },
                { 
                    data: 'estado_badge', 
                    name: 'estado', 
                    orderable: false, 
                    searchable: false, 
                    width: '10%',
                    render: function(data, type, row) {
                        return data;
                    }
                },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false, 
                    width: '15%',
                    render: function(data, type, row) {
                        return data;
                    }
                }
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[4, "desc"]], // Ordenar por fecha de vencimiento descendente
            columnDefs: [
                { targets: [0, 3, 4, 5, 6], className: "text-center" }
            ],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No hay SOATs registrados",
                emptyTable: "No hay SOATs registrados para este vehículo",
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
            },
            drawCallback: function() {
                // Debug: verificar elementos creados
                var actionCells = $('.soat-management-modal .dropdown').length;
                var dropdownToggles = $('.soat-management-modal .dropdown-toggle').length;
                console.log('Dropdowns encontrados:', actionCells, 'Toggles:', dropdownToggles);
                
                // Reinicializar tooltips después de cada redibujado
                $('[data-bs-toggle="tooltip"]').tooltip();
                
                // Ajustar el ancho de la tabla al contenedor
                $('#soatHistoryTable').css('width', '100%');
                $('.dataTables_wrapper').css('width', '100%');
                
                // Manejar dropdowns con el mismo comportamiento que la tabla principal
                $('.soat-management-modal .dropdown-toggle').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var $this = $(this);
                    var $dropdown = $this.next('.dropdown-menu');
                    
                    // Cerrar otros dropdowns abiertos
                    $('.soat-management-modal .dropdown-menu').not($dropdown).removeClass('show').hide();
                    
                    // Toggle del dropdown actual
                    if ($dropdown.hasClass('show')) {
                        $dropdown.removeClass('show').hide();
                        return;
                    }
                    
                    // Mostrar el dropdown
                    $dropdown.addClass('show').show();
                    
                    // Posicionamiento inteligente
                    setTimeout(function() {
                        var buttonRect = $this[0].getBoundingClientRect();
                        var dropdownRect = $dropdown[0].getBoundingClientRect();
                        var windowHeight = window.innerHeight;
                        var windowWidth = window.innerWidth;
                        
                        // Calcular posición vertical
                        var spaceBelow = windowHeight - buttonRect.bottom;
                        var spaceAbove = buttonRect.top;
                        var dropdownHeight = dropdownRect.height;
                        
                        var top, left;
                        
                        if (spaceBelow >= dropdownHeight || spaceBelow > spaceAbove) {
                            // Mostrar debajo
                            top = buttonRect.bottom + window.scrollY + 2;
                        } else {
                            // Mostrar arriba
                            top = buttonRect.top + window.scrollY - dropdownHeight - 2;
                        }
                        
                        // Calcular posición horizontal
                        left = buttonRect.left + window.scrollX;
                        
                        // Ajustar si se sale de la pantalla por la derecha
                        if (left + dropdownRect.width > windowWidth) {
                            left = windowWidth - dropdownRect.width - 10;
                        }
                        
                        // Ajustar si se sale de la pantalla por la izquierda
                        if (left < 10) {
                            left = 10;
                        }
                        
                       
                    }, 10);
                });
                
                // Cerrar dropdowns al hacer clic fuera
                $(document).off('click.soat-dropdowns').on('click.soat-dropdowns', function(e) {
                    if (!$(e.target).closest('.dropdown').length) {
                        $('.soat-management-modal .dropdown-menu').removeClass('show').hide();
                    }
                });
            },
            initComplete: function() {
                // Ajustar el ancho al completar la inicialización
                $('#soatHistoryTable').css('width', '100%');
                $('.dataTables_wrapper').css('width', '100%');
                this.api().columns.adjust();
            }
        });
    }

    // Inicializar Select2 para proveedores en modal de vehículo
    // Inicializar Select2 para proveedores en modal de vehículo
    function initializeProveedoresVehiculoSelect2() {
        $('#proveedor_vehiculo_id').select2({
            dropdownParent: $('#soatVehiculoModal'),
            placeholder: 'Seleccione un proveedor',
            allowClear: true,
            language: {
                noResults: function() { return 'No hay resultados'; }
            }
        });
    }

    // Botón para nuevo SOAT desde gestión de vehículo
    $('#btnNuevoSoatVehiculo').click(function() {
        $('#soatVehiculoForm')[0].reset();
        $('#soat_vehiculo_id').val('');
        $('#soatVehiculoModalLabel').text('Nuevo SOAT');
        loadProveedoresVehiculo();
        // Limpiar selects y mostrar placeholder
        setTimeout(function() {
            $('#proveedor_vehiculo_id').val('').trigger('change');
        }, 200);
        // Asegurar que el botón esté habilitado
        $('#soatVehiculoForm button[type="submit"]').prop('disabled', false);
        $('#soatVehiculoModal').modal('show');
    });

    // Envío del formulario de SOAT de vehículo
    $('#soatVehiculoForm').on('submit', function(e) {
        e.preventDefault();
        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true);
        
        // Ajustar valor de estado (checkbox)
        if ($('#estado_vehiculo').is(':checked')) {
            $('#estado_vehiculo').val(1);
        } else {
            if ($('#estado_vehiculo').next('input[type=hidden][name=estado]').length === 0) {
                $('<input>').attr({type: 'hidden', name: 'estado', value: 0}).insertAfter($('#estado_vehiculo'));
            }
        }
        
        var id = $('#soat_vehiculo_id').val();
        var url = id ? '/soats/' + id : '/soats';
        var method = id ? 'PUT' : 'POST';
        var formData = $(this).serialize();
        
        Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(res) {
                Swal.fire('Guardado!', res.message || 'El SOAT ha sido guardado correctamente.', 'success');
                $('#soatVehiculoModal').modal('hide');
                
                // Recargar historial de SOATs
                if (soatHistoryTable) {
                    soatHistoryTable.ajax.reload();
                }
                
                // Recargar información del vehículo y tabla principal
                reloadVehiculoInfo(currentVehiculoId);
                
                $submitBtn.prop('disabled', false);
            },
            error: function(xhr) {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        var input = $('#' + field + '_vehiculo');
                        if (input.length === 0) {
                            input = $('#' + field.replace('_id', '_vehiculo_id'));
                        }
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                    });
                    Swal.fire('Error de Validación', 'Por favor, corrija los errores en el formulario.', 'error');
                } else {
                    Swal.fire('Error!', 'Hubo un problema al guardar el SOAT.', 'error');
                }
                $submitBtn.prop('disabled', false);
            }
        });
    });

    // Función para editar SOAT desde gestión de vehículo
    window.editSoatVehiculo = function(id) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        $.get('/soats/' + id, function(data) {
            Swal.close();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            
            $('#soat_vehiculo_id').val(data.id);
            $('#numero_soat_vehiculo').val(data.numero_soat || '');
            $('#fecha_emision_vehiculo').val(data.fecha_emision || '');
            $('#fecha_vencimiento_vehiculo').val(data.fecha_vencimiento || '');
            
            // Cargar proveedores y manejar selección
            loadProveedoresVehiculo(data.proveedor_id).done(function() {
                var proveedorVal = data.proveedor_id ? data.proveedor_id.toString() : '';
                if ($('#proveedor_vehiculo_id option[value="'+proveedorVal+'"], #proveedor_vehiculo_id option[value='+proveedorVal+']').length > 0) {
                    $('#proveedor_vehiculo_id').val(proveedorVal).trigger('change.select2');
                } else if (proveedorVal) {
                    var nombreProveedor = data.proveedor && data.proveedor.razon_social ? data.proveedor.razon_social + ' (inactivo)' : 'Proveedor inactivo';
                    if (data.proveedor && data.proveedor.ruc) {
                        nombreProveedor += ' - ' + data.proveedor.ruc;
                    }
                    $('#proveedor_vehiculo_id').append('<option value="'+proveedorVal+'" selected>'+nombreProveedor+'</option>');
                    $('#proveedor_vehiculo_id').val(proveedorVal).trigger('change.select2');
                    setTimeout(function(){
                        $('#proveedor_vehiculo_id option[value="'+proveedorVal+'"]').prop('disabled', true);
                    }, 0);
                } else {
                    $('#proveedor_vehiculo_id').val('').trigger('change.select2');
                }
            });
            
            if (data.estado == 1) {
                $('#estado_vehiculo').prop('checked', true);
            } else {
                $('#estado_vehiculo').prop('checked', false);
            }
            
            $('#soatVehiculoModalLabel').text('Editar SOAT');
            $('#soatVehiculoModal').modal('show');
            // Asegurar que el botón esté habilitado
            $('#soatVehiculoForm button[type="submit"]').prop('disabled', false);
        }).fail(function() {
            Swal.close();
            Swal.fire('Error!', 'No se pudieron cargar los datos del SOAT.', 'error');
        });
    };

    // Función para eliminar SOAT desde gestión de vehículo
    window.deleteSoatVehiculo = function(id) {
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
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                
                $.ajax({
                    url: '/soats/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', res.message || 'SOAT eliminado correctamente.', 'success');
                        
                        // Recargar historial de SOATs
                        if (soatHistoryTable) {
                            soatHistoryTable.ajax.reload();
                        }
                        
                        // Recargar información del vehículo y tabla principal
                        reloadVehiculoInfo(currentVehiculoId);
                    },
                    error: function(xhr) {
                        var message = 'No se pudo eliminar el SOAT';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', message, 'error');
                    }
                });
            }
        });
    };

    // Inicializar Select2 para proveedores
    initializeProveedoresVehiculoSelect2();
    
    // Cargar proveedores dinámicamente
    loadProveedoresVehiculo();

    // Si el usuario cambia el select de proveedor y la opción es inactiva, eliminarla y mostrar placeholder
    $('#proveedor_vehiculo_id').on('change', function() {
        var $opt = $(this).find('option:selected');
        if ($opt.is(':disabled')) {
            $(this).val('').trigger('change.select2');
            $opt.remove();
        }
    });

    // Limpiar modal al cerrarlo
    $('#soatVehiculoModal').on('hidden.bs.modal', function () {
        $('#soatVehiculoForm')[0].reset();
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();
        $('#proveedor_vehiculo_id').val('').trigger('change');
        // Asegurar que el botón esté habilitado
        $('#soatVehiculoForm button[type="submit"]').prop('disabled', false);
    });

    // Limpiar modal de gestión de SOATs al cerrarlo
    $('#soatManagementModal').on('hidden.bs.modal', function () {
        if (soatHistoryTable) {
            soatHistoryTable.destroy();
            soatHistoryTable = null;
        }
        // Limpiar event listeners de dropdowns
        $(document).off('click.soat-dropdowns');
        $('.soat-management-modal .dropdown-toggle').off('click');
        $('.soat-management-modal .dropdown-menu').removeClass('show').hide();
    });
    </script>
@endsection