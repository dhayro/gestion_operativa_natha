@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    @vite(['resources/scss/light/plugins/table/datatable/dt-global_style.scss'])
    @vite(['resources/scss/light/plugins/table/datatable/custom_dt_custom.scss'])
    @vite(['resources/scss/dark/plugins/table/datatable/dt-global_style.scss'])
    @vite(['resources/scss/dark/plugins/table/datatable/custom_dt_custom.scss'])
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
    @vite(['resources/scss/light/plugins/sweetalerts2/custom-sweetalert.scss'])
    @vite(['resources/scss/dark/plugins/sweetalerts2/custom-sweetalert.scss'])

    <style>
        .modal-content {
            background: #fff !important;
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de Ubigeos</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ubigeoModal" onclick="openCreateModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo Ubigeo
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
                    <table id="zero-config" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Código Postal</th>
                                <th>Dependencia</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán dinámicamente con DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar ubigeo -->
<div class="modal fade" id="ubigeoModal" tabindex="-1" aria-labelledby="ubigeoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="ubigeoForm" action="" method="POST">
                @csrf
                <input type="hidden" id="ubigeoId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="ubigeoModalLabel">Crear/Editar Ubigeo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" >
                    </div>
                    <div class="mb-3">
                        <label for="codigo_postal" class="form-label">Código Postal</label>
                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal">
                    </div>
                    <div class="mb-3">
                        <label for="dependencia_id" class="form-label">Dependencia</label>
                        <select class="form-select" id="dependencia_id" name="dependencia_id">
                            <option value="">Seleccione una dependencia</option>
                            <!-- Opciones cargadas dinámicamente -->
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="estado" name="estado" checked>
                        <label class="form-check-label" for="estado">Activo</label>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/table/datatable/spanish.js')}}"></script>

    @vite(['resources/scss/light/plugins/table/datatable/custom_dt_custom.scss'])
    
    <!-- SweetAlert2 -->
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    
    <script>
        // Validación en tiempo real
        function setupFormValidation() {
            // Remover listeners previos para evitar duplicados
            $('#nombre, #codigo_postal, #dependencia_id').off('blur focus change');
            
            // Limpiar errores previos
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            
            // Validación del campo nombre (máximo 100 caracteres)
            $('#nombre').on('blur', function() {
                validateField($(this), 'El nombre es obligatorio y no puede exceder 100 caracteres', function(value) {
                    return value.trim().length > 0 && value.trim().length <= 100;
                });
            });
            
            // Validación del código postal (máximo 10 caracteres, opcional)
            $('#codigo_postal').on('blur', function() {
                var value = $(this).val().trim();
                if (value.length > 0) {
                    validateField($(this), 'El código postal no puede exceder 10 caracteres', function(value) {
                        return value.trim().length <= 10;
                    });
                } else {
                    $(this).removeClass('is-invalid is-valid');
                    $(this).next('.invalid-feedback').remove();
                }
            });
            
            // Validar al hacer focus (quitar error si existe)
            $('#nombre, #codigo_postal, #dependencia_id').on('focus', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            });
        }

        // Función para validar campos individuales
        function validateField(field, message, validationFunction) {
            var value = field.val();
            var isValid = validationFunction(value);
            
            // Remover clases y mensajes previos
            field.removeClass('is-invalid is-valid');
            field.next('.invalid-feedback').remove();
            
            if (!isValid) {
                // Agregar clase de error y mensaje
                field.addClass('is-invalid');
                field.after(`<div class="invalid-feedback">${message}</div>`);
                return false;
            } else {
                // Agregar clase de éxito
                field.addClass('is-valid');
                return true;
            }
        }

        // Validación completa del formulario
        function validateForm() {
            var isValid = true;
            var firstErrorField = null;
            
            // Validar nombre (obligatorio, máximo 100 caracteres)
            if (!validateField($('#nombre'), 'El nombre es obligatorio y no puede exceder 100 caracteres', function(value) {
                return value.trim().length > 0 && value.trim().length <= 100;
            })) {
                isValid = false;
                if (!firstErrorField) firstErrorField = $('#nombre');
            }
            
            // Validar código postal (opcional, máximo 10 caracteres)
            var codigoPostal = $('#codigo_postal').val().trim();
            if (codigoPostal.length > 0) {
                if (!validateField($('#codigo_postal'), 'El código postal no puede exceder 10 caracteres', function(value) {
                    return value.trim().length <= 10;
                })) {
                    isValid = false;
                    if (!firstErrorField) firstErrorField = $('#codigo_postal');
                }
            }
            
            // Hacer focus en el primer campo con error
            if (firstErrorField) {
                firstErrorField.focus();
            }
            
            return isValid;
        }

        // Actualizar la función openCreateModal
        window.openCreateModal = function() {
            // Limpiar formulario completamente
            $('#ubigeoForm')[0].reset();
            
            // Limpiar valores específicos
            $('#ubigeoId').val('');
            $('#nombre').val('');
            $('#codigo_postal').val('');
            $('#dependencia_id').val('');
            $('#estado').prop('checked', true);
            
            // Limpiar validaciones
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            
            // Configurar modal para creación
            $('#ubigeoModalLabel').text('Crear Ubigeo');
            $('#ubigeoForm').attr('action', '{{ route("ubigeo.store") }}');
            
            // Remover campo method si existe
            $('#method-field').remove();
            
            // Recargar dependencias y configurar validación
            loadDependencias().then(function() {
                setupFormValidation();
            });
        }

        // Reemplazar la función de edición con esta versión optimizada:
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            
            // Mostrar loading más ligero
            Swal.fire({
                title: 'Cargando...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Cargar datos del ubigeo y dependencias en paralelo
            Promise.all([
                $.get('{{ route("ubigeo.show", ":id") }}'.replace(':id', id)),
                loadDependencias()
            ])
            .then(function([ubigeoData]) {
                // Cerrar loading
                Swal.close();
                
                // Limpiar validaciones previas
                $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                $('.invalid-feedback').remove();
                
                // Llenar el formulario
                $('#ubigeoId').val(ubigeoData.id);
                $('#nombre').val(ubigeoData.nombre || '');
                $('#codigo_postal').val(ubigeoData.codigo_postal || '');
                $('#dependencia_id').val(ubigeoData.dependencia_id || '');
                $('#estado').prop('checked', Boolean(ubigeoData.estado));
                
                // Configurar modal para edición
                $('#ubigeoModalLabel').text('Editar Ubigeo');
                $('#ubigeoForm').attr('action', '{{ route("ubigeo.update", ":id") }}'.replace(':id', id));
                
                // Agregar campo method PUT
                $('#method-field').remove();
                $('#ubigeoForm').append('<input type="hidden" name="_method" value="PUT" id="method-field">');
                
                // Configurar validación
                setupFormValidation();
                
                // Mostrar modal
                $('#ubigeoModal').modal('show');
            })
            .catch(function(error) {
                Swal.close();
                console.error('Error al cargar datos:', error);
                Swal.fire(
                    'Error!',
                    'No se pudieron cargar los datos del registro.',
                    'error'
                );
            });
        });

        // Actualizar la función loadDependencias para que retorne una promesa
        function loadDependencias() {
            return $.get('{{ route("ubigeo.select") }}')
                .done(function(data) {
                    var select = $('#dependencia_id');
                    select.empty().append('<option value="">Seleccione una dependencia</option>');
                    $.each(data, function(index, item) {
                        select.append(`<option value="${item.id}">${item.text}</option>`);
                    });
                })
                .fail(function() {
                    console.error('Error al cargar dependencias');
                });
        }

        // Reemplazar $(document).ready con esta versión optimizada:
        $(document).ready(function() {
            // Cache de dependencias para evitar múltiples llamadas
            let dependenciasCache = null;
            let dependenciasPromise = null;
            
            // Función optimizada para cargar dependencias con cache
            window.loadDependencias = function() {
                if (dependenciasPromise) {
                    return dependenciasPromise;
                }
                
                if (dependenciasCache) {
                    var select = $('#dependencia_id');
                    select.empty().append('<option value="">Seleccione una dependencia</option>');
                    $.each(dependenciasCache, function(index, item) {
                        select.append(`<option value="${item.id}">${item.text}</option>`);
                    });
                    return Promise.resolve();
                }
                
                dependenciasPromise = $.get('{{ route("ubigeo.select") }}')
                    .done(function(data) {
                        dependenciasCache = data;
                        var select = $('#dependencia_id');
                        select.empty().append('<option value="">Seleccione una dependencia</option>');
                        $.each(data, function(index, item) {
                            select.append(`<option value="${item.id}">${item.text}</option>`);
                        });
                    })
                    .fail(function() {
                        console.error('Error al cargar dependencias');
                    })
                    .always(function() {
                        dependenciasPromise = null;
                    });
                    
                return dependenciasPromise;
            };

            // Hacer la variable table global
            window.ubigeoTable = $('#zero-config').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('ubigeo.data') }}",
                    "type": "GET"
                },
                "columns": [
                    { 
                        "data": "DT_RowIndex", 
                        "name": "DT_RowIndex", 
                        "orderable": false, 
                        "searchable": false,
                        "width": "5%" 
                    },
                    { "data": "nombre", "width": "25%" },
                    { "data": "codigo_postal", "width": "15%" },
                    { "data": "dependencia_display", "width": "25%" },
                    { 
                        "data": "estado_badge",
                        "width": "15%",
                        "orderable": false,
                        "searchable": false
                    },
                    { 
                        "data": "action",
                        "orderable": false,
                        "searchable": false,
                        "width": "15%"
                    }
                ],
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                "order": [[1, "asc"]],
                "columnDefs": [
                    {
                        "targets": [4, 5], // columnas estado_badge y action
                        "className": "text-center"
                    }
                ],
                // Configuración para procesar HTML
                "createdRow": function(row, data, dataIndex) {
                    // Procesar la columna estado_badge
                    $('td:eq(4)', row).html(data.estado_badge);
                    // Procesar la columna action
                    $('td:eq(5)', row).html(data.action);
                },
                "language": {
                    "processing": "Procesando...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "Ningún dato disponible en esta tabla",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });

            // Cargar dependencias solo una vez al inicializar
            loadDependencias();

            // Configurar validación inicial
            setupFormValidation();

            // Guardar registro
            $('#ubigeoForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validar formulario antes de enviar
                if (!validateForm()) {
                    Swal.fire({
                        title: 'Campos Requeridos',
                        text: 'Por favor, complete todos los campos obligatorios correctamente.',
                        icon: 'warning',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
                
                var form = $(this);
                var url = form.attr('action');
                
                // Preparar datos del formulario correctamente
                var formData = new FormData();
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('nombre', $('#nombre').val().trim());
                formData.append('codigo_postal', $('#codigo_postal').val().trim());
                formData.append('dependencia_id', $('#dependencia_id').val());
                formData.append('estado', $('#estado').is(':checked') ? '1' : '0');
                
                // Si es edición, agregar método PUT
                if ($('#method-field').length > 0) {
                    formData.append('_method', 'PUT');
                }
                
                // Mostrar loading
                Swal.fire({
                    title: 'Guardando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire(
                            'Guardado!',
                            'El registro ha sido guardado correctamente.',
                            'success'
                        );
                        $('#ubigeoModal').modal('hide');
                        window.ubigeoTable.ajax.reload(); // Usar la variable global
                        form[0].reset();
                        $('#method-field').remove();
                    },
                    error: function(xhr) {
                        console.error('Error al guardar:', xhr);
                        
                        // Limpiar errores previos
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();
                        
                        if (xhr.status === 422) {
                            // Errores de validación
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                var input = $('#' + field);
                                input.addClass('is-invalid');
                                input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                            });
                            
                            Swal.fire(
                                'Error de Validación',
                                'Por favor, corrija los errores en el formulario.',
                                'error'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al guardar el registro.',
                                'error'
                            );
                        }
                    }
                });
            });

            // Limpiar formulario cuando se cierre el modal
            $('#ubigeoModal').on('hidden.bs.modal', function () {
                $('#ubigeoForm')[0].reset();
                $('#method-field').remove();
                $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                $('.invalid-feedback').remove();
            });
        });
        
