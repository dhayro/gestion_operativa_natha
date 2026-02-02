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
        
        /* Badge styling */
        .badge-success {
            background-color: #1abc9c !important;
            color: white !important;
        }
        .badge-danger {
            background-color: #e74c3c !important;
            color: white !important;
        }
        
        /* Readonly/Disabled input styling */
        .form-control:disabled,
        .form-control[readonly] {
            font-weight: bold;
            background-color: #f8f9fa !important;
            color: #212529 !important;
            cursor: not-allowed;
        }
        
        /* Google Places Autocomplete styling */
        .pac-container {
            z-index: 10000 !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .pac-item {
            padding: 8px 10px !important;
            cursor: pointer;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .pac-item:hover {
            background-color: #f0f0f0 !important;
        }
        
        .pac-item-selected {
            background-color: #1abc9c !important;
            color: white;
        }
        
        .pac-matched {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de Suministros</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#suministroModal" id="btnNuevoSuministro">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Nuevo Suministro</span>
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="suministroTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Ruta</th>
                                <th>Medidor</th>
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

<!-- Modal para crear/editar suministro -->
<div class="modal fade" id="suministroModal" tabindex="-1" aria-labelledby="suministroModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="suministroForm" action="" method="POST">
                @csrf
                <input type="hidden" id="suministro_id" name="suministro_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="suministroModalLabel">Crear/Editar Suministro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="codigo" name="codigo" maxlength="50" placeholder="Código del suministro" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del suministro" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ruta" class="form-label">Ruta</label>
                            <input type="text" class="form-control" id="ruta" name="ruta" maxlength="50" placeholder="Número de ruta">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="caja" class="form-label">Caja</label>
                            <input type="text" class="form-control" id="caja" name="caja" maxlength="50" placeholder="Número de caja">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="referencia" class="form-label">Referencia</label>
                            <input type="text" class="form-control" id="referencia" name="referencia" placeholder="Referencia o punto de interés">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tarifa" class="form-label">Tarifa</label>
                            <input type="text" class="form-control" id="tarifa" name="tarifa" maxlength="50" placeholder="Tipo de tarifa">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="serie" class="form-label">Serie</label>
                            <input type="text" class="form-control" id="serie" name="serie" maxlength="50" placeholder="Número de serie">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="medidor_id" class="form-label">Medidor</label>
                            <select class="form-control select2" id="medidor_id" name="medidor_id">
                                <option value="">Seleccione un medidor</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departamento_id" class="form-label">Departamento</label>
                            <select class="form-control select2" id="departamento_id" name="departamento_id">
                                <option value="">Seleccione un departamento</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="provincia_id" class="form-label">Provincia</label>
                            <select class="form-control select2" id="provincia_id" name="provincia_id" disabled>
                                <option value="">Seleccione una provincia</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ubigeo_id" class="form-label">Distrito</label>
                            <select class="form-control select2" id="ubigeo_id" name="ubigeo_id" disabled>
                                <option value="">Seleccione un distrito</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitud" class="form-label">Latitud</label>
                            <input type="text" class="form-control" id="latitud" name="latitud" maxlength="50" placeholder="Coordenada de latitud" readonly>
                            <small class="form-text text-muted">Haz clic en el mapa para obtener coordenadas</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitud" class="form-label">Longitud</label>
                            <input type="text" class="form-control" id="longitud" name="longitud" maxlength="50" placeholder="Coordenada de longitud" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="direccion" class="form-label">🏠 Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Escribe la calle, avenida o lugar (Google Places Autocomplete)">
                            <small class="form-text text-muted">Escribe la dirección y selecciona de las sugerencias</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">📍 Ubicación en Mapa</label>
                            <div id="suministroMap" style="width: 100%; height: 400px; border: 2px solid #ddd; border-radius: 8px;"></div>
                            <small class="form-text text-muted mt-2 d-block">Haz clic en el mapa para marcar la ubicación del suministro</small>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBx6-6asBthkJUP0RJwfEhEi9ug9z4bCg&libraries=places"></script>
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script>
    var suministroTable;
    var suministroMap;
    var suministroMarker;
    var currentMarker = null;
    var autocompletePlace;
    
    // Coordenadas de Pucallpa
    const PUCALLPA_CENTER = { lat: -8.3789, lng: -74.5234 };

    function initMap() {
        suministroMap = new google.maps.Map(document.getElementById('suministroMap'), {
            zoom: 14,
            center: PUCALLPA_CENTER,
            mapTypeId: 'roadmap',
            gestureHandling: 'greedy'
        });

        // Inicializar Google Places Autocomplete
        const directionInput = document.getElementById('direccion');
        autocompletePlace = new google.maps.places.Autocomplete(directionInput, {
            componentRestrictions: { country: 'pe' }, // Restringir a Perú
            types: ['geocode', 'establishment'],
            fields: ['geometry', 'formatted_address', 'address_components']
        });

        autocompletePlace.bindTo('bounds', suministroMap);

        // Listener cuando se selecciona un lugar del autocomplete
        autocompletePlace.addListener('place_changed', function() {
            const place = autocompletePlace.getPlace();

            if (!place.geometry) {
                console.log("No geometry found for the selected place");
                return;
            }

            // Obtener coordenadas
            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();

            // Actualizar inputs de coordenadas
            $('#latitud').val(lat.toFixed(6));
            $('#longitud').val(lng.toFixed(6));

            // Actualizar dirección completa
            $('#direccion').val(place.formatted_address);

            // Eliminar marcador anterior
            if (currentMarker) {
                currentMarker.setMap(null);
            }

            // Crear nuevo marcador
            currentMarker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: suministroMap,
                title: place.formatted_address,
                animation: google.maps.Animation.DROP
            });

            // Info window
            const infoWindow = new google.maps.InfoWindow({
                content: `<div style="text-align: center; font-size: 12px;">
                    <strong>Ubicación</strong><br>
                    ${place.formatted_address}<br>
                    <br>
                    <strong>Coordenadas:</strong><br>
                    Lat: ${lat.toFixed(6)}<br>
                    Lng: ${lng.toFixed(6)}
                </div>`
            });

            currentMarker.addListener('click', function() {
                infoWindow.open(suministroMap, currentMarker);
            });

            infoWindow.open(suministroMap, currentMarker);

            // Centrar y zoom al lugar
            suministroMap.setCenter({ lat: lat, lng: lng });
            suministroMap.setZoom(17);
        });

        // Event listener para click directo en el mapa
        suministroMap.addListener('click', function(event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            
            // Actualizar inputs
            $('#latitud').val(lat.toFixed(6));
            $('#longitud').val(lng.toFixed(6));
            
            // Eliminar marcador anterior
            if (currentMarker) {
                currentMarker.setMap(null);
            }
            
            // Crear nuevo marcador
            currentMarker = new google.maps.Marker({
                position: event.latLng,
                map: suministroMap,
                title: `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`,
                animation: google.maps.Animation.DROP
            });
            
            // Info window
            const infoWindow = new google.maps.InfoWindow({
                content: `<div style="text-align: center;">
                    <strong>Ubicación del Suministro</strong><br>
                    Lat: ${lat.toFixed(6)}<br>
                    Lng: ${lng.toFixed(6)}
                </div>`
            });
            
            currentMarker.addListener('click', function() {
                infoWindow.open(suministroMap, currentMarker);
            });
            
            infoWindow.open(suministroMap, currentMarker);
        });
    }
    
    // Cargar marcador existente en el mapa
    function cargarMarcadorExistente(lat, lng) {
        if (lat && lng && lat !== '' && lng !== '') {
            const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
            
            // Eliminar marcador anterior
            if (currentMarker) {
                currentMarker.setMap(null);
            }
            
            // Crear marcador
            currentMarker = new google.maps.Marker({
                position: position,
                map: suministroMap,
                title: `Lat: ${lat}, Lng: ${lng}`,
                animation: google.maps.Animation.DROP
            });
            
            // Centrar mapa en el marcador
            suministroMap.setCenter(position);
            suministroMap.setZoom(16);
            
            // Info window
            const infoWindow = new google.maps.InfoWindow({
                content: `<div style="text-align: center;">
                    <strong>Ubicación del Suministro</strong><br>
                    Lat: ${lat}<br>
                    Lng: ${lng}
                </div>`
            });
            
            currentMarker.addListener('click', function() {
                infoWindow.open(suministroMap, currentMarker);
            });
            
            infoWindow.open(suministroMap, currentMarker);
        }
    }

    function setupFormValidation() {
        $('#codigo, #nombre').off('blur focus');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();

        $('#codigo').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length === 0) {
                validateField($(this), 'El código es obligatorio', function(value) {
                    return value.length > 0;
                });
            } else if (value.length > 50) {
                validateField($(this), 'El código no puede exceder 50 caracteres', function(value) {
                    return value.length <= 50;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        $('#nombre').on('blur', function() {
            var value = $(this).val().trim();
            if (value.length === 0) {
                validateField($(this), 'El nombre es obligatorio', function(value) {
                    return value.length > 0;
                });
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        $('#codigo, #nombre').on('focus', function() {
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
        var codigo = $('#codigo').val().trim();
        var nombre = $('#nombre').val().trim();
        var isValid = true;

        if (codigo.length === 0) {
            validateField($('#codigo'), 'El código es obligatorio', function(value) {
                return value.length > 0;
            });
            isValid = false;
        }

        if (nombre.length === 0) {
            validateField($('#nombre'), 'El nombre es obligatorio', function(value) {
                return value.length > 0;
            });
            isValid = false;
        }

        return isValid;
    }

    function loadSuministrosTable() {
        if ($.fn.DataTable.isDataTable('#suministroTable')) {
            suministroTable.destroy();
        }

        suministroTable = $('#suministroTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('suministro.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'codigo', name: 'codigo', orderable: true, searchable: true },
                { data: 'nombre', name: 'nombre', orderable: true, searchable: true },
                { data: 'ruta', name: 'ruta', orderable: false, searchable: false },
                { data: 'medidor_serie', name: 'medidor_serie', orderable: false, searchable: false },
                { data: 'ubigeo_nombre', name: 'ubigeo_nombre', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[1, "asc"]],
            columnDefs: [
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
    }

    function loadMedidores(suministroId = null) {
        var url = '{{ route('suministro.medidores') }}';
        if (suministroId) {
            url += '?suministro_id=' + suministroId;
        }
        
        return $.get(url)
            .done(function(data) {
                var select = $('#medidor_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${select.data('selected') == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#suministroModal'),
                    placeholder: 'Buscar y seleccionar medidor...',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar medidores');
            });
    }
    
    function loadMedidoresAndSelect(suministroId, medidorId) {
        var url = '{{ route('suministro.medidores') }}?suministro_id=' + suministroId;
        
        return $.get(url)
            .done(function(data) {
                var select = $('#medidor_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}" ${medidorId == item.id ? 'selected' : ''}>${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#suministroModal'),
                    placeholder: 'Buscar y seleccionar medidor...',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
                console.log('Medidor seleccionado:', medidorId);
            })
            .fail(function() {
                console.error('Error al cargar medidores');
            });
    }

    function loadDepartamentos() {
        return $.get('{{ route('suministro.departamentos') }}')
            .done(function(data) {
                var select = $('#departamento_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}">${item.text}</option>`);
                });
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#suministroModal'),
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
                dropdownParent: $('#suministroModal'),
                placeholder: 'Seleccionar provincia',
                allowClear: true,
                language: { noResults: function() { return 'No hay resultados'; } }
            });
            $('#ubigeo_id').empty().append('<option value="">Seleccione</option>').prop('disabled', true).select2({
                dropdownParent: $('#suministroModal'),
                placeholder: 'Seleccionar distrito',
                allowClear: true,
                language: { noResults: function() { return 'No hay resultados'; } }
            });
            return;
        }

        return $.get('{{ route('suministro.provincias', ':id') }}'.replace(':id', departamentoId))
            .done(function(data) {
                var select = $('#provincia_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}">${item.text}</option>`);
                });
                select.prop('disabled', false);
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#suministroModal'),
                    placeholder: 'Seleccionar provincia',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
                $('#ubigeo_id').empty().append('<option value="">Seleccione</option>').prop('disabled', true).select2({
                    dropdownParent: $('#suministroModal'),
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
                dropdownParent: $('#suministroModal'),
                placeholder: 'Seleccionar distrito',
                allowClear: true,
                language: { noResults: function() { return 'No hay resultados'; } }
            });
            return;
        }

        return $.get('{{ route('suministro.distritos', ':id') }}'.replace(':id', provinciaId))
            .done(function(data) {
                var select = $('#ubigeo_id');
                select.empty().append('<option value="">Seleccione</option>');
                $.each(data, function(index, item) {
                    select.append(`<option value="${item.id}">${item.text}</option>`);
                });
                select.prop('disabled', false);
                // Re-inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#suministroModal'),
                    placeholder: 'Seleccionar distrito',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
            })
            .fail(function() {
                console.error('Error al cargar distritos');
            });
    }

    $(document).ready(function() {
        // Inicializar Select2 para todos los selects
        $('#medidor_id').select2({
            dropdownParent: $('#suministroModal'),
            placeholder: 'Buscar y seleccionar medidor...',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });
        $('#departamento_id').select2({
            dropdownParent: $('#suministroModal'),
            placeholder: 'Seleccionar departamento',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });
        $('#provincia_id').select2({
            dropdownParent: $('#suministroModal'),
            placeholder: 'Seleccionar provincia',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });
        $('#ubigeo_id').select2({
            dropdownParent: $('#suministroModal'),
            placeholder: 'Seleccionar distrito',
            allowClear: true,
            language: { noResults: function() { return 'No hay resultados'; } }
        });
        
        setupFormValidation();
        loadSuministrosTable();
        loadMedidores();
        loadDepartamentos();
        initMap(); // Inicializar mapa

        $('#btnNuevoSuministro').click(function() {
            $('#suministroForm')[0].reset();
            $('#suministro_id').val('');
            $('#estado').prop('checked', true);
            $('#departamento_id').val('');
            $('#latitud').val('');
            $('#longitud').val('');
            $('#provincia_id').empty().append('<option value="">Seleccione una provincia</option>').prop('disabled', true);
            $('#ubigeo_id').empty().append('<option value="">Seleccione un distrito</option>').prop('disabled', true);
            
            // Eliminar marcador y centrar en Pucallpa
            if (currentMarker) {
                currentMarker.setMap(null);
                currentMarker = null;
            }
            suministroMap.setCenter(PUCALLPA_CENTER);
            suministroMap.setZoom(14);
            
            // Limpiar medidor y mostrar placeholder
            loadMedidores();
            setTimeout(function() {
                $('#medidor_id').val('').trigger('change');
            }, 200);
            
            $('#suministroModalLabel').text('Crear Nuevo Suministro');
            setupFormValidation();
        });

        $('#departamento_id').on('change', function() {
            var departamentoId = $(this).val();
            loadProvincias(departamentoId);
        });

        $('#provincia_id').on('change', function() {
            var provinciaId = $(this).val();
            loadDistritos(provinciaId);
        });

        $('#suministroForm').on('submit', function(e) {
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

            var id = $('#suministro_id').val();
            var url = id ? '/suministro/' + id : '/suministro';
            var method = id ? 'PUT' : 'POST';

            var formData = {
                codigo: $('#codigo').val(),
                nombre: $('#nombre').val(),
                ruta: $('#ruta').val(),
                direccion: $('#direccion').val(),
                referencia: $('#referencia').val(),
                caja: $('#caja').val(),
                tarifa: $('#tarifa').val(),
                latitud: $('#latitud').val(),
                longitud: $('#longitud').val(),
                serie: $('#serie').val(),
                medidor_id: $('#medidor_id').val() ? $('#medidor_id').val() : null,
                ubigeo_id: $('#ubigeo_id').val() ? $('#ubigeo_id').val() : null,
                estado: estadoChecked ? 1 : 0,
                _token: '{{ csrf_token() }}'
            };
            
            console.log('Datos enviados:', formData);

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
                    $('#suministroModal').modal('hide');
                    loadSuministrosTable();
                    loadMedidores();
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

        // Redimensionar mapa cuando se abre el modal
        $('#suministroModal').on('shown.bs.modal', function () {
            if (suministroMap) {
                google.maps.event.trigger(suministroMap, 'resize');
                
                // Si no hay coordenadas guardadas (crear nuevo), centrar en Pucallpa
                var latitud = $('#latitud').val();
                var longitud = $('#longitud').val();
                
                if (!latitud || !longitud || latitud === '' || longitud === '') {
                    suministroMap.setCenter(PUCALLPA_CENTER);
                    suministroMap.setZoom(14);
                }
                // Si hay coordenadas, el marcador ya está posicionado correctamente
            }
        });

        $('#suministroModal').on('hidden.bs.modal', function () {
            $('#suministroForm')[0].reset();
            $('#estado').prop('checked', true);
            $('#departamento_id').val('');
            $('#latitud').val('');
            $('#longitud').val('');
            $('#provincia_id').empty().append('<option value="">Seleccione una provincia</option>').prop('disabled', true);
            $('#ubigeo_id').empty().append('<option value="">Seleccione un distrito</option>').prop('disabled', true);
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
        });
    });

    window.editSuministro = function(id) {
        $.get('/suministro/' + id, function(data) {
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();

            $('#suministro_id').val(data.id);
            $('#codigo').val(data.codigo || '');
            $('#nombre').val(data.nombre || '');
            $('#ruta').val(data.ruta || '');
            $('#direccion').val(data.direccion || '');
            $('#referencia').val(data.referencia || '');
            $('#caja').val(data.caja || '');
            $('#tarifa').val(data.tarifa || '');
            $('#latitud').val(data.latitud || '');
            $('#longitud').val(data.longitud || '');
            $('#serie').val(data.serie || '');
            
            // Cargar medidores PRIMERO antes de asignar el valor
            if (data.id) {
                loadMedidoresAndSelect(data.id, data.medidor_id);
            } else {
                $('#medidor_id').val(data.medidor_id || '');
            }
            
            // Cargar marcador en el mapa si existen coordenadas
            if (data.latitud && data.longitud) {
                cargarMarcadorExistente(data.latitud, data.longitud);
            } else {
                // Si no hay coordenadas, centrar en Pucallpa
                if (currentMarker) {
                    currentMarker.setMap(null);
                    currentMarker = null;
                }
                suministroMap.setCenter(PUCALLPA_CENTER);
                suministroMap.setZoom(14);
            }

            if (data.estado == 1 || data.estado === true) {
                $('#estado').prop('checked', true);
            } else {
                $('#estado').prop('checked', false);
            }

            // Cargar la jerarquía del ubigeo si existe
            if (data.ubigeo_id) {
                $.get('/suministro/ubigeo-jerarquia/' + data.ubigeo_id, function(hierarquia) {
                    if (hierarquia.success) {
                        // Cargar departamentos
                        $.get('{{ route('suministro.departamentos') }}', function(departamentos) {
                            $('#departamento_id').empty().append('<option value="">Seleccione un departamento</option>');
                            $.each(departamentos, function(index, item) {
                                $('#departamento_id').append(`<option value="${item.id}">${item.text}</option>`);
                            });
                            
                            // Seleccionar el departamento
                            if (hierarquia.departamento_id) {
                                $('#departamento_id').val(hierarquia.departamento_id);
                                
                                // Cargar provincias después de seleccionar departamento
                                $.get('{{ route('suministro.provincias', ':id') }}'.replace(':id', hierarquia.departamento_id), function(provincias) {
                                    $('#provincia_id').empty().append('<option value="">Seleccione una provincia</option>');
                                    $.each(provincias, function(index, item) {
                                        $('#provincia_id').append(`<option value="${item.id}">${item.text}</option>`);
                                    });
                                    $('#provincia_id').prop('disabled', false);
                                    
                                    // Seleccionar la provincia
                                    if (hierarquia.provincia_id) {
                                        $('#provincia_id').val(hierarquia.provincia_id);
                                        
                                        // Cargar distritos después de seleccionar provincia
                                        $.get('{{ route('suministro.distritos', ':id') }}'.replace(':id', hierarquia.provincia_id), function(distritos) {
                                            $('#ubigeo_id').empty().append('<option value="">Seleccione un distrito</option>');
                                            $.each(distritos, function(index, item) {
                                                $('#ubigeo_id').append(`<option value="${item.id}">${item.text}</option>`);
                                            });
                                            $('#ubigeo_id').prop('disabled', false);
                                            
                                            // Seleccionar el distrito - AQUÍ es donde se carga la data
                                            $('#ubigeo_id').val(hierarquia.distrito_id);
                                            console.log('Distrito seleccionado:', hierarquia.distrito_id);
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
                
                // Cargar medidores incluyendo el actual
                loadMedidores(data.id);
            }

            $('#suministroModalLabel').text('Editar Suministro');
            $('#suministroModal').modal('show');
            setupFormValidation();
        }).fail(function() {
            Swal.fire('Error!', 'No se pudieron cargar los datos del registro.', 'error');
        });
    };

    window.deleteSuministro = function(id) {
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
                    url: '/suministro/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Eliminado!', 'Suministro eliminado correctamente.', 'success');
                        loadSuministrosTable();
                        loadMedidores();
                    },
                    error: function(xhr) {
                        var message = 'No se pudo eliminar el suministro';
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
