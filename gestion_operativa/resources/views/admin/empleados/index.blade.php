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
        
        /* Badge styles for user status */
        .badge-warning {
            background-color: #f39c12 !important;
            color: white !important;
        }
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
        <h4 class="">Gestión de Empleados</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#empleadoModal" id="btnNuevoEmpleado">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo Empleado
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="empleadosTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>DNI</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Dirección</th>
                                <th>Cargo</th>
                                <th>Área</th>
                                <th>Ubigeo</th>
                                <th>Usuario</th>
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

<!-- Modal para crear/editar empleado -->
<div class="modal fade" id="empleadoModal" tabindex="-1" aria-labelledby="empleadoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="empleadoForm" action="" method="POST">
                @csrf
                <input type="hidden" id="empleado_id" name="empleado_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="empleadoModalLabel">Crear/Editar Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="dni" class="form-label">DNI</label>
                            <input type="text" class="form-control" id="dni" name="dni" maxlength="8" pattern="\d{8}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,8)">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cargo_id" class="form-label">Cargo</label>
                            <select class="form-control select2" id="cargo_id" name="cargo_id">
                                <option value="">Seleccione</option>
                                @foreach($cargos as $cargo)
                                    @if($cargo->estado == 1)
                                        <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="area_id" class="form-label">Área</label>
                            <select class="form-control select2" id="area_id" name="area_id">
                                <option value="">Seleccione</option>
                                @foreach($areas as $area)
                                    @if($area->estado == 1)
                                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="departamento_id" class="form-label">Departamento</label>
                            <select class="form-control select2" id="departamento_id" name="departamento_id">
                                <option value="">Seleccione un departamento</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="provincia_id" class="form-label">Provincia</label>
                            <select class="form-control select2" id="provincia_id" name="provincia_id" disabled>
                                <option value="">Seleccione una provincia</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ubigeo_id" class="form-label">Distrito</label>
                            <select class="form-control select2" id="ubigeo_id" name="ubigeo_id" disabled>
                                <option value="">Seleccione un distrito</option>
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

<!-- Modal para crear usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="crearUsuarioForm" action="" method="POST">
                @csrf
                <input type="hidden" id="crear_empleado_id" name="empleado_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Se creará un usuario con el email del empleado.</strong>
                    </div>
                    <div class="mb-3">
                        <label for="crear_perfil" class="form-label">Perfil de Usuario <span class="text-danger">*</span></label>
                        <select class="form-select" id="crear_perfil" name="perfil" required>
                            <option value="">Seleccione un perfil</option>
                            <option value="admin">Administrador</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="tecnico">Técnico</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="crear_password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="crear_password" name="password" minlength="6" required>
                        <div class="form-text">Mínimo 6 caracteres</div>
                    </div>
                    <div class="mb-3">
                        <label for="crear_password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="crear_password_confirmation" name="password_confirmation" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para gestionar usuario -->
