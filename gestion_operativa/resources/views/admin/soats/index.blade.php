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
        .badge-warning {
            background-color: #f39c12 !important;
            color: white !important;
        }
        .badge-secondary {
            background-color: #6c757d !important;
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
        
        .select2-container {
            width: 100% !important;
        }
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
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de SOATs</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#soatModal" id="btnNuevoSoat">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo SOAT
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="soatsTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vehículo</th>
                                <th>Proveedor</th>
                                <th>Número SOAT</th>
                                <th>Fecha Emisión</th>
                                <th>Fecha Vencimiento</th>
                                <th>Vigencia</th>
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

<!-- Modal para crear/editar SOAT -->
<div class="modal fade" id="soatModal" tabindex="-1" aria-labelledby="soatModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="soatForm" action="" method="POST">
                @csrf
                <input type="hidden" id="soat_id" name="soat_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="soatModalLabel">Crear/Editar SOAT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_id" class="form-label">Vehículo <span class="text-danger">*</span></label>
                            <select class="form-control" id="vehiculo_id" name="vehiculo_id" required>
                                <option value="">Seleccione un vehículo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control" id="proveedor_id" name="proveedor_id" required>
                                <option value="">Seleccione un proveedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="numero_soat" class="form-label">Número SOAT <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="numero_soat" name="numero_soat" maxlength="200" placeholder="Ingrese el número del SOAT" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_emision" class="form-label">Fecha de Emisión <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
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

@endsection

@section('scripts')
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    // Obtener parámetro vehiculo_id de la URL si existe
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Inicializar Select2 para vehículos
    function initializeVehiculosSelect2() {
        $('#vehiculo_id').select2({
            dropdownParent: $('#soatModal'),
            placeholder: 'Seleccione un vehículo',
            allowClear: true,
            ajax: {
                url: '/api/select/vehiculos',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                },
                cache: true
            }
        });
    }

    // Inicializar Select2 para proveedores
    function initializeProveedoresSelect2() {
        $('#proveedor_id').select2({
            dropdownParent: $('#soatModal'),
            placeholder: 'Seleccione un proveedor',
            allowClear: true,
            ajax: {
                url: '/api/select/proveedores',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        })
                    };
                },
                cache: true
            }
        });
    }

    // Validación en tiempo real
    function setupFormValidation() {
        // Limpiar validaciones previas
        $('#vehiculo_id, #proveedor_id, #numero_soat, #fecha_emision, #fecha_vencimiento').off('blur focus change');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        // Validar campos requeridos
        $('#numero_soat').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length === 0) {
                validateField($(this), 'El número de SOAT es obligatorio', function(value) {
                    return value.length > 0;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        $('#fecha_emision, #fecha_vencimiento').on('blur change', function() {
            var value = $(this).val();
            var fieldName = $(this).attr('name');
            var displayName = fieldName === 'fecha_emision' ? 'Fecha de emisión' : 'Fecha de vencimiento';
            
            if (!value) {
                validateField($(this), `${displayName} es obligatoria`, function(value) {
                    return value.length > 0;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
                
                // Validar que fecha vencimiento sea posterior a emisión
                if (fieldName === 'fecha_vencimiento' || (fieldName === 'fecha_emision' && $('#fecha_vencimiento').val())) {
                    var emision = $('#fecha_emision').val();
                    var vencimiento = $('#fecha_vencimiento').val();
                    
                    if (emision && vencimiento && new Date(vencimiento) <= new Date(emision)) {
                        validateField($('#fecha_vencimiento'), 'La fecha de vencimiento debe ser posterior a la fecha de emisión', function() {
                            return false;
                        });
                    }
                }
            }
        });

        // Limpiar error al enfocar
        $('#vehiculo_id, #proveedor_id, #numero_soat, #fecha_emision, #fecha_vencimiento').on('focus', function() {
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

        // Validar vehículo
        var vehiculo = $('#vehiculo_id').val();
        if (!vehiculo) {
            $('#vehiculo_id').next('.select2').find('.select2-selection').addClass('is-invalid');
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#vehiculo_id');
        }

        // Validar proveedor
        var proveedor = $('#proveedor_id').val();
        if (!proveedor) {
            $('#proveedor_id').next('.select2').find('.select2-selection').addClass('is-invalid');
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#proveedor_id');
        }

        // Validar campos requeridos
        var requiredFields = ['numero_soat', 'fecha_emision', 'fecha_vencimiento'];
        requiredFields.forEach(function(fieldName) {
            var field = $('#' + fieldName);
            var value = field.val().trim();
            
            if (value.length === 0) {
                var displayName = field.prev('label').text().replace(' *', '');
                if (!validateField(field, `${displayName} es obligatorio`, function(value) {
                    return value.length > 0;
                })) {
                    isValid = false;
                    if (!firstErrorField) firstErrorField = field;
                }
            }
        });

        // Validar que fecha vencimiento sea posterior a emisión
        var emision = $('#fecha_emision').val();
        var vencimiento = $('#fecha_vencimiento').val();
        
        if (emision && vencimiento && new Date(vencimiento) <= new Date(emision)) {
            if (!validateField($('#fecha_vencimiento'), 'La fecha de vencimiento debe ser posterior a la fecha de emisión', function() {
                return false;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#fecha_vencimiento');
            }
        }

        if (firstErrorField) {
            firstErrorField.focus();
        }
        return isValid;
    }

    $(document).ready(function() {
        var table = $('#soatsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('soats.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'vehiculo_info', name: 'vehiculo_info', orderable: false },
                { data: 'proveedor_nombre', name: 'proveedor.nombre' },
                { data: 'numero_soat', name: 'numero_soat' },
                { data: 'fecha_emision_formatted', name: 'fecha_emision' },
                { data: 'fecha_vencimiento_formatted', name: 'fecha_vencimiento' },
                { data: 'vigencia_badge', name: 'vigencia_badge', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[5, "desc"]],
            columnDefs: [
                { targets: [6, 7, 8], className: "text-center" }
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

        initializeVehiculosSelect2();
        initializeProveedoresSelect2();
        setupFormValidation();

        // Verificar si hay un vehículo preseleccionado en la URL
        var vehiculoIdFromUrl = getUrlParameter('vehiculo_id');

        $('#btnNuevoSoat').click(function() {
            $('#soatForm')[0].reset();
            $('#soat_id').val('');
            $('#soatModalLabel').text('Nuevo SOAT');
            $('#vehiculo_id').val(null).trigger('change');
            $('#proveedor_id').val(null).trigger('change');
            // Asegurar que el botón esté habilitado
            $('#soatForm button[type="submit"]').prop('disabled', false);
            
            // Si hay vehículo en URL, preseleccionarlo
            if (vehiculoIdFromUrl) {
                // Cargar vehículo específico
                $.get('/vehiculos/' + vehiculoIdFromUrl, function(vehiculo) {
                    var vehiculoText = vehiculo.marca + ' ' + vehiculo.nombre + ' - ' + vehiculo.placa;
                    var option = new Option(vehiculoText, vehiculoIdFromUrl, true, true);
                    $('#vehiculo_id').append(option).trigger('change');
                });
            }
            
            setupFormValidation();
        });

        $('#soatForm').on('submit', function(e) {
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
            var id = $('#soat_id').val();
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
                    $('#soatModal').modal('hide');
                    table.ajax.reload();
                    $('#soatForm')[0].reset();
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
                        Swal.fire('Error!', 'Hubo un problema al guardar el SOAT.', 'error');
                    }
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        $('#soatModal').on('hidden.bs.modal', function () {
            $('#soatForm')[0].reset();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#vehiculo_id').val(null).trigger('change');
            $('#proveedor_id').val(null).trigger('change');
            // Asegurar que el botón esté habilitado
            $('#soatForm button[type="submit"]').prop('disabled', false);
        });
    });

    window.editSoat = function(id) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        $.get('/soats/' + id, function(data) {
            Swal.close();
            console.log('Datos del SOAT:', data); // Debug log
            console.log('Proveedor data:', data.proveedor); // Debug específico para proveedor
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#soat_id').val(data.id);
            $('#numero_soat').val(data.numero_soat || '');
            
            // Debug para fechas
            console.log('Fecha emisión recibida:', data.fecha_emision);
            console.log('Fecha vencimiento recibida:', data.fecha_vencimiento);
            
            $('#fecha_emision').val(data.fecha_emision || '');
            $('#fecha_vencimiento').val(data.fecha_vencimiento || '');
            
            // Configurar vehículo
            if (data.vehiculo_id && data.vehiculo) {
                // Limpiar y agregar la opción del vehículo
                $('#vehiculo_id').empty();
                var vehiculoText = data.vehiculo.marca + ' ' + data.vehiculo.nombre + ' - ' + data.vehiculo.placa;
                var option = new Option(vehiculoText, data.vehiculo_id, true, true);
                $('#vehiculo_id').append(option).trigger('change');
            }
            
            // Configurar proveedor
            if (data.proveedor_id && data.proveedor) {
                // Limpiar y agregar la opción del proveedor
                $('#proveedor_id').empty();
                var proveedorText = data.proveedor.razon_social || data.proveedor.nombre;
                if (data.proveedor.ruc) {
                    proveedorText += ' - ' + data.proveedor.ruc;
                }
                var option = new Option(proveedorText, data.proveedor_id, true, true);
                $('#proveedor_id').append(option).trigger('change');
            }
            
            if (data.estado == 1) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }
            $('#soatModalLabel').text('Editar SOAT');
            $('#soatModal').modal('show');
            // Asegurar que el botón esté habilitado
            $('#soatForm button[type="submit"]').prop('disabled', false);
            setupFormValidation();
        }).fail(function() {
            Swal.close();
            Swal.fire('Error!', 'No se pudieron cargar los datos del SOAT.', 'error');
        });
    };

    window.deleteSoat = function(id) {
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
                        $('#soatsTable').DataTable().ajax.reload();
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
    </script>
@endsection