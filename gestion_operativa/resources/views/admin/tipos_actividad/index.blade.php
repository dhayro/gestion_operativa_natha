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
            <h4 class="" id="pageTitle">Gestión de Tipos de Actividad</h4>
            <small id="breadcrumbNav" class="text-muted"></small>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tiposActividadModal" id="btnNuevoTiposActividad">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span id="btnNuevoText">Nuevo Tipo de Actividad</span>
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="tiposActividadTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
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

<!-- Modal para crear/editar tipo de actividad -->
<div class="modal fade" id="tiposActividadModal" tabindex="-1" aria-labelledby="tiposActividadModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="tiposActividadForm" action="" method="POST">
                @csrf
                <input type="hidden" id="tipos_actividad_id" name="tipos_actividad_id">
                <input type="hidden" id="padre_id" name="dependencia_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="tiposActividadModalLabel">Crear/Editar Tipo de Actividad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50" placeholder="Ingrese el nombre del tipo de actividad" required>
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
            } else if (value.length > 50) {
                validateField($(this), 'El nombre no puede exceder 50 caracteres', function(value) {
                    return value.length <= 50;
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
        if (nombre.length > 50) {
            validateField($('#nombre'), 'El nombre no puede exceder 50 caracteres', function(value) {
                return value.length <= 50;
            });
            return false;
        }
        return true;
    }

    // Cargar tabla de tipos
    function loadTiposTable(padreId = null) {
        if ($('#tiposActividadTable').length) {
            // Si ya existe la tabla, destruirla
            $('#tiposActividadTable').DataTable().destroy();
        }

        // Construir URL con parámetro opcional
        var url = '{{ route('tipos-actividad.data') }}';
        if (padreId) {
            url += '?dependencia_id=' + padreId;
        }

        // Establecer título y botón
        if (padreId) {
            $('#pageTitle').text('Subactividades de: ' + currentPadreName);
            $('#breadcrumbNav').html('<small><a href="javascript:void(0);" onclick="volverNivel()">← Volver</a></small>');
            $('#btnNuevoText').text('Nueva Subactividad');
        } else {
            $('#pageTitle').text('Gestión de Tipos de Actividad');
            $('#breadcrumbNav').html('');
            $('#btnNuevoText').text('Nuevo Tipo de Actividad');
        }

        var table = $('#tiposActividadTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: url,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nombre', name: 'nombre', orderable: false, searchable: true },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
            columnDefs: [
                { targets: [2, 3], className: "text-center" }
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
        
        loadTiposTable(currentPadreId);
    };

    $(document).ready(function() {
        setupFormValidation();
        navigationStack = []; // Inicializar stack vacío
        loadTiposTable();

        $('#btnNuevoTiposActividad').click(function() {
            $('#tiposActividadForm')[0].reset();
            $('#tipos_actividad_id').val('');
            $('#padre_id').val(currentPadreId || '');
            $('#estado').prop('checked', true);
            $('#tiposActividadModalLabel').text(currentPadreId ? 'Crear Subactividad' : 'Crear Nuevo Tipo de Actividad');
            setupFormValidation();
        });

        $('#tiposActividadForm').on('submit', function(e) {
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
            
            var id = $('#tipos_actividad_id').val();
            var url = id ? '/tipos-actividad/' + id : '/tipos-actividad';
            var method = id ? 'PUT' : 'POST';
            
            // Preparar datos
            var formData = {
                nombre: $('#nombre').val(),
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
                    $('#tiposActividadModal').modal('hide');
                    loadTiposTable(currentPadreId);
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

        $('#tiposActividadModal').on('hidden.bs.modal', function () {
            $('#tiposActividadForm')[0].reset();
            $('#estado').prop('checked', true);
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });

    window.editTiposActividad = function(id) {
        $.get('/tipos-actividad/' + id, function(data) {
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            
            $('#tipos_actividad_id').val(data.id);
            $('#nombre').val(data.nombre || '');
            $('#padre_id').val(data.dependencia_id || '');
            
            if (data.estado == 1 || data.estado === true) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }
            
            $('#tiposActividadModalLabel').text('Editar Tipo de Actividad');
            $('#tiposActividadModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
        });
    };

    // Ver subactividades
    window.verSubactividades = function(id, nombre) {
        // Agregar el nivel actual al stack
        navigationStack.push({
            id: currentPadreId,
            nombre: currentPadreName
        });
        
        currentPadreId = id;
        currentPadreName = nombre;
        loadTiposTable(id);
    };

    window.deleteTiposActividad = function(id) {
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
                    url: '/tipos-actividad/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', 'Tipo de actividad eliminado correctamente.', 'success');
                        loadTiposTable(currentPadreId);
                    },
                    error: function(xhr) {
                        var message = 'No se pudo eliminar el tipo de actividad';
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