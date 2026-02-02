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
        
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de Proveedores</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#proveedorModal" id="btnNuevoProveedor">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo Proveedor
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="proveedoresTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Razón Social</th>
                                <th>RUC</th>
                                <th>Contacto</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Ubigeo</th>
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

<!-- Modal para crear/editar proveedor -->
<div class="modal fade" id="proveedorModal" tabindex="-1" aria-labelledby="proveedorModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="proveedorForm" action="" method="POST">
                @csrf
                <input type="hidden" id="proveedor_id" name="proveedor_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="proveedorModalLabel">Crear/Editar Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="razon_social" class="form-label">Razón Social</label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social" maxlength="100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ruc" class="form-label">RUC</label>
                            <input type="text" class="form-control" id="ruc" name="ruc" maxlength="11" required pattern="\d{11}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contacto" class="form-label">Contacto</label>
                            <input type="text" class="form-control" id="contacto" name="contacto" maxlength="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" maxlength="100">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" maxlength="15">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ubigeo_id" class="form-label">Ubigeo</label>
                            <select class="form-control select2" id="ubigeo_id" name="ubigeo_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" maxlength="200">
                        </div>
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
    // Carga dinámica de ubigeos
    function loadUbigeos(selectedId = null) {
        return $.get('{{ route('ubigeo.select') }}')
            .done(function(data) {
                var select = $('#ubigeo_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${selectedId == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#proveedorModal'),
                    placeholder: 'Seleccione un ubigeo',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar ubigeos');
            });
    }

    // Validación en tiempo real para proveedores
    function setupFormValidation() {
        $('#razon_social, #ruc, #contacto, #email, #telefono, #direccion, #ubigeo_id').off('blur focus change');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        $('#razon_social').on('blur', function() {
            validateField($(this), 'La razón social es obligatoria y no puede exceder 100 caracteres', function(value) {
                var val = (value || '').trim();
                return val.length > 0 && val.length <= 100;
            });
        });
        $('#ruc').on('blur', function() {
            validateField($(this), 'El RUC es obligatorio y debe tener 11 dígitos', function(value) {
                return /^\d{11}$/.test((value || '').trim());
            });
        });
        $('#contacto').on('blur', function() {
            var value = ($(this).val() || '').trim();
            if (value.length > 0) {
                validateField($(this), 'El contacto no puede exceder 100 caracteres', function(value) {
                    return (value || '').length <= 100;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#email').on('blur', function() {
            var value = ($(this).val() || '').trim();
            if (value.length > 0) {
                validateField($(this), 'Ingrese un email válido y menor a 100 caracteres', function(value) {
                    return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value || '') && (value || '').length <= 100;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#telefono').on('blur', function() {
            var value = ($(this).val() || '').trim();
            if (value.length > 0) {
                validateField($(this), 'El teléfono no puede exceder 15 caracteres', function(value) {
                    return (value || '').length <= 15;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#direccion').on('blur', function() {
            var value = ($(this).val() || '').trim();
            if (value.length > 0) {
                validateField($(this), 'La dirección no puede exceder 200 caracteres', function(value) {
                    return (value || '').length <= 200;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#ubigeo_id').on('blur change', function() {
            var value = ($(this).val() || '').trim();
            if (value.length > 0) {
                validateField($(this), 'Seleccione un ubigeo válido', function(value) {
                    var $opt = $('#ubigeo_id option:selected');
                    return (value || '').trim() !== '' && !$opt.is(':disabled');
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Limpiar error al enfocar
        $('#razon_social, #ruc, #contacto, #email, #telefono, #direccion, #ubigeo_id').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });
    }

    function validateField(field, message, validationFunction) {
        var value = field.val() || ''; // Asegurar que nunca sea null
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
        if (!validateField($('#razon_social'), 'La razón social es obligatoria y no puede exceder 100 caracteres', function(value) {
            var val = (value || '').trim();
            return val.length > 0 && val.length <= 100;
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#razon_social');
        }
        if (!validateField($('#ruc'), 'El RUC es obligatorio y debe tener 11 dígitos', function(value) {
            return /^\d{11}$/.test((value || '').trim());
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#ruc');
        }
        if (firstErrorField) {
            firstErrorField.focus();
        }
        return isValid;
    }

    $(document).ready(function() {
        // Inicializar Select2 con placeholder y allowClear igual que empleados
        $('#ubigeo_id').select2({
            dropdownParent: $('#proveedorModal'),
            placeholder: 'Seleccione un ubigeo',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });

        // Cargar todos los selects dinámicamente al inicializar
        loadUbigeos();
        setupFormValidation();

        var table = $('#proveedoresTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('proveedores.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'razon_social', name: 'razon_social' },
                { data: 'ruc', name: 'ruc' },
                { data: 'contacto', name: 'contacto' },
                { data: 'email', name: 'email' },
                { data: 'telefono', name: 'telefono' },
                { data: 'ubigeo', name: 'ubigeo' },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
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

        // Si el usuario cambia el select y la opción es inactiva, eliminarla y mostrar placeholder
        $('#ubigeo_id').on('change', function() {
            var $opt = $(this).find('option:selected');
            if ($opt.is(':disabled')) {
                $(this).val('').trigger('change.select2');
                $opt.remove();
            }
        });

        $('#btnNuevoProveedor').click(function() {
            $('#proveedorForm')[0].reset();
            $('#proveedor_id').val('');
            $('#proveedorModalLabel').text('Nuevo Proveedor');
            loadUbigeos();
            // Limpiar selects y mostrar placeholder
            setTimeout(function() {
                $('#ubigeo_id').val('').trigger('change');
            }, 200);
            setupFormValidation();
        });

        $('#proveedorForm').on('submit', function(e) {
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
            var id = $('#proveedor_id').val();
            var url = id ? '/proveedores/' + id : '/proveedores';
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
                    $('#proveedorModal').modal('hide');
                    table.ajax.reload();
                    $('#proveedorForm')[0].reset();
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

        $('#proveedorModal').on('hidden.bs.modal', function () {
            $('#proveedorForm')[0].reset();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });

    window.editProveedor = function(id) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        $.get('/proveedores/' + id, function(data) {
            Swal.close();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#proveedor_id').val(data.id);
            $('#razon_social').val(data.razon_social);
            $('#ruc').val(data.ruc);
            $('#contacto').val(data.contacto);
            $('#email').val(data.email);
            $('#telefono').val(data.telefono);
            $('#direccion').val(data.direccion);
            
            // Cargar ubigeos y manejar selección
            loadUbigeos(data.ubigeo_id).done(function() {
                var ubigeoVal = data.ubigeo_id ? data.ubigeo_id.toString() : '';
                if ($('#ubigeo_id option[value="'+ubigeoVal+'"], #ubigeo_id option[value='+ubigeoVal+']').length > 0) {
                    $('#ubigeo_id').val(ubigeoVal).trigger('change.select2');
                } else if (ubigeoVal) {
                    var nombreUbigeo = data.ubigeo && data.ubigeo.nombre ? data.ubigeo.nombre + ' (inactivo)' : 'Inactivo';
                    $('#ubigeo_id').append('<option value="'+ubigeoVal+'" selected>'+nombreUbigeo+'</option>');
                    $('#ubigeo_id').val(ubigeoVal).trigger('change.select2');
                    setTimeout(function(){
                        $('#ubigeo_id option[value="'+ubigeoVal+'"]').prop('disabled', true);
                    }, 0);
                } else {
                    $('#ubigeo_id').val('').trigger('change.select2');
                }
            });
            
            if (data.estado == 1) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }
            $('#proveedorModalLabel').text('Editar Proveedor');
            $('#proveedorModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.close();
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
        });
    };

    window.deleteProveedor = function(id) {
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
                    url: '/proveedores/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', 'Proveedor eliminado correctamente.', 'success');
                        $('#proveedoresTable').DataTable().ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar el proveedor', 'error');
                    }
                });
            }
        });
    };
    </script>
@endsection