@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
<link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">



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

        .papeleta-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        /* Estilos para campos readonly */
        input[readonly],
        textarea[readonly] {
            background-color: #e9ecef !important;
            color: #212529 !important;
            font-weight: 500;
        }

        input[readonly]:focus {
            background-color: #e9ecef !important;
            color: #212529 !important;
            border-color: #bfc9d4 !important;
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
                                <th>Dotación Combustible</th>
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
                            <small class="form-text text-muted">Se cargará automáticamente el último kilometraje registrado al seleccionar vehículo. Puede editar manualmente si no hay registro previo o si es necesario.</small>
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
                            <label for="miembros_cuadrilla" class="form-label">
                                Miembros de la Cuadrilla
                                <span class="badge bg-info ms-2" id="miembros-badge">0 seleccionados</span>
                            </label>
                            <select class="form-control select2" id="miembros_cuadrilla" name="miembros_cuadrilla[]" multiple>
                                <!-- Se llenará dinámicamente según la cuadrilla del vehículo -->
                            </select>
                            <small class="form-text text-muted">Seleccione los miembros que van en la comisión</small>
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
                    
                    <!-- Información de salida -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hora de Salida</label>
                                <input type="text" class="form-control" id="finalizar_hora_salida" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kilometraje de Salida</label>
                                <input type="number" class="form-control" id="finalizar_km_salida" step="0.001" readonly>
                            </div>
                        </div>
                    </div>

                    <hr>
                    
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

<!-- Modal Vale de Combustible -->
<div class="modal fade" id="valeModal" tabindex="-1" aria-labelledby="valeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="valeModalLabel">Vale de Combustible</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="valeIframe" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar dotación de combustible -->
<div class="modal fade" id="dotacionModal" tabindex="-1" aria-labelledby="dotacionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="dotacionForm">
                @csrf
                <input type="hidden" id="dotacion_id" name="dotacion_id">
                <input type="hidden" id="papeleta_id_dotacion" name="papeleta_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="dotacionModalLabel">Nueva Dotación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="numero_vale" class="form-label">Número de Vale</label>
                        <input type="text" class="form-control" id="numero_vale" name="numero_vale" readonly disabled style="background-color: #f0f0f0; cursor: not-allowed; font-weight: 600; color: #333;">
                        <small class="form-text text-muted">Se genera automáticamente</small>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_combustible_id" class="form-label">Tipo de Combustible <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="tipo_combustible_id" name="tipo_combustible_id" required>
                            <option value="">Seleccione un tipo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad_gl" class="form-label">Cantidad (GL) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cantidad_gl" name="cantidad_gl" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio_unitario" class="form-label">Precio Unitario (S/.)</label>
                        <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_carga" class="form-label">Fecha de Carga</label>
                        <input type="date" class="form-control" id="fecha_carga" name="fecha_carga">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Costo Total</label>
                        <input type="text" class="form-control" id="costo_total_display" disabled value="S/. 0.00" style="font-weight: 600; color: #333;">
                        <small class="form-text text-muted">Se calcula automáticamente (Cantidad × Precio Unitario)</small>
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
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    var papeletasTable;
    var dotacionesTable;
    window.miembrosParaSeleccionar = [];
    window.esCambioManualVehiculo = false;

    // ========================================
    // DOCUMENT READY
    // ========================================
    $(document).ready(function() {
        console.log('✅ Scripts cargados - Inicializando Sistema de Papeletas');
        
        // Cargar datos iniciales para los selects
        loadAsignacionesVehiculos();
        loadTiposCombustible();
        
        // Inicializar Select2 para miembros
        initMiembrosCuadrillaSelect();
        
        // Inicializar DataTable
        initPapeletasTable();
        
        // Eventos del Modal Papeleta
        $('#btnNuevaPapeleta').on('click', function() {
            $('#papeletaForm')[0].reset();
            $('#papeleta_id').val('');
            $('#papeletaModalLabel').text('Nueva Papeleta');
            $('#fecha').attr('min', new Date().toISOString().split('T')[0]);
            $('#fecha').val(new Date().toISOString().split('T')[0]);
            window.miembrosParaSeleccionar = [];
            window.esCambioManualVehiculo = false;
            limpiarCamposPersonal();
            // Recargar selects
            loadAsignacionesVehiculos();
            loadTiposCombustible();
        });

        // Evento: Cambio de vehículo (Select2)
        $(document).on('select2:select', '#asignacion_vehiculo_id', function() {
            window.esCambioManualVehiculo = true;
            var vehiculoId = $(this).val();
            console.log('✅ Vehículo seleccionado:', vehiculoId);
            console.log('📍 LLAMANDO A ENDPOINTS:');
            console.log('   1. /papeletas/cuadrilla-info/' + vehiculoId);
            console.log('   2. /papeletas/ultimo-kilometraje/' + vehiculoId);
            
            if (vehiculoId) {
                cargarInformacionCuadrilla(vehiculoId);
                cargarUltimoKilometraje(vehiculoId);
            } else {
                limpiarCamposPersonal();
            }
        });

        // Evento: Envío de Formulario Papeleta
        $('#papeletaForm').on('submit', function(e) {
            e.preventDefault();
            guardarPapeleta();
        });

        // Evento: Calcular costo total dotación
        $(document).on('change', '#cantidad_gl, #precio_unitario', function() {
            calcularCostoTotal();
        });

        // Evento: Botón agregar dotación
        $(document).on('click', '#btnAgregarDotacion', function() {
            $('#dotacionForm')[0].reset();
            $('#dotacion_id').val('');
            $('#dotacionModalLabel').text('Nueva Dotación');
            calcularCostoTotal();
            $('#dotacionModal').modal('show');
        });

        // Evento: Botón refrescar dotaciones
        $(document).on('click', '#btnRefrescarDotaciones', function() {
            var papeletaId = $('#papeleta_id_dotacion').val();
            if (papeletaId) {
                abrirDotaciones(papeletaId, $('#papeletaNumero').text());
            }
        });

        // Evento: Envío de Formulario Dotación
        $('#dotacionForm').on('submit', function(e) {
            e.preventDefault();
            guardarDotacion();
        });

        // Evento: Envío Iniciar Viaje
        $('#iniciarViajeForm').on('submit', function(e) {
            e.preventDefault();
            guardarIniciarViaje();
        });

        // Evento: Envío Finalizar Viaje
        $('#finalizarViajeForm').on('submit', function(e) {
            e.preventDefault();
            guardarFinalizarViaje();
        });

        // Evento: Envío Anular Papeleta
        $('#anularPapeletaForm').on('submit', function(e) {
            e.preventDefault();
            guardarAnularPapeleta();
        });

        // Evento: Botón descargar vale de combustible
        $(document).on('click', '#btnDescargarVale', function() {
            var valeId = $(this).data('vale-id');
            if (valeId) {
                descargarVale(valeId);
            }
        });

        // Limpieza al cerrar modal
        $('#papeletaModal').on('hidden.bs.modal', function() {
            window.miembrosParaSeleccionar = [];
            window.esCambioManualVehiculo = false;
            $('#miembros_cuadrilla').select2('destroy');
            initMiembrosCuadrillaSelect();
        });
    });

    // ========================================
    // INICIALIZACIÓN DE SELECT2
    // ========================================

    /**
     * Cargar vehículos/asignaciones dinámicamente con Select2 AJAX
     */
    function loadAsignacionesVehiculos(selectedId = null) {
        var select = $('#asignacion_vehiculo_id');
        var papeletaId = $('#papeleta_id').val() || null; // Obtener ID de papeleta si está editando
        
        // Destruir Select2 existente si existe
        if (select.hasClass('select2-hidden-accessible')) {
            select.select2('destroy');
        }
        
        // Limpiar opciones
        select.empty().append('<option value="">Seleccione un vehículo</option>');
        
        // Inicializar Select2 con AJAX
        select.select2({
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
                        page: params.page || 1,
                        papeleta_id: papeletaId // Enviar ID de papeleta para edición
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
                cache: false
            }
        });
        
        // Si hay un ID seleccionado, preseleccionarlo
        if (selectedId) {
            console.log('📍 Preseleccionando vehículo ID:', selectedId);
            
            // Esperar a que Select2 esté completamente inicializado
            setTimeout(function() {
                $.ajax({
                    url: '/papeletas/asignaciones-disponibles',
                    dataType: 'json',
                    data: { search: '', page: 1, papeleta_id: papeletaId, perPage: 100 },
                    success: function(data) {
                        console.log('📦 Datos de vehículos cargados:', data.results.length, 'resultados');
                        var selected = data.results.find(r => parseInt(r.id) === parseInt(selectedId));
                        
                        if (selected) {
                            console.log('✅ Vehículo encontrado:', selected.text);
                            // Crear y agregar la opción
                            var option = new Option(selected.text, selected.id, true, true);
                            select.append(option);
                            // Establecer el valor
                            select.val(selectedId);
                            // Disparar cambio
                            select.trigger('change');
                        } else {
                            console.warn('⚠️ Vehículo ID', selectedId, 'no encontrado');
                            console.log('Resultados disponibles:', data.results.map(r => r.id + ':' + r.text));
                        }
                    },
                    error: function(xhr) {
                        console.error('❌ Error al cargar vehículos:', xhr);
                    }
                });
            }, 400);
        }
        
        return Promise.resolve();
    }

    /**
     * Cargar tipos de combustible dinámicamente
     */
    /**
     * Cargar tipos de combustible dinámicamente con Select2
     */
    function loadTiposCombustible(selectedId = null) {
        var select = $('#tipo_combustible_id');
        
        // Destruir Select2 existente si existe
        if (select.hasClass('select2-hidden-accessible')) {
            select.select2('destroy');
        }
        
        // Limpiar opciones
        select.empty().append('<option value="">Seleccione un tipo</option>');
        
        // Cargar datos y llenar opciones
        return $.get('/tipo_combustibles/api/select')
            .done(function(data) {
                console.log('✅ Tipos de combustible cargados:', data.length);
                
                $.each(data, function(index, item) {
                    var option = new Option(item.text, item.id, false, selectedId == item.id);
                    select.append(option);
                });
                
                // Inicializar Select2 después de cargar opciones
                select.select2({
                    dropdownParent: $('#dotacionModal'),
                    placeholder: 'Seleccione un tipo de combustible',
                    allowClear: true,
                    language: { noResults: function() { return 'No hay resultados'; } }
                });
                
                // Si hay un ID seleccionado, asegurarse que esté establecido
                if (selectedId) {
                    select.val(selectedId).trigger('change');
                }
            })
            .fail(function(xhr) {
                console.error('❌ Error al cargar tipos de combustible:', xhr);
                
                // Inicializar Select2 de todas formas
                select.select2({
                    dropdownParent: $('#dotacionModal'),
                    placeholder: 'Error al cargar tipos',
                    allowClear: true
                });
            });
    }

    /**
     * Inicializar Select2 para miembros de cuadrilla
     * Con comportamiento de ocultar opciones seleccionadas
     */
    function initMiembrosCuadrillaSelect() {
        var select = $('#miembros_cuadrilla');
        
        console.log('🔧 Inicializando Select2 de miembros. Opciones actuales:', select.find('option').length);
        
        // Destruir si ya existe
        if (select.hasClass('select2-hidden-accessible')) {
            console.log('  ↩️ Destruyendo Select2 anterior');
            select.select2('destroy');
        }
        
        // Inicializar nuevo con templateResult para ocultar seleccionados
        select.select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione miembros de la cuadrilla',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#papeletaModal'),
            width: '100%',
            // Mostrar/ocultar opciones según si están seleccionadas
            templateResult: function(data) {
                if (!data.id) return data.text; // Placeholder
                
                var $option = $(data.element);
                var isSelected = select.find('option:selected[value="' + data.id + '"]').length > 0;
                
                if (isSelected) {
                    return null; // Ocultar opción seleccionada del dropdown
                }
                return data.text;
            }
        });
        
        // Event listener para manejar cambios
        select.on('change', function() {
            var cantidad = $(this).val() ? $(this).val().length : 0;
            var texto = cantidad === 1 ? '1 seleccionado' : cantidad + ' seleccionados';
            $('#miembros-badge').text(texto);
            console.log('Miembros seleccionados:', $(this).val());
            // Trigger change para que Select2 refresque el dropdown
            select.select2('open');
            setTimeout(function() {
                select.select2('close');
            }, 100);
        });
        
        // Inicializar contador con valores actuales
        var cantidadActual = select.val() ? select.val().length : 0;
        var textoActual = cantidadActual === 1 ? '1 seleccionado' : cantidadActual + ' seleccionados';
        $('#miembros-badge').text(textoActual);
        
        console.log('✅ Select2 de miembros inicializado con contador de seleccionados');
    }

    // ========================================
    // CARGA DE DATOS DINÁMICOS
    // ========================================

    /**
     * Cargar información de la cuadrilla
     */
    function cargarInformacionCuadrilla(asignacionVehiculoId) {
        console.log('🔄 Cargando información de cuadrilla para asignación:', asignacionVehiculoId);
        
        $.ajax({
            url: '/papeletas/cuadrilla-info/' + asignacionVehiculoId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('✅ RESPUESTA COMPLETA:', JSON.stringify(data, null, 2));
                
                // Destruir Select2 existente
                if ($('#miembros_cuadrilla').hasClass('select2-hidden-accessible')) {
                    $('#miembros_cuadrilla').select2('destroy');
                }
                
                // Limpiar opciones anteriores
                $('#miembros_cuadrilla').empty();
                
                // Intentar obtener empleados de diferentes estructuras
                var empleados = [];
                if (data && data.cuadrilla && data.cuadrilla.empleados) {
                    empleados = data.cuadrilla.empleados;
                    console.log('📍 Empleados encontrados en data.cuadrilla.empleados');
                } else if (data && data.empleados) {
                    empleados = data.empleados;
                    console.log('📍 Empleados encontrados en data.empleados');
                }
                
                console.log('📦 Empleados a procesar:', empleados);
                
                // Cargar nuevas opciones de miembros
                if (empleados && empleados.length > 0) {
                    console.log('👥 Miembros encontrados:', empleados.length);
                    
                    $.each(empleados, function(index, empleado) {
                        console.log('  ➕ Agregando miembro:', empleado.id, '-', empleado.nombre_completo || (empleado.nombre + ' ' + empleado.apellido));
                        var nombre = empleado.nombre_completo || (empleado.nombre + ' ' + empleado.apellido);
                        var option = new Option(nombre, empleado.id, false, false);
                        $('#miembros_cuadrilla').append(option);
                    });
                    
                    console.log('✅ Todas las opciones de miembros cargadas');
                } else {
                    console.log('⚠️  No hay empleados en la respuesta. Data completa:', data);
                }
                
                // Reinicializar Select2 DESPUÉS de agregar opciones
                initMiembrosCuadrillaSelect();
                console.log('✅ Select2 reinicializado');

                // Si estamos editando y hay miembros para seleccionar
                var esEdicion = $('#papeleta_id').val() !== '';
                if (esEdicion && window.miembrosParaSeleccionar && window.miembrosParaSeleccionar.length > 0) {
                    console.log('🔄 Modo edición: intentando seleccionar miembros guardados');
                    console.log('   Miembros a seleccionar:', window.miembrosParaSeleccionar);
                    
                    setTimeout(function() {
                        var miembrosValidos = [];
                        
                        window.miembrosParaSeleccionar.forEach(function(id) {
                            var idNum = parseInt(id);
                            var existe = $('#miembros_cuadrilla').find('option[value="' + idNum + '"]').length > 0;
                            
                            console.log('   ✓ Verificando ID:', idNum, '- Existe:', existe);
                            
                            if (existe) {
                                miembrosValidos.push(idNum);
                                console.log('   ✅ Miembro válido para seleccionar:', idNum);
                            }
                        });
                        
                        console.log('🎯 Miembros válidos finales:', miembrosValidos);
                        
                        if (miembrosValidos.length > 0) {
                            $('#miembros_cuadrilla').val(miembrosValidos).trigger('change');
                            console.log('✅ Miembros seleccionados en Select2');
                        } else {
                            console.log('ℹ️  No hay miembros válidos para seleccionar, limpiando');
                            $('#miembros_cuadrilla').val(null).trigger('change');
                        }
                        
                        window.miembrosParaSeleccionar = [];
                    }, 200);
                } else {
                    console.log('ℹ️  Nueva papeleta o sin miembros para seleccionar');
                    $('#miembros_cuadrilla').val(null).trigger('change');
                }
            },
            error: function(xhr) {
                console.error('❌ Error al cargar información de cuadrilla:', xhr);
                console.error('URL intentada: /papeletas/cuadrilla-info/' + asignacionVehiculoId);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                limpiarCamposPersonal();
            }
        });
    }

    /**
     * Limpiar campos de personal
     */
    function limpiarCamposPersonal() {
        $('#miembros_cuadrilla').select2('destroy');
        $('#miembros_cuadrilla').empty();
        $('#personal_adicional').val('');
        initMiembrosCuadrillaSelect();
    }

    /**
     * Cargar último kilometraje registrado
     */
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

    // ========================================
    // INICIALIZACIÓN DE DATATABLE
    // ========================================

    /**
     * Inicializar DataTable de Papeletas
     */
    function initPapeletasTable() {
        papeletasTable = $('#papeletasTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '/papeletas/data',
                type: 'GET',
                dataSrc: function(json) {
                    return json.data || [];
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'correlativo', name: 'correlativo' },
                { data: 'fecha_formatted', name: 'fecha' },
                { data: 'vehiculo_info', name: 'vehiculo_info' },
                { data: 'cuadrilla_nombre', name: 'cuadrilla_nombre' },
                { data: 'destino', name: 'destino' },
                { data: 'km_recorridos', name: 'km_recorridos', orderable: false, searchable: false },
                { data: 'dotacion_info', name: 'dotacion_info', orderable: false, searchable: false },
                { data: 'estado_operacion', name: 'estado_operacion', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[2, 'desc']],
            columnDefs: [
                { targets: [7, 8, 9, 10], className: 'text-center' }
            ],
            language: {
                processing: 'Procesando...',
                lengthMenu: 'Mostrar _MENU_ registros',
                zeroRecords: 'No se encontraron resultados',
                emptyTable: 'Ningún dato disponible en esta tabla',
                info: 'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
                infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
                infoFiltered: '(filtrado de un total de _MAX_ registros)',
                search: 'Buscar:',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                }
            }
        });

        console.log('✅ DataTable de papeletas cargado');
    }

    // ========================================
    // FUNCIONES GLOBALES (VENTANA)
    // ========================================

    /**
     * Ver detalles de una papeleta
     */
    window.verPapeleta = function(id) {
        console.log('Viewing papeleta ID:', id);
        $.get('/papeletas/' + id, function(data) {
            console.log('Papeleta data:', data);
            var html = generateDetalleHTML(data);
            $('#detallePapeletaContent').html(html);
            $('#detallePapeletaModal').modal('show');
        }).fail(function(xhr) {
            console.error('Error:', xhr);
            Swal.fire({
                title: 'Error',
                text: 'No se pudo cargar los detalles de la papeleta',
                icon: 'error'
            });
        });
    };

    /**
     * Editar papeleta
     */
    window.editPapeleta = function(id) {
        console.log('Editing papeleta ID:', id);
        $.get('/papeletas/' + id, function(data) {
            console.log('Papeleta for edit:', data);
            
            // Limpiar y resetear formulario
            $('#papeletaForm')[0].reset();
            $('#papeleta_id').val(data.id);
            window.esCambioManualVehiculo = false;
            
            // Inicializar Select2 de vehículos
            loadAsignacionesVehiculos(data.asignacion_vehiculo_id);
            
            // Convertir fecha a formato YYYY-MM-DD
            var fecha = data.fecha;
            if (fecha.includes('T')) {
                fecha = fecha.split('T')[0];
            } else if (fecha.includes('/')) {
                var partes = fecha.split('/');
                fecha = partes[2] + '-' + partes[1] + '-' + partes[0];
            } else if (fecha.includes('-') && fecha.split('-')[0].length === 2) {
                var partes = fecha.split('-');
                fecha = partes[2] + '-' + partes[1] + '-' + partes[0];
            }
            $('#fecha').val(fecha);
            
            $('#destino').val(data.destino || '');
            $('#motivo').val(data.motivo || '');
            $('#km_salida').val(parseFloat(data.km_salida || 0).toFixed(3));
            $('#personal_adicional').val(data.personal_adicional || '');
            
            // Guardar miembros para pre-seleccionar después
            if (data.miembros_cuadrilla) {
                if (typeof data.miembros_cuadrilla === 'string') {
                    window.miembrosParaSeleccionar = JSON.parse(data.miembros_cuadrilla);
                } else if (Array.isArray(data.miembros_cuadrilla)) {
                    window.miembrosParaSeleccionar = data.miembros_cuadrilla;
                }
            }
            
            // Cargar información de cuadrilla y mostrar histórico de km después de un delay
            setTimeout(function() {
                console.log('🔄 Cargando información de cuadrilla en modo edición...');
                cargarInformacionCuadrilla(data.asignacion_vehiculo_id);
                
                // También cargar y mostrar el histórico del último km
                console.log('📊 Cargando histórico de km para este vehículo...');
                cargarUltimoKilometraje(data.asignacion_vehiculo_id);
            }, 500);
            
            // Mostrar modal
            $('#papeletaModalLabel').text('Editar Papeleta');
            $('#papeletaModal').modal('show');
        }).fail(function(xhr) {
            console.error('Error:', xhr);
            Swal.fire({
                title: 'Error',
                text: 'No se pudo cargar los datos de la papeleta',
                icon: 'error'
            });
        });
    };

    /**
     * Iniciar viaje
     */
    window.iniciarViaje = function(id) {
        console.log('Starting trip for papeleta:', id);
        $.get('/papeletas/' + id, function(data) {
            $('#iniciar_papeleta_id').val(id);
            $('#km_salida_confirm').val(parseFloat(data.km_salida || 0).toFixed(3));
            $('#iniciarViajeModal').modal('show');
        }).fail(function(xhr) {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo cargar la papeleta',
                icon: 'error'
            });
        });
    };

    /**
     * Finalizar viaje
     */
    window.finalizarViaje = function(id) {
        console.log('Ending trip for papeleta:', id);
        $.get('/papeletas/' + id, function(data) {
            $('#finalizar_papeleta_id').val(id);
            
            // Mostrar hora de salida si existe
            if (data.fecha_hora_salida) {
                var fechaSalida = new Date(data.fecha_hora_salida);
                var horaFormato = fechaSalida.toLocaleString('es-PE', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                $('#finalizar_hora_salida').val(horaFormato);
            } else {
                $('#finalizar_hora_salida').val('No iniciado');
            }
            
            // Mostrar km de salida
            $('#finalizar_km_salida').val(data.km_salida || '');
            
            $('#km_llegada').val('');
            $('#observaciones').val('');
            $('#finalizarViajeModal').modal('show');
        }).fail(function(xhr) {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo cargar la papeleta',
                icon: 'error'
            });
        });
    };

    /**
     * Anular papeleta
     */
    window.anularPapeleta = function(id) {
        console.log('Cancelling papeleta:', id);
        $('#anular_papeleta_id').val(id);
        $('#motivo_anulacion').val('');
        $('#anularPapeletaModal').modal('show');
    };

    /**
     * Abrir dotaciones de combustible
     */
    window.abrirDotaciones = function(papeletaId, correlativo) {
        console.log('✅ Abriendo dotaciones para papeleta:', papeletaId, '- Correlativo:', correlativo);
        
        $('#papeletaNumero').text(correlativo);
        $('#papeleta_id_dotacion').val(papeletaId);
        
        // Destruir tabla existente si existe
        if ($.fn.DataTable.isDataTable('#dotacionesTable')) {
            $('#dotacionesTable').DataTable().destroy();
        }
        
        // Crear nueva DataTable
        dotacionesTable = $('#dotacionesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '/papeletas/' + papeletaId + '/dotaciones',
                type: 'GET',
                dataSrc: function(json) {
                    console.log('📊 Datos de dotaciones cargados:', json);
                    // Actualizar resumen
                    if (json.resumen) {
                        actualizarResumenDotaciones(json.resumen);
                    }
                    return json.data || [];
                },
                error: function(xhr) {
                    console.error('❌ Error al cargar dotaciones:', xhr);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar las dotaciones',
                        icon: 'error'
                    });
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'fecha_carga_formatted', name: 'fecha_carga' },
                { data: 'tipo_nombre', name: 'tipo_nombre' },
                { data: 'cantidad_gl', name: 'cantidad_gl' },
                { data: 'precio_unitario', name: 'precio_unitario' },
                { data: 'costo_total', name: 'costo_total' },
                { data: 'numero_vale', name: 'numero_vale' },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 10,
            language: {
                processing: 'Procesando...',
                search: 'Buscar:',
                zeroRecords: 'No se encontraron dotaciones',
                emptyTable: 'Sin dotaciones registradas',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                }
            },
            columnDefs: [
                { targets: [3, 4, 5], className: 'text-right' }
            ]
        });
        
        console.log('✅ DataTable de dotaciones inicializado');
    };

    /**
     * Previsualizar PDF Vista 1
     */
    window.previsualizarPdf = function(id) {
        console.log('Previewing PDF Vista 1 for papeleta:', id);
        $('#vista1Iframe').attr('src', '/papeletas/' + id + '/preview');
        $('#vista1Modal').modal('show');
    };

    /**
     * Previsualizar PDF Vista 2 (Doble)
     */
    window.previsualizarPdfDoble = function(id) {
        console.log('Previewing PDF Vista 2 (Doble) for papeleta:', id);
        $('#vista2Iframe').attr('src', '/papeletas/' + id + '/preview-doble');
        $('#vista2Modal').modal('show');
    };

    /**
     * Alias para compatibilidad con código anterior
     */
    window.imprimirDobleHorizontal = function(id) {
        window.previsualizarPdfDoble(id);
    };

    /**
     * Descargar PDF Individual
     */
    window.descargarPdf = function(id) {
        console.log('Downloading PDF for papeleta:', id);
        window.open('/papeletas/' + id + '/pdf', '_blank');
    };

    /**
     * Descargar PDF Doble
     */
    window.descargarPdfDoble = function(id) {
        console.log('Downloading PDF Doble for papeleta:', id);
        window.open('/papeletas/' + id + '/pdf-doble', '_blank');
    };

    /**
     * Previsualizar Vale de Combustible
     */
    window.previsualizarVale = function(id) {
        console.log('Previewing Vale for dotacion:', id);
        $('#valeIframe').attr('src', '/papeletas/vale/' + id + '/preview');
        $('#btnDescargarVale').data('vale-id', id);
        $('#valeModal').modal('show');
    };

    /**
     * Descargar Vale de Combustible
     */
    window.descargarVale = function(id) {
        console.log('Downloading Vale for dotacion:', id);
        window.open('/papeletas/vale/' + id + '/descargar', '_blank');
    };

    /**
     * Alias para compatibilidad con código anterior
     */
    window.imprimirPdf = function(id) {
        window.descargarPdf(id);
    };

    window.imprimirDosPdf = function(id) {
        window.descargarPdfDoble(id);
    };

    /**
     * Abrir modal para crear o editar dotación
     */
    window.abrirModalDotacion = function(papeletaId) {
        console.log('✅ Abriendo modal para dotación - Papeleta ID:', papeletaId);
        
        // Resetear formulario
        $('#dotacionForm')[0].reset();
        $('#papeleta_id_dotacion').val(papeletaId);
        
        // Establecer fecha de carga a hoy
        var hoy = new Date().toISOString().split('T')[0];
        $('#fecha_carga').val(hoy);
        console.log('📅 Fecha de carga establecida a:', hoy);
        
        // Inicializar costo total
        $('#costo_total_display').val('S/. 0.00');
        
        // Cargar tipos de combustible (esperar a que se complete antes de continuar)
        loadTiposCombustible().done(function() {
            console.log('✅ Tipos de combustible cargados');
            
            // Verificar si ya existe una dotación para esta papeleta
            $.ajax({
                url: '/papeletas/' + papeletaId + '/dotacion-existe',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('✅ Respuesta de verificación:', response);
                    console.log('📊 Dotación data:', response.dotacion);
                    
                    if (response.existe && response.dotacion) {
                        // Modo edición: cargar datos de la dotación existente
                        var dotacion = response.dotacion;
                        console.log('📝 Modo edición - Dotación encontrada:', dotacion);
                        
                        $('#dotacion_id').val(dotacion.id);
                        
                        // Establecer tipo de combustible
                        setTimeout(function() {
                            console.log('🔧 Estableciendo tipo_combustible_id:', dotacion.tipo_combustible_id);
                            $('#tipo_combustible_id').val(dotacion.tipo_combustible_id).trigger('change');
                        }, 100);
                        
                        // Establecer cantidad
                        console.log('📦 Estableciendo cantidad_gl:', dotacion.cantidad_gl);
                        $('#cantidad_gl').val(dotacion.cantidad_gl);
                        
                        // Establecer precio unitario
                        console.log('💰 Estableciendo precio_unitario:', dotacion.precio_unitario);
                        $('#precio_unitario').val(dotacion.precio_unitario);
                        
                        // Establecer número de vale
                        console.log('🏷️  Estableciendo numero_vale:', dotacion.numero_vale);
                        $('#numero_vale').val(dotacion.numero_vale || '');
                        
                        // Establecer fecha de carga (convertir de ISO a YYYY-MM-DD)
                        if (dotacion.fecha_carga) {
                            var fechaISO = dotacion.fecha_carga;
                            // Extraer solo la parte de fecha (YYYY-MM-DD) del formato ISO
                            var fechaFormato = fechaISO.split('T')[0];
                            console.log('📅 Fecha original:', fechaISO, '-> convertida a:', fechaFormato);
                            $('#fecha_carga').val(fechaFormato);
                        }
                        
                        $('#dotacionModalLabel').text('Editar Dotación');
                        
                        // Calcular y mostrar costo total después de un delay
                        setTimeout(function() {
                            console.log('🧮 Calculando costo total...');
                            calcularCostoTotal();
                        }, 200);
                    } else {
                        // Modo creación: nueva dotación
                        console.log('✨ Modo creación - Nueva dotación');
                        $('#dotacion_id').val('');
                        $('#numero_vale').val('');
                        $('#dotacionModalLabel').text('Nueva Dotación');
                        // Generar número de vale automáticamente
                        generarNumeroPróximoVale(papeletaId);
                        // Calcular costo total inicial
                        calcularCostoTotal();
                    }
                    
                    // Mostrar modal
                    $('#dotacionModal').modal('show');
                },
                error: function(xhr) {
                    console.error('❌ Error al verificar dotación:', xhr);
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    // En caso de error, asumir que es nueva
                    $('#dotacion_id').val('');
                    $('#numero_vale').val('');
                    $('#dotacionModalLabel').text('Nueva Dotación');
                    // Generar número de vale automáticamente
                    generarNumeroPróximoVale(papeletaId);
                    calcularCostoTotal();
                    $('#dotacionModal').modal('show');
                }
            });
        });
    };

    // ========================================
    // FUNCIONES DE GUARDADO
    // ========================================

    /**
     * Guardar papeleta (crear o editar)
     */
    function guardarPapeleta() {
        var papeletaId = $('#papeleta_id').val();
        var url = papeletaId ? '/papeletas/' + papeletaId : '/papeletas';
        var method = papeletaId ? 'PUT' : 'POST';
        
        // Crear FormData para enviar arrays correctamente
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', method); // Para que Laravel reconozca PUT
        formData.append('asignacion_vehiculo_id', $('#asignacion_vehiculo_id').val());
        formData.append('fecha', $('#fecha').val());
        formData.append('destino', $('#destino').val());
        formData.append('motivo', $('#motivo').val());
        formData.append('km_salida', $('#km_salida').val());
        formData.append('personal_adicional', $('#personal_adicional').val());
        
        // Agregar cada miembro seleccionado
        var miembrosSeleccionados = $('#miembros_cuadrilla').val() || [];
        $.each(miembrosSeleccionados, function(index, value) {
            formData.append('miembros_cuadrilla[]', value);
        });

        $.ajax({
            url: url,
            type: 'POST', // FormData requiere POST incluso para PUT (usa _method)
            data: formData,
            processData: false, // No procesar FormData
            contentType: false, // No establecer Content-Type (FormData lo hace)
            success: function(response) {
                console.log('Papeleta saved:', response);
                Swal.fire({
                    title: 'Éxito',
                    text: 'Papeleta guardada correctamente',
                    icon: 'success',
                    timer: 2000
                });
                $('#papeletaModal').modal('hide');
                papeletasTable.ajax.reload();
            },
            error: function(xhr) {
                console.error('Error saving papeleta:', xhr);
                var errorMsg = 'Error al guardar la papeleta';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n');
                }
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error'
                });
            }
        });
    }

    /**
     * Guardar dotación
     */
    function guardarDotacion() {
        var papeletaId = $('#papeleta_id_dotacion').val();
        var dotacionId = $('#dotacion_id').val();
        var url = dotacionId 
            ? '/papeletas/' + papeletaId + '/dotaciones/' + dotacionId 
            : '/papeletas/' + papeletaId + '/dotaciones';
        var method = dotacionId ? 'PUT' : 'POST';

        var formData = {
            _token: '{{ csrf_token() }}',
            tipo_combustible_id: $('#tipo_combustible_id').val(),
            cantidad_gl: $('#cantidad_gl').val(),
            precio_unitario: $('#precio_unitario').val() || 0,
            fecha_carga: $('#fecha_carga').val() || new Date().toISOString().split('T')[0],
            numero_vale: $('#numero_vale').val()
        };
        
        console.log('=== GUARDANDO DOTACIÓN ===');
        console.log('Papeleta ID:', papeletaId);
        console.log('Dotación ID:', dotacionId);
        console.log('Método:', method);
        console.log('URL:', url);
        console.log('Datos:', formData);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                console.log('✅ Dotación guardada exitosamente:', response);
                var accion = dotacionId ? 'actualizada' : 'creada';
                Swal.fire({
                    title: 'Éxito',
                    text: 'Dotación de combustible ' + accion + ' correctamente',
                    icon: 'success',
                    timer: 2000
                });
                $('#dotacionModal').modal('hide');
                // Recargar la tabla de papeletas para actualizar la columna de dotación
                if (papeletasTable) {
                    papeletasTable.draw();
                }
            },
            error: function(xhr) {
                console.error('❌ Error al guardar dotación:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                var errorMsg = 'Error al guardar la dotación de combustible';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error'
                });
            }
        });
    }

    /**
     * Guardar inicio de viaje
     */
    function guardarIniciarViaje() {
        var papeletaId = $('#iniciar_papeleta_id').val();
        var kmSalida = $('#km_salida_confirm').val();

        $.ajax({
            url: '/papeletas/' + papeletaId + '/iniciar',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                km_salida: kmSalida
            },
            success: function(response) {
                console.log('Trip started:', response);
                Swal.fire({
                    title: 'Éxito',
                    text: 'Viaje iniciado correctamente',
                    icon: 'success',
                    timer: 2000
                });
                $('#iniciarViajeModal').modal('hide');
                papeletasTable.ajax.reload();
            },
            error: function(xhr) {
                console.error('Error starting trip:', xhr);
                var errorMsg = 'Error al iniciar el viaje';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error'
                });
            }
        });
    }

    /**
     * Guardar fin de viaje
     */
    function guardarFinalizarViaje() {
        var papeletaId = $('#finalizar_papeleta_id').val();
        var kmLlegada = $('#km_llegada').val();
        var observaciones = $('#observaciones').val();

        $.ajax({
            url: '/papeletas/' + papeletaId + '/finalizar',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                km_llegada: kmLlegada,
                observaciones: observaciones
            },
            success: function(response) {
                console.log('Trip ended:', response);
                Swal.fire({
                    title: 'Éxito',
                    text: 'Viaje finalizado correctamente',
                    icon: 'success',
                    timer: 2000
                });
                $('#finalizarViajeModal').modal('hide');
                papeletasTable.ajax.reload();
            },
            error: function(xhr) {
                console.error('Error ending trip:', xhr);
                var errorMsg = 'Error al finalizar el viaje';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error'
                });
            }
        });
    }

    /**
     * Guardar anulación de papeleta
     */
    function guardarAnularPapeleta() {
        var papeletaId = $('#anular_papeleta_id').val();
        var motivo = $('#motivo_anulacion').val();

        $.ajax({
            url: '/papeletas/' + papeletaId + '/anular',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                motivo_anulacion: motivo
            },
            success: function(response) {
                console.log('Papeleta cancelled:', response);
                Swal.fire({
                    title: 'Éxito',
                    text: 'Papeleta anulada correctamente',
                    icon: 'success',
                    timer: 2000
                });
                $('#anularPapeletaModal').modal('hide');
                papeletasTable.ajax.reload();
            },
            error: function(xhr) {
                console.error('Error cancelling papeleta:', xhr);
                var errorMsg = 'Error al anular la papeleta';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error'
                });
            }
        });
    }

    // ========================================
    // FUNCIONES AUXILIARES
    // ========================================

    /**
     * Generar número de vale correlativo automático
     */
    function generarNumeroPróximoVale(papeletaId) {
        $.ajax({
            url: '/papeletas/' + papeletaId + '/proximo-numero-vale',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    console.log('✅ Número de vale generado:', response.numero_vale);
                    $('#numero_vale').val(response.numero_vale);
                } else {
                    console.error('❌ Error al generar número de vale:', response.error);
                }
            },
            error: function(xhr) {
                console.error('❌ Error AJAX al generar número de vale:', xhr);
            }
        });
    }

    /**
     * Calcular costo total
     */
    function calcularCostoTotal() {
        var cantidad = parseFloat($('#cantidad_gl').val()) || 0;
        var precio = parseFloat($('#precio_unitario').val()) || 0;
        var total = cantidad * precio;

        // Actualizar el campo de visualización con formato S/.
        $('#costo_total_display').val('S/. ' + total.toFixed(2));
        console.log('💰 Costo total calculado:', total.toFixed(2));
    }

    /**
     * Actualizar resumen de dotaciones
     */
    function actualizarResumenDotaciones(resumen) {
        if (resumen) {
            $('#totalGalones').text(parseFloat(resumen.total_galones || 0).toFixed(2));
            $('#totalCosto').text(parseFloat(resumen.total_costo || 0).toFixed(2));
            $('#cantidadCargas').text(resumen.cantidad_cargas || 0);
            $('#precioPromedio').text(parseFloat(resumen.precio_promedio || 0).toFixed(2));
        }
    }

    /**
     * Generar HTML de detalles de papeleta
     */
    function generateDetalleHTML(data) {
        var estadoBadge = '';
        switch(data.estado_operacion) {
            case 'Pendiente':
                estadoBadge = '<span class="badge badge-secondary">Pendiente</span>';
                break;
            case 'En Viaje':
                estadoBadge = '<span class="badge badge-info">En Viaje</span>';
                break;
            case 'Finalizado':
                estadoBadge = '<span class="badge badge-success">Finalizado</span>';
                break;
            case 'Anulado':
                estadoBadge = '<span class="badge badge-danger">Anulado</span>';
                break;
        }

        var html = '<div class="papeleta-details">';
        html += '<div class="row">';
        html += '<div class="col-md-6"><h6>Correlativo: <strong>' + (data.correlativo || '-') + '</strong></h6></div>';
        html += '<div class="col-md-6"><h6>Estado: ' + estadoBadge + '</h6></div>';
        html += '</div>';
        html += '<hr>';
        html += '<div class="row">';
        html += '<div class="col-md-6"><h6>Fecha: <strong>' + (data.fecha_formatted || '-') + '</strong></h6></div>';
        html += '<div class="col-md-6"><h6>Destino: <strong>' + (data.destino || '-') + '</strong></h6></div>';
        html += '</div>';
        html += '<div class="row">';
        html += '<div class="col-md-6"><h6>Vehículo: <strong>' + (data.vehiculo_info || '-') + '</strong></h6></div>';
        html += '<div class="col-md-6"><h6>Cuadrilla: <strong>' + (data.cuadrilla_nombre || '-') + '</strong></h6></div>';
        html += '</div>';
        html += '<div class="row">';
        html += '<div class="col-md-6"><h6>KM Salida: <strong>' + (parseFloat(data.km_salida || 0).toFixed(3)) + '</strong></h6></div>';
        html += '<div class="col-md-6"><h6>KM Llegada: <strong>' + (parseFloat(data.km_llegada || 0).toFixed(3)) + '</strong></h6></div>';
        html += '</div>';
        html += '<div class="row">';
        html += '<div class="col-md-12"><h6>Motivo: <strong>' + (data.motivo || '-') + '</strong></h6></div>';
        html += '</div>';
        if (data.observaciones) {
            html += '<div class="row">';
            html += '<div class="col-md-12"><h6>Observaciones: <strong>' + data.observaciones + '</strong></h6></div>';
            html += '</div>';
        }
        html += '</div>';
        
        return html;
    }
    </script>
@endsection