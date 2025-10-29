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
        <h4 class="">Gestión de Medidores</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#medidorModal" id="btnNuevoMedidor">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Nuevo Medidor</span>
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="medidorTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Serie</th>
                                <th>Modelo</th>
                                <th>Marca</th>
                                <th>Material</th>
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

<!-- Modal para crear/editar medidor -->
<div class="modal fade" id="medidorModal" tabindex="-1" aria-labelledby="medidorModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="medidorForm" action="" method="POST">
                @csrf
                <input type="hidden" id="medidor_id" name="medidor_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="medidorModalLabel">Crear/Editar Medidor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="serie" class="form-label">Serie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="serie" name="serie" maxlength="50" placeholder="Número de serie" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modelo" name="modelo" maxlength="50" placeholder="Modelo del medidor" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="marca" name="marca" maxlength="50" placeholder="Marca">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="año_fabricacion" class="form-label">Año Fabricación</label>
                            <input type="number" class="form-control" id="año_fabricacion" name="año_fabricacion" min="1000" max="9999" maxlength="4" placeholder="YYYY" inputmode="numeric">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacidad_amperios" class="form-label">Capacidad Amperios</label>
                            <input type="text" class="form-control" id="capacidad_amperios" name="capacidad_amperios" maxlength="10" placeholder="Capacidad en amperios">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="numero_hilos" class="form-label">Número de Hilos</label>
                            <input type="number" class="form-control" id="numero_hilos" name="numero_hilos" placeholder="Número de hilos">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="material_id" class="form-label">Material</label>
                            <select class="form-select" id="material_id" name="material_id">
                                <option value="">Seleccione un material</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fm" class="form-label">FM</label>
                            <input type="text" class="form-control" id="fm" name="fm" maxlength="50" placeholder="FM">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3 d-flex align-items-center">
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
    var medidorTable;

    function setupFormValidation() {
        $('#serie, #modelo').off('blur focus');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        $('#serie').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length === 0) {
                validateField($(this), 'La serie es obligatoria', function(value) {
                    return value.length > 0;
                });
            } else if (value.length > 50) {
                validateField($(this), 'La serie no puede exceder 50 caracteres', function(value) {
                    return value.length <= 50;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        $('#modelo').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length === 0) {
                validateField($(this), 'El modelo es obligatorio', function(value) {
                    return value.length > 0;
                });
            } else if (value.length > 50) {
                validateField($(this), 'El modelo no puede exceder 50 caracteres', function(value) {
                    return value.length <= 50;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Validación para año fabricación - solo 4 dígitos
        $('#año_fabricacion').off('blur change keyup').on('keyup', function() {
            var value = $(this).val().trim();
            // Solo permitir dígitos
            $(this).val(value.replace(/[^\d]/g, ''));
            // Limitar a 4 dígitos
            if ($(this).val().length > 4) {
                $(this).val($(this).val().substring(0, 4));
            }
        }).on('blur', function() {
            var value = $(this).val().trim();
            if (value.length > 0 && value.length !== 4) {
                validateField($(this), 'El año debe tener exactamente 4 dígitos', function(val) {
                    return val.length === 4;
                });
            } else if (value.length === 4) {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        $('#serie, #modelo').on('focus', function() {
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
        var serie = $('#serie').val().trim();
        var modelo = $('#modelo').val().trim();
        var isValid = true;

        if (serie.length === 0) {
            validateField($('#serie'), 'La serie es obligatoria', function(value) {
                return value.length > 0;
            });
            isValid = false;
        }

        if (modelo.length === 0) {
            validateField($('#modelo'), 'El modelo es obligatorio', function(value) {
                return value.length > 0;
            });
            isValid = false;
        }

        return isValid;
    }

    function loadMedidoresTable() {
        if ($.fn.DataTable.isDataTable('#medidorTable')) {
            medidorTable.destroy();
        }

        medidorTable = $('#medidorTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('medidor.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'serie', name: 'serie', orderable: true, searchable: true },
                { data: 'modelo', name: 'modelo', orderable: true, searchable: true },
                { data: 'marca', name: 'marca', orderable: false, searchable: false },
                { data: 'material_nombre', name: 'material_nombre', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
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
    }

    function loadMateriales() {
        $.get('{{ route('medidor.materiales') }}', function(data) {
            var select = $('#material_id');
            select.empty().append('<option value="">Seleccione un material</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });
    }

    $(document).ready(function() {
        setupFormValidation();
        loadMedidoresTable();
        loadMateriales();

        $('#btnNuevoMedidor').click(function() {
            $('#medidorForm')[0].reset();
            $('#medidor_id').val('');
            $('#estado').prop('checked', true);
            $('#medidorModalLabel').text('Crear Nuevo Medidor');
            setupFormValidation();
        });

        $('#medidorForm').on('submit', function(e) {
            var estadoChecked = $('#estado').is(':checked');

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

            var id = $('#medidor_id').val();
            var url = id ? '/medidor/' + id : '/medidor';
            var method = id ? 'PUT' : 'POST';

            var formData = {
                serie: $('#serie').val(),
                modelo: $('#modelo').val(),
                marca: $('#marca').val(),
                año_fabricacion: $('#año_fabricacion').val(),
                capacidad_amperios: $('#capacidad_amperios').val(),
                numero_hilos: $('#numero_hilos').val(),
                material_id: $('#material_id').val() || null,
                fm: $('#fm').val(),
                estado: estadoChecked ? 1 : 0,
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
                data: formData,
                success: function(res) {
                    Swal.fire('Guardado!', 'El registro ha sido guardado correctamente.', 'success');
                    $('#medidorModal').modal('hide');
                    loadMedidoresTable();
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

        $('#medidorModal').on('hidden.bs.modal', function () {
            $('#medidorForm')[0].reset();
            $('#estado').prop('checked', true);
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });

    window.editMedidor = function(id) {
        $.get('/medidor/' + id, function(data) {
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();

            $('#medidor_id').val(data.id);
            $('#serie').val(data.serie || '');
            $('#modelo').val(data.modelo || '');
            $('#marca').val(data.marca || '');
            $('#año_fabricacion').val(data.año_fabricacion || '');
            $('#capacidad_amperios').val(data.capacidad_amperios || '');
            $('#numero_hilos').val(data.numero_hilos || '');
            $('#material_id').val(data.material_id || '');
            $('#fm').val(data.fm || '');

            if (data.estado == 1 || data.estado === true) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }

            $('#medidorModalLabel').text('Editar Medidor');
            $('#medidorModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
        });
    };

    window.deleteMedidor = function(id) {
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
                    url: '/medidor/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', 'Medidor eliminado correctamente.', 'success');
                        loadMedidoresTable();
                    },
                    error: function(xhr) {
                        var message = 'No se pudo eliminar el medidor';
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
