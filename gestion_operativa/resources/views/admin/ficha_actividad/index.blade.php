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
        
        /* Readonly/Disabled input styling */
        .form-control:disabled,
        .form-control[readonly] {
            font-weight: bold;
            background-color: #f8f9fa !important;
            color: #212529 !important;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de Fichas de Actividad</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fichaModal" id="btnNuevaFicha">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Nueva Ficha de Actividad</span>
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="fichaTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Suministro</th>
                                <th>Tipo Actividad</th>
                                <th>Piso</th>
                                <th>Fecha</th>
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

<!-- Modal para crear/editar ficha -->
<div class="modal fade" id="fichaModal" tabindex="-1" aria-labelledby="fichaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="fichaForm" action="" method="POST">
                @csrf
                <input type="hidden" id="ficha_id" name="ficha_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="fichaModalLabel">Crear/Editar Ficha de Actividad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="suministro_id" class="form-label">Suministro <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="suministro_id" name="suministro_id" required>
                                <option value="">Seleccione un suministro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo_actividad_id" class="form-label">Tipo de Actividad <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="tipo_actividad_id" name="tipo_actividad_id" required>
                                <option value="">Seleccione un tipo</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="numero_piso" class="form-label">Número de Piso</label>
                            <input type="text" class="form-control" id="numero_piso" name="numero_piso" maxlength="10" placeholder="Ej: 3, 4A, PB">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="datetime-local" class="form-control" id="fecha" name="fecha">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_propiedad_id" class="form-label">Tipo de Propiedad</label>
                            <select class="form-control select2" id="tipo_propiedad_id" name="tipo_propiedad_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="construccion_id" class="form-label">Construcción</label>
                            <select class="form-control select2" id="construccion_id" name="construccion_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uso_id" class="form-label">Uso</label>
                            <select class="form-control select2" id="uso_id" name="uso_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="situacion_id" class="form-label">Situación</label>
                            <select class="form-control select2" id="situacion_id" name="situacion_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="situacion_detalle" class="form-label">Detalle de Situación</label>
                            <input type="text" class="form-control" id="situacion_detalle" name="situacion_detalle" maxlength="100" placeholder="Descripción detallada">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="servicio_electrico_id" class="form-label">Servicio Eléctrico</label>
                            <select class="form-control select2" id="servicio_electrico_id" name="servicio_electrico_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="documento" class="form-label">Documento</label>
                            <input type="text" class="form-control" id="documento" name="documento" maxlength="100" placeholder="Referencia">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="suministro_derecho" class="form-label">Suministro Derecho</label>
                            <input type="text" class="form-control" id="suministro_derecho" name="suministro_derecho" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="suministro_izquierdo" class="form-label">Suministro Izquierdo</label>
                            <input type="text" class="form-control" id="suministro_izquierdo" name="suministro_izquierdo" maxlength="50">
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
                        <div class="col-md-6 mb-3">
                            <label for="latitud" class="form-label">📍 Latitud</label>
                            <input type="text" class="form-control" id="latitud" name="latitud" maxlength="50" placeholder="Ej: -8.3789" readonly>
                            <small class="form-text text-muted">Haz clic en el mapa para establecer</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitud" class="form-label">📍 Longitud</label>
                            <input type="text" class="form-control" id="longitud" name="longitud" maxlength="50" placeholder="Ej: -74.5234" readonly>
                            <small class="form-text text-muted">Haz clic en el mapa para establecer</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">🗺️ Ubicación en Mapa</label>
                            <div id="fichaMap" style="width: 100%; height: 350px; border: 2px solid #007bff; border-radius: 8px; background: #f0f0f0;"></div>
                            <small class="form-text text-muted mt-2">Haz clic en el mapa para seleccionar la ubicación</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea class="form-control" id="observacion" name="observacion" rows="3" placeholder="Notas adicionales"></textarea>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBx6-6asBthkJUP0RJwfEhEi9ug9z4bCg&libraries=places"></script>

<script>
    let tableRecords = null;
    let fichaMap;
    let fichaMapMarker = null;
    let autocompletePlace = null;
    
    // Coordenadas de Pucallpa (por defecto)
    const PUCALLPA_CENTER = { lat: -8.3789, lng: -74.5234 };

    $(document).ready(function() {
        configurarSelect2();
        initDataTable();
        cargarOpciones();
        configurarFormulario();
    });



    function configurarSelect2() {
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('#fichaModal')
        });
    }

    function initDataTable() {
        if ($.fn.dataTable.isDataTable('#fichaTable')) {
            tableRecords.destroy();
        }

        tableRecords = $('#fichaTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('fichas_actividad.getData') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'suministro_nombre', name: 'suministro_nombre' },
                { data: 'tipo_actividad', name: 'tipo_actividad' },
                { data: 'numero_piso', name: 'numero_piso', defaultContent: '-' },
                { data: 'fecha_formateada', name: 'fecha_formateada' },
                { data: 'estado_badge', name: 'estado_badge', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[4, 'desc']],
            pageLength: 25,
            language: {
                url: "{{ asset('plugins/src/table/datatable/i18n/es-ES.json') }}"
            }
        });
    }

    function cargarOpciones() {
        // Suministros
        $.get('/suministro/api/select', function(data) {
            var select = $('#suministro_id');
            select.empty().append('<option value="">Seleccione un suministro</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.nombre}</option>`);
            });
        });

        // Tipos de Actividad
        $.get('/tipos-actividad/select', function(data) {
            var select = $('#tipo_actividad_id');
            select.empty().append('<option value="">Seleccione un tipo</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.nombre}</option>`);
            });
        });

        // Tipos de Propiedad
        $.get('/tipo_propiedades/select', function(data) {
            var select = $('#tipo_propiedad_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });

        // Construcciones
        $.get('/construcciones/select', function(data) {
            var select = $('#construccion_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });

        // Usos
        $.get('/usos/select', function(data) {
            var select = $('#uso_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });

        // Situaciones
        $.get('/situaciones/select', function(data) {
            var select = $('#situacion_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });

        // Servicios Eléctricos
        $.get('/servicios-electricos/select', function(data) {
            var select = $('#servicio_electrico_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });
    }

    function configurarFormulario() {
        $('#fichaForm').on('submit', function(e) {
            e.preventDefault();
            guardarFicha();
        });

        $('#btnNuevaFicha').on('click', function() {
            limpiarFormulario();
        });

        // Mostrar modal limpio al cerrarlo
        $('#fichaModal').on('hidden.bs.modal', function() {
            limpiarFormulario();
        });

        // Inicializar mapa cuando se muestra el modal
        $('#fichaModal').on('shown.bs.modal', function() {
            if (!fichaMap) {
                inicializarMapa();
            }
            // Asegurar que el mapa se redibuje correctamente
            google.maps.event.trigger(fichaMap, 'resize');
            fichaMap.setCenter(PUCALLPA_CENTER);
            fichaMap.setZoom(14);
        });
    }

    function limpiarFormulario() {
        $('#ficha_id').val('');
        $('#fichaForm')[0].reset();
        $('#fichaForm').attr('action', '{{ route("fichas_actividad.store") }}');
        $('.select2').val('').trigger('change');
        $('#fichaModalLabel').text('Crear Ficha de Actividad');
        $('#estado').prop('checked', true);
        $('#latitud').val('');
        $('#longitud').val('');
    }

    function guardarFicha() {
        const formData = {
            tipo_actividad_id: $('#tipo_actividad_id').val(),
            suministro_id: $('#suministro_id').val(),
            tipo_propiedad_id: $('#tipo_propiedad_id').val() || null,
            construccion_id: $('#construccion_id').val() || null,
            servicio_electrico_id: $('#servicio_electrico_id').val() || null,
            uso_id: $('#uso_id').val() || null,
            numero_piso: $('#numero_piso').val() || null,
            situacion_id: $('#situacion_id').val() || null,
            situacion_detalle: $('#situacion_detalle').val() || null,
            suministro_derecho: $('#suministro_derecho').val() || null,
            suministro_izquierdo: $('#suministro_izquierdo').val() || null,
            direccion: $('#direccion').val() || null,
            latitud: $('#latitud').val() || null,
            longitud: $('#longitud').val() || null,
            observacion: $('#observacion').val() || null,
            documento: $('#documento').val() || null,
            fecha: $('#fecha').val() || null,
            estado: $('#estado').is(':checked') ? 1 : 0
        };

        const id = $('#ficha_id').val();
        const url = id ? `{{ url('fichas-actividad') }}/${id}` : '{{ route("fichas_actividad.store") }}';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(formData),
            success: function(response) {
                Swal.fire('Éxito', response.message || 'Guardado correctamente', 'success');
                $('#fichaModal').modal('hide');
                tableRecords.ajax.reload();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'No se pudo guardar la ficha';
                Swal.fire('Error', msg, 'error');
            }
        });
    }

    function editarFicha(id) {
        $.ajax({
            url: `{{ url('fichas-actividad') }}/${id}`,
            type: 'GET',
            success: function(response) {
                const ficha = response.data;
                $('#ficha_id').val(ficha.id);
                $('#suministro_id').val(ficha.suministro_id).trigger('change');
                $('#tipo_actividad_id').val(ficha.tipo_actividad_id).trigger('change');
                $('#tipo_propiedad_id').val(ficha.tipo_propiedad_id).trigger('change');
                $('#construccion_id').val(ficha.construccion_id).trigger('change');
                $('#servicio_electrico_id').val(ficha.servicio_electrico_id).trigger('change');
                $('#uso_id').val(ficha.uso_id).trigger('change');
                $('#numero_piso').val(ficha.numero_piso || '');
                $('#situacion_id').val(ficha.situacion_id).trigger('change');
                $('#situacion_detalle').val(ficha.situacion_detalle || '');
                $('#suministro_derecho').val(ficha.suministro_derecho || '');
                $('#suministro_izquierdo').val(ficha.suministro_izquierdo || '');
                $('#direccion').val(ficha.direccion || '');
                $('#latitud').val(ficha.latitud || '');
                $('#longitud').val(ficha.longitud || '');
                $('#observacion').val(ficha.observacion || '');
                $('#documento').val(ficha.documento || '');
                $('#estado').prop('checked', ficha.estado ? true : false);
                if (ficha.fecha) {
                    $('#fecha').val(ficha.fecha.substring(0, 16));
                }
                $('#fichaModalLabel').text('Editar Ficha de Actividad');
                $('#fichaModal').modal('show');
                // Inicializar y cargar mapa después de mostrar el modal
                setTimeout(function() {
                    if (!fichaMap) {
                        inicializarMapa();
                    }
                    cargarMarcadorExistente(ficha.latitud, ficha.longitud);
                }, 500);
            },
            error: function() {
                Swal.fire('Error', 'No se pudo cargar la ficha', 'error');
            }
        });
    }

    function eliminarFicha(id) {
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#e74c3c'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('fichas-actividad') }}/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Eliminado', response.message || 'Eliminado correctamente', 'success');
                        tableRecords.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }
                });
            }
        });
    }



    // Funciones para el mapa
    function inicializarMapa() {
        // Centro en Pucallpa por defecto
        let ubicacionInicial = PUCALLPA_CENTER;
        let zoomInicial = 14;
        
        fichaMap = new google.maps.Map(document.getElementById('fichaMap'), {
            zoom: zoomInicial,
            center: ubicacionInicial,
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

        autocompletePlace.bindTo('bounds', fichaMap);

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
            if (fichaMapMarker) {
                fichaMapMarker.setMap(null);
            }

            // Crear nuevo marcador
            fichaMapMarker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: fichaMap,
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

            fichaMapMarker.addListener('click', function() {
                infoWindow.open(fichaMap, fichaMapMarker);
            });

            infoWindow.open(fichaMap, fichaMapMarker);

            // Centrar y zoom al lugar
            fichaMap.setCenter({ lat: lat, lng: lng });
            fichaMap.setZoom(17);
        });

        // Listener para click directo en el mapa
        fichaMap.addListener('click', function(event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            
            // Actualizar inputs
            $('#latitud').val(lat.toFixed(6));
            $('#longitud').val(lng.toFixed(6));
            
            // Eliminar marcador anterior
            if (fichaMapMarker) {
                fichaMapMarker.setMap(null);
            }
            
            // Crear nuevo marcador
            fichaMapMarker = new google.maps.Marker({
                position: event.latLng,
                map: fichaMap,
                title: `✓ Capturado: ${lat.toFixed(6)}, ${lng.toFixed(6)}`,
                animation: google.maps.Animation.DROP
            });
            
            // Info window
            const infoWindow = new google.maps.InfoWindow({
                content: `<div style="text-align: center; font-weight: bold;">
                 <strong>Ubicación</strong><br>
                    Lat: ${lat.toFixed(6)}<br>
                    Lng: ${lng.toFixed(6)}
                </div>`
            });
            
            fichaMapMarker.addListener('click', function() {
                infoWindow.open(fichaMap, fichaMapMarker);
            });
            
            infoWindow.open(fichaMap, fichaMapMarker);
        });
    }

    // Cargar marcador existente en el mapa
    function cargarMarcadorExistente(lat, lng) {
        if (lat && lng && lat !== '' && lng !== '') {
            const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
            
            // Eliminar marcador anterior
            if (fichaMapMarker) {
                fichaMapMarker.setMap(null);
            }
            
            // Crear nuevo marcador
            fichaMapMarker = new google.maps.Marker({
                position: position,
                map: fichaMap,
                title: `Lat: ${lat}, Lng: ${lng}`,
                animation: google.maps.Animation.DROP
            });
            
            // Info window
            const infoWindow = new google.maps.InfoWindow({
                content: `<div style="text-align: center;">
                    <strong>Ubicación</strong><br>
                    Lat: ${lat}<br>
                    Lng: ${lng}
                </div>`
            });
            
            fichaMapMarker.addListener('click', function() {
                infoWindow.open(fichaMap, fichaMapMarker);
            });
            
            infoWindow.open(fichaMap, fichaMapMarker);
            
            // Centrar y zoom
            fichaMap.setCenter(position);
            fichaMap.setZoom(16);
        } else {
            // Centrar en Pucallpa si no hay coordenadas
            fichaMap.setCenter(PUCALLPA_CENTER);
            fichaMap.setZoom(14);
        }
    }
</script>
@endsection