// Agregar estas funciones globales para que funcionen con onclick
window.viewUbigeo = function(id) {
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Cargar datos del ubigeo
    $.get('{{ route("ubigeo.show", ":id") }}'.replace(':id', id))
        .done(function(data) {
            Swal.close();
            
            // Mostrar información en un modal de solo lectura
            Swal.fire({
                title: 'Información del Ubigeo',
                html: `
                    <div class="text-left">
                        <p><strong>Nombre:</strong> ${data.nombre || 'N/A'}</p>
                        <p><strong>Código Postal:</strong> ${data.codigo_postal || 'N/A'}</p>
                        <p><strong>Dependencia:</strong> ${data.dependencia_nombre || 'Sin dependencia'}</p>
                        <p><strong>Estado:</strong> ${data.estado ? 'Activo' : 'Inactivo'}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Cerrar'
            });
        })
        .fail(function() {
            Swal.close();
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
        });
};

window.editUbigeo = function(id) {
    // Mostrar loading
    Swal.fire({
        title: 'Cargando...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Cargar datos del ubigeo y dependencias en paralelo
    Promise.all([
        $.get('{{ route("ubigeo.show", ":id") }}'.replace(':id', id)),
        loadDependencias()
    ])
    .then(function([ubigeoData]) {
        // Cerrar loading
        Swal.close();
        
        // Limpiar validaciones previas
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();
        
        // Llenar el formulario
        $('#ubigeoId').val(ubigeoData.id);
        $('#nombre').val(ubigeoData.nombre || '');
        $('#codigo_postal').val(ubigeoData.codigo_postal || '');
        $('#dependencia_id').val(ubigeoData.dependencia_id || '');
        $('#estado').prop('checked', Boolean(ubigeoData.estado));
        
        // Configurar modal para edición
        $('#ubigeoModalLabel').text('Editar Ubigeo');
        $('#ubigeoForm').attr('action', '{{ route("ubigeo.update", ":id") }}'.replace(':id', id));
        
        // Agregar campo method PUT
        $('#method-field').remove();
        $('#ubigeoForm').append('<input type="hidden" name="_method" value="PUT" id="method-field">');
        
        // Configurar validación
        setupFormValidation();
        
        // Mostrar modal
        $('#ubigeoModal').modal('show');
    })
    .catch(function(error) {
        Swal.close();
        console.error('Error al cargar datos:', error);
        Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
    });
};

window.deleteUbigeo = function(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("ubigeo.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire(
                        'Eliminado!',
                        'El registro ha sido eliminado correctamente.',
                        'success'
                    );
                    window.ubigeoTable.ajax.reload(); // Usar la variable global
                },
                error: function(xhr) {
                    console.error('Error al eliminar:', xhr);
                    let errorMessage = 'Hubo un problema al eliminar el registro.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
};
    </script>
    
@endsection