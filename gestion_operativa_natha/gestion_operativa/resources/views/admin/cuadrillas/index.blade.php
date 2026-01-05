@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .modal-content { background: #fff !important; }
        .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }
        
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
        
        /* Empleados column styling */
        .empleados-count-cell {
            min-width: 80px;
            width: 100px;
        }
        
        /* Vehiculos column styling */
        .vehiculos-count-cell {
            min-width: 80px;
            width: 100px;
        }
        
        /* Badge styling for empleados count */
        .badge-success {
            background-color: #1abc9c !important;
            color: white !important;
        }
        .badge-info {
            background-color: #3498db !important;
            color: white !important;
        }
        .badge-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }

        /* Modal responsive styling */
        .modal-xl {
            max-width: 1200px;
        }
        
        .modal-xxl {
            max-width: 1400px;
        }
        
        .modal-full {
            max-width: 95%;
            margin: 1rem auto;
        }

        /* Responsive table para modal de empleados */
        .empleados-history-table {
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .empleados-history-table {
                font-size: 0.8rem;
            }
            
            .empleados-history-table th,
            .empleados-history-table td {
                padding: 0.5rem 0.25rem !important;
                white-space: nowrap;
            }
            
            .modal-full {
                max-width: 98%;
                margin: 0.5rem auto;
            }
            
            .modal-body {
                padding: 1rem 0.5rem !important;
            }

            .row.mb-3 {
                margin-bottom: 1rem !important;
            }

            .col-lg-9, .col-md-8 {
                margin-bottom: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .empleados-history-table {
                font-size: 0.75rem;
            }
            
            .empleados-history-table .dropdown-toggle {
                padding: 0.25rem;
            }
            
            .badge {
                font-size: 0.7em;
            }

            .btn {
                font-size: 0.8rem;
                padding: 0.375rem 0.5rem;
            }
        }
        
        /* Ajustes para columnas específicas en móvil */
        @media (max-width: 992px) {
            .empleados-history-table th:nth-child(2),
            .empleados-history-table td:nth-child(2) {
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .empleados-history-table th:nth-child(4),
            .empleados-history-table td:nth-child(4) {
                max-width: 120px;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .empleados-history-table th:nth-child(5),
            .empleados-history-table td:nth-child(5) {
                max-width: 120px;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de Cuadrillas</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cuadrillaModal" id="btnNuevaCuadrilla">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nueva Cuadrilla
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="cuadrillasTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Empleados</th>
                                <th>Vehículos</th>
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

<!-- Modal para crear/editar cuadrilla -->
<div class="modal fade" id="cuadrillaModal" tabindex="-1" aria-labelledby="cuadrillaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="cuadrillaForm" action="" method="POST">
                @csrf
                <input type="hidden" id="cuadrilla_id" name="cuadrilla_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="cuadrillaModalLabel">Crear/Editar Cuadrilla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nombre" class="form-label">Nombre <small class="text-muted">(Se autogenera si se deja vacío)</small></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" placeholder="Ingrese el nombre de la cuadrilla o déjelo vacío para autogenerar">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
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

<!-- Modal para gestionar empleados de cuadrilla -->
<div class="modal fade" id="empleadosCuadrillaModal" tabindex="-1" aria-labelledby="empleadosCuadrillaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl" style="max-width: 90%; height: 85vh;">
        <div class="modal-content" style="height: 100%;">
            <div class="modal-header">
                <h5 class="modal-title" id="empleadosCuadrillaModalLabel">Gestionar Empleados - <span id="cuadrillaNombre"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="height: calc(85vh - 120px); overflow-y: auto;">
                <div class="row mb-3">
                    <div class="col-lg-9 col-md-8 col-sm-12">
                        <label for="empleadoSelect" class="form-label">Asignar Empleado</label>
                        <select class="form-control" id="empleadoSelect" name="empleado_id" style="width: 100%;">
                            <option value="">Seleccione un empleado</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="asignarEmpleado()">
                            <i class="fas fa-plus"></i> Asignar
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive" style="height: calc(100% - 100px);">
                    <table id="empleadosCuadrillaTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Empleado</th>
                                <th>DNI</th>
                                <th>Cargo</th>
                                <th>Área</th>
                                <th>Fecha Asignación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para gestionar vehículos de cuadrilla -->
<div class="modal fade" id="vehiculosCuadrillaModal" tabindex="-1" aria-labelledby="vehiculosCuadrillaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl" style="max-width: 90%; height: 85vh;">
        <div class="modal-content" style="height: 100%;">
            <div class="modal-header">
                <h5 class="modal-title" id="vehiculosCuadrillaModalLabel">Gestionar Vehículos - <span id="cuadrillaNombreVehiculos"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="height: calc(85vh - 120px); overflow-y: auto;">
                <div class="row mb-3">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <label for="vehiculoSelect" class="form-label">Seleccionar Vehículo <span class="text-danger">*</span></label>
                        <select class="form-control" id="vehiculoSelect" name="vehiculo_id" style="width: 100%;">
                            <option value="">Seleccione un vehículo</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <label for="choferSelect" class="form-label">Chofer (Opcional)</label>
                        <select class="form-control" id="choferSelect" name="empleado_id" style="width: 100%;">
                            <option value="">Sin chofer asignado</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-12 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="asignarVehiculo()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Asignar
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive" style="height: calc(100% - 100px);">
                    <table id="vehiculosCuadrillaTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vehículo</th>
                                <th>Placa</th>
                                <th>Tipo</th>
                                <th>Marca/Modelo</th>
                                <th>Chofer</th>
                                <th>Fecha Asignación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    // Validación en tiempo real
    function setupFormValidation() {
        // Limpiar validaciones previas
        $('#nombre, #fecha_inicio, #fecha_fin, #estado').off('blur focus change');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        // Validación nombre (opcional, máximo 100)
        $('#nombre').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length > 0) {
                validateField($(this), 'El nombre no puede exceder 100 caracteres', function(value) {
                    return value.length <= 100;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Validación fecha_fin >= fecha_inicio
        $('#fecha_fin').on('blur change', function() {
            var fechaInicio = $('#fecha_inicio').val();
            var fechaFin = $(this).val();
            if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
                validateField($(this), 'La fecha de fin debe ser posterior o igual a la fecha de inicio', function() {
                    return false;
                });
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        $('#fecha_inicio').on('blur change', function() {
            var fechaInicio = $(this).val();
            var fechaFin = $('#fecha_fin').val();
            if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
                validateField($('#fecha_fin'), 'La fecha de fin debe ser posterior o igual a la fecha de inicio', function() {
                    return false;
                });
            } else {
                $('#fecha_fin').removeClass('is-invalid');
                $('#fecha_fin').next('.invalid-feedback').remove();
            }
        });

        // Limpiar error al enfocar
        $('#nombre, #fecha_inicio, #fecha_fin, #estado').on('focus', function() {
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

        // Validar nombre si se proporciona
        var nombre = $('#nombre').val().trim();
        if (nombre.length > 0 && nombre.length > 100) {
            if (!validateField($('#nombre'), 'El nombre no puede exceder 100 caracteres', function(value) {
                return value.length <= 100;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#nombre');
            }
        }

        // Validar fechas
        var fechaInicio = $('#fecha_inicio').val();
        var fechaFin = $('#fecha_fin').val();
        if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
            if (!validateField($('#fecha_fin'), 'La fecha de fin debe ser posterior o igual a la fecha de inicio', function() {
                return false;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#fecha_fin');
            }
        }

        if (firstErrorField) {
            firstErrorField.focus();
        }
        return isValid;
    }

    $(document).ready(function() {
        var table = $('#cuadrillasTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('cuadrillas.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nombre', name: 'nombre' },
                { data: 'fecha_inicio_formatted', name: 'fecha_inicio' },
                { data: 'fecha_fin_formatted', name: 'fecha_fin' },
                { data: 'empleados_count_formatted', name: 'empleados_count', orderable: true, searchable: false },
                { data: 'vehiculos_count_formatted', name: 'vehiculos_count', orderable: true, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
            columnDefs: [
                { targets: [4], className: "text-center empleados-count-cell" },
                { targets: [5], className: "text-center vehiculos-count-cell" },
                { targets: [6, 7], className: "text-center" }
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

        setupFormValidation();

        $('#btnNuevaCuadrilla').click(function() {
            $('#cuadrillaForm')[0].reset();
            $('#cuadrilla_id').val('');
            $('#cuadrillaModalLabel').text('Nueva Cuadrilla');
            setupFormValidation();
        });

        $('#cuadrillaForm').on('submit', function(e) {
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
            var id = $('#cuadrilla_id').val();
            var url = id ? '/cuadrillas/' + id : '/cuadrillas';
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
                    Swal.fire('Guardado!', 'El registro ha sido guardado correctamente.', 'success');
                    $('#cuadrillaModal').modal('hide');
                    table.ajax.reload();
                    $('#cuadrillaForm')[0].reset();
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
                        Swal.fire('Error!', 'Hubo un problema al guardar el registro.', 'error');
                    }
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        $('#cuadrillaModal').on('hidden.bs.modal', function () {
            $('#cuadrillaForm')[0].reset();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });

    window.editCuadrilla = function(id) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        $.get('/cuadrillas/' + id, function(data) {
            Swal.close();
            console.log('Datos recibidos:', data); // Debug para ver los datos
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#cuadrilla_id').val(data.id);
            $('#nombre').val(data.nombre || '');
            
            // Convertir fechas ISO a formato YYYY-MM-DD para input[type="date"]
            if (data.fecha_inicio) {
                var fechaInicio = data.fecha_inicio.split('T')[0]; // Extraer solo YYYY-MM-DD
                $('#fecha_inicio').val(fechaInicio);
            } else {
                $('#fecha_inicio').val('');
            }
            
            if (data.fecha_fin) {
                var fechaFin = data.fecha_fin.split('T')[0]; // Extraer solo YYYY-MM-DD
                $('#fecha_fin').val(fechaFin);
            } else {
                $('#fecha_fin').val('');
            }
            
            if (data.estado == 1) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }
            $('#cuadrillaModalLabel').text('Editar Cuadrilla');
            $('#cuadrillaModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.close();
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
        });
    };

    window.deleteCuadrilla = function(id) {
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
                    url: '/cuadrillas/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', 'Cuadrilla eliminada correctamente.', 'success');
                        $('#cuadrillasTable').DataTable().ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar la cuadrilla', 'error');
                    }
                });
            }
        });
    };

    // Variables globales para gestión de empleados
    var currentCuadrillaId = null;
    var empleadosCuadrillaTable = null;
    var vehiculosCuadrillaTable = null;

    // Función para gestionar empleados de una cuadrilla
    window.gestionarEmpleados = function(cuadrillaId, cuadrillaNombre) {
        currentCuadrillaId = cuadrillaId;
        $('#cuadrillaNombre').text(cuadrillaNombre);
        
        // Inicializar Select2 para empleados disponibles
        $('#empleadoSelect').select2({
            dropdownParent: $('#empleadosCuadrillaModal'),
            placeholder: 'Buscar empleado...',
            allowClear: true,
            ajax: {
                url: '/cuadrillas/' + cuadrillaId + '/empleados/disponibles',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
           
            templateSelection: function(empleado) {
                return empleado.text;
            }
        });

        // Inicializar DataTable de empleados asignados
        if (empleadosCuadrillaTable) {
            empleadosCuadrillaTable.destroy();
        }
        
        empleadosCuadrillaTable = $('#empleadosCuadrillaTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '/cuadrillas/' + cuadrillaId + '/empleados/data',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'empleado_nombre', name: 'empleado_nombre' },
                { data: 'empleado_dni', name: 'empleado_dni' },
                { data: 'empleado_cargo', name: 'empleado_cargo' },
                { data: 'empleado_area', name: 'empleado_area' },
                { data: 'fecha_asignacion_formatted', name: 'fecha_asignacion' },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[5, "desc"]], // Ordenar por fecha de asignación descendente
            columnDefs: [
                { targets: [6, 7], className: "text-center" }
            ],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No hay empleados asignados",
                emptyTable: "No hay empleados asignados a esta cuadrilla",
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

        $('#empleadosCuadrillaModal').modal('show');
    };

    // Función para asignar empleado a cuadrilla
    window.asignarEmpleado = function() {
        var empleadoId = $('#empleadoSelect').val();
        
        if (!empleadoId) {
            Swal.fire('Error', 'Debe seleccionar un empleado', 'warning');
            return;
        }

        Swal.fire({
            title: 'Asignando empleado...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '/cuadrillas/empleados/asignar',
            type: 'POST',
            data: {
                cuadrilla_id: currentCuadrillaId,
                empleado_id: empleadoId,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                Swal.fire('Asignado!', 'Empleado asignado correctamente a la cuadrilla.', 'success');
                empleadosCuadrillaTable.ajax.reload();
                $('#empleadoSelect').val('').trigger('change');
                // Recargar también la tabla principal para actualizar el contador
                $('#cuadrillasTable').DataTable().ajax.reload(null, false);
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON.message) {
                    Swal.fire('Error', xhr.responseJSON.message, 'warning');
                } else {
                    Swal.fire('Error', 'No se pudo asignar el empleado', 'error');
                }
            }
        });
    };

    // Función para cambiar estado de asignación
    window.toggleEstadoAsignacion = function(asignacionId, nuevoEstado) {
        // Convertir string a boolean
        var estadoBoolean = nuevoEstado === 'true' || nuevoEstado === true;
        var accion = estadoBoolean ? 'activar' : 'desactivar';
        
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Desea ' + accion + ' esta asignación?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, ' + accion,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/cuadrillas/empleados/' + asignacionId + '/toggle',
                    type: 'PUT',
                    data: {
                        estado: estadoBoolean,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Actualizado!', res.message || 'Estado de asignación actualizado correctamente.', 'success');
                            empleadosCuadrillaTable.ajax.reload();
                            $('#cuadrillasTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'No se pudo actualizar el estado', 'error');
                        }
                    },
                    error: function(xhr) {
                        var message = 'No se pudo actualizar el estado';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', message, 'error');
                    }
                });
            }
        });
    };

    // Función para remover empleado de cuadrilla
    window.removeEmpleadoFromCuadrilla = function(asignacionId) {
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Esta acción removerá permanentemente al empleado de la cuadrilla',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/cuadrillas/empleados/' + asignacionId,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Removido!', 'Empleado removido de la cuadrilla correctamente.', 'success');
                        empleadosCuadrillaTable.ajax.reload();
                        $('#cuadrillasTable').DataTable().ajax.reload(null, false);
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo remover el empleado', 'error');
                    }
                });
            }
        });
    };

    // Limpiar el modal al cerrarlo
    $('#empleadosCuadrillaModal').on('hidden.bs.modal', function () {
        $('#empleadoSelect').val('').trigger('change');
        if (empleadosCuadrillaTable) {
            empleadosCuadrillaTable.destroy();
            empleadosCuadrillaTable = null;
        }
    });

    // Variables globales para gestión de vehículos
    var currentCuadrillaIdVehiculos = null;

    // Función para gestionar vehículos de una cuadrilla
    window.gestionarVehiculos = function(cuadrillaId, cuadrillaNombre) {
        currentCuadrillaIdVehiculos = cuadrillaId;
        $('#cuadrillaNombreVehiculos').text(cuadrillaNombre);
        
        // Inicializar Select2 para vehículos disponibles
        $('#vehiculoSelect').select2({
            dropdownParent: $('#vehiculosCuadrillaModal'),
            placeholder: 'Buscar vehículo...',
            allowClear: true,
            ajax: {
                url: '/cuadrillas/' + cuadrillaId + '/vehiculos/disponibles',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            templateSelection: function(vehiculo) {
                return vehiculo.text;
            }
        });

        // Inicializar Select2 para choferes (empleados de la cuadrilla)
        $('#choferSelect').select2({
            dropdownParent: $('#vehiculosCuadrillaModal'),
            placeholder: 'Buscar chofer...',
            allowClear: true,
            ajax: {
                url: '/cuadrillas/' + cuadrillaId + '/empleados-chofer',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            templateSelection: function(empleado) {
                return empleado.text;
            }
        });

        // Inicializar DataTable de vehículos asignados
        if (vehiculosCuadrillaTable) {
            vehiculosCuadrillaTable.destroy();
        }
        
        vehiculosCuadrillaTable = $('#vehiculosCuadrillaTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '/cuadrillas/' + cuadrillaId + '/vehiculos/data',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'vehiculo_nombre', name: 'vehiculo_nombre' },
                { data: 'vehiculo_placa', name: 'vehiculo_placa' },
                { data: 'vehiculo_tipo', name: 'vehiculo_tipo' },
                { data: 'vehiculo_marca_modelo', name: 'vehiculo_marca_modelo' },
                { data: 'chofer_nombre', name: 'chofer_nombre', orderable: false },
                { data: 'fecha_asignacion_formatted', name: 'fecha_asignacion' },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            order: [[6, "desc"]], // Ordenar por fecha de asignación descendente
            columnDefs: [
                { targets: [7, 8], className: "text-center" }
            ],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No hay vehículos asignados",
                emptyTable: "No hay vehículos asignados a esta cuadrilla",
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

        $('#vehiculosCuadrillaModal').modal('show');
    };

    // Función para asignar vehículo a cuadrilla
    window.asignarVehiculo = function() {
        var vehiculoId = $('#vehiculoSelect').val();
        var choferId = $('#choferSelect').val();
        
        if (!vehiculoId) {
            Swal.fire('Error', 'Debe seleccionar un vehículo', 'warning');
            return;
        }

        Swal.fire({
            title: 'Asignando vehículo...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '/cuadrillas/vehiculos/asignar',
            type: 'POST',
            data: {
                cuadrilla_id: currentCuadrillaIdVehiculos,
                vehiculo_id: vehiculoId,
                empleado_id: choferId, // Incluir el chofer
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                Swal.fire('Asignado!', 'Vehículo asignado correctamente a la cuadrilla.', 'success');
                vehiculosCuadrillaTable.ajax.reload();
                $('#vehiculoSelect').val('').trigger('change');
                $('#choferSelect').val('').trigger('change');
                // Recargar también la tabla principal para actualizar el contador
                $('#cuadrillasTable').DataTable().ajax.reload(null, false);
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON.message) {
                    Swal.fire('Error', xhr.responseJSON.message, 'warning');
                } else {
                    Swal.fire('Error', 'No se pudo asignar el vehículo', 'error');
                }
            }
        });
    };

    // Función para cambiar estado de asignación de vehículo
    window.toggleEstadoAsignacionVehiculo = function(asignacionId, nuevoEstado) {
        // Convertir string a boolean
        var estadoBoolean = nuevoEstado === 'true' || nuevoEstado === true;
        var accion = estadoBoolean ? 'activar' : 'desactivar';
        
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Desea ' + accion + ' esta asignación?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, ' + accion,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/cuadrillas/vehiculos/' + asignacionId + '/toggle',
                    type: 'PUT',
                    data: {
                        estado: estadoBoolean,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Actualizado!', res.message || 'Estado de asignación actualizado correctamente.', 'success');
                            vehiculosCuadrillaTable.ajax.reload();
                            $('#cuadrillasTable').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Error', res.message || 'No se pudo actualizar el estado', 'error');
                        }
                    },
                    error: function(xhr) {
                        var message = 'No se pudo actualizar el estado';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', message, 'error');
                    }
                });
            }
        });
    };

    // Función para remover vehículo de cuadrilla
    window.removeVehiculoFromCuadrilla = function(asignacionId) {
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Esta acción removerá permanentemente el vehículo de la cuadrilla',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/cuadrillas/vehiculos/' + asignacionId,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Removido!', 'Vehículo removido de la cuadrilla correctamente.', 'success');
                        vehiculosCuadrillaTable.ajax.reload();
                        $('#cuadrillasTable').DataTable().ajax.reload(null, false);
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo remover el vehículo', 'error');
                    }
                });
            }
        });
    };

    // Limpiar el modal de vehículos al cerrarlo
    $('#vehiculosCuadrillaModal').on('hidden.bs.modal', function () {
        $('#vehiculoSelect').val('').trigger('change');
        $('#choferSelect').val('').trigger('change');
        if (vehiculosCuadrillaTable) {
            vehiculosCuadrillaTable.destroy();
            vehiculosCuadrillaTable = null;
        }
    });
    </script>
@endsection