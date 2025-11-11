@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .modal-content { background: #fff !important; }
        .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }
        .nav-tabs .nav-link.active { border-bottom-color: #007bff !important; color: #007bff !important; font-weight: bold; }
        .nav-tabs .nav-link { cursor: pointer; }
        .tab-content { margin-top: 20px; }
        .table-actions { display: flex; gap: 5px; }
        .btn-sm { padding: 0.3rem 0.6rem; font-size: 0.85rem; }
        .img-thumbnail { max-width: 100px; cursor: pointer; }
        
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #bfc9d4 !important;
            border-radius: 6px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
        }
        .select2-container { width: 100% !important; }
        
        .badge-success { background-color: #1abc9c !important; color: white !important; }
        .badge-danger { background-color: #e74c3c !important; color: white !important; }
        .badge-warning { background-color: #f39c12 !important; color: white !important; }

        .pac-container { z-index: 10000 !important; }
        .form-control:disabled, .form-control[readonly] {
            font-weight: bold;
            background-color: #f8f9fa !important;
            color: #212529 !important;
        }

        .tab-section-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4>Gestión de Fichas de Actividad</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fichaModal" id="btnNuevaFicha">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nueva Ficha de Actividad
        </button>
    </div>
</div>

<!-- TABLA PRINCIPAL -->
<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <h5 class="widget-title">Listado de Fichas</h5>
            </div>
            <div class="widget-content widget-content-area">
                <div class="table-responsive">
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

<!-- MODAL PRINCIPAL CON TABS -->
<div class="modal fade" id="fichaModal" tabindex="-1" aria-labelledby="fichaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div id="fichaForm" data-form="true">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="ficha_id" name="ficha_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="fichaModalLabel">Ficha de Actividad - Información General</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- TABS NAVIGATION -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-info" data-bs-toggle="tab" data-bs-target="#content-info" type="button" role="tab">
                                📋 Información
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-empleados" data-bs-toggle="tab" data-bs-target="#content-empleados" type="button" role="tab">
                                👥 Empleados
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-medidores" data-bs-toggle="tab" data-bs-target="#content-medidores" type="button" role="tab">
                                📊 Medidores
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-precintos" data-bs-toggle="tab" data-bs-target="#content-precintos" type="button" role="tab">
                                🔐 Precintos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-materiales" data-bs-toggle="tab" data-bs-target="#content-materiales" type="button" role="tab">
                                🔧 Materiales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-fotos" data-bs-toggle="tab" data-bs-target="#content-fotos" type="button" role="tab">
                                📸 Fotos
                            </button>
                        </li>
                    </ul>

                    <!-- TABS CONTENT -->
                    <div class="tab-content">

                        <!-- TAB: INFORMACIÓN -->
                        <div class="tab-pane fade show active" id="content-info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="suministro_id" class="form-label">Suministro <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="suministro_id" name="suministro_id" required>
                                        <option value="">Seleccione un suministro</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_actividad_id" class="form-label">Tipo de Actividad <span class="text-danger">*</span></label>
                                    <div id="selector_multinivel_container">
                                        <!-- Se generará dinámicamente -->
                                    </div>
                                    <!-- Campo oculto para enviar el ID de la actividad seleccionada -->
                                    <input type="hidden" id="tipo_actividad_id" name="tipo_actividad_id" value="">
                                    <small class="form-text text-muted">Navega por los niveles hasta elegir la actividad deseada</small>
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
                                    <input type="text" class="form-control" id="situacion_detalle" name="situacion_detalle" maxlength="100">
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
                                    <input type="text" class="form-control" id="documento" name="documento" maxlength="100">
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
                                    <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección completa">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label">📍 Latitud</label>
                                    <input type="text" class="form-control" id="latitud" name="latitud" maxlength="50" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label">📍 Longitud</label>
                                    <input type="text" class="form-control" id="longitud" name="longitud" maxlength="50" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">🗺️ Ubicación en Mapa</label>
                                    <div id="fichaMap" style="width: 100%; height: 350px; border: 2px solid #007bff; border-radius: 8px; background: #f0f0f0;"></div>
                                    <small class="form-text text-muted mt-2">Haz clic en el mapa para seleccionar ubicación</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="observacion" class="form-label">Observación</label>
                                    <textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="estado" name="estado" value="1" checked>
                                        <label class="form-check-label" for="estado">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: EMPLEADOS -->
                        <div class="tab-pane fade" id="content-empleados" role="tabpanel">
                            <div class="tab-section-title">
                                <span>👥 Empleados Asignados</span>
                                <button type="button" class="btn btn-sm btn-light" onclick="showEmpleadoForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Agregar Empleado
                                </button>
                            </div>
                            <div id="empleadosList"></div>
                        </div>

                        <!-- TAB: MEDIDORES -->
                        <div class="tab-pane fade" id="content-medidores" role="tabpanel">
                            <div class="tab-section-title d-flex align-items-center gap-2">
                                <span>📊 Medidores</span>
                                <button type="button" class="btn btn-sm btn-light" onclick="showMedidorForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Agregar Medidor
                                </button>
                                <button type="button" class="btn btn-sm btn-info text-white" onclick="verAntecedenteMedidor()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 8v4l3 3" />
                                    </svg>
                                    Antecedente
                                </button>
                            </div>
                            <div id="medidoresList"></div>
                        </div>

                        <!-- TAB: PRECINTOS -->
                        <div class="tab-pane fade" id="content-precintos" role="tabpanel">
                            <div class="tab-section-title">
                                <span>🔐 Precintos</span>
                                <button type="button" class="btn btn-sm btn-light" onclick="showPrecintoForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Agregar Precinto
                                </button>
                            </div>
                            <div id="precintosList"></div>
                        </div>

                        <!-- TAB: MATERIALES -->
                        <div class="tab-pane fade" id="content-materiales" role="tabpanel">
                            <div class="tab-section-title">
                                <span>🔧 Materiales Utilizados</span>
                                <button type="button" class="btn btn-sm btn-light" onclick="showMaterialForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Agregar Material
                                </button>
                            </div>
                            <div id="materialesList"></div>
                        </div>

                        <!-- TAB: FOTOS -->
                        <div class="tab-pane fade" id="content-fotos" role="tabpanel">
                            <div class="tab-section-title">
                                <span>📸 Fotos de la Actividad</span>
                                <button type="button" class="btn btn-sm btn-light" onclick="showFotoForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Agregar Foto
                                </button>
                            </div>
                            <div id="fotosList"></div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarFicha()">Guardar Ficha</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: AGREGAR EMPLEADO -->
<div class="modal fade" id="empleadoModal" tabindex="-1" aria-labelledby="empleadoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="empleadoModalLabel">👥 Agregar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Paso 1: Seleccionar Cuadrilla -->
                <div class="mb-3">
                    <label for="cuadrilla_seleccionada" class="form-label">Seleccionar Cuadrilla <span class="text-danger">*</span></label>
                    <select class="form-control" id="cuadrilla_seleccionada" onchange="cargarEmpleadosDeCuadrilla()" required>
                        <option value="">-- Selecciona una cuadrilla --</option>
                    </select>
                    <small class="form-text text-muted">Elige la cuadrilla primero</small>
                </div>

                <!-- Paso 2: Seleccionar Empleado (se llena después de elegir cuadrilla) -->
                <div class="mb-3">
                    <label for="cuadrilla_empleado_id" class="form-label">Seleccionar Empleado <span class="text-danger">*</span></label>
                    <select class="form-control" id="cuadrilla_empleado_id" required disabled>
                        <option value="">-- Selecciona una cuadrilla primero --</option>
                    </select>
                    <small class="form-text text-muted">Se mostrarán los empleados de la cuadrilla seleccionada</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEmpleado()">Agregar Empleado</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: AGREGAR MEDIDOR -->
<div class="modal fade" id="medidorModal" tabindex="-1" aria-labelledby="medidorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="medidorModalLabel">📊 Agregar Medidor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="medidorForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="medidor_id" class="form-label">Medidor <span class="text-danger">*</span></label>
                        <select class="form-control" id="medidor_id" required>
                            <option value="">Cargando...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="medidor_tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-control" id="medidor_tipo" required>
                            <option value="">-- Selecciona tipo --</option>
                            <option value="nuevo">🆕 Nuevo</option>
                            <option value="retirado">🔴 Retirado</option>
                            <option value="existente">✓ Existente</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="medidor_lectura" class="form-label">Lectura</label>
                                <input type="number" class="form-control" id="medidor_lectura" placeholder="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="digitos_enteros" class="form-label">Dígitos Enteros</label>
                                <input type="number" class="form-control" id="digitos_enteros" placeholder="0" step="1">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="digitos_decimales" class="form-label">Dígitos Decimales</label>
                        <input type="number" class="form-control" id="digitos_decimales" placeholder="0" step="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarMedidor()">Agregar Medidor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: AGREGAR PRECINTO -->
<div class="modal fade" id="precintoModal" tabindex="-1" aria-labelledby="precintoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="precintoModalLabel">🔐 Agregar Precinto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="precintoForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="medidor_ficha_actividad_id" class="form-label">Medidor <span class="text-danger">*</span></label>
                        <select class="form-control" id="medidor_ficha_actividad_id" required>
                            <option value="">Cargando...</option>
                        </select>
                        <small class="form-text text-muted">Selecciona un medidor asignado a esta ficha</small>
                    </div>

                    <div class="mb-3">
                        <label for="precinto_tipo" class="form-label">Tipo de Precinto <span class="text-danger">*</span></label>
                        <select class="form-control" id="precinto_tipo" required>
                            <option value="">-- Selecciona tipo --</option>
                            <option value="tapa">🔒 Tapa</option>
                            <option value="caja">📦 Caja</option>
                            <option value="bornera">⚡ Bornera</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="numero_precinto" class="form-label">Número de Precinto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="numero_precinto" placeholder="Ej: PREC-2025-001" required>
                        <small class="form-text text-muted">Debe ser único en el sistema</small>
                    </div>

                    <div class="mb-3">
                        <label for="precinto_material_id" class="form-label">Material (Precinto)</label>
                        <select class="form-control" id="precinto_material_id">
                            <option value="">Cargando...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarPrecinto()">Agregar Precinto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: AGREGAR MATERIAL -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialModalLabel">🔧 Agregar Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="materialForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="material_id_detalle" class="form-label">Material <span class="text-danger">*</span></label>
                        <select class="form-control" id="material_id_detalle" required>
                            <option value="">Cargando...</option>
                        </select>
                        <small class="form-text text-muted">Materiales de la cuadrilla en obra</small>
                    </div>

                    <div class="mb-3">
                        <label for="material_cantidad" class="form-label">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="material_cantidad" placeholder="0.00" step="0.01" min="0.001" required>
                    </div>

                    <div class="mb-3">
                        <label for="material_observacion" class="form-label">Observación</label>
                        <textarea class="form-control" id="material_observacion" rows="3" placeholder="Nota sobre el material usado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarMaterial()">Agregar Material</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: AGREGAR FOTO -->
<div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotoModalLabel">📸 Agregar Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fotoForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="foto_url" class="form-label">URL de la Foto <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="foto_url" placeholder="https://..." required>
                        <small class="form-text text-muted">Enlace completo de la imagen (ej: de CloudStorage, Imgur, etc)</small>
                    </div>

                    <div class="mb-3">
                        <label for="foto_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="foto_descripcion" rows="3" placeholder="Describe el contenido de la foto..."></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong>💡 Tips para subir fotos:</strong>
                            <ul class="mb-0">
                                <li>Puedes usar servicios gratuitos como <strong>imgur.com</strong> o <strong>photobucket.com</strong></li>
                                <li>Sube la foto y copia el enlace aquí</li>
                                <li>También puedes usar URL de Google Drive (compartida públicamente)</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarFoto()">Agregar Foto</button>
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
    const PUCALLPA_CENTER = { lat: -8.3789, lng: -74.5234 };
    let fichaActualId = null;
    let fichaMap;
    let fichaMapMarker = null;
    let evitarGuardoAutomatico = false; // Flag para prevenir guardos accidentales

    $(document).ready(function() {
        inicializarTabla();
        configurarSelect2();
        cargarOpciones();
        configurarFormulario();
    });

    function inicializarTabla() {
        if ($.fn.dataTable.isDataTable('#fichaTable')) {
            $('#fichaTable').DataTable().destroy();
        }

        $('#fichaTable').DataTable({
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

    function configurarSelect2() {
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('#fichaModal')
        });
    }

    function cargarOpciones() {
        $.get('/suministro/api/select', function(data) {
            var select = $('#suministro_id');
            select.empty().append('<option value="">Seleccione un suministro</option>');
            $.each(data, function(i, item) {
                select.append(`<option value="${item.id}">${item.nombre}</option>`);
            });
        });

        // Cargar árbol de actividades multinivel
        $.get('/tipos-actividad/con-hijos/select', function(data) {
            construirSelectorMultinivel(data);
        }).fail(function() {
            console.error('Error al cargar actividades');
        });

        $.get('/tipo_propiedades/select', function(data) {
            var select = $('#tipo_propiedad_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(i, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });

        $.get('/construcciones/select', function(data) {
            var select = $('#construccion_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(i, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });

        $.get('/usos/select', function(data) {
            var select = $('#uso_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(i, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });

        $.get('/situaciones/select', function(data) {
            var select = $('#situacion_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(i, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });

        $.get('/servicios-electricos/select', function(data) {
            var select = $('#servicio_electrico_id');
            select.empty().append('<option value="">Seleccione</option>');
            $.each(data, function(i, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
        });
    }

    // Variable global para guardar el árbol de actividades
    let arbolActividades = [];

    function construirSelectorMultinivel(arbol) {
        arbolActividades = arbol;
        const container = $('#selector_multinivel_container');
        container.empty();

        // Crear primer nivel (actividades principales)
        const select0 = $(`
            <select class="form-control select2 actividad-selector" data-nivel="0" name="actividad_nivel_0" required>
                <option value="">-- Selecciona una actividad --</option>
            </select>
        `);

        $.each(arbol, function(i, actividad) {
            select0.append(`<option value="${actividad.id}" data-hijos='${JSON.stringify(actividad.children || [])}'>📋 ${actividad.text}</option>`);
        });

        container.append(select0);

        // Event listener para nivel 0
        select0.off('change').on('change', function() {
            manejarCambioActividad(0);
        });

        // Inicializar Select2
        select0.select2({
            width: '100%',
            dropdownParent: $('#fichaModal')
        });

        // Guardar el ID final en hidden field
        $('#tipo_actividad_id').val('');
    }

    function manejarCambioActividad(nivelActual) {
        const selectActual = $(`.actividad-selector[data-nivel="${nivelActual}"]`);
        const idSeleccionado = selectActual.val();
        const hijosCodificados = selectActual.find('option:selected').data('hijos');
        const container = $('#selector_multinivel_container');

        // Eliminar todos los selectores posteriores a este nivel
        $(`.actividad-selector[data-nivel]`).each(function() {
            const nivel = parseInt($(this).data('nivel'));
            if (nivel > nivelActual) {
                $(this).closest('div').remove();
            }
        });

        // Si no hay selección, limpiar
        if (!idSeleccionado) {
            $('#tipo_actividad_id').val('');
            return;
        }

        // IMPORTANTE: Actualizar el campo oculto con la actividad seleccionada
        $('#tipo_actividad_id').val(idSeleccionado);

        // Si hay hijos, crear nuevo selector para el siguiente nivel
        if (hijosCodificados && Array.isArray(hijosCodificados) && hijosCodificados.length > 0) {
            const nivelSiguiente = nivelActual + 1;
            const selectNuevo = $(`
                <div class="mt-2">
                    <select class="form-control select2 actividad-selector" data-nivel="${nivelSiguiente}" name="actividad_nivel_${nivelSiguiente}" required>
                        <option value="">-- Selecciona un nivel más específico --</option>
                    </select>
                </div>
            `);

            $.each(hijosCodificados, function(i, hijo) {
                selectNuevo.find('select').append(`<option value="${hijo.id}" data-hijos='${JSON.stringify(hijo.children || [])}'>${'→'.repeat(nivelSiguiente)} ${hijo.text}</option>`);
            });

            container.append(selectNuevo);

            // Event listener para nuevo nivel
            selectNuevo.find('select').off('change').on('change', function() {
                manejarCambioActividad(nivelSiguiente);
            });

            // Inicializar Select2
            selectNuevo.find('select').select2({
                width: '100%',
                dropdownParent: $('#fichaModal')
            });
        }
    }

    function actualizarActividadFinal() {
        // Encontrar el selector con valor más profundo
        let idFinal = '';
        $(`.actividad-selector[data-nivel]`).each(function() {
            const valor = $(this).val();
            if (valor) {
                idFinal = valor;
            }
        });
        $('#tipo_actividad_id').val(idFinal);
        console.log('✓ Actividad final actualizada: ' + idFinal);
    }

    function configurarFormulario() {
        // No usar submit event - usar onclick directo en el botón en su lugar
        // Esto previene que se ejecute guardarFicha() automáticamente

        $('#btnNuevaFicha').on('click', function() {
            fichaActualId = null;
            // Limpiar antes de mostrar el modal
            limpiarFormulario();
            // Recargar opciones solo al crear nueva ficha
            cargarOpciones();
            // Mostrar el modal (por si se quiere abrir manualmente)
            $('#fichaModal').modal('show');
        });

        // Limpiar SIEMPRE antes de mostrar el modal, sin importar cómo se abra, solo si es nueva ficha
        $('#fichaModal').on('show.bs.modal', function() {
            if (!fichaActualId) {
                limpiarFormulario();
                cargarOpciones();
            }
        });

        $('#fichaModal').on('hidden.bs.modal', function() {
            limpiarFormulario();
        });

        $('#fichaModal').on('shown.bs.modal', function() {
            if (!fichaMap) {
                inicializarMapa();
            }
            google.maps.event.trigger(fichaMap, 'resize');
            fichaMap.setCenter(PUCALLPA_CENTER);
            fichaMap.setZoom(14);
        });
    }

    function limpiarFormulario() {
    // Limpiar todos los campos del formulario principal
    $('#ficha_id').val('');
    // $('#fichaForm')[0].reset(); // No usar, #fichaForm es un div, no un form
    $('#latitud').val('');
    $('#longitud').val('');
    $('#estado').prop('checked', true); // Siempre activo al crear
    $('#fichaModalLabel').text('Ficha de Actividad - Información General');
    limpiarDetalles();

        // Limpiar todos los inputs, textareas y selects (incluyendo los ocultos)
        $('#fichaForm').find('input[type="text"], input[type="number"], input[type="date"], input[type="hidden"], textarea').val('');
        $('#fichaForm').find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
        $('#fichaForm').find('select').val('').trigger('change');
        // Limpiar select2
        $('#suministro_id').val('').trigger('change');
        $('#tipo_propiedad_id').val('').trigger('change');
        $('#construccion_id').val('').trigger('change');
        $('#servicio_electrico_id').val('').trigger('change');
        $('#uso_id').val('').trigger('change');
        $('#situacion_id').val('').trigger('change');

        // Limpiar selector multinivel de actividades
        construirSelectorMultinivel(arbolActividades);

        // Limpiar archivos adjuntos y previews si existen
        if (typeof limpiarArchivosAdjuntos === 'function') limpiarArchivosAdjuntos();
        if (typeof limpiarPreviews === 'function') limpiarPreviews();

        // Reiniciar tabs al primero
        $('#fichaModal .nav-tabs .nav-link').removeClass('active');
        $('#fichaModal .nav-tabs .nav-link').first().addClass('active');
        $('#fichaModal .tab-pane').removeClass('active show');
        $('#fichaModal .tab-pane').first().addClass('active show');

        // Cerrar alertas o mensajes dentro del modal
        $('#fichaModal .alert').remove();

        // Limpiar cualquier error de validación
        $('#fichaForm .is-invalid').removeClass('is-invalid');
        $('#fichaForm .invalid-feedback').remove();

        // Limpiar campos especiales
        if (typeof limpiarCamposEspeciales === 'function') limpiarCamposEspeciales();
    }

    function limpiarDetalles() {
        $('#empleadosList').html('');
        $('#medidoresList').html('');
        $('#precintosList').html('');
        $('#materialesList').html('');
        $('#fotosList').html('');
    }

    function guardarFicha() {
        console.log('🔴 ALERTA: guardarFicha() fue llamado. evitarGuardoAutomatico:', evitarGuardoAutomatico);
        console.trace('📍 STACK TRACE - Quién llamó a guardarFicha():');
        
        // Prevenir guardos accidentales mientras se está eliminando un detalle
        if (evitarGuardoAutomatico) {
            console.log('⚠️ GUARDO BLOQUEADO: evitarGuardoAutomatico es TRUE');
            return;
        }

        console.log('✅ Procediendo a guardar ficha...');

        const data = {
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
            latitud: $('#latitud').val() || null,
            longitud: $('#longitud').val() || null,
            direccion: $('#direccion').val() || null,
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
            data: JSON.stringify(data),
            success: function(response) {
                fichaActualId = response.data.id;
                Swal.fire('Éxito', response.message, 'success');
                cargarDetalles(fichaActualId);
                // Recargar la tabla de DataTables
                if ($.fn.dataTable.isDataTable('#fichaTable')) {
                    $('#fichaTable').DataTable().ajax.reload();
                }
                // No cerrar el modal automáticamente
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Error al guardar';
                Swal.fire('Error', msg, 'error');
            }
        });
    }

    function setSuministroSelect2(id, nombre) {
        var $suministro = $('#suministro_id');
        var intentos = 0;
        function intentarSeleccionar() {
            intentos++;
            // 1. Destruir select2 antes de modificar opciones
            if ($suministro.data('select2')) {
                $suministro.select2('destroy');
            }
            // 2. Agregar la opción si no existe
            if ($suministro.find('option[value="'+id+'"]').length === 0 && nombre) {
                $suministro.append('<option value="'+id+'">'+nombre+'</option>');
            }
            // 3. Asignar el valor
            $suministro.val(id);
            // 4. Volver a inicializar select2
            $suministro.select2({ width: '100%', dropdownParent: $('#fichaModal') });
            // 5. Forzar trigger de cambio
            $suministro.trigger('change');
            // Seguimiento
            console.log('[Suministro] Intento', intentos, 'valor actual =', $suministro.val(), 'esperado =', id);
            if ($suministro.val() != id && intentos < 10) {
                setTimeout(intentarSeleccionar, 150);
            } else {
                if ($suministro.val() != id) {
                    console.warn('[Suministro] No se pudo seleccionar el suministro con id', id, 'tras', intentos, 'intentos.');
                } else {
                    console.log('[Suministro] Selección exitosa:', id);
                }
            }
        }
        intentarSeleccionar();
    }

    function setSelect2Value(selector, id, nombre) {
        var $select = $(selector);
        if (!$select.length) return;
        if ($select.data('select2')) {
            $select.select2('destroy');
        }
        if (id && $select.find('option[value="'+id+'"]').length === 0 && nombre) {
            $select.append('<option value="'+id+'">'+nombre+'</option>');
        }
        $select.val(id);
        $select.select2({ width: '100%', dropdownParent: $('#fichaModal') });
        $select.trigger('change');
    }

    /**
     * Cargar pecosas disponibles según la cuadrilla de los empleados de la ficha
     * Si hay empleados asignados, carga pecosas de esa cuadrilla
     * Si no hay empleados, muestra todas las pecosas
     */

    function editarFicha(id) {
        $.ajax({
            url: `{{ url('fichas-actividad') }}/${id}`,
            type: 'GET',
            success: function(response) {
                fichaActualId = id;
                const ficha = response.data;
                
                $('#ficha_id').val(ficha.id);

                // Refuerzo selección de suministro
                var $suministro = $('#suministro_id');
                function setSuministro() {
                    if ($suministro.find('option[value="'+ficha.suministro_id+'"]').length === 0 && ficha.suministro) {
                        $suministro.append('<option value="'+ficha.suministro_id+'">'+ficha.suministro.nombre+'</option>');
                    }
                    $suministro.val(ficha.suministro_id).trigger('change').trigger({type:'select2:select'});
                }
                if ($suministro.children('option').length <= 1) {
                    // Si aún no cargó, esperar y reintentar
                    setTimeout(setSuministro, 200);
                } else {
                    setSuministro();
                }
                
                // Usar función robusta para seleccionar suministro
                setSuministroSelect2(ficha.suministro_id, ficha.suministro ? ficha.suministro.nombre : null);
                
                // Reconstruir el selector multinivel con la actividad guardada
                reconstruirSelectorMultinivel(ficha.tipo_actividad_id);
                
                setSelect2Value('#tipo_propiedad_id', ficha.tipo_propiedad_id, ficha.tipo_propiedad ? ficha.tipo_propiedad.nombre : null);
                setSelect2Value('#construccion_id', ficha.construccion_id, ficha.construccion ? ficha.construccion.nombre : null);
                setSelect2Value('#servicio_electrico_id', ficha.servicio_electrico_id, ficha.servicio_electrico ? ficha.servicio_electrico.nombre : null);
                setSelect2Value('#uso_id', ficha.uso_id, ficha.uso ? ficha.uso.nombre : null);
                setSelect2Value('#situacion_id', ficha.situacion_id, ficha.situacion ? ficha.situacion.nombre : null);
                
                $('#numero_piso').val(ficha.numero_piso || '');
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

                setTimeout(function() {
                    if (!fichaMap) {
                        inicializarMapa();
                    }
                    cargarMarcador(ficha.latitud, ficha.longitud);
                    cargarDetalles(id);
                }, 500);
            }
        });
    }

    function reconstruirSelectorMultinivel(actividadId) {
        // Obtener los datos de la actividad seleccionada y su jerarquía
        $.get(`/tipos-actividad/${actividadId}`, function(tipoData) {
            const tipo = tipoData.data;
            const ruta = []; // Array para guardar la ruta hasta el ID final

            // Función para construir la ruta desde el padre hasta el ID actual
            function construirRuta(tipoActual, callback) {
                ruta.unshift(tipoActual.id); // Agregar al inicio del array

                if (tipoActual.dependencia_id) {
                    // Obtener el padre
                    $.get(`/tipos-actividad/${tipoActual.dependencia_id}`, function(padreData) {
                        construirRuta(padreData.data, callback);
                    });
                } else {
                    // Llegamos a la raíz
                    callback();
                }
            }

            construirRuta(tipo, function() {
                // Ahora ruta contiene [raiz_id, nivel1_id, ..., actividadId]
                construirSelectorMultinivel(arbolActividades);

                // Seleccionar cada nivel
                setTimeout(function() {
                    $.each(ruta, function(index, id) {
                        const selector = $(`.actividad-selector[data-nivel="${index}"]`);
                        if (selector.length > 0) {
                            selector.val(id).trigger('change');
                        }
                    });
                }, 100);
            });
        });
    }

    function cargarDetalles(fichaId) {
        cargarEmpleados(fichaId);
        cargarMedidores(fichaId);
        cargarPrecintos(fichaId);
        cargarMateriales(fichaId);
        cargarFotos(fichaId);
    }

    // ===== FUNCIONES DE CARGA POR TAB =====

    function cargarEmpleados(fichaId) {
        $.get(`/fichas-actividad/${fichaId}/detalles/empleados`, function(response) {
            console.log('✓ Empleados cargados:', response.data);
            let html = '';
            
            if (response.data.length === 0) {
                html = '<div class="empty-state"><p>No hay empleados asignados</p></div>';
            } else {
                html = '<table class="table table-sm"><thead><tr><th>Cuadrilla</th><th>Empleado</th><th>Acciones</th></tr></thead><tbody>';
                $.each(response.data, function(i, item) {
                    const empleadoNombre = item.cuadrilla_empleado?.empleado?.nombre || 'N/A';
                    const empleadoApellido = item.cuadrilla_empleado?.empleado?.apellido || '';
                    const empleadoCompleto = empleadoApellido ? `${empleadoNombre} ${empleadoApellido}` : empleadoNombre;
                    const cuadrilla = item.cuadrilla_empleado?.cuadrilla?.nombre || 'N/A';
                    const btnEliminar = '<button class="btn btn-sm btn-danger" onclick="eliminarEmpleado(' + fichaId + ', ' + item.id + ')">Eliminar</button>';
                    html += '<tr><td>' + cuadrilla + '</td><td>' + empleadoCompleto + '</td><td>' + btnEliminar + '</td></tr>';
                    console.log('Row ' + i + ':', {ficha: fichaId, empleado_id: item.id, cuadrilla: cuadrilla, empleado: empleadoCompleto});
                });
                html += '</tbody></table>';
            }
            $('#empleadosList').html(html);
            console.log('✓ HTML generado y renderizado');
        }).fail(function(error) {
            console.error('✗ Error cargando empleados:', error);
        });
    }

    function cargarMedidores(fichaId) {
        $.get(`/fichas-actividad/${fichaId}/detalles/medidores`, function(response) {
            console.log('✓ Medidores cargados:', response.data);
            let html = '';
            if (response.data.length === 0) {
                html = '<div class="empty-state"><p>No hay medidores registrados</p></div>';
            } else {
                html = '<table class="table table-sm"><thead><tr><th>Medidor</th><th>Tipo</th><th>Lectura</th><th>Acciones</th></tr></thead><tbody>';
                $.each(response.data, function(i, item) {
                    const medidorSerie = item.medidor?.serie || 'N/A';
                    const btnEliminar = `<button class=\"btn btn-sm btn-danger\" onclick=\"eliminarMedidor(${fichaId}, ${item.id})\">Eliminar</button>`;
                    html += `<tr><td>${medidorSerie}</td><td><span class=\"badge bg-info\">${item.tipo}</span></td><td>${item.lectura || '-'}</td><td>${btnEliminar}</td></tr>`;
                    console.log('Row ' + i + ':', {medidor: medidorSerie, tipo: item.tipo});
                });
                html += '</tbody></table>';
            }
            $('#medidoresList').html(html);
        });
        // Modal para historial de medidores
        $(document.body).append(`
        <div class="modal fade" id="antecedenteMedidorModal" tabindex="-1" aria-labelledby="antecedenteMedidorLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="antecedenteMedidorLabel">Historial de Medidores del Suministro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <div id="historialMedidorContent" style="overflow-x:auto;">
                  <div class="text-center text-muted">Cargando historial...</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        `);

        // Función global para mostrar el historial
        window.verAntecedenteMedidor = function() {
                // Obtener el suministro seleccionado en la ficha
                const suministroId = $('#suministro_id').val();
                if (!suministroId) {
                        Swal.fire('Aviso', 'Primero selecciona un suministro en la ficha', 'warning');
                        return;
                }
                $('#antecedenteMedidorModal').modal('show');
                $('#historialMedidorContent').html('<div class="text-center text-muted">Cargando historial...</div>');
                $.get(`/suministro/${suministroId}/medidores-historial`, function(resp) {
                        if (!resp.success || !resp.data || resp.data.length === 0) {
                                $('#historialMedidorContent').html('<div class="alert alert-info">No hay historial de medidores para este suministro.</div>');
                                return;
                        }
                        let html = '<table class="table table-bordered table-sm"><thead><tr><th>Serie</th><th>Modelo</th><th>Fecha</th><th>Observaciones</th><th>Ficha</th><th>Estado</th></tr></thead><tbody>';
                        resp.data.forEach(function(item) {
                                html += `<tr>
                                        <td>${item.medidor_serie || '-'}</td>
                                        <td>${item.medidor_modelo || '-'}</td>
                                        <td>${item.fecha_cambio || '-'}</td>
                                        <td>${item.observaciones || '-'}</td>
                                        <td>${item.ficha_id ? ('#' + item.ficha_id) : '-'}</td>
                                        <td>${item.estado}</td>
                                </tr>`;
                        });
                        html += '</tbody></table>';
                        $('#historialMedidorContent').html(html);
                }).fail(function() {
                        $('#historialMedidorContent').html('<div class="alert alert-danger">Error al cargar el historial</div>');
                });
        }
    }

    function cargarPrecintos(fichaId) {
        $.get(`/fichas-actividad/${fichaId}/detalles/precintos`, function(response) {
            console.log('✓ Precintos cargados:', response.data);
            let html = '';
            if (response.data.length === 0) {
                html = '<div class="empty-state"><p>No hay precintos registrados</p></div>';
            } else {
                html = '<table class="table table-sm"><thead><tr><th>Número</th><th>Tipo</th><th>Medidor</th><th>Acciones</th></tr></thead><tbody>';
                $.each(response.data, function(i, item) {
                    const medidorSerie = item.medidor_ficha_actividad?.medidor?.serie || 'N/A';
                    html += `<tr><td>${item.numero_precinto}</td><td><span class="badge bg-warning">${item.tipo}</span></td><td>${medidorSerie}</td><td><button class="btn btn-sm btn-danger" onclick="eliminarPrecinto(${fichaId}, ${item.id})">Eliminar</button></td></tr>`;
                    console.log('Row ' + i + ':', {numero: item.numero_precinto, medidor: medidorSerie});
                });
                html += '</tbody></table>';
            }
            $('#precintosList').html(html);
        });
    }

    function cargarMateriales(fichaId) {
        $.get(`/fichas-actividad/${fichaId}/detalles/materiales`, function(response) {
            let html = '';
            if (response.data.length === 0) {
                html = '<div class="empty-state"><p>No hay materiales registrados</p></div>';
            } else {
                html = '<table class="table table-sm"><thead><tr><th>Material</th><th>Cantidad</th><th>Observación</th><th>Acciones</th></tr></thead><tbody>';
                $.each(response.data, function(i, item) {
                    html += `<tr><td>${item.material?.nombre}</td><td>${item.cantidad}</td><td>${item.observacion || '-'}</td><td><button class="btn btn-sm btn-danger" onclick="eliminarMaterial(${fichaId}, ${item.id})">Eliminar</button></td></tr>`;
                });
                html += '</tbody></table>';
            }
            $('#materialesList').html(html);
        });
    }

    function cargarFotos(fichaId) {
        $.get(`/fichas-actividad/${fichaId}/detalles/fotos`, function(response) {
            let html = '';
            if (response.data.length === 0) {
                html = '<div class="empty-state"><p>No hay fotos</p></div>';
            } else {
                html = '<div class="row">';
                $.each(response.data, function(i, item) {
                    html += `<div class="col-md-4 mb-3">
                        <div class="card">
                            <img src="${item.url}" class="card-img-top img-thumbnail" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <p class="card-text">${item.descripcion || '-'}</p>
                                <button class="btn btn-sm btn-danger" onclick="eliminarFoto(${fichaId}, ${item.id})">Eliminar</button>
                            </div>
                        </div>
                    </div>`;
                });
                html += '</div>';
            }
            $('#fotosList').html(html);
        });
    }

    // ===== FUNCIONES PARA MOSTRAR FORMULARIOS =====

    function showEmpleadoForm() {
        if (!fichaActualId) {
            Swal.fire('Aviso', 'Primero guarda la ficha principal', 'warning');
            return;
        }
        cargarCuadrillasEmpleados();
        $('#empleadoModal').modal('show');
    }

    function showMedidorForm() {
        if (!fichaActualId) {
            Swal.fire('Aviso', 'Primero guarda la ficha principal', 'warning');
            return;
        }
        cargarMedidoresDisponibles();
        $('#medidorModal').modal('show');
    }

    function showPrecintoForm() {
        if (!fichaActualId) {
            Swal.fire('Aviso', 'Primero guarda la ficha principal', 'warning');
            return;
        }
        cargarMedidoresAsignados();
        cargarMaterialesParaPrecintos();
        $('#precintoModal').modal('show');
    }

    function showMaterialForm() {
        if (!fichaActualId) {
            Swal.fire('Aviso', 'Primero guarda la ficha principal', 'warning');
            return;
        }
        cargarMaterialesDisponibles();
        $('#materialModal').modal('show');
    }

    function showFotoForm() {
        if (!fichaActualId) {
            Swal.fire('Aviso', 'Primero guarda la ficha principal', 'warning');
            return;
        }
        $('#fotoModal').modal('show');
    }

    // ===== CARGAR OPCIONES PARA MODALES =====

    function cargarCuadrillasEmpleados() {
        console.log('✓ Iniciando carga de cuadrilla del usuario autenticado...');
        
        // Obtener SOLO la cuadrilla del usuario autenticado
        $.get('/api/user/me', function(userResponse) {
            console.log('👤 Usuario autenticado:', userResponse);
            
            if (!userResponse.cuadrilla || !userResponse.cuadrilla.id) {
                Swal.fire('Error', 'Tu usuario no tiene cuadrilla asignada', 'error');
                return;
            }
            
            const cuadrilla = userResponse.cuadrilla;
            const cuadrillaId = cuadrilla.id;
            
            // Mostrar SOLO tu cuadrilla en el select
            let html = `<option value="${cuadrillaId}" selected>${cuadrilla.nombre}</option>`;
            $('#cuadrilla_seleccionada').html(html);
            console.log('✓ Tu cuadrilla mostrada:', cuadrilla.nombre);
            
            // Cargar empleados de tu cuadrilla automáticamente
            cargarEmpleadosDeCuadrilla();
        }).fail(function(error) {
            console.error('✗ Error obteniendo cuadrilla del usuario:', error);
            Swal.fire('Error', 'No se pudo obtener tu información de cuadrilla', 'error');
        });
    }

    function cargarEmpleadosDeCuadrilla() {
        const cuadrillaId = $('#cuadrilla_seleccionada').val();
        console.log('✓ Cargando empleados de tu cuadrilla:', cuadrillaId);

        // Si no hay cuadrilla seleccionada, limpiar
        if (!cuadrillaId) {
            $('#cuadrilla_empleado_id')
                .html('<option value="">-- Selecciona una cuadrilla primero --</option>')
                .prop('disabled', true);
            console.log('✗ No hay cuadrilla seleccionada');
            return;
        }

        // Cargar empleados de la cuadrilla seleccionada
        // Pasar fichaActualId para excluir empleados ya agregados a esta ficha
        $.get(`/cuadrillas/${cuadrillaId}/empleados/disponibles?ficha_id=${fichaActualId}`, function(data) {
            console.log('✓ Empleados de tu cuadrilla cargados:', data);
            
            // El API retorna {results: [...], pagination: {...}}
            const empleados = data.results || data || [];
            console.log('✓ Empleados a procesar:', empleados);
            
            let html = '<option value="">-- Selecciona un empleado --</option>';

            if (empleados.length === 0) {
                html = '<option value="">No hay empleados disponibles en esta cuadrilla</option>';
                $('#cuadrilla_empleado_id').html(html).prop('disabled', true);
                console.log('⚠ No hay empleados disponibles');
            } else {
                $.each(empleados, function(i, item) {
                    html += `<option value="${item.id}">${item.text}</option>`;
                });
                $('#cuadrilla_empleado_id').html(html).prop('disabled', false);
                console.log('✓ Select de empleados actualizado con', empleados.length, 'empleados');
            }
        }).fail(function(error) {
            console.error('✗ Error cargando empleados:', error);
            Swal.fire('Error', 'No se pudieron cargar los empleados de la cuadrilla', 'error');
            $('#cuadrilla_empleado_id').prop('disabled', true);
        });
    }

    function cargarMedidoresDisponibles() {
        var suministroId = $('#suministro_id').val();
        $.get(`/medidor/select?ficha_id=${fichaActualId}&suministro_id=${suministroId}`, function(data) {
            console.log('✓ Medidores disponibles cargados:', data);
            let html = '<option value="">-- Selecciona un medidor --</option>';
            $.each(data, function(i, item) {
                html += `<option value="${item.id}">${item.numero}</option>`;
            });
            $('#medidor_id').html(html);
        }).fail(function(error) {
            console.error('✗ Error cargando medidores:', error);
            Swal.fire('Error', 'No se pudieron cargar los medidores', 'error');
        });
    }

    function cargarMedidoresAsignados() {
        $.get(`/fichas-actividad/${fichaActualId}/detalles/medidores`, function(response) {
            let html = '<option value="">-- Selecciona un medidor --</option>';
            $.each(response.data, function(i, item) {
                html += `<option value="${item.id}">${item.medidor.serie} - ${item.medidor.modelo} (${item.tipo})</option>`;
            });
            $('#medidor_ficha_actividad_id').html(html);
        }).fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los medidores asignados', 'error');
        });
    }

    function cargarMaterialesParaPrecintos() {
        $.get('/materiales/select', function(data) {
            let html = '<option value="">-- Selecciona un material (precinto) --</option>';
            let primerMaterial = null;
            $.each(data, function(i, item) {
                // Solo mostrar materiales que inicien con "precinto" (case-insensitive)
                if (item.nombre.toLowerCase().startsWith('precinto')) {
                    html += `<option value="${item.id}">${item.nombre}</option>`;
                    // Guardar el primero encontrado
                    if (!primerMaterial) {
                        primerMaterial = item.id;
                    }
                }
            });
            $('#precinto_material_id').html(html);
            // Seleccionar automáticamente el primer material si existe
            if (primerMaterial) {
                $('#precinto_material_id').val(primerMaterial);
            }
        }).fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los materiales', 'error');
        });
    }

    function cargarMaterialesDisponibles() {
        const select = $('#material_id_detalle');
        select.html('<option value="">Cargando materiales...</option>');
        
        if (!fichaActualId) {
            select.html('<option value="">-- Primero guarda la ficha --</option>');
            return;
        }

        console.log('🔄 Cargando materiales disponibles para ficha:', fichaActualId);

        // PASO 1: Obtener la cuadrilla del usuario autenticado
        $.get('/api/user/me', function(userResponse) {
            console.log('👤 Usuario autenticado:', userResponse);
            
            if (!userResponse.cuadrilla_id) {
                select.html('<option value="">-- Tu usuario no tiene cuadrilla asignada --</option>');
                return;
            }
            
            const cuadrillaId = userResponse.cuadrilla_id;
            console.log('👤 Tu cuadrilla ID:', cuadrillaId);
            
            // PASO 2: Obtener PECOSA de TU cuadrilla
            $.get(`/pecosas/cuadrilla/${cuadrillaId}/pecosas`, function(pecosasResponse) {
                console.log('📦 Tus PECOSAS:', pecosasResponse);
                
                if (!pecosasResponse.success || !pecosasResponse.data || pecosasResponse.data.length === 0) {
                    console.warn('⚠️ Tu cuadrilla NO tiene PECOSA asignada');
                    select.html('<option value="">-- Tu cuadrilla no tiene PECOSA asignada --</option>');
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'warning',
                        title: '⚠️ Tu cuadrilla no tiene PECOSA'
                    });
                    return;
                }
                
                const pecosaId = pecosasResponse.data[0].id;
                console.log('📦 Tu PECOSA seleccionada ID:', pecosaId);
                cargarMaterialesDePecosa(pecosaId);
            }).fail(function(err) {
                console.error('❌ Error obteniendo pecosas:', err);
                select.html('<option value="">-- Error al cargar pecosas --</option>');
            });
        }).fail(function(err) {
            console.error('❌ Error obteniendo usuario autenticado:', err);
            select.html('<option value="">-- Error al cargar información de usuario --</option>');
        });
    }

    function cargarMaterialesDePecosa(pecosaId) {
        const select = $('#material_id_detalle');
        console.log('📥 Cargando materiales de pecosa:', pecosaId);
        
        $.ajax({
            url: `/pecosas/${pecosaId}/materiales`,
            type: 'GET',
            success: function(response) {
                console.log('📥 Respuesta completa:', response);
                console.log('📥 Materiales recibidos:', response.data);
                console.log('📥 Cantidad de materiales:', response.data?.length || 0);
                
                if (response.success && Array.isArray(response.data) && response.data.length > 0) {
                    console.log('✅ Mostrando materiales en SELECT');
                    select.html('<option value="">-- Selecciona un material --</option>');
                    $.each(response.data, function(i, material) {
                        console.log(`  Material ${i+1}:`, material);
                        const label = material.nombre + ' (Stock: ' + material.saldo_disponible + ' ' + (material.unidad_medida?.nombre || 'un') + ')';
                        select.append(`<option value="${material.id}" data-saldo="${material.saldo_disponible}" data-unidad="${material.unidad_medida?.nombre || 'un'}" data-precio="${material.precio_unitario || 0}">${label}</option>`);
                    });
                } else {
                    // PECOSA vacía - mostrar mensaje útil
                    console.warn('⚠️ PECOSA sin materiales');
                    select.html('<option value="">-- PECOSA sin materiales asignados --</option>');
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'warning',
                        title: '⚠️ Esta PECOSA no tiene materiales asignados'
                    });
                    
                    console.warn('⚠️ PECOSA ' + response.pecosa_nro + ' sin materiales. Agrega materiales desde el módulo de PECOSAS.');
                }
            },
            error: function(error) {
                console.error('❌ Error cargando materiales de pecosa:', error);
                console.error('Status:', error.status);
                console.error('StatusText:', error.statusText);
                console.error('ResponseText:', error.responseText);
                const errorMsg = error.responseJSON?.message || error.statusText || 'Error desconocido';
                select.html('<option value="">-- Error: ' + errorMsg + ' --</option>');
            }
        });
    }

    // ===== GUARDAR DETALLES =====

    function guardarEmpleado() {
        const cuadrilla_id = $('#cuadrilla_seleccionada').val();
        const cuadrilla_empleado_id = $('#cuadrilla_empleado_id').val();

        if (!cuadrilla_id) {
            Swal.fire('Error', 'Selecciona una cuadrilla', 'error');
            return;
        }

        if (!cuadrilla_empleado_id) {
            Swal.fire('Error', 'Selecciona un empleado', 'error');
            return;
        }

        $.ajax({
            url: `/fichas-actividad/${fichaActualId}/detalles/empleados`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { 
                cuadrilla_empleado_id: cuadrilla_empleado_id 
            },
            success: function() {
                // Mostrar notificación
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Empleado agregado correctamente'
                });
                
                // Recargar empleados en la tabla principal
                cargarEmpleados(fichaActualId);
                
                // Limpiar los selects
                $('#cuadrilla_seleccionada').val('').trigger('change');
                $('#cuadrilla_empleado_id').val('').trigger('change');
                
                // Cerrar el modal de empleado
                const empleadoModal = bootstrap.Modal.getInstance(document.getElementById('empleadoModal'));
                if (empleadoModal) {
                    empleadoModal.hide();
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error al agregar', 'error');
            }
        });
    }

    function guardarMedidor() {
        const medidor_id = $('#medidor_id').val();
        const tipo = $('#medidor_tipo').val();
        const lectura = $('#medidor_lectura').val();
        const digitos_enteros = $('#digitos_enteros').val();
        const digitos_decimales = $('#digitos_decimales').val();

        if (!medidor_id || !tipo) {
            Swal.fire('Error', 'Completa los campos requeridos', 'error');
            return;
        }

        $.ajax({
            url: `/fichas-actividad/${fichaActualId}/detalles/medidores`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json',
            data: JSON.stringify({
                medidor_id: medidor_id,
                tipo: tipo,
                lectura: lectura || null,
                digitos_enteros: digitos_enteros || null,
                digitos_decimales: digitos_decimales || null
            }),
            success: function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Medidor agregado correctamente'
                });
                cargarMedidores(fichaActualId);
                $('#medidorForm')[0].reset();
                
                // Cerrar el modal de medidor
                const medidorModal = bootstrap.Modal.getInstance(document.getElementById('medidorModal'));
                if (medidorModal) {
                    medidorModal.hide();
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error al agregar', 'error');
            }
        });
    }

    function guardarPrecinto() {
        const medidor_ficha_actividad_id = $('#medidor_ficha_actividad_id').val();
        const tipo = $('#precinto_tipo').val();
        const numero_precinto = $('#numero_precinto').val();
        const material_id = $('#precinto_material_id').val();

        if (!medidor_ficha_actividad_id || !tipo || !numero_precinto) {
            Swal.fire('Error', 'Completa los campos requeridos', 'error');
            return;
        }

        $.ajax({
            url: `/fichas-actividad/${fichaActualId}/detalles/precintos`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json',
            data: JSON.stringify({
                medidor_ficha_actividad_id: medidor_ficha_actividad_id,
                tipo: tipo,
                numero_precinto: numero_precinto,
                material_id: material_id || null
            }),
            success: function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Precinto agregado correctamente'
                });
                cargarPrecintos(fichaActualId);
                $('#precintoForm')[0].reset();
                
                // Cerrar el modal de precinto
                const precintoModal = bootstrap.Modal.getInstance(document.getElementById('precintoModal'));
                if (precintoModal) {
                    precintoModal.hide();
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error al agregar', 'error');
            }
        });
    }

    function guardarMaterial() {
        const material_id = $('#material_id_detalle').val();
        const cantidad = parseFloat($('#material_cantidad').val());
        const observacion = $('#material_observacion').val();

        if (!material_id || !cantidad) {
            Swal.fire('Error', 'Completa los campos requeridos', 'error');
            return;
        }

        // Obtener saldo disponible del select
        const $option = $(`#material_id_detalle option[value="${material_id}"]`);
        const saldo = parseFloat($option.data('saldo')) || 0;

        // Validar que la cantidad no exceda el saldo
        if (saldo > 0 && cantidad > saldo) {
            Swal.fire('Error', `No puedes usar ${cantidad} ${$option.data('unidad')}. Solo hay ${saldo} disponible.`, 'error');
            return;
        }

        console.log('📦 Guardando material:', { material_id, cantidad, observacion, fichaActualId });

        // PASO 1: Agregar material a la ficha
        $.ajax({
            url: `/fichas-actividad/${fichaActualId}/detalles/materiales`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json',
            data: JSON.stringify({
                material_id: material_id,
                cantidad: cantidad,
                observacion: observacion || null
            }),
            success: function(response) {
                console.log('✓ Material agregado a la ficha:', response);
                
                // Limpiar y recargar inmediatamente
                limpiarFormularioMaterial();
                cargarMateriales(fichaActualId);  // USAR cargarMateriales en lugar de cargarMaterialesFicha
                
                Swal.fire('Éxito', 'Material agregado correctamente', 'success');
                
                // Cerrar modal
                const modalElement = document.getElementById('materialModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                }
                
                // PASO 2: Ahora descuentar de pecosa (en background, sin bloquear)
                $.get(`/fichas-actividad/${fichaActualId}/detalles/empleados`, function(empleadosResponse) {
                    console.log('📋 Empleados obtenidos:', empleadosResponse);
                    
                    if (!empleadosResponse.data || empleadosResponse.data.length === 0) {
                        console.warn('⚠️ Sin empleados asignados, no se puede descuentar de pecosa');
                        return;
                    }
                    
                    const primerEmpleado = empleadosResponse.data[0];
                    const cuadrillaEmpleadoId = primerEmpleado.cuadrilla_empleado_id;
                    console.log('👤 Cuadrilla empleado ID:', cuadrillaEmpleadoId);
                    
                    // PASO 3: Obtener pecosas de esa cuadrilla
                    $.get(`/pecosas/cuadrilla/${cuadrillaEmpleadoId}/pecosas`, function(pecosasResponse) {
                        console.log('📄 Pecosas por cuadrilla (ID=' + cuadrillaEmpleadoId + '):', pecosasResponse);
                        
                        // Si no hay pecosas para esta cuadrilla, buscar TODAS las pecosas
                        if (!pecosasResponse.success || !pecosasResponse.data || pecosasResponse.data.length === 0) {
                            console.warn('⚠️ Sin pecosas para cuadrilla_empleado_id=' + cuadrillaEmpleadoId);
                            console.log('� Buscando todas las pecosas disponibles...');
                            
                            $.get('/pecosas/todas', function(todasResponse) {
                                console.log('📄 Todas las pecosas disponibles:', todasResponse);
                                
                                if (!todasResponse.success || !todasResponse.data || todasResponse.data.length === 0) {
                                    console.error('❌ No hay pecosas disponibles en el sistema');
                                    return;
                                }
                                
                                const pecosaId = todasResponse.data[0].id;
                                const pecosaNro = todasResponse.data[0].nro_documento;
                                const pecosaEmpleado = todasResponse.data[0].empleado;
                                console.log('� Pecosa seleccionada (de todas):', pecosaNro, 'ID:', pecosaId, 'Empleado:', pecosaEmpleado);
                                
                                // PASO 4: Registrar la salida del material
                                registrarSalidaMaterial(pecosaId, material_id, cantidad, observacion);
                            }).fail(function(err) {
                                console.error('❌ Error obteniendo todas las pecosas:', err);
                            });
                        } else {
                            const pecosaId = pecosasResponse.data[0].id;
                            const pecosaNro = pecosasResponse.data[0].nro_documento;
                            console.log('📦 Pecosa seleccionada (de la cuadrilla):', pecosaNro, 'ID:', pecosaId);
                            
                            // PASO 4: Registrar la salida del material
                            registrarSalidaMaterial(pecosaId, material_id, cantidad, observacion);
                        }
                    }).fail(function(err) {
                        console.error('❌ Error obteniendo pecosas por cuadrilla:', err);
                    });
                }).fail(function(err) {
                    console.error('❌ Error obteniendo empleados:', err);
                });
            },
            error: function(xhr) {
                console.error('❌ Error agregando material:', xhr);
                const message = xhr.responseJSON?.message || xhr.responseJSON?.errors?.material_id?.[0] || 'Error al agregar material';
                Swal.fire('Error', message, 'error');
            }
        });
    }

    function registrarSalidaMaterial(pecosaId, materialId, cantidad, observacion) {
        console.log('💰 Registrando salida:', { pecosaId, materialId, cantidad, fichaActualId: fichaActualId });
        
        $.ajax({
            url: '/pecosas/registrar-salida',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json',
            data: JSON.stringify({
                pecosa_id: pecosaId,
                material_id: materialId,
                cantidad: cantidad,
                ficha_actividad_id: fichaActualId,
                observaciones: observacion || null
            }),
            success: function(response) {
                console.log('✓ Salida registrada en pecosa:', response);
                // Recargar el select de materiales para reflejar el nuevo saldo
                cargarMaterialesDisponibles();
            },
            error: function(xhr) {
                console.error('❌ Error registrando salida:', xhr);
                if (xhr.responseJSON?.message) {
                    console.error('Mensaje:', xhr.responseJSON.message);
                }
            }
        });
    }

    function limpiarFormularioMaterial() {
        $('#material_id_detalle').val('').trigger('change');
        $('#material_cantidad').val('');
        $('#material_observacion').val('');
    }

    function guardarFoto() {
        const url = $('#foto_url').val();
        const descripcion = $('#foto_descripcion').val();

        if (!url) {
            Swal.fire('Error', 'Ingresa la URL de la foto', 'error');
            return;
        }

        $.ajax({
            url: `/fichas-actividad/${fichaActualId}/detalles/fotos`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json',
            data: JSON.stringify({
                url: url,
                descripcion: descripcion || null
            }),
            success: function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Foto agregada correctamente'
                });
                cargarFotos(fichaActualId);
                $('#fotoForm')[0].reset();
                
                // Cerrar el modal de foto
                const fotoModal = bootstrap.Modal.getInstance(document.getElementById('fotoModal'));
                if (fotoModal) {
                    fotoModal.hide();
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Error al agregar', 'error');
            }
        });
    }

    // ===== FUNCIONES PARA ELIMINAR =====

    function eliminarEmpleado(fichaId, empleadoId) {
        console.log('🔴 INICIO eliminarEmpleado - fichaId:', fichaId, 'empleadoId:', empleadoId);
        Swal.fire({
            title: '¿Está seguro?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            console.log('🔴 Resultado de Swal:', result.isConfirmed);
            if (result.isConfirmed) {
                console.log('🔄 Eliminando empleado:', {fichaId, empleadoId});
                console.log('📍 URL a eliminar:', `/fichas-actividad/${fichaId}/detalles/empleados/${empleadoId}`);
                evitarGuardoAutomatico = true; // Prevenir guardos accidentales
                
                console.log('🔴 Iniciando AJAX DELETE...');
                $.ajax({
                    url: `/fichas-actividad/${fichaId}/detalles/empleados/${empleadoId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    dataType: 'json',
                    success: function(response) {
                        console.log('✓ SUCCESS - Empleado eliminado exitosamente:', response);
                        console.log('✓ Response status:', response.success);
                        // Mostrar notificación de éxito
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Empleado removido correctamente'
                        });
                        // Recargar empleados silenciosamente
                        console.log('🔄 Recargando empleados...');
                        cargarEmpleados(fichaId);
                        // Permitir guardos nuevamente después de 3 segundos (más tiempo)
                        setTimeout(() => { 
                            evitarGuardoAutomatico = false;
                            console.log('✓ Flag desactivado, guardos permitidos nuevamente');
                        }, 3000);
                    },
                    error: function(xhr, status, error) {
                        console.error('✗ ERROR - Eliminando empleado');
                        console.error('✗ Status Code:', xhr.status);
                        console.error('✗ Status Text:', status);
                        console.error('✗ Error:', error);
                        console.error('✗ Response Text:', xhr.responseText);
                        console.error('✗ Response JSON:', xhr.responseJSON);
                        
                        const mensaje = xhr.responseJSON?.message || 'No se pudo eliminar el empleado';
                        Swal.fire('Error', mensaje, 'error');
                        evitarGuardoAutomatico = false; // Permitir guardos nuevamente
                    },
                    complete: function(xhr, status) {
                        console.log('🔴 AJAX COMPLETE - Status:', status);
                    }
                });
            }
        });
    }

    function eliminarMedidor(fichaId, medidorId) {
        Swal.fire({
            title: '¿Está seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                evitarGuardoAutomatico = true;
                $.ajax({
                    url: `/fichas-actividad/${fichaId}/detalles/medidores/${medidorId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Medidor removido correctamente'
                        });
                        cargarMedidores(fichaActualId);
                        setTimeout(() => { evitarGuardoAutomatico = false; }, 3000);
                    },
                    error: function(error) {
                        const mensaje = error.responseJSON?.message || 'No se pudo eliminar el medidor';
                        Swal.fire('Error', mensaje, 'error');
                        evitarGuardoAutomatico = false;
                    }
                });
            }
        });
    }

    function eliminarPrecinto(fichaId, precintoId) {
        Swal.fire({
            title: '¿Está seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                evitarGuardoAutomatico = true;
                $.ajax({
                    url: `/fichas-actividad/${fichaActualId}/detalles/precintos/${precintoId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Precinto removido correctamente'
                        });
                        cargarPrecintos(fichaActualId);
                        setTimeout(() => { evitarGuardoAutomatico = false; }, 3000);
                    },
                    error: function(error) {
                        const mensaje = error.responseJSON?.message || 'No se pudo eliminar el precinto';
                        Swal.fire('Error', mensaje, 'error');
                        evitarGuardoAutomatico = false;
                    }
                });
            }
        });
    }

    function eliminarMaterial(fichaId, materialId) {
        Swal.fire({
            title: '¿Está seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                evitarGuardoAutomatico = true;
                $.ajax({
                    url: `/fichas-actividad/${fichaActualId}/detalles/materiales/${materialId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Material removido correctamente'
                        });
                        cargarMateriales(fichaActualId);
                        setTimeout(() => { evitarGuardoAutomatico = false; }, 3000);
                    },
                    error: function(error) {
                        const mensaje = error.responseJSON?.message || 'No se pudo eliminar el material';
                        Swal.fire('Error', mensaje, 'error');
                        evitarGuardoAutomatico = false;
                    }
                });
            }
        });
    }

    function eliminarFoto(fichaId, fotoId) {
        Swal.fire({
            title: '¿Está seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                evitarGuardoAutomatico = true;
                $.ajax({
                    url: `/fichas-actividad/${fichaActualId}/detalles/fotos/${fotoId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Foto removida correctamente'
                        });
                        cargarFotos(fichaId);
                        setTimeout(() => { evitarGuardoAutomatico = false; }, 3000);
                    },
                    error: function(error) {
                        const mensaje = error.responseJSON?.message || 'No se pudo eliminar la foto';
                        Swal.fire('Error', mensaje, 'error');
                        evitarGuardoAutomatico = false;
                    }
                });
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
            confirmButtonColor: '#e74c3c'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('fichas-actividad') }}/${id}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() {
                        Swal.fire('Eliminado', '', 'success');
                        inicializarTabla();
                    }
                });
            }
        });
    }

    // ===== MAPA =====

    function inicializarMapa() {
        fichaMap = new google.maps.Map(document.getElementById('fichaMap'), {
            zoom: 14,
            center: PUCALLPA_CENTER,
            mapTypeId: 'roadmap'
        });

        fichaMap.addListener('click', function(event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();
            
            $('#latitud').val(lat.toFixed(6));
            $('#longitud').val(lng.toFixed(6));
            
            if (fichaMapMarker) fichaMapMarker.setMap(null);
            
            fichaMapMarker = new google.maps.Marker({
                position: event.latLng,
                map: fichaMap,
                animation: google.maps.Animation.DROP
            });
        });
    }

    function cargarMarcador(lat, lng) {
        if (lat && lng) {
            const pos = { lat: parseFloat(lat), lng: parseFloat(lng) };
            if (fichaMapMarker) fichaMapMarker.setMap(null);
            fichaMapMarker = new google.maps.Marker({
                position: pos,
                map: fichaMap
            });
            fichaMap.setCenter(pos);
            fichaMap.setZoom(16);
        }
    }
</script>
@endsection
