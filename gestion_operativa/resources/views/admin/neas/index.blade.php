@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
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
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de NEAs</h4>
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
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Nro. Documento</th>
                                <th>Tipo Comprobante</th>
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

<!-- Modal para crear/editar NEA -->
<div class="modal fade" id="neaModal" tabindex="-1" aria-labelledby="neaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="neaForm" action="" method="POST">
                @csrf
                <input type="hidden" id="nea_id" name="nea_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="neaModalLabel">Crear/Editar NEA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control" id="proveedor_id" name="proveedor_id" required>
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
                            <input type="text" class="form-control" id="nro_documento" name="nro_documento" maxlength="50" placeholder="Ej: NEA-001" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo_comprobante_id" class="form-label">Tipo Comprobante <span class="text-danger">*</span></label>
                            <select class="form-control" id="tipo_comprobante_id" name="tipo_comprobante_id" required>
                                <option value="">Seleccione un tipo</option>
                                @foreach($tiposComprobantes as $id => $nombre)
                                    <option value="{{ $id }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Ingrese observaciones (opcional)"></textarea>
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
    <script>
    // Validación en tiempo real
    function setupFormValidation() {
        $('#proveedor_id, #fecha, #nro_documento, #tipo_comprobante_id').off('blur focus change');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        // Validación nro_documento (requerido, máximo 50)
        $('#nro_documento').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length === 0) {
                validateField($(this), 'El número de documento es obligatorio', function(value) {
                    return value.length > 0;
                });
            } else if (value.length > 50) {
                validateField($(this), 'El número de documento no puede exceder 50 caracteres', function(value) {
                    return value.length <= 50;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Limpiar error al enfocar
        $('#proveedor_id, #fecha, #nro_documento, #tipo_comprobante_id, #estado').on('focus', function() {
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

        // Validar proveedor
        if ($('#proveedor_id').val() === '') {
            if (!validateField($('#proveedor_id'), 'El proveedor es obligatorio', function(value) {
                return value.length > 0;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#proveedor_id');
            }
        }

        // Validar fecha
        if ($('#fecha').val() === '') {
            if (!validateField($('#fecha'), 'La fecha es obligatoria', function(value) {
                return value.length > 0;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#fecha');
            }
        }

        // Validar nro_documento
        var nro_documento = $('#nro_documento').val().trim();
        if (nro_documento.length === 0) {
            if (!validateField($('#nro_documento'), 'El número de documento es obligatorio', function(value) {
                return value.length > 0;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#nro_documento');
            }
        } else if (nro_documento.length > 50) {
            if (!validateField($('#nro_documento'), 'El número de documento no puede exceder 50 caracteres', function(value) {
                return value.length <= 50;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#nro_documento');
            }
        }

        // Validar tipo comprobante
        if ($('#tipo_comprobante_id').val() === '') {
            if (!validateField($('#tipo_comprobante_id'), 'El tipo de comprobante es obligatorio', function(value) {
                return value.length > 0;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#tipo_comprobante_id');
            }
        }

        if (firstErrorField) {
            firstErrorField.focus();
        }
        return isValid;
    }

    $(document).ready(function() {
        var table = $('#neasTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('neas.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'proveedor_nombre', name: 'proveedor.nombre' },
                { data: 'fecha', name: 'fecha' },
                { data: 'nro_documento', name: 'nro_documento' },
                { data: 'tipo_comprobante_nombre', name: 'tipoComprobante.nombre' },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[2, "desc"]],
            columnDefs: [
                { targets: [5, 6], className: "text-center" }
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

        $('#btnNuevaNea').click(function() {
            $('#neaForm')[0].reset();
            $('#nea_id').val('');
            $('#neaModalLabel').text('Nueva NEA');
            setupFormValidation();
        });

        $('#neaForm').on('submit', function(e) {
            if ($('#estado').is(':checked')) {
                $('#estado').val(1);
            } else {
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
            var id = $('#nea_id').val();
            var url = id ? '/neas/' + id : '/neas';
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
                    Swal.fire('Guardado!', res.message || 'El registro ha sido guardado correctamente.', 'success');
                    $('#neaModal').modal('hide');
                    table.ajax.reload();
                    $('#neaForm')[0].reset();
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

        $('#neaModal').on('hidden.bs.modal', function () {
            $('#neaForm')[0].reset();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });

    window.editNea = function(id) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        $.get('/neas/' + id, function(data) {
            Swal.close();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#nea_id').val(data.id);
            $('#proveedor_id').val(data.proveedor_id);
            $('#fecha').val(data.fecha);
            $('#nro_documento').val(data.nro_documento);
            $('#tipo_comprobante_id').val(data.tipo_comprobante_id);
            $('#observaciones').val(data.observaciones || '');
            
            if (data.estado == 1) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }
            $('#neaModalLabel').text('Editar NEA');
            $('#neaModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.close();
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
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
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                $.ajax({
                    url: '/neas/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', res.message || 'NEA eliminada correctamente.', 'success');
                        $('#neasTable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        var message = 'No se pudo eliminar la NEA';
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