<div class="modal fade" id="gestionarUsuarioModal" tabindex="-1" aria-labelledby="gestionarUsuarioModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="gestionarUsuarioForm" action="" method="POST">
                @csrf
                <input type="hidden" id="gestionar_empleado_id" name="empleado_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="gestionarUsuarioModalLabel">Gestionar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gestionar_perfil" class="form-label">Perfil de Usuario <span class="text-danger">*</span></label>
                            <select class="form-select" id="gestionar_perfil" name="perfil" required>
                                <option value="admin">Administrador</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="tecnico">Técnico</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gestionar_estado" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" id="gestionar_estado" name="estado" required>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="gestionar_password" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="gestionar_password" name="password" minlength="6">
                        <div class="form-text">Dejar vacío para mantener la contraseña actual</div>
                    </div>
                    <div class="mb-3">
                        <label for="gestionar_password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" id="gestionar_password_confirmation" name="password_confirmation" minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnEliminarUsuario">Eliminar Usuario</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
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
    // Carga dinámica de cargos
    function loadCargos(selectedId = null) {
        return $.get('{{ route('cargos.select') }}')
            .done(function(data) {
                var select = $('#cargo_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${selectedId == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#empleadoModal'),
                    placeholder: 'Seleccione un cargo',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar cargos');
            });
    }

    // Carga dinámica de áreas
    function loadAreas(selectedId = null) {
        return $.get('{{ route('areas.select') }}')
            .done(function(data) {
                var select = $('#area_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${selectedId == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#empleadoModal'),
                    placeholder: 'Seleccione un área',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar áreas');
            });
    }

    // Carga dinámica de departamentos
    function loadDepartamentos() {
        return $.get('{{ route('empleado.departamentos') }}')
            .done(function(data) {
                var select = $('#departamento_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}">${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#empleadoModal'),
                    placeholder: 'Seleccionar departamento',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar departamentos');
            });
    }

    function loadProvincias(departamentoId) {
        if (!departamentoId) {
            $('#provincia_id').empty().append('<option value="">Seleccione</option>').prop('disabled', true).select2({
                dropdownParent: $('#empleadoModal'),
                placeholder: 'Seleccionar provincia',
                allowClear: true,
                language: { noResults: function() { return 'No hay resultados'; } }
            });
            $('#ubigeo_id').empty().append('<option value="">Seleccione</option>').prop('disabled', true).select2({
                dropdownParent: $('#empleadoModal'),
                placeholder: 'Seleccionar distrito',
                allowClear: true,
                language: { noResults: function() { return 'No hay resultados'; } }
            });
            return;
        }

        return $.get('{{ route('empleado.provincias', ':id') }}'.replace(':id', departamentoId))
            .done(function(data) {
                var select = $('#provincia_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}">${item.text}</option>`);
                });
                select.prop('disabled', false);
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#empleadoModal'),
                    placeholder: 'Seleccionar provincia',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
                $('#ubigeo_id').empty().append('<option value="">Seleccione</option>').prop('disabled', true).select2({
                    dropdownParent: $('#empleadoModal'),
                    placeholder: 'Seleccionar distrito',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar provincias');
            });
    }

    function loadDistritos(provinciaId) {
        if (!provinciaId) {
            $('#ubigeo_id').empty().append('<option value="">Seleccione</option>').prop('disabled', true).select2({
                dropdownParent: $('#empleadoModal'),
                placeholder: 'Seleccionar distrito',
                allowClear: true,
                language: { noResults: function() { return 'No hay resultados'; } }
            });
            return;
        }

        return $.get('{{ route('empleado.distritos', ':id') }}'.replace(':id', provinciaId))
            .done(function(data) {
                var select = $('#ubigeo_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}">${item.text}</option>`);
                });
                select.prop('disabled', false);
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#empleadoModal'),
                    placeholder: 'Seleccionar distrito',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar distritos');
            });
    }

    // Validación en tiempo real
    function setupFormValidation() {
        // Limpiar validaciones previas
        $('#nombre, #apellido, #dni, #telefono, #email, #direccion, #cargo_id, #area_id, #ubigeo_id, #estado').off('blur focus change');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        // Validación nombre
        $('#nombre').on('blur', function() {
            validateField($(this), 'El nombre es obligatorio y no puede exceder 50 caracteres', function(value) {
                return value.trim().length > 0 && value.trim().length <= 50;
            });
        });
        // Validación apellido
        $('#apellido').on('blur', function() {
            validateField($(this), 'El apellido es obligatorio y no puede exceder 100 caracteres', function(value) {
                return value.trim().length > 0 && value.trim().length <= 100;
            });
        });
        // Validación DNI
        $('#dni').on('blur', function() {
            validateField($(this), 'El DNI es obligatorio, debe tener 8 dígitos', function(value) {
                return /^\d{8}$/.test(value.trim());
            });
        });
        // Validación teléfono (opcional, máximo 15)
        $('#telefono').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length > 0) {
                validateField($(this), 'El teléfono no puede exceder 15 caracteres', function(value) {
                    return value.length <= 15;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        // Validación email (opcional, formato email, máximo 100)
        $('#email').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length > 0) {
                validateField($(this), 'Ingrese un email válido y menor a 100 caracteres', function(value) {
                    return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value) && value.length <= 100;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        // Validación dirección (opcional, máximo 200)
        $('#direccion').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length > 0) {
                validateField($(this), 'La dirección no puede exceder 200 caracteres', function(value) {
                    return value.length <= 200;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        // Validación selects obligatorios
        $('#cargo_id').on('blur change', function() {
            validateField($(this), 'Seleccione un cargo válido', function(value) {
                var $opt = $('#cargo_id option:selected');
                return value.trim() !== '' && !$opt.is(':disabled');
            });
        });
        $('#area_id').on('blur change', function() {
            validateField($(this), 'Seleccione un área válida', function(value) {
                var $opt = $('#area_id option:selected');
                return value.trim() !== '' && !$opt.is(':disabled');
            });
        });
        $('#departamento_id').on('blur change', function() {
            validateField($(this), 'Seleccione un departamento válido', function(value) {
                var $opt = $('#departamento_id option:selected');
                return value.trim() !== '' && !$opt.is(':disabled');
            });
        });
        $('#provincia_id').on('blur change', function() {
            validateField($(this), 'Seleccione una provincia válida', function(value) {
                var $opt = $('#provincia_id option:selected');
                return value.trim() !== '' && !$opt.is(':disabled');
            });
        });
        $('#ubigeo_id').on('blur change', function() {
            validateField($(this), 'Seleccione un distrito válido', function(value) {
                var $opt = $('#ubigeo_id option:selected');
                return value.trim() !== '' && !$opt.is(':disabled');
            });
        });
        $('#estado').on('blur change', function() {
            validateField($(this), 'Seleccione el estado', function(value) {
                return value.trim() !== '';
            });
        });

        // Limpiar error al enfocar
        $('#nombre, #apellido, #dni, #telefono, #email, #direccion, #cargo_id, #area_id, #departamento_id, #provincia_id, #ubigeo_id, #estado').on('focus', function() {
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
        if (!validateField($('#nombre'), 'El nombre es obligatorio y no puede exceder 50 caracteres', function(value) {
            return value.trim().length > 0 && value.trim().length <= 50;
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#nombre');
        }
        if (!validateField($('#apellido'), 'El apellido es obligatorio y no puede exceder 100 caracteres', function(value) {
            return value.trim().length > 0 && value.trim().length <= 100;
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#apellido');
        }
        if (!validateField($('#dni'), 'El DNI es obligatorio, debe tener 8 dígitos', function(value) {
            return /^\d{8}$/.test(value.trim());
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#dni');
        }
        // Validar selects obligatorios
        if (!validateField($('#cargo_id'), 'Seleccione un cargo válido', function(value) {
            var $opt = $('#cargo_id option:selected');
            return value.trim() !== '' && !$opt.is(':disabled');
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#cargo_id');
        }
        if (!validateField($('#area_id'), 'Seleccione un área válida', function(value) {
            var $opt = $('#area_id option:selected');
            return value.trim() !== '' && !$opt.is(':disabled');
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#area_id');
        }
        if (!validateField($('#departamento_id'), 'Seleccione un departamento válido', function(value) {
            var $opt = $('#departamento_id option:selected');
            return value.trim() !== '' && !$opt.is(':disabled');
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#departamento_id');
        }
        if (!validateField($('#provincia_id'), 'Seleccione una provincia válida', function(value) {
            var $opt = $('#provincia_id option:selected');
            return value.trim() !== '' && !$opt.is(':disabled');
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#provincia_id');
        }
        if (!validateField($('#ubigeo_id'), 'Seleccione un distrito válido', function(value) {
            var $opt = $('#ubigeo_id option:selected');
            return value.trim() !== '' && !$opt.is(':disabled');
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#ubigeo_id');
        }
        if (!validateField($('#estado'), 'Seleccione el estado', function(value) { return value.trim() !== ''; })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#estado');
        }
        if (!validateField($('#dni'), 'El DNI es obligatorio y no puede exceder 15 caracteres', function(value) {
            return value.trim().length > 0 && value.trim().length <= 15;
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#dni');
        }
        if (firstErrorField) {
            firstErrorField.focus();
        }
        return isValid;
    }

    $(document).ready(function() {
        // Inicializar Select2 con placeholder y allowClear igual que materiales
        $('#cargo_id').select2({
            dropdownParent: $('#empleadoModal'),
            placeholder: 'Seleccione un cargo',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });
        $('#area_id').select2({
            dropdownParent: $('#empleadoModal'),
            placeholder: 'Seleccione un área',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });
        $('#departamento_id').select2({
            dropdownParent: $('#empleadoModal'),
            placeholder: 'Seleccionar departamento',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });
        $('#provincia_id').select2({
            dropdownParent: $('#empleadoModal'),
            placeholder: 'Seleccionar provincia',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });
        $('#ubigeo_id').select2({
            dropdownParent: $('#empleadoModal'),
            placeholder: 'Seleccionar distrito',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });

        // Event listeners para cascada de ubigeo
        $('#departamento_id').on('change', function() {
            var departamentoId = $(this).val();
            loadProvincias(departamentoId);
        });

        $('#provincia_id').on('change', function() {
            var provinciaId = $(this).val();
            loadDistritos(provinciaId);
        });

        var table = $('#empleadosTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('empleados.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nombre', name: 'nombre' },
                { data: 'apellido', name: 'apellido' },
                { data: 'dni', name: 'dni' },
                { data: 'telefono', name: 'telefono' },
                { data: 'email', name: 'email' },
                { data: 'direccion', name: 'direccion' },
                { data: 'cargo', name: 'cargo.nombre' },
                { data: 'area', name: 'area.nombre' },
                { data: 'ubigeo', name: 'ubigeo.nombre' },
                { data: 'usuario_estado', name: 'usuario_estado', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
            columnDefs: [
                { targets: [10, 11, 12], className: "text-center" }
            ],
            createdRow: function(row, data, dataIndex) {
                $('td:eq(10)', row).html(data.usuario_estado);
                $('td:eq(11)', row).html(data.estado_badge);
                $('td:eq(12)', row).html(data.action);
            },
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

        // Cargar todos los selects dinámicamente al inicializar
        loadCargos();
        loadAreas();
        loadDepartamentos();
        setupFormValidation();

        // Si el usuario cambia el select y la opción es inactiva, eliminarla y mostrar placeholder
        $('#cargo_id').on('change', function() {
            var $opt = $(this).find('option:selected');
            if ($opt.is(':disabled')) {
                $(this).val('').trigger('change.select2');
                $opt.remove();
            }
        });
        $('#area_id').on('change', function() {
            var $opt = $(this).find('option:selected');
            if ($opt.is(':disabled')) {
                $(this).val('').trigger('change.select2');
                $opt.remove();
            }
        });
        $('#ubigeo_id').on('change', function() {
            var $opt = $(this).find('option:selected');
            if ($opt.is(':disabled')) {
                $(this).val('').trigger('change.select2');
                $opt.remove();
            }
        });

        $('#btnNuevoEmpleado').click(function() {
            $('#empleadoForm')[0].reset();
            $('#empleado_id').val('');
            $('#empleadoModalLabel').text('Nuevo Empleado');
            loadCargos();
            loadAreas();
            loadDepartamentos();
            // Limpiar selects y mostrar placeholder
            setTimeout(function() {
                $('#cargo_id').val('').trigger('change');
                $('#area_id').val('').trigger('change');
                $('#departamento_id').val('').trigger('change');
                $('#provincia_id').val('').trigger('change');
                $('#ubigeo_id').val('').trigger('change');
            }, 200);
            setupFormValidation();
        });

        $('#empleadoForm').on('submit', function(e) {
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
            var id = $('#empleado_id').val();
            var url = id ? '/empleados/' + id : '/empleados';
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
                    $('#empleadoModal').modal('hide');
                    table.ajax.reload();
                    $('#empleadoForm')[0].reset();
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

        $('#empleadoModal').on('hidden.bs.modal', function () {
            $('#empleadoForm')[0].reset();
            $('#departamento_id').val('');
            $('#provincia_id').prop('disabled', true).empty().append('<option value="">Seleccione una provincia</option>');
            $('#ubigeo_id').prop('disabled', true).empty().append('<option value="">Seleccione un distrito</option>');
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });

    window.editEmpleado = function(id) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        $.get('/empleados/' + id, function(data) {
            Swal.close();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#empleado_id').val(data.id);
            $('#nombre').val(data.nombre);
            $('#apellido').val(data.apellido);
            $('#dni').val(data.dni);
            $('#telefono').val(data.telefono);
            $('#email').val(data.email);
            $('#direccion').val(data.direccion);
            
            // Cargar cargos y manejar selección
            loadCargos(data.cargo_id).done(function() {
                var cargoVal = data.cargo_id ? data.cargo_id.toString() : '';
                if ($('#cargo_id option[value="'+cargoVal+'"], #cargo_id option[value='+cargoVal+']').length > 0) {
                    $('#cargo_id').val(cargoVal).trigger('change.select2');
                } else if (cargoVal) {
                    var nombreCargo = data.cargo && data.cargo.nombre ? data.cargo.nombre + ' (inactivo)' : 'Inactivo';
                    $('#cargo_id').append('<option value="'+cargoVal+'" selected>'+nombreCargo+'</option>');
                    $('#cargo_id').val(cargoVal).trigger('change.select2');
                    setTimeout(function(){
                        $('#cargo_id option[value="'+cargoVal+'"]').prop('disabled', true);
                    }, 0);
                } else {
                    $('#cargo_id').val('').trigger('change.select2');
                }
            });
            
            // Cargar áreas y manejar selección
            loadAreas(data.area_id).done(function() {
                var areaVal = data.area_id ? data.area_id.toString() : '';
                if ($('#area_id option[value="'+areaVal+'"], #area_id option[value='+areaVal+']').length > 0) {
                    $('#area_id').val(areaVal).trigger('change.select2');
                } else if (areaVal) {
                    var nombreArea = data.area && data.area.nombre ? data.area.nombre + ' (inactiva)' : 'Inactiva';
                    $('#area_id').append('<option value="'+areaVal+'" selected>'+nombreArea+'</option>');
                    $('#area_id').val(areaVal).trigger('change.select2');
                    setTimeout(function(){
                        $('#area_id option[value="'+areaVal+'"]').prop('disabled', true);
                    }, 0);
                } else {
                    $('#area_id').val('').trigger('change.select2');
                }
            });
            
            // Cargar cascada de ubigeo y manejar selección
            if (data.ubigeo_id) {
                $.get('/empleados/ubigeo-jerarquia/' + data.ubigeo_id, function(hierarquia) {
                    if (hierarquia.success) {
                        // Cargar departamentos
                        $.get('{{ route('empleado.departamentos') }}', function(departamentos) {
                            $('#departamento_id').empty().append('<option value="">Seleccione un departamento</option>');
                            $.each(departamentos, function(index, item) {
                                $('#departamento_id').append(`<option value="${item.id}">${item.text}</option>`);
                            });
                            
                            // Seleccionar el departamento
                            if (hierarquia.departamento_id) {
                                $('#departamento_id').val(hierarquia.departamento_id);
                                
                                // Cargar provincias después de seleccionar departamento
                                $.get('{{ route('empleado.provincias', ':id') }}'.replace(':id', hierarquia.departamento_id), function(provincias) {
                                    $('#provincia_id').empty().append('<option value="">Seleccione una provincia</option>');
                                    $.each(provincias, function(index, item) {
                                        $('#provincia_id').append(`<option value="${item.id}">${item.text}</option>`);
                                    });
                                    $('#provincia_id').prop('disabled', false);
                                    
                                    // Seleccionar la provincia
                                    if (hierarquia.provincia_id) {
                                        $('#provincia_id').val(hierarquia.provincia_id);
                                        
                                        // Cargar distritos después de seleccionar provincia
                                        $.get('{{ route('empleado.distritos', ':id') }}'.replace(':id', hierarquia.provincia_id), function(distritos) {
                                            $('#ubigeo_id').empty().append('<option value="">Seleccione un distrito</option>');
                                            $.each(distritos, function(index, item) {
                                                $('#ubigeo_id').append(`<option value="${item.id}">${item.text}</option>`);
                                            });
                                            $('#ubigeo_id').prop('disabled', false);
                                            
                                            // Seleccionar el distrito
                                            $('#ubigeo_id').val(hierarquia.distrito_id);
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            } else {
                loadDepartamentos();
            }
            
            if (data.estado == 1) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }
            $('#empleadoModalLabel').text('Editar Empleado');
            $('#empleadoModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.close();
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
        });
    };

    window.deleteEmpleado = function(id) {
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
                    url: '/empleados/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', 'Empleado eliminado correctamente.', 'success');
                        $('#empleadosTable').DataTable().ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar el empleado', 'error');
                    }
                });
            }
        });
    };

    // Función para crear usuario
    window.crearUsuario = function(empleadoId) {
        $('#crear_empleado_id').val(empleadoId);
        $('#crearUsuarioForm')[0].reset();
        $('#crear_empleado_id').val(empleadoId);
        $('#crearUsuarioModal').modal('show');
    };

    // Función para gestionar usuario existente
    window.gestionarUsuario = function(empleadoId) {
        $.get('/empleados/' + empleadoId + '/usuario')
            .done(function(data) {
                if (data.success && data.usuario) {
                    $('#gestionar_empleado_id').val(empleadoId);
                    $('#gestionar_perfil').val(data.usuario.perfil);
                    $('#gestionar_estado').val(data.usuario.estado ? '1' : '0');
                    $('#gestionar_password').val('');
                    $('#gestionar_password_confirmation').val('');
                    $('#gestionarUsuarioModalLabel').text('Gestionar Usuario: ' + data.empleado.nombre + ' ' + data.empleado.apellido);
                    $('#gestionarUsuarioModal').modal('show');
                } else {
                    Swal.fire('Error', 'No se pudo cargar la información del usuario', 'error');
                }
            })
            .fail(function() {
                Swal.fire('Error', 'No se pudo cargar la información del usuario', 'error');
            });
    };

    // Submit form crear usuario
    $('#crearUsuarioForm').on('submit', function(e) {
        e.preventDefault();
        
        var password = $('#crear_password').val();
        var confirmation = $('#crear_password_confirmation').val();
        
        if (password !== confirmation) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }
        
        var empleadoId = $('#crear_empleado_id').val();
        
        $.ajax({
            url: '/empleados/' + empleadoId + '/usuario',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    Swal.fire('Creado!', res.message, 'success');
                    $('#crearUsuarioModal').modal('hide');
                    $('#empleadosTable').DataTable().ajax.reload();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = Object.values(errors).join('\n');
                    Swal.fire('Error de Validación', errorMsg, 'error');
                } else {
                    Swal.fire('Error!', 'Hubo un problema al crear el usuario.', 'error');
                }
            }
        });
    });

    // Submit form gestionar usuario
    $('#gestionarUsuarioForm').on('submit', function(e) {
        e.preventDefault();
        
        var password = $('#gestionar_password').val();
        var confirmation = $('#gestionar_password_confirmation').val();
        
        if (password && password !== confirmation) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }
        
        var empleadoId = $('#gestionar_empleado_id').val();
        
        $.ajax({
            url: '/empleados/' + empleadoId + '/usuario',
            type: 'PUT',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    Swal.fire('Actualizado!', res.message, 'success');
                    $('#gestionarUsuarioModal').modal('hide');
                    $('#empleadosTable').DataTable().ajax.reload();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = Object.values(errors).join('\n');
                    Swal.fire('Error de Validación', errorMsg, 'error');
                } else {
                    Swal.fire('Error!', 'Hubo un problema al actualizar el usuario.', 'error');
                }
            }
        });
    });

    // Eliminar usuario
    $('#btnEliminarUsuario').on('click', function() {
        var empleadoId = $('#gestionar_empleado_id').val();
        
        Swal.fire({
            title: '¿Eliminar Usuario?',
            text: 'Esta acción no se puede deshacer. El empleado quedará sin acceso al sistema.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/empleados/' + empleadoId + '/usuario',
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Eliminado!', res.message, 'success');
                            $('#gestionarUsuarioModal').modal('hide');
                            $('#empleadosTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Hubo un problema al eliminar el usuario.', 'error');
                    }
                });
            }
        });
    });
    </script>
@endsection
