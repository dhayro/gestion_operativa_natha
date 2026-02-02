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
        <h4 class="">Gestión de Materiales</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#materialModal" id="btnNuevoMaterial">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo Material
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="materialesTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Categoría</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th>Precio Unitario</th>
                                <th>Stock Mínimo</th>
                                <th>Código</th>
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

<!-- Modal para crear/editar material -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="materialForm" action="" method="POST">
                @csrf
                <input type="hidden" id="material_id" name="material_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="materialModalLabel">Crear/Editar Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-control select2" id="categoria_id" name="categoria_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unidad_medida_id" class="form-label">Unidad de Medida</label>
                            <select class="form-control select2" id="unidad_medida_id" name="unidad_medida_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="codigo_material" class="form-label">Código</label>
                            <input type="text" class="form-control" id="codigo_material" name="codigo_material" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="precio_unitario" class="form-label">Precio Unitario</label>
                            <input type="number" step="0.001" class="form-control" id="precio_unitario" name="precio_unitario">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" name="stock_minimo">
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
    // Carga dinámica de categorías
    function loadCategorias(selectedId = null) {
        return $.get('{{ route('categorias.select') }}')
            .done(function(data) {
                var select = $('#categoria_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${selectedId == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#materialModal'),
                    placeholder: 'Seleccione una categoría',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar categorías');
            });
    }

    // Carga dinámica de unidades de medida
    function loadUnidades(selectedId = null) {
        return $.get('{{ route('unidades.select') }}')
            .done(function(data) {
                var select = $('#unidad_medida_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${selectedId == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#materialModal'),
                    placeholder: 'Seleccione una unidad de medida',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar unidades de medida');
            });
    }
    // Validación en tiempo real para materiales
    function setupFormValidation() {
        $('#nombre, #codigo_material, #descripcion, #precio_unitario, #stock_minimo, #categoria_id, #unidad_medida_id').off('blur focus change');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        $('#nombre').on('blur', function() {
            validateField($(this), 'El nombre es obligatorio y no puede exceder 100 caracteres', function(value) {
                var val = (value || '').trim();
                return val.length > 0 && val.length <= 100;
            });
        });
        $('#codigo_material').on('blur', function() {
            validateField($(this), 'El código es obligatorio y no puede exceder 50 caracteres', function(value) {
                var val = (value || '').trim();
                return val.length > 0 && val.length <= 50;
            });
        });
        $('#descripcion').on('blur', function() {
            var value = ($(this).val() || '').trim();
            if (value.length > 0) {
                validateField($(this), 'La descripción no puede exceder 255 caracteres', function(value) {
                    return value.length <= 255;
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#precio_unitario').on('blur', function() {
            var value = ($(this).val() || '').trim();
            if (value.length > 0) {
                validateField($(this), 'El precio debe ser un número válido', function(value) {
                    return !isNaN(value);
                });
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#stock_minimo').on('blur', function() {
            validateField($(this), 'El stock mínimo es obligatorio y debe ser un número', function(value) {
                return (value || '').trim() !== '' && !isNaN(value);
            });
        });
        $('#categoria_id').on('blur change', function() {
            validateField($(this), 'Seleccione una categoría', function(value) {
                return (value || '').trim() !== '';
            });
        });
        $('#unidad_medida_id').on('blur change', function() {
            validateField($(this), 'Seleccione una unidad de medida', function(value) {
                return (value || '').trim() !== '';
            });
        });
        $('#nombre, #codigo_material, #descripcion, #precio_unitario, #stock_minimo, #categoria_id, #unidad_medida_id').on('focus', function() {
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
        if (!validateField($('#nombre'), 'El nombre es obligatorio y no puede exceder 100 caracteres', function(value) {
            var val = (value || '').trim();
            return val.length > 0 && val.length <= 100;
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#nombre');
        }
        if (!validateField($('#codigo_material'), 'El código es obligatorio y no puede exceder 50 caracteres', function(value) {
            var val = (value || '').trim();
            return val.length > 0 && val.length <= 50;
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#codigo_material');
        }
        if (($('#descripcion').val() || '').trim().length > 0 && !validateField($('#descripcion'), 'La descripción no puede exceder 255 caracteres', function(value) {
            return value.length <= 255;
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#descripcion');
        }
        if (($('#precio_unitario').val() || '').trim().length > 0 && !validateField($('#precio_unitario'), 'El precio debe ser un número válido', function(value) {
            return !isNaN(value);
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#precio_unitario');
        }
        if (!validateField($('#stock_minimo'), 'El stock mínimo es obligatorio y debe ser un número', function(value) {
            return (value || '').trim() !== '' && !isNaN(value);
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#stock_minimo');
        }
        if (!validateField($('#categoria_id'), 'Seleccione una categoría válida', function(value) {
            var $opt = $('#categoria_id option:selected');
            return (value || '').trim() !== '' && !$opt.is(':disabled');
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#categoria_id');
        }
        if (!validateField($('#unidad_medida_id'), 'Seleccione una unidad de medida válida', function(value) {
            var $opt = $('#unidad_medida_id option:selected');
            return (value || '').trim() !== '' && !$opt.is(':disabled');
        })) {
            isValid = false;
            if (!firstErrorField) firstErrorField = $('#unidad_medida_id');
        }
        if (firstErrorField) {
            firstErrorField.focus();
        }
        return isValid;
    }

    $(document).ready(function() {
        // Inicializar Select2 con placeholder para distinguir opción vacía
        $('#categoria_id').select2({
            dropdownParent: $('#materialModal'),
            placeholder: 'Seleccione una categoría',
            allowClear: true,
            language: {
                noResults: function() { return 'No hay resultados'; }
            }
        });
        $('#unidad_medida_id').select2({
            dropdownParent: $('#materialModal'),
            placeholder: 'Seleccione una unidad de medida',
            allowClear: true,
            language: {
                noResults: function() { return 'No hay resultados'; }
            }
        });

        // Cargar todos los selects dinámicamente al inicializar
        loadCategorias();
        loadUnidades();
        setupFormValidation();
        var table = $('#materialesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('materiales.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'categoria', name: 'categoria' },
                { data: 'nombre', name: 'nombre' },
                { data: 'descripcion', name: 'descripcion' },
                { data: 'unidad', name: 'unidad' },
                { data: 'precio_unitario', name: 'precio_unitario' },
                { data: 'stock_minimo', name: 'stock_minimo' },
                { data: 'codigo_material', name: 'codigo_material' },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
            columnDefs: [
                { targets: [8, 9], className: "text-center" }
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

        $('#btnNuevoMaterial').click(function() {
            $('#materialForm')[0].reset();
            $('#material_id').val('');
            $('#materialModalLabel').text('Nuevo Material');
            loadCategorias();
            loadUnidades();
            // Limpiar selects y mostrar placeholder
            setTimeout(function() {
                $('#categoria_id').val('').trigger('change');
                $('#unidad_medida_id').val('').trigger('change');
            }, 200);
            setupFormValidation();
        });

        // Si el usuario cambia el select y la opción es inactiva, eliminarla y mostrar placeholder
        $('#categoria_id').on('change', function() {
            var $opt = $(this).find('option:selected');
            if ($opt.is(':disabled')) {
                $(this).val('').trigger('change.select2');
                $opt.remove();
            }
        });
        $('#unidad_medida_id').on('change', function() {
            var $opt = $(this).find('option:selected');
            if ($opt.is(':disabled')) {
                $(this).val('').trigger('change.select2');
                $opt.remove();
            }
        });

        setupFormValidation();

        $('#materialForm').on('submit', function(e) {
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
            var id = $('#material_id').val();
            var url = id ? '/materiales/' + id : '/materiales';
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
                    $('#materialModal').modal('hide');
                    table.ajax.reload();
                    $('#materialForm')[0].reset();
                    $submitBtn.prop('disabled', false);
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Hubo un problema al guardar el registro.', 'error');
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        window.editMaterial = function(id) {
            Swal.fire({
                title: 'Cargando...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            $.get('/materiales/' + id, function(data) {
                Swal.close();
                $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
                $('.invalid-feedback').remove();
                $('#material_id').val(data.id);
                $('#nombre').val(data.nombre);
                $('#descripcion').val(data.descripcion);
                $('#precio_unitario').val(data.precio_unitario);
                $('#stock_minimo').val(data.stock_minimo);
                $('#codigo_material').val(data.codigo_material);
                
                // Cargar categorías y manejar selección
                loadCategorias(data.categoria_id).done(function() {
                    var catVal = data.categoria_id ? data.categoria_id.toString() : '';
                    if ($('#categoria_id option[value="'+catVal+'"], #categoria_id option[value='+catVal+']').length > 0) {
                        $('#categoria_id').val(catVal).trigger('change.select2');
                    } else if (catVal) {
                        var nombreCat = data.categoria && data.categoria.nombre ? data.categoria.nombre + ' (inactiva)' : 'Inactiva';
                        $('#categoria_id').append('<option value="'+catVal+'" selected>'+nombreCat+'</option>');
                        $('#categoria_id').val(catVal).trigger('change.select2');
                        setTimeout(function(){
                            $('#categoria_id option[value="'+catVal+'"]').prop('disabled', true);
                        }, 0);
                    } else {
                        $('#categoria_id').val('').trigger('change.select2');
                    }
                });
                
                // Cargar unidades y manejar selección
                loadUnidades(data.unidad_medida_id).done(function() {
                    var uniVal = data.unidad_medida_id ? data.unidad_medida_id.toString() : '';
                    if ($('#unidad_medida_id option[value="'+uniVal+'"], #unidad_medida_id option[value='+uniVal+']').length > 0) {
                        $('#unidad_medida_id').val(uniVal).trigger('change.select2');
                    } else if (uniVal) {
                        var nombreUni = data.unidad_medida && data.unidad_medida.nombre ? data.unidad_medida.nombre + ' (inactiva)' : 'Inactiva';
                        $('#unidad_medida_id').append('<option value="'+uniVal+'" selected>'+nombreUni+'</option>');
                        $('#unidad_medida_id').val(uniVal).trigger('change.select2');
                        setTimeout(function(){
                            $('#unidad_medida_id option[value="'+uniVal+'"]').prop('disabled', true);
                        }, 0);
                    } else {
                        $('#unidad_medida_id').val('').trigger('change.select2');
                    }
                });
                
                if (data.estado == 1) {
                    $('#estado').prop('checked', true);
                } else {
                    $('#estado').prop('checked', false);
                }
                $('#materialModalLabel').text('Editar Material');
                $('#materialModal').modal('show');
                setupFormValidation();
                
            }).fail(function() {
                Swal.close();
                Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
            });
        };

        window.deleteMaterial = function(id) {
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
                        url: '/materiales/' + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('Eliminado!', 'Material eliminado correctamente.', 'success');
                            $('#materialesTable').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Error', 'No se pudo eliminar el material', 'error');
                        }
                    });
                }
            });
        };

        // Limpiar validaciones al cerrar modal
        $('#materialModal').on('hidden.bs.modal', function () {
            $('#materialForm')[0].reset();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });
    </script>
@endsection
