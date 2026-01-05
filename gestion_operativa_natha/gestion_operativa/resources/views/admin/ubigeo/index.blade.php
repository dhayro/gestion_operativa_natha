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
        <div>
            <h4 class="" id="pageTitle">Gestión de Ubigeos</h4>
            <small id="breadcrumbNav" class="text-muted"></small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ubigeoModal" id="btnNuevoUbigeo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span id="btnNuevoText">Nuevo Ubigeo</span>
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="ubigeoTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Código Postal</th>
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

<!-- Modal para crear/editar ubigeo -->
<div class="modal fade" id="ubigeoModal" tabindex="-1" aria-labelledby="ubigeoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="ubigeoForm" action="" method="POST">
                @csrf
                <input type="hidden" id="ubigeo_id" name="ubigeo_id">
                <input type="hidden" id="padre_id" name="dependencia_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="ubigeoModalLabel">Crear/Editar Ubigeo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" placeholder="Ingrese el nombre del ubigeo" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo_postal" class="form-label">Código Postal</label>
                            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" maxlength="10" placeholder="Ej: 12345">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-center">
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
    var currentPadreId = null;
    var currentPadreName = null;
    var navigationStack = []; // Stack para rastrear la navegación

    // Validación en tiempo real
    function setupFormValidation() {
        $('#nombre').off('blur focus');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        $('#nombre').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length === 0) {
                validateField($(this), 'El nombre es obligatorio', function(value) {
                    return value.length > 0;
                });
            } else if (value.length > 100) {
                validateField($(this), 'El nombre no puede exceder 100 caracteres', function(value) {
                    return value.length <= 100;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        $('#nombre').on('focus', function() {
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
        var nombre = $('#nombre').val().trim();
        if (nombre.length === 0) {
            validateField($('#nombre'), 'El nombre es obligatorio', function(value) {
                return value.length > 0;
            });
            return false;
        }
        if (nombre.length > 100) {
            validateField($('#nombre'), 'El nombre no puede exceder 100 caracteres', function(value) {
                return value.length <= 100;
            });
            return false;
        }
        return true;
    }

    // Cargar tabla de ubigeos
    function loadUbigeoTable(padreId = null) {
        if ($('#ubigeoTable').length) {
            // Si ya existe la tabla, destruirla
            $('#ubigeoTable').DataTable().destroy();
        }

        // Construir URL con parámetro opcional
        var url = '{{ route('ubigeo.data') }}';
        if (padreId) {
            url += '?dependencia_id=' + padreId;
        }

        // Establecer título y botón
        if (padreId) {
            $('#pageTitle').text('Sububigeos de: ' + currentPadreName);
            $('#breadcrumbNav').html('<small><a href="javascript:void(0);" onclick="volverNivel()">← Volver</a></small>');
            $('#btnNuevoText').text('Nuevo Sububigeo');
        } else {
            $('#pageTitle').text('Gestión de Ubigeos');
            $('#breadcrumbNav').html('');
            $('#btnNuevoText').text('Nuevo Ubigeo');
        }

        var table = $('#ubigeoTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: url,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nombre', name: 'nombre', orderable: false, searchable: true },
                { data: 'codigo_postal_display', name: 'codigo_postal', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
            columnDefs: [
                { targets: [3, 4], className: "text-center" }
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

    // Volver al nivel anterior
    window.volverNivel = function() {
        if (navigationStack.length > 0) {
            var nivelAnterior = navigationStack[navigationStack.length - 1];
            currentPadreId = nivelAnterior.id;
            currentPadreName = nivelAnterior.nombre;
            navigationStack.pop(); // Sacar el nivel actual
        } else {
            currentPadreId = null;
            currentPadreName = null;
        }
        
        loadUbigeoTable(currentPadreId);
    };

    $(document).ready(function() {
        setupFormValidation();
        navigationStack = []; // Inicializar stack vacío
        loadUbigeoTable();

        $('#btnNuevoUbigeo').click(function() {
            $('#ubigeoForm')[0].reset();
            $('#ubigeo_id').val('');
            $('#padre_id').val(currentPadreId || '');
            $('#estado').prop('checked', true);
            $('#ubigeoModalLabel').text(currentPadreId ? 'Crear Sububigeo' : 'Crear Nuevo Ubigeo');
            setupFormValidation();
        });

        $('#ubigeoForm').on('submit', function(e) {
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
            
            var id = $('#ubigeo_id').val();
            var url = id ? '/ubigeo/' + id : '/ubigeo';
            var method = id ? 'PUT' : 'POST';
            
            // Preparar datos
            var formData = {
                nombre: $('#nombre').val(),
                codigo_postal: $('#codigo_postal').val(),
                dependencia_id: $('#padre_id').val() || null,
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
                    $('#ubigeoModal').modal('hide');
                    loadUbigeoTable(currentPadreId);
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

        $('#ubigeoModal').on('hidden.bs.modal', function () {
            $('#ubigeoForm')[0].reset();
            $('#estado').prop('checked', true);
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });

    window.editUbigeo = function(id) {
        $.get('/ubigeo/' + id, function(data) {
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            
            $('#ubigeo_id').val(data.id);
            $('#nombre').val(data.nombre || '');
            $('#codigo_postal').val(data.codigo_postal || '');
            $('#padre_id').val(data.dependencia_id || '');
            
            if (data.estado == 1 || data.estado === true) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }
            
            $('#ubigeoModalLabel').text('Editar Ubigeo');
            $('#ubigeoModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
        });
    };

    // Ver sububigeos
    window.verSububigeos = function(id, nombre) {
        // Agregar el nivel actual al stack
        navigationStack.push({
            id: currentPadreId,
            nombre: currentPadreName
        });
        
        currentPadreId = id;
        currentPadreName = nombre;
        loadUbigeoTable(id);
    };

    window.deleteUbigeo = function(id) {
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
                    url: '/ubigeo/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', 'Ubigeo eliminado correctamente.', 'success');
                        loadUbigeoTable(currentPadreId);
                    },
                    error: function(xhr) {
                        var message = 'No se pudo eliminar el ubigeo';
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