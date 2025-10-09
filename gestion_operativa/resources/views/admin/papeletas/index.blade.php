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
        .badge-warning {
            background-color: #f39c12 !important;
            color: white !important;
        }
        .badge-info {
            background-color: #3498db !important;
            color: white !important;
        }
        .badge-danger {
            background-color: #e74c3c !important;
            color: white !important;
        }
        .badge-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }

        /* Modal responsive styling */
        .modal-xl {
            max-width: 1200px;
        }
        
        .papeleta-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .status-timeline {
            border-left: 3px solid #dee2e6;
            padding-left: 20px;
            margin-left: 10px;
        }
        
        .status-timeline .status-item {
            position: relative;
            padding-bottom: 20px;
        }
        
        .status-timeline .status-item:before {
            content: '';
            position: absolute;
            left: -27px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dee2e6;
        }
        
        .status-timeline .status-item.completed:before {
            background: #28a745;
        }
        
        .status-timeline .status-item.current:before {
            background: #ffc107;
        }

        /* Estilos para modales de PDF */
        .modal-xl .modal-body {
            padding: 0;
        }

        .modal-xl iframe {
            width: 100%;
            height: 70vh;
            border: none;
        }

        @media (max-width: 768px) {
            .modal-xl iframe {
                height: 60vh;
            }
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de Papeletas</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#papeletaModal" id="btnNuevaPapeleta">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nueva Papeleta
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="papeletasTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Correlativo</th>
                                <th>Fecha</th>
                                <th>Vehículo</th>
                                <th>Cuadrilla</th>
                                <th>Destino</th>
                                <th>Km Recorridos</th>
                                <th>Estado Operación</th>
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

<!-- Modal para crear/editar papeleta -->
<div class="modal fade" id="papeletaModal" tabindex="-1" aria-labelledby="papeletaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="papeletaForm" action="" method="POST">
                @csrf
                <input type="hidden" id="papeleta_id" name="papeleta_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="papeletaModalLabel">Crear/Editar Papeleta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="asignacion_vehiculo_id" class="form-label">Vehículo Asignado <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="asignacion_vehiculo_id" name="asignacion_vehiculo_id" required>
                                <option value="">Seleccione un vehículo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="destino" class="form-label">Destino <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="destino" name="destino" maxlength="255" placeholder="Ingrese el destino" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="motivo" class="form-label">Motivo <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="motivo" name="motivo" rows="3" placeholder="Ingrese el motivo del viaje" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="km_salida" class="form-label">Kilómetros de Salida <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="km_salida" name="km_salida" step="0.001" min="0" placeholder="0.000" required>
                                <span class="input-group-text">km</span>
                            </div>
                            <small class="form-text text-muted">Se cargará automáticamente el último kilometraje registrado al seleccionar vehículo</small>
                        </div>
                    </div>
                    
                    <!-- Sección de Personal de Comisión -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-users me-2"></i>Personal de la Comisión
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="miembros_cuadrilla" class="form-label">Miembros de la Cuadrilla</label>
                            <select class="form-control select2" id="miembros_cuadrilla" name="miembros_cuadrilla[]" multiple>
                                <!-- Se llenará dinámicamente según la cuadrilla del vehículo -->
                            </select>
                            <small class="form-text text-muted">Seleccione los miembros de la cuadrilla que van en la comisión</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="personal_adicional" class="form-label">Personal Adicional</label>
                            <textarea class="form-control" id="personal_adicional" name="personal_adicional" rows="3" placeholder="Ingrese nombres de personal adicional no perteneciente a la cuadrilla (uno por línea)"></textarea>
                            <small class="form-text text-muted">Personal externo o de otras áreas que acompaña en la comisión</small>
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

<!-- Modal para ver detalles de papeleta -->
<div class="modal fade" id="detallePapeletaModal" tabindex="-1" aria-labelledby="detallePapeletaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detallePapeletaModalLabel">Detalles de Papeleta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detallePapeletaContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para iniciar viaje -->
<div class="modal fade" id="iniciarViajeModal" tabindex="-1" aria-labelledby="iniciarViajeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="iniciarViajeForm">
                @csrf
                <input type="hidden" id="iniciar_papeleta_id" name="papeleta_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="iniciarViajeModalLabel">Iniciar Viaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Confirme el kilometraje actual del vehículo antes de iniciar el viaje.</strong>
                    </div>
                    <div class="mb-3">
                        <label for="km_salida_confirm" class="form-label">Kilómetros de Salida <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="km_salida_confirm" name="km_salida" step="0.001" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Iniciar Viaje</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para finalizar viaje -->
<div class="modal fade" id="finalizarViajeModal" tabindex="-1" aria-labelledby="finalizarViajeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="finalizarViajeForm">
                @csrf
                <input type="hidden" id="finalizar_papeleta_id" name="papeleta_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="finalizarViajeModalLabel">Finalizar Viaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <strong>Confirme el kilometraje final del vehículo para completar el viaje.</strong>
                    </div>
                    <div class="mb-3">
                        <label for="km_llegada" class="form-label">Kilómetros de Llegada <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="km_llegada" name="km_llegada" step="0.001" min="0" required>
                        <small class="form-text text-muted">Debe ser mayor a los kilómetros de salida</small>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" maxlength="1000" placeholder="Ingrese observaciones del viaje (opcional)"></textarea>
                        <small class="form-text text-muted">Máximo 1000 caracteres. Campo opcional para registrar incidencias, novedades o comentarios del viaje.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Finalizar Viaje</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para anular papeleta -->
<div class="modal fade" id="anularPapeletaModal" tabindex="-1" aria-labelledby="anularPapeletaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="anularPapeletaForm">
                @csrf
                <input type="hidden" id="anular_papeleta_id" name="papeleta_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="anularPapeletaModalLabel">Anular Papeleta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>¿Está seguro que desea anular esta papeleta?</strong>
                        <br>Esta acción no se puede deshacer.
                    </div>
                    <div class="mb-3">
                        <label for="motivo_anulacion" class="form-label">Motivo de Anulación <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivo_anulacion" name="motivo_anulacion" rows="3" maxlength="200" placeholder="Ingrese el motivo de la anulación" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Anular Papeleta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Vista 1 PDF -->
<div class="modal fade" id="vista1Modal" tabindex="-1" aria-labelledby="vista1ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vista1ModalLabel">Vista 1 - Papeleta Individual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="vista1Iframe" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Vista 2 PDF -->
<div class="modal fade" id="vista2Modal" tabindex="-1" aria-labelledby="vista2ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vista2ModalLabel">Vista 2 - Papeleta Doble</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="vista2Iframe" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    // Configurar Select2 para vehículos (función global)
    function initVehiculoSelect() {
        // Destruir Select2 existente si existe
        if ($('#asignacion_vehiculo_id').hasClass('select2-hidden-accessible')) {
            $('#asignacion_vehiculo_id').select2('destroy');
        }
        
        // Limpiar opciones
        $('#asignacion_vehiculo_id').empty().append('<option value="">Seleccione un vehículo</option>');
        
        // Inicializar Select2
        $('#asignacion_vehiculo_id').select2({
            dropdownParent: $('#papeletaModal'),
            placeholder: 'Seleccione un vehículo...',
            allowClear: true,
            language: {
                noResults: function() { return 'No hay resultados'; }
            },
            ajax: {
                url: '/papeletas/asignaciones-disponibles',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });
        
        // Evento cuando se selecciona un vehículo - cargar último kilometraje y personal
        $('#asignacion_vehiculo_id').on('select2:select', function (e) {
            var vehiculoId = e.params.data.id;
            var isEditing = $('#papeleta_id').val() !== '';
            
            console.log('=== VEHÍCULO SELECCIONADO ===');
            console.log('Vehículo ID:', vehiculoId);
            console.log('Es edición:', isEditing);
            console.log('Miembros para seleccionar:', window.miembrosParaSeleccionar);
            console.log('Es cambio manual:', window.esCambioManualVehiculo);
            
            // Solo cargar automáticamente si estamos creando (no editando)
            if (vehiculoId && !isEditing) {
                cargarUltimoKilometraje(vehiculoId);
            }
            
            // Cargar información de la cuadrilla y empleados
            cargarInformacionCuadrilla(vehiculoId);
        });
        
        // Evento cuando se limpia la selección
        $('#asignacion_vehiculo_id').on('select2:clear', function (e) {
            $('#km_salida').val('');
            limpiarCamposPersonal();
        });
    }
    
    // Función para inicializar Select2 de miembros de cuadrilla
    function initMiembrosCuadrillaSelect() {
        $('#miembros_cuadrilla').select2({
            dropdownParent: $('#papeletaModal'),
            placeholder: 'Seleccione miembros de la cuadrilla...',
            allowClear: true,
            closeOnSelect: false,
            language: {
                noResults: function() { return 'No hay miembros disponibles'; }
            }
        });
    }
    
    // Función para cargar información de la cuadrilla
    function cargarInformacionCuadrilla(asignacionVehiculoId) {
        $.ajax({
            url: '/papeletas/cuadrilla-info/' + asignacionVehiculoId,
            type: 'GET',
            success: function(response) {
                if (response.success && response.cuadrilla) {
                    // Cargar miembros de cuadrilla
                    $('#miembros_cuadrilla').empty();
                    if (response.cuadrilla.empleados && response.cuadrilla.empleados.length > 0) {
                        response.cuadrilla.empleados.forEach(function(empleado) {
                            var option = new Option(
                                empleado.nombre + ' ' + empleado.apellido,
                                empleado.id,
                                false,
                                false
                            );
                            $('#miembros_cuadrilla').append(option);
                        });
                    }
                    
                    console.log('=== LÓGICA DE SELECCIÓN DE MIEMBROS ===');
                    console.log('Miembros disponibles cargados:', response.cuadrilla.empleados.length);
                    console.log('window.miembrosParaSeleccionar:', window.miembrosParaSeleccionar);
                    console.log('Es edición (papeleta_id):', $('#papeleta_id').val());
                    
                    // Si estamos en modo edición Y tenemos miembros para seleccionar
                    var esEdicion = $('#papeleta_id').val() !== '';
                    var hayMiembrosParaSeleccionar = window.miembrosParaSeleccionar && 
                                                   Array.isArray(window.miembrosParaSeleccionar) && 
                                                   window.miembrosParaSeleccionar.length > 0;
                    
                    if (esEdicion && hayMiembrosParaSeleccionar) {
                        console.log('✅ MODO EDICIÓN - Seleccionando miembros guardados');
                        
                        // Convertir a números para asegurar que coincidan
                        var miembrosIds = window.miembrosParaSeleccionar.map(function(id) {
                            return parseInt(id);
                        });
                        
                        console.log('IDs convertidos a enteros:', miembrosIds);
                        
                        // Verificar que los IDs existen en las opciones disponibles
                        var opcionesDisponibles = [];
                        $('#miembros_cuadrilla option').each(function() {
                            if ($(this).val()) {
                                opcionesDisponibles.push(parseInt($(this).val()));
                                console.log('Opción disponible:', $(this).val(), '-', $(this).text());
                            }
                        });
                        
                        console.log('Opciones disponibles (IDs):', opcionesDisponibles);
                        
                        // Filtrar solo los IDs que realmente existen
                        var miembrosValidos = miembrosIds.filter(function(id) {
                            return opcionesDisponibles.includes(id);
                        });
                        
                        console.log('Miembros válidos para seleccionar:', miembrosValidos);
                        
                        if (miembrosValidos.length > 0) {
                            // Usar setTimeout para asegurar que el DOM esté listo
                            setTimeout(function() {
                                $('#miembros_cuadrilla').val(miembrosValidos).trigger('change');
                                console.log('✅ Miembros seleccionados correctamente con delay');
                                
                                // Verificar que la selección se aplicó
                                var seleccionados = $('#miembros_cuadrilla').val();
                                console.log('Verificación - Valores seleccionados:', seleccionados);
                            }, 100);
                        } else {
                            console.log('⚠️ No se encontraron miembros válidos para seleccionar');
                        }
                        
                        // Limpiar la variable global después de usar (solo en primera carga)
                        if (!window.esCambioManualVehiculo) {
                            window.miembrosParaSeleccionar = null;
                            console.log('🧹 Variable global limpiada después de primera carga');
                        }
                    } else {
                        // Nueva papeleta o cambio de vehículo
                        $('#miembros_cuadrilla').val(null).trigger('change');
                        console.log('🔄 Nueva papeleta o cambio de vehículo - Sin selección previa');
                    }

                    // Manejar chofer permanente
                    if (response.chofer_permanente) {
                        // Mostrar información del chofer permanente
                        const choferInfo = `
                            <div class="alert alert-info mt-2" id="chofer-permanente-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Chofer asignado:</strong> ${response.chofer_permanente.nombre_completo} (${response.chofer_permanente.dni})
                            </div>
                        `;
                        // Eliminar alerta anterior si existe
                        $('#chofer-permanente-info').remove();
                        // Agregar después del campo de kilometraje
                        $('#km_salida').parent().parent().after(choferInfo);
                    } else {
                        // Eliminar alerta si no hay chofer permanente
                        $('#chofer-permanente-info').remove();
                    }
                    
                    // Resetear flag después de la primera carga para permitir detección de cambios manuales
                    window.esCambioManualVehiculo = false;
                } else {
                    $('#miembros_cuadrilla').empty();
                    $('#chofer-permanente-info').remove();
                    console.log('❌ No se encontró información de cuadrilla');
                }
            },
            error: function(xhr) {
                console.error('Error al cargar información de cuadrilla:', xhr);
                $('#miembros_cuadrilla').empty();
                $('#chofer-permanente-info').remove();
            }
        });
    }
    
    // Función para limpiar campos de personal
    function limpiarCamposPersonal() {
        if ($('#miembros_cuadrilla').hasClass('select2-hidden-accessible')) {
            $('#miembros_cuadrilla').empty().trigger('change');
        }
        $('#personal_adicional').val('');
        $('#chofer-permanente-info').remove(); // Limpiar info del chofer permanente
    }
    
    // Función para cargar el último kilometraje del vehículo
    function cargarUltimoKilometraje(asignacionVehiculoId) {
        console.log('Cargando último kilometraje para vehículo:', asignacionVehiculoId);
        
        // Mostrar indicador de carga
        $('#km_salida').prop('disabled', true).val('Cargando...');
        
        $.ajax({
            url: '/papeletas/ultimo-kilometraje/' + asignacionVehiculoId,
            type: 'GET',
            success: function(response) {
                console.log('=== RESPUESTA COMPLETA DEL SERVIDOR ===');
                console.log(response);
                
                if (response.success && response.ultimo_kilometraje !== null) {
                    $('#km_salida').val(response.ultimo_kilometraje);
                    
                    // Mostrar mensaje informativo detallado
                    var mensaje = 'Último km: ' + response.ultimo_kilometraje;
                    if (response.origen) {
                        mensaje += ' (' + response.origen + ')';
                    }
                    if (response.papeleta_id) {
                        mensaje += ' - Papeleta #' + response.papeleta_id;
                    }
                    
                    console.log('✅ ' + mensaje);
                    $('#km_salida').attr('title', mensaje);
                    
                    // Mostrar notificación success discreta
                    if (response.origen) {
                        Swal.fire({
                            title: 'Kilometraje cargado',
                            text: mensaje,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            position: 'top-end',
                            toast: true
                        });
                    }
                } else {
                    $('#km_salida').val('');
                    console.log('⚠️ No se encontró kilometraje previo');
                    
                    // Mostrar información de debug si está disponible
                    if (response.debug_info) {
                        console.log('Debug info:', response.debug_info);
                    }
                    
                    // Mostrar mensaje informativo
                    var mensaje = response.mensaje || 'No se encontró kilometraje previo para este vehículo';
                    Swal.fire({
                        title: 'Sin kilometraje previo',
                        text: mensaje + ' Puede ingresar manualmente.',
                        icon: 'info',
                        timer: 3000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                }
                
                $('#km_salida').prop('disabled', false);
            },
            error: function(xhr) {
                console.error('❌ Error al cargar kilometraje:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response Text:', xhr.responseText);
                
                $('#km_salida').val('').prop('disabled', false);
                
                // Mostrar error con más detalle
                var errorMsg = 'No se pudo cargar el último kilometraje.';
                if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                    errorMsg += ' ' + xhr.responseJSON.mensaje;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMsg + ' Ingrese manualmente.',
                    icon: 'error',
                    timer: 4000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }
        });
    }

    $(document).ready(function() {
        // Configurar fecha mínima como hoy
        $('#fecha').attr('min', new Date().toISOString().split('T')[0]);
        
        var table = $('#papeletasTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('papeletas.data') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'correlativo', name: 'correlativo' },
                { data: 'fecha_formatted', name: 'fecha' },
                { data: 'vehiculo_info', name: 'vehiculo_info' },
                { data: 'cuadrilla_nombre', name: 'cuadrilla_nombre' },
                { data: 'destino', name: 'destino' },
                { data: 'km_recorridos', name: 'km_recorridos', orderable: false, searchable: false },
                { data: 'estado_operacion', name: 'estado_operacion', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[2, "desc"]],
            columnDefs: [
                { targets: [6, 7, 8, 9], className: "text-center" }
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

        // Botón nueva papeleta
        $('#btnNuevaPapeleta').click(function() {
            // Limpiar formulario
            $('#papeletaForm')[0].reset();
            $('#papeleta_id').val('');
            
            // Limpiar variables globales
            window.miembrosParaSeleccionar = null;
            window.esCambioManualVehiculo = false;
            
            // Configurar fecha mínima como hoy para nuevas papeletas
            var today = new Date().toISOString().split('T')[0];
            $('#fecha').val(today);
            
            $('#papeletaModalLabel').text('Nueva Papeleta');
            initVehiculoSelect();
            initMiembrosCuadrillaSelect();
            $('#papeletaModal').modal('show');
        });

        // Submit formulario papeleta
        $('#papeletaForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#papeleta_id').val();
            var url = id ? '/papeletas/' + id : '/papeletas';
            var method = id ? 'PUT' : 'POST';
            
            // Debugging: Mostrar datos que se van a enviar
            var formData = $(this).serialize();
            console.log('=== DATOS DEL FORMULARIO ===');
            console.log('ID papeleta:', id);
            console.log('Método:', method);
            console.log('URL:', url);
            console.log('Datos serialized:', formData);
            
            // Mostrar valores específicos importantes
            console.log('Vehículo seleccionado:', $('#asignacion_vehiculo_id').val());
            console.log('Miembros seleccionados:', $('#miembros_cuadrilla').val());
            console.log('Personal adicional:', $('#personal_adicional').val());
            
            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(res) {
                    console.log('✅ Respuesta exitosa:', res);
                    Swal.fire('Guardado!', 'Papeleta guardada correctamente.', 'success');
                    $('#papeletaModal').modal('hide');
                    table.ajax.reload();
                },
                error: function(xhr) {
                    console.error('❌ Error en submit:', xhr);
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        var errorMsg = Object.values(errors).join('\n');
                        Swal.fire('Error de Validación', errorMsg, 'error');
                    } else {
                        var message = xhr.responseJSON && xhr.responseJSON.message 
                                    ? xhr.responseJSON.message 
                                    : 'Hubo un problema al guardar la papeleta.';
                        Swal.fire('Error!', message, 'error');
                    }
                }
            });
        });

        // Limpiar modal al cerrar
        $('#papeletaModal').on('hidden.bs.modal', function () {
            // Resetear formulario
            $('#papeletaForm')[0].reset();
            $('#papeleta_id').val('');
            
            // Limpiar Select2 de vehículos
            if ($('#asignacion_vehiculo_id').hasClass('select2-hidden-accessible')) {
                $('#asignacion_vehiculo_id').select2('destroy');
            }
            $('#asignacion_vehiculo_id').empty().append('<option value="">Seleccione un vehículo</option>');
            
            // Limpiar Select2 de miembros de cuadrilla
            if ($('#miembros_cuadrilla').hasClass('select2-hidden-accessible')) {
                $('#miembros_cuadrilla').select2('destroy');
            }
            $('#miembros_cuadrilla').empty();
            
            // Limpiar campos de texto
            $('#personal_adicional').val('');
            
            // Eliminar información del chofer permanente
            $('#chofer-permanente-info').remove();
            
            // Limpiar variable global de miembros
            window.miembrosParaSeleccionar = null;
            window.esCambioManualVehiculo = false;
            
            // Resetear fecha mínima
            $('#fecha').attr('min', new Date().toISOString().split('T')[0]);
        });
    });

    // Funciones globales para acciones
    window.verPapeleta = function(id) {
        $.get('/papeletas/' + id, function(data) {
            var html = generateDetalleHTML(data);
            $('#detallePapeletaContent').html(html);
            $('#detallePapeletaModal').modal('show');
        });
    };

    window.editPapeleta = function(id) {
        $.get('/papeletas/' + id, function(data) {
            console.log('Datos recibidos para edición:', data);
            
            // Limpiar formulario primero
            $('#papeletaForm')[0].reset();
            $('#papeleta_id').val(data.id);
            
            // Llenar campos básicos
            $('#destino').val(data.destino || '');
            $('#motivo').val(data.motivo || '');
            // En edición, mantener el km_salida original (no cargar automáticamente)
            $('#km_salida').val(data.km_salida || '');
            
            // Llenar personal adicional
            $('#personal_adicional').val(data.personal_adicional || '');
            
            // Manejar fecha - extraer solo la parte de fecha del formato ISO
            if (data.fecha) {
                var fechaParaInput = data.fecha;
                
                // Si viene en formato ISO (2025-10-08T00:00:00.000000Z), extraer solo la fecha
                if (data.fecha.includes('T')) {
                    fechaParaInput = data.fecha.split('T')[0];
                }
                // Si viene en formato YYYY-MM-DD, usarlo directamente
                else if (/^\d{4}-\d{2}-\d{2}$/.test(data.fecha)) {
                    fechaParaInput = data.fecha;
                }
                // Si viene en formato DD/MM/YYYY
                else if (/^\d{2}\/\d{2}\/\d{4}$/.test(data.fecha)) {
                    var partes = data.fecha.split('/');
                    fechaParaInput = partes[2] + '-' + partes[1] + '-' + partes[0];
                }
                // Si viene en formato DD-MM-YYYY
                else if (/^\d{2}-\d{2}-\d{4}$/.test(data.fecha)) {
                    var partes = data.fecha.split('-');
                    fechaParaInput = partes[2] + '-' + partes[1] + '-' + partes[0];
                }
                
                console.log('Fecha original:', data.fecha);
                console.log('Fecha convertida para input:', fechaParaInput);
                $('#fecha').val(fechaParaInput);
            }
            
            // Verificar qué información de vehículo tenemos disponible
            var vehiculoId = data.asignacion_vehiculo_id || 
                           (data.asignacion_vehiculo && data.asignacion_vehiculo.id) ||
                           (data.vehiculo && data.vehiculo.id);
                           
            var vehiculoTexto = data.vehiculo_info || 
                              (data.vehiculo && data.vehiculo.placa) ||
                              (data.asignacion_vehiculo && data.asignacion_vehiculo.vehiculo && data.asignacion_vehiculo.vehiculo.placa) ||
                              'Vehículo ID: ' + vehiculoId;
                              
            console.log('Vehículo - ID:', vehiculoId, 'Texto:', vehiculoTexto);
            console.log('Miembros cuadrilla RAW:', data.miembros_cuadrilla);
            console.log('Tipo de miembros_cuadrilla:', typeof data.miembros_cuadrilla);
            
            // Procesar miembros de cuadrilla para asegurar formato correcto
            var miembrosCuadrilla = [];
            if (data.miembros_cuadrilla) {
                if (Array.isArray(data.miembros_cuadrilla)) {
                    miembrosCuadrilla = data.miembros_cuadrilla.map(function(id) {
                        return parseInt(id);
                    });
                } else if (typeof data.miembros_cuadrilla === 'string') {
                    try {
                        var parsed = JSON.parse(data.miembros_cuadrilla);
                        if (Array.isArray(parsed)) {
                            miembrosCuadrilla = parsed.map(function(id) {
                                return parseInt(id);
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing miembros_cuadrilla:', e);
                        miembrosCuadrilla = [];
                    }
                }
            }
            
            console.log('Miembros cuadrilla procesados:', miembrosCuadrilla);
            
            // Inicializar Select2
            initVehiculoSelect();
            initMiembrosCuadrillaSelect();
            
            // Variable global para guardar los miembros seleccionados
            window.miembrosParaSeleccionar = miembrosCuadrilla;
            window.esCambioManualVehiculo = false; // Resetear flag
            
            console.log('=== VARIABLES GLOBALES CONFIGURADAS ===');
            console.log('window.miembrosParaSeleccionar:', window.miembrosParaSeleccionar);
            console.log('window.esCambioManualVehiculo:', window.esCambioManualVehiculo);
            
            // Configurar vehículo después de un delay
            setTimeout(function() {
                if (vehiculoId && vehiculoTexto) {
                    // Limpiar select y agregar opción
                    $('#asignacion_vehiculo_id').empty();
                    $('#asignacion_vehiculo_id').append('<option value="">Seleccione un vehículo</option>');
                    
                    var option = new Option(vehiculoTexto, vehiculoId, true, true);
                    $('#asignacion_vehiculo_id').append(option);
                    
                    // Asegurar que el valor esté correctamente establecido para el formulario
                    $('#asignacion_vehiculo_id').val(vehiculoId);
                    
                    console.log('✅ Vehículo configurado en edición - ID:', vehiculoId);
                    console.log('Valor actual del select:', $('#asignacion_vehiculo_id').val());
                    
                    // IMPORTANTE: En modo edición, cargar manualmente la información de la cuadrilla
                    // porque el evento change no se dispara al establecer valores programáticamente
                    console.log('🔄 Cargando información de cuadrilla manualmente para edición...');
                    cargarInformacionCuadrilla(vehiculoId);
                    
                    // Configurar detector de cambio manual después de la primera carga
                    setTimeout(function() {
                        $('#asignacion_vehiculo_id').on('select2:select.manual', function() {
                            window.esCambioManualVehiculo = true;
                            console.log('🔄 Cambio manual de vehículo detectado');
                        });
                    }, 1000); // Aumentar delay para asegurar que la carga inicial termine
                } else {
                    console.warn('No se encontraron datos de vehículo válidos');
                }
                
                $('#papeletaModalLabel').text('Editar Papeleta');
                $('#papeletaModal').modal('show');
                
            }, 300);
            
        }).fail(function(xhr) {
            console.error('Error al cargar datos:', xhr);
            Swal.fire('Error!', 'No se pudieron cargar los datos de la papeleta.', 'error');
        });
    };

    window.iniciarViaje = function(id) {
        $.get('/papeletas/' + id, function(data) {
            $('#iniciar_papeleta_id').val(id);
            $('#km_salida_confirm').val(data.km_salida);
            $('#iniciarViajeModal').modal('show');
        });
    };

    window.finalizarViaje = function(id) {
        $('#finalizar_papeleta_id').val(id);
        $('#finalizarViajeModal').modal('show');
    };

    window.anularPapeleta = function(id) {
        $('#anular_papeleta_id').val(id);
        $('#anularPapeletaModal').modal('show');
    };

    // Funciones para PDF
    window.imprimirPdf = function(id) {
        window.open('/papeletas/' + id + '/pdf', '_blank');
    };

    window.imprimirDosPdf = function(id) {
        window.open('/papeletas/' + id + '/pdf-doble', '_blank');
    };

    window.imprimirDobleHorizontal = function(id) {
        $('#vista2Iframe').attr('src', '/papeletas/' + id + '/preview-doble');
        $('#vista2Modal').modal('show');
    };

    window.previsualizarPdf = function(id) {
        $('#vista1Iframe').attr('src', '/papeletas/' + id + '/preview');
        $('#vista1Modal').modal('show');
    };

    // Formularios de acciones
    $('#iniciarViajeForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#iniciar_papeleta_id').val();
        $.ajax({
            url: '/papeletas/' + id + '/iniciar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire('Iniciado!', res.message, 'success');
                $('#iniciarViajeModal').modal('hide');
                $('#papeletasTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON.message, 'error');
            }
        });
    });

    $('#finalizarViajeForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#finalizar_papeleta_id').val();
        $.ajax({
            url: '/papeletas/' + id + '/finalizar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire('Finalizado!', res.message, 'success');
                $('#finalizarViajeModal').modal('hide');
                $('#papeletasTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON.message, 'error');
            }
        });
    });

    $('#anularPapeletaForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#anular_papeleta_id').val();
        $.ajax({
            url: '/papeletas/' + id + '/anular',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire('Anulada!', res.message, 'success');
                $('#anularPapeletaModal').modal('hide');
                $('#papeletasTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON.message, 'error');
            }
        });
    });

    function generateDetalleHTML(data) {
        var html = `
            <div class="papeleta-details">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Información General</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Correlativo:</strong></td><td>${data.correlativo || 'N/A'}</td></tr>
                            <tr><td><strong>Fecha:</strong></td><td>${data.fecha || 'N/A'}</td></tr>
                            <tr><td><strong>Destino:</strong></td><td>${data.destino || 'N/A'}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td>
                                ${data.estado ? 
                                    (data.completada ? '<span class="badge badge-success">Completada</span>' :
                                     data.en_curso ? '<span class="badge badge-warning">En Curso</span>' :
                                     '<span class="badge badge-info">Programada</span>') :
                                    '<span class="badge badge-danger">Anulada</span>'
                                }
                            </td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-car me-2"></i>Información del Vehículo</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Vehículo:</strong></td><td>${data.vehiculo_info || 'N/A'}</td></tr>
                            <tr><td><strong>Km Salida:</strong></td><td>${data.km_salida ? data.km_salida + ' km' : 'N/A'}</td></tr>
                            <tr><td><strong>Km Llegada:</strong></td><td>${data.km_llegada ? data.km_llegada + ' km' : 'N/A'}</td></tr>
                            <tr><td><strong>Km Recorridos:</strong></td><td>${data.km_recorridos ? data.km_recorridos + ' km' : 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fas fa-route me-2"></i>Detalles del Viaje</h6>
                        <table class="table table-sm">
                            <tr><td style="width: 150px;"><strong>Motivo:</strong></td><td>${data.motivo || 'N/A'}</td></tr>
                            <tr><td><strong>Fecha/Hora Salida:</strong></td><td>${data.fecha_hora_salida || 'Pendiente'}</td></tr>
                            <tr><td><strong>Fecha/Hora Llegada:</strong></td><td>${data.fecha_hora_llegada || 'Pendiente'}</td></tr>
                        </table>
                    </div>
                </div>
                
                ${data.observaciones ? `
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fas fa-sticky-note me-2"></i>Observaciones</h6>
                        <div class="alert alert-light">
                            ${data.observaciones}
                        </div>
                    </div>
                </div>
                ` : ''}
                
                ${data.miembros_cuadrilla_empleados && data.miembros_cuadrilla_empleados.length > 0 ? `
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fas fa-users me-2"></i>Personal de la Comisión</h6>
                        <p><strong>Miembros de Cuadrilla:</strong></p>
                        <ul>
                            ${data.miembros_cuadrilla_empleados.map(empleado => 
                                `<li>${empleado.nombre} ${empleado.apellido} - ${empleado.cargo ? empleado.cargo.nombre : 'Sin cargo'}</li>`
                            ).join('')}
                        </ul>
                    </div>
                </div>
                ` : ''}
                
                ${data.personal_adicional ? `
                <div class="row">
                    <div class="col-md-12">
                        <p><strong>Personal Adicional:</strong></p>
                        <div class="alert alert-info">
                            ${data.personal_adicional}
                        </div>
                    </div>
                </div>
                ` : ''}
                
                ${!data.estado && data.motivo_anulacion ? `
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Anulación</h6>
                        <div class="alert alert-danger">
                            <strong>Motivo:</strong> ${data.motivo_anulacion}<br>
                            <strong>Fecha:</strong> ${data.fecha_anulacion || 'N/A'}
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
        `;
        return html;
    }
    </script>
@endsection