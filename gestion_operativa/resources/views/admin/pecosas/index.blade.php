@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .modal-content { background: #fff !important; }
        .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }

        .form-control:disabled,
        .form-control[readonly] {
            font-weight: bold;
            background-color: #f8f9fa !important;
            color: #212529 !important;
            cursor: not-allowed;
        }
        
        /* SweetAlert2 Mejorado */
        .swal2-popup {
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        }
        .swal2-title {
            font-size: 20px !important;
            font-weight: 600 !important;
            color: #2c3e50 !important;
            margin-bottom: 15px !important;
        }
        .swal2-html-container {
            font-size: 14px !important;
            color: #555 !important;
            line-height: 1.6 !important;
        }
        .swal2-confirm {
            border-radius: 6px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }
        .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2) !important;
        }
        .swal2-cancel {
            border-radius: 6px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
        }
        .swal2-textarea {
            border: 2px solid #bfc9d4 !important;
            border-radius: 6px !important;
            padding: 10px !important;
            font-family: inherit !important;
            transition: border-color 0.3s !important;
        }
        .swal2-textarea:focus {
            border-color: #ff9800 !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.1) !important;
        }
        
        /* Select2 Bootstrap 5 integration */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #bfc9d4 !important;
            border-radius: 6px !important;
            font-size: 14px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #212529 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            border: 1px solid #bfc9d4 !important;
            border-radius: 6px !important;
            z-index: 10000 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #7c3aed !important;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1) !important;
        }
        .select2-results__option {
            padding: 10px 12px !important;
            font-size: 14px !important;
        }
        .select2-results__option--highlighted {
            background-color: #007bff !important;
            color: white !important;
        }
        .select2-results__option--selected {
            background-color: #f0f0f0 !important;
            color: #333 !important;
        }
        .select2-selection__clear {
            margin-right: 5px !important;
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

        /* Tabla de detalles */
        .detalle-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .detalle-field {
            flex: 1;
            min-width: 150px;
        }

        .detalle-total {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .btn-remove-detalle {
            padding: 6px 12px;
            height: 38px;
        }

        .resumen-totales {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            margin-top: 20px;
        }

        .resumen-total {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .total-item {
            display: flex;
            flex-direction: column;
        }

        .total-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .total-valor {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .detalle-row {
                flex-direction: column;
            }
            .resumen-total {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de PECOSAs (Partes de Entrega de Comisión de Obra)</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pecosaModal" id="btnNuevaPecosa">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nueva PECOSA
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="pecosasTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nro. Documento</th>
                                <th>Fecha</th>
                                <th>Empleado</th>
                                <th>Cuadrilla</th>
                                <th>Ítems</th>
                                <th>Total S/.</th>
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


<!-- Modal para ver detalles -->
<div class="modal fade" id="pecosaDetallesModal" tabindex="-1" aria-labelledby="pecosaDetallesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pecosaDetallesModalLabel">Detalles de PECOSA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="pecosaDetallesContent">
                <!-- Se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista previa de PDF -->
<div class="modal fade" id="pecosaPDFModal" tabindex="-1" aria-labelledby="pecosaPDFModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pecosaPDFModalLabel">Vista Previa - PECOSA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pecosaPDFFrame" src="" style="width: 100%; height: 70vh; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar PECOSA con detalles -->
<div class="modal fade" id="pecosaModal" tabindex="-1" aria-labelledby="pecosaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="pecosaForm" action="" method="POST">
                @csrf
                <input type="hidden" id="pecosa_id" name="pecosa_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="pecosaModalLabel">Nueva PECOSA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Sección de datos principales -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Datos de la PECOSA</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cuadrilla_id" class="form-label">Cuadrilla <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="cuadrilla_id" name="cuadrilla_id" required>
                                        <option value="">Seleccione una cuadrilla</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="empleado_id" class="form-label">Empleado <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="empleado_id" name="empleado_id" required>
                                        <option value="">Seleccione un empleado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_pecosa" class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="fecha_pecosa" name="fecha" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nro_documento_pecosa" class="form-label">Nro. Documento <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nro_documento_pecosa" name="nro_documento" maxlength="50" placeholder="Auto-generado" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="btnGenerarNumeroPecosa" title="Generar número automático">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36M20.49 15a9 9 0 0 1-14.85 3.36"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="observaciones_pecosa" class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observaciones_pecosa" name="observaciones" rows="2" placeholder="Ingrese observaciones (opcional)"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="estado_pecosa" name="estado" value="1" checked>
                                        <label class="form-check-label" for="estado_pecosa">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de detalles de NEA -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Detalles de Materiales (desde NEA)</h6>
                        </div>
                        <div class="card-body">
                            <!-- Formulario para agregar detalle -->
                            <div class="form-agregardetalle mb-4 p-4" style="background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0;">
                                <h6 class="mb-3">Agregar Material desde NEA</h6>
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label for="formNeaDetalle" class="form-label">Material NEA <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="formNeaDetalle">
                                            <option value="">Seleccione un material</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="formStockDisponible" class="form-label">Stock Disponible</label>
                                        <input type="text" class="form-control" id="formStockDisponible" readonly style="background-color: #f0f0f0;">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="formCantidadPecosa" class="form-label">Cantidad a Entregar <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="formCantidadPecosa" placeholder="0.000" step="0.001" min="0">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-success w-100" id="btnAgregarDetallePecosa">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-1">
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                            Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de detalles agregados -->
                            <div class="table-responsive" id="detallesPecosaTablaContainer" style="display:none;">
                                <table class="table table-striped table-bordered table-hover table-sm" id="detallesPecosaTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%">Material</th>
                                            <th style="width: 15%">Cantidad</th>
                                            <th style="width: 15%">Unidad</th>
                                            <th style="width: 20%">Subtotal</th>
                                            <th style="width: 10%; text-align: center;">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detallesPecosaTbody">
                                    </tbody>
                                </table>
                            </div>

                            <!-- Resumen de totales -->
                            <div class="resumen-totales" id="resumenTotalesPecosa" style="display:none;">
                                <div class="resumen-total">
                                    <div class="total-item">
                                        <span class="total-label">Subtotal</span>
                                        <span class="total-valor" id="totalSinIgvPecosa">S/ 0.00</span>
                                    </div>
                                    <div class="total-item" id="igvRowPecosa" style="display:none;">
                                        <span class="total-label">IGV (18%)</span>
                                        <span class="total-valor" id="totalIgvPecosa">S/ 0.00</span>
                                    </div>
                                    <div class="total-item">
                                        <span class="total-label">Total</span>
                                        <span class="total-valor" id="totalConIgvPecosa">S/ 0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarPecosa">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        let detalleCounterPecosa = 0;
        
        // ===== CONTROL TEMPORAL DE MATERIALES EN TABLA =====
        // Mantiene un registro de todos los materiales que están en la tabla de detalles
        // para sincronizar correctamente el SELECT y los stocks
        let controlTemporalMateriales = {};

        function actualizarControlTemporal() {
            // Limpiar y reconstruir el control temporal
            controlTemporalMateriales = {};
            
            $('#detallesPecosaTbody tr').each(function() {
                const neaDetalleId = parseInt($(this).find('.form-detalle-nea-detalle-id').val());
                const cantidad = parseFloat($(this).find('.form-detalle-cantidad').val()) || 0;
                const estaGuardado = !!$(this).find('.form-detalle-id').val();
                
                if (!controlTemporalMateriales[neaDetalleId]) {
                    controlTemporalMateriales[neaDetalleId] = {
                        cantidadTotal: 0,
                        cantidadGuardada: 0,
                        cantidadNueva: 0,
                        enTabla: true
                    };
                }
                
                controlTemporalMateriales[neaDetalleId].cantidadTotal += cantidad;
                
                if (estaGuardado) {
                    controlTemporalMateriales[neaDetalleId].cantidadGuardada += cantidad;
                } else {
                    controlTemporalMateriales[neaDetalleId].cantidadNueva += cantidad;
                }
            });
            
            console.log('Control temporal actualizado:', controlTemporalMateriales);
        }

        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#pecosasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/pecosas/data',
                columns: [
                    { data: 'id' },
                    { data: 'nro_documento' },
                    { data: 'fecha_formatted' },
                    { data: 'empleado_nombre' },
                    { data: 'cuadrilla_nombre' },
                    { data: 'cantidad_detalles' },
                    { data: 'total_con_igv' },
                    { data: 'estado_badge' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            // Cargar cuadrillas al iniciar
            cargarCuadrillas();

            // Select2 inicialización
            $('#cuadrilla_id').select2();
            $('#empleado_id').select2();
            $('#formNeaDetalle').select2();

            // Event listeners
            $('#cuadrilla_id').on('change', function() {
                cargarEmpleados($(this).val());
            });

            $('#empleado_id').on('change', function() {
                cargarNeaDetalles($(this).val());
            });

            $('#formNeaDetalle').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const neaDetalleId = parseInt($(this).val());
                const stock = selectedOption.data('stock') || 0;
                const unidad = selectedOption.data('unidad') || 'UND';
                
                // Mostrar stock disponible con formato
                $('#formStockDisponible').val(stock.toFixed(3) + ' ' + unidad);
                
                // Log para debug
                console.log(`Material seleccionado: ${neaDetalleId}, Stock disponible: ${stock.toFixed(3)} ${unidad}`);
            });

            $('#btnGenerarNumeroPecosa').on('click', generarNumeroPecosa);
            $('#btnAgregarDetallePecosa').on('click', agregarDetallePecosa);
            $('#btnGuardarPecosa').on('click', guardarPecosa);
            $('#btnNuevaPecosa').on('click', function() {
                resetFormPecosa();
            });

            setFechaHoy();
            generarNumeroPecosa();

            // Si viene de la redirección del método edit(), abrir el modal automáticamente
            @if(session('edit_id'))
                setTimeout(() => {
                    editarPecosa({{ session('edit_id') }});
                }, 500);
            @endif
        });

        function cargarCuadrillas() {
            $.get('/cuadrillas/api/select', function(data) {
                console.log('Respuesta cuadrillas:', data);
                const select = $('#cuadrilla_id');
                select.find('option:not(:first)').remove();
                
                // La API devuelve { results: [...], pagination: {...} }
                const items = data.results || (Array.isArray(data) ? data : (data.data || []));
                
                if (Array.isArray(items) && items.length > 0) {
                    items.forEach(function(item) {
                        select.append(`<option value="${item.id}">${item.text}</option>`);
                    });
                    console.log('Cuadrillas cargadas:', items.length);
                } else {
                    console.warn('No hay cuadrillas o formato incorrecto:', items);
                }
            }).fail(function(error) {
                console.error('Error al cargar cuadrillas:', error);
            });
        }

        function cargarEmpleados(cuadrillaId) {
            if (!cuadrillaId) {
                $('#empleado_id').find('option:not(:first)').remove();
                $('#formNeaDetalle').find('option:not(:first)').remove();
                return;
            }

            $.get(`/pecosas/empleados/${cuadrillaId}`, function(data) {
                const select = $('#empleado_id');
                select.find('option:not(:first)').remove();
                
                // El controller devuelve directamente un array
                const items = Array.isArray(data) ? data : (data.results || (data.data || []));
                
                if (Array.isArray(items) && items.length > 0) {
                    items.forEach(function(item) {
                        // Mostrar nombre completo (nombre + apellido)
                        const nombreCompleto = item.empleado_apellido 
                            ? item.empleado_nombre + ' ' + item.empleado_apellido
                            : item.empleado_nombre;
                        select.append(`<option value="${item.id}">${nombreCompleto}</option>`);
                    });
                    console.log('Empleados cargados:', items.length);
                } else {
                    console.warn('No hay empleados disponibles');
                }
                select.trigger('change');
            }).fail(function(error) {
                console.error('Error al cargar empleados:', error);
            });
        }

        function cargarNeaDetalles(cuadrillaEmpleadoId) {
            if (!cuadrillaEmpleadoId) {
                $('#formNeaDetalle').find('option:not(:first)').remove();
                $('#formStockDisponible').val('');
                return;
            }

            $.get(`/pecosas/nea-detalles/${cuadrillaEmpleadoId}`, function(data) {
                const select = $('#formNeaDetalle');
                select.find('option:not(:first)').remove();
                
                // El controller devuelve directamente un array
                const items = Array.isArray(data) ? data : (data.results || (data.data || []));
                
                if (Array.isArray(items) && items.length > 0) {
                    items.forEach(function(item) {
                        let stockReal = item.stock_disponible;
                        let debeMostrarse = false;
                        
                        // Calcular cambios netos para este material en la tabla
                        let cambiosNetosConsumidos = 0;  // Lo que realmente se consume (positivo)
                        let cambiosNetosLiberados = 0;   // Lo que se libera (negativo)
                        
                        // Contar cambios en filas GUARDADAS (con ID)
                        $('#detallesPecosaTbody tr').each(function() {
                            const nea = parseInt($(this).find('.form-detalle-nea-detalle-id').val());
                            if (nea === item.id) {
                                const detalleId = $(this).find('.form-detalle-id').val();
                                if (detalleId) {
                                    // Detalle GUARDADO - calcular diferencia
                                    const cantGuardada = parseFloat($(this).find('.form-detalle-cantidad-guardada').val()) || 0;
                                    const cantActual = parseFloat($(this).find('.form-detalle-cantidad').val()) || 0;
                                    const diferencia = cantActual - cantGuardada;
                                    
                                    if (diferencia > 0) {
                                        cambiosNetosConsumidos += diferencia;  // Aumentó: consume stock
                                    } else {
                                        cambiosNetosLiberados += Math.abs(diferencia);  // Disminuyó: libera stock
                                    }
                                }
                            }
                        });
                        
                        // Contar detalles NUEVOS (sin ID) - pero solo si cantidad > 0
                        $('#detallesPecosaTbody tr').each(function() {
                            const nea = parseInt($(this).find('.form-detalle-nea-detalle-id').val());
                            if (nea === item.id) {
                                const detalleId = $(this).find('.form-detalle-id').val();
                                const cantidad = parseFloat($(this).find('.form-detalle-cantidad').val()) || 0;
                                if (!detalleId && cantidad > 0) {
                                    // Detalle NUEVO - contar como consumo de stock
                                    cambiosNetosConsumidos += cantidad;
                                }
                            }
                        });
                        
                        // Stock disponible en SELECT = stock original - consumidos + liberados
                        stockReal = item.stock_disponible - cambiosNetosConsumidos + cambiosNetosLiberados;
                        
                        if (cambiosNetosConsumidos > 0 || cambiosNetosLiberados > 0 || item.stock_disponible > 0) {
                            debeMostrarse = true;
                        }
                        
                        if (debeMostrarse && stockReal >= 0) {
                            select.append(`<option value="${item.id}" data-stock="${Math.max(0, stockReal)}" data-stock-original="${item.stock_disponible}" data-precio="${item.precio_unitario}" data-igv="${item.incluye_igv}" data-unidad="${item.unidad_medida}">${item.material_nombre}</option>`);
                        }
                    });
                    console.log('Materiales NEA cargados:', items.length);
                } else {
                    console.warn('No hay materiales NEA disponibles');
                }
            }).fail(function(error) {
                console.error('Error al cargar NEA detalles:', error);
            });
        }

        function setFechaHoy() {
            const hoy = new Date().toISOString().split('T')[0];
            $('#fecha_pecosa').val(hoy);
        }

        function generarNumeroPecosa() {
            $.get('/pecosas/proximo-numero', function(data) {
                $('#nro_documento_pecosa').val(data.proximo_numero);
            });
        }

        function agregarDetallePecosa() {
            const neaDetalleId = $('#formNeaDetalle').val();
            const neaDetalleName = $('#formNeaDetalle option:selected').text();
            const cantidad = $('#formCantidadPecosa').val();
            // IMPORTANTE: Usar data-stock (que ya tiene cambios netos calculados) en lugar de data-stock-original
            const stockDisponible = parseFloat($('#formNeaDetalle').find('option:selected').data('stock')) || 0;
            const stockOriginal = parseFloat($('#formNeaDetalle').find('option:selected').data('stock-original')) || 0;
            const precio = parseFloat($('#formNeaDetalle').find('option:selected').data('precio')) || 0;
            const unidad = $('#formNeaDetalle').find('option:selected').data('unidad') || 'UND';

            if (!neaDetalleId || !cantidad) {
                Swal.fire('Error', 'Seleccione material y cantidad', 'error');
                return;
            }

            const cantidadNum = parseFloat(cantidad);
            
            if (cantidadNum <= 0) {
                Swal.fire('Error', 'Cantidad debe ser mayor a 0', 'error');
                return;
            }

            // El stockDisponible ya tiene todos los cambios netos calculados
            if (cantidadNum > stockDisponible) {
                Swal.fire('Error', `Stock insuficiente. Disponible: ${stockDisponible.toFixed(3)} ${unidad}`, 'error');
                return;
            }

            const subtotal = cantidadNum * precio;

            let filaHtml = `
                <tr>
                    <td>
                        <input type="hidden" class="form-detalle-nea-detalle-id" value="${neaDetalleId}">
                        <input type="hidden" class="form-detalle-stock-original" value="${stockOriginal}">
                        <input type="hidden" class="form-detalle-unidad" value="${unidad}">
                        ${neaDetalleName}
                    </td>
                    <td>
                        <input type="number" class="form-control form-detalle-cantidad" value="${cantidad}" step="0.001" min="0" max="${stockDisponible}" data-stock-disponible="${stockDisponible}">
                    </td>
                    <td style="text-align: center;">
                        ${unidad}
                    </td>
                    <td>
                        <span class="form-detalle-subtotal">S/ ${subtotal.toFixed(2)}</span>
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removerDetallePecosa(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;

            $('#detallesPecosaTbody').append(filaHtml);
            $('#detallesPecosaTablaContainer').show();

            // Agregar event listeners a los inputs - VALIDACIÓN EN TIEMPO REAL
            $('#detallesPecosaTbody tr').last().find('.form-detalle-cantidad').on('change keyup blur', function() {
                const $fila = $(this);
                const $row = $fila.closest('tr');
                const neaDetalleIdRow = parseInt($row.find('.form-detalle-nea-detalle-id').val());
                const stockOriginalRow = parseFloat($row.find('.form-detalle-stock-original').val());
                const unidadRow = $row.find('.form-detalle-unidad').val();
                let cantidadNueva = parseFloat($fila.val()) || 0;
                
                // Actualizar control temporal para cálculo preciso
                actualizarControlTemporal();
                
                // Stock disponible = original - solo lo que agregamos nuevo (sin contar esta fila)
                let cantidadOtrasFilas = 0;
                $('#detallesPecosaTbody tr').each(function() {
                    const $otherRow = $(this);
                    if ($otherRow[0] !== $row[0]) {
                        const detalleId = $otherRow.find('.form-detalle-id').val();
                        // Solo contar si es nueva (sin ID guardado)
                        if (!detalleId && parseInt($otherRow.find('.form-detalle-nea-detalle-id').val()) === neaDetalleIdRow) {
                            cantidadOtrasFilas += parseFloat($otherRow.find('.form-detalle-cantidad').val()) || 0;
                        }
                    }
                });

                const stockDisponibleActual = stockOriginalRow - cantidadOtrasFilas;

                if (cantidadNueva > stockDisponibleActual) {
                    Swal.fire('Error', `No puede exceder stock disponible: ${stockDisponibleActual.toFixed(3)} ${unidadRow}`, 'error');
                    $fila.val(stockDisponibleActual);
                    cantidadNueva = stockDisponibleActual;
                }
                
                // Actualizar max attribute dinámicamente
                $fila.attr('max', stockDisponibleActual.toFixed(3));

                // Recalcular subtotal y totales en tiempo real
                calcularSubtotalFilaPecosa($row);
                calcularTotalesPecosa();
                
                // Limpiar campo de stock disponible para nuevo material
                $('#formStockDisponible').val('');
                
                // Recalcular SELECT con stock disponible actualizado
                // Usar setTimeout para asegurar que se recalcule con los datos actualizados
                const cuadrillaEmpleadoId = $('#empleado_id').val();
                if (cuadrillaEmpleadoId) {
                    setTimeout(() => {
                        cargarNeaDetalles(cuadrillaEmpleadoId);
                    }, 50);
                }
            });

            // Limpiar formulario
            $('#formNeaDetalle').val('').trigger('change');
            $('#formCantidadPecosa').val('');
            $('#formStockDisponible').val('');

            // Recalcular stock disponible de NEA después de agregar
            // Usar setTimeout para asegurar que el DOM está actualizado
            const cuadrillaEmpleadoId = $('#empleado_id').val();
            if (cuadrillaEmpleadoId) {
                setTimeout(() => {
                    cargarNeaDetalles(cuadrillaEmpleadoId);
                }, 50);
            }

            // Enfocar en el campo de material
            $('#formNeaDetalle').focus();

            calcularTotalesPecosa();
        }

        function removerDetallePecosa(btn) {
            const $fila = $(btn).closest('tr');
            const detalleId = $fila.find('.form-detalle-id').val();
            const $cantidadInput = $fila.find('.form-detalle-cantidad');
            const cantidadActual = parseFloat($cantidadInput.val()) || 0;
            
            // Si es una fila NUEVA (sin ID), eliminarla directamente
            if (!detalleId) {
                $fila.remove();
            } else {
                // Si es GUARDADA, poner cantidad a 0 (así se libera el stock)
                // y trigger el evento para que recalcule como si editara
                $cantidadInput.val(0);
                $cantidadInput.trigger('change');
                return; // No eliminar, solo poner a 0
            }
            
            // Ocultar tabla si no hay detalles
            if ($('#detallesPecosaTbody tr').length === 0) {
                $('#detallesPecosaTablaContainer').hide();
            }
            
            // Actualizar control temporal
            actualizarControlTemporal();
            
            // Recalcular totales
            calcularTotalesPecosa();
            
            // Limpiar stock disponible
            $('#formStockDisponible').val('');
            
            // Recalcular SELECT
            setTimeout(() => {
                const cuadrillaEmpleadoId = $('#empleado_id').val();
                if (cuadrillaEmpleadoId) {
                    cargarNeaDetalles(cuadrillaEmpleadoId);
                }
            }, 50);
        }

        function calcularSubtotalFilaPecosa($fila) {
            const cantidad = parseFloat($fila.find('.form-detalle-cantidad').val()) || 0;
            const neaDetalleId = $fila.find('.form-detalle-nea-detalle-id').val();
            
            // Buscar el precio en el SELECT actual
            let precio = 0;
            const selectedOption = $('#formNeaDetalle').find(`option[value="${neaDetalleId}"]`);
            
            if (selectedOption.length) {
                precio = parseFloat(selectedOption.data('precio')) || 0;
            } else {
                // Si no está en el SELECT actual, buscar en todas las opciones (en caso de edición)
                console.warn(`Material ${neaDetalleId} no encontrado en SELECT, intentando búsqueda alternativa`);
                precio = 0;
            }
            
            const subtotal = cantidad * precio;
            $fila.find('.form-detalle-subtotal').text('S/ ' + subtotal.toFixed(2));
        }

        function calcularTotalesPecosa() {
            let totalSinIgv = 0;
            let totalIgv = 0;
            let hayIgv = false;

            $('#detallesPecosaTbody tr').each(function() {
                const $this = $(this);
                const cantidad = parseFloat($this.find('.form-detalle-cantidad').val()) || 0;
                const neaDetalleId = $this.find('.form-detalle-nea-detalle-id').val();
                
                // Buscar opción en el SELECT
                const selectedOption = $('#formNeaDetalle').find(`option[value="${neaDetalleId}"]`);
                const precio = parseFloat(selectedOption.data('precio')) || 0;
                const incluye = selectedOption.data('igv');
                
                const subtotal = cantidad * precio;
                
                if (incluye) {
                    hayIgv = true;
                    const sinIgv = subtotal / 1.18;
                    totalSinIgv += sinIgv;
                    totalIgv += subtotal - sinIgv;
                } else {
                    totalSinIgv += subtotal;
                }
            });

            const totalConIgv = totalSinIgv + totalIgv;

            // Mostrar/ocultar fila de IGV
            if (hayIgv) {
                $('#igvRowPecosa').show();
            } else {
                $('#igvRowPecosa').hide();
            }
            
            // Actualizar valores con formato
            $('#totalSinIgvPecosa').text('S/ ' + totalSinIgv.toFixed(2));
            $('#totalIgvPecosa').text('S/ ' + totalIgv.toFixed(2));
            $('#totalConIgvPecosa').text('S/ ' + totalConIgv.toFixed(2));
            
            // Mostrar/ocultar resumen
            if ($('#detallesPecosaTbody tr').length > 0) {
                $('#resumenTotalesPecosa').show();
            } else {
                $('#resumenTotalesPecosa').hide();
            }
        }

        function resetFormPecosa() {
            $('#pecosaForm')[0].reset();
            $('#pecosa_id').val('');
            $('#detallesPecosaTbody').html('');
            $('#detallesPecosaTablaContainer').hide();
            detalleCounterPecosa = 0;
            $('#resumenTotalesPecosa').hide();
            setFechaHoy();
            
            // Resetear select2
            $('#cuadrilla_id').val(null).trigger('change');
            $('#empleado_id').val(null).trigger('change');
            $('#formNeaDetalle').val('').trigger('change');
            
            // Resetear formulario de agregar detalle
            $('#formCantidadPecosa').val('');
            $('#formPrecioPecosa').val('');
            $('#formIncluye_pecosa').prop('checked', false);
            $('#formStockDisponible').val('');
            
            // Limpiar control temporal
            controlTemporalMateriales = {};
            
            generarNumeroPecosa();
        }

        function guardarPecosa(e) {
            e.preventDefault();

            // Validar campos principales
            const cuadrilla_empleado_id = $('#empleado_id').val();
            const fecha = $('#fecha_pecosa').val();

            if (!cuadrilla_empleado_id) {
                Swal.fire('Error', 'Debe seleccionar un empleado', 'error');
                return;
            }
            if (!fecha) {
                Swal.fire('Error', 'Debe ingresar una fecha', 'error');
                return;
            }

            // Validar detalles
            const detalles = [];
            $('#detallesPecosaTbody tr').each(function() {
                const $this = $(this);
                const detalleId = $this.find('.form-detalle-id').val();
                const neaDetalleId = $this.find('.form-detalle-nea-detalle-id').val();
                const cantidad = parseFloat($this.find('.form-detalle-cantidad').val());
                
                // Agregar si tiene valores válidos (con o sin ID)
                if (neaDetalleId && cantidad > 0) {
                    const detalle = {
                        nea_detalle_id: parseInt(neaDetalleId),
                        cantidad: cantidad
                    };
                    
                    // Si tiene ID, incluirlo (es un detalle existente)
                    if (detalleId && detalleId.trim() !== '') {
                        detalle.id = detalleId;
                    }
                    
                    detalles.push(detalle);
                }
            });

            if (detalles.length === 0) {
                Swal.fire('Error', 'Debe agregar al menos un material', 'error');
                return;
            }

            const estado = $('#estado_pecosa').is(':checked') ? 1 : 0;
            const pecosaId = $('#pecosa_id').val();
            const url = pecosaId ? `/pecosas/${pecosaId}` : '/pecosas';
            const method = pecosaId ? 'PUT' : 'POST';

            const formData = {
                cuadrilla_empleado_id: parseInt(cuadrilla_empleado_id),
                fecha: fecha,
                nro_documento: $('#nro_documento_pecosa').val(),
                observaciones: $('#observaciones_pecosa').val(),
                estado: estado,
                detalles: detalles,
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
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function(res) {
                    // Limpiar CACHÉ en memoria
                    controlTemporalMateriales = {};
                    
                    Swal.fire('¡Guardado!', res.message, 'success').then(() => {
                        $('#pecosaModal').modal('hide');
                        // Resetear formulario para borrar caché de datos
                        resetFormPecosa();
                        // Recargar tabla
                        $('#pecosasTable').DataTable().ajax.reload();
                    });
                },
                error: function(xhr) {
                    let mensaje = 'Error al guardar la PECOSA';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', mensaje, 'error');
                }
            });
        }

        window.verPecosa = function(id) {
            $.get(`/pecosas/${id}`, function(pecosa) {
                let detallesHtml = '<div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Material</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th><th>IGV</th><th>Total</th></tr></thead><tbody>';
                
                pecosa.detalles.forEach(d => {
                    const materialNombre = d.nea_detalle && d.nea_detalle.material ? d.nea_detalle.material.nombre : 'N/A';
                    const subtotal = parseFloat(d.cantidad) * parseFloat(d.precio_unitario);
                    
                    // Calcular IGV correctamente
                    let igv = 0;
                    if (d.incluye_igv) {
                        // El precio ya incluye IGV, extraemos el IGV
                        igv = subtotal - (subtotal / 1.18);
                    }
                    
                    const total = subtotal + igv;
                    
                    detallesHtml += `<tr>
                        <td>${materialNombre}</td>
                        <td>${parseFloat(d.cantidad).toFixed(3)}</td>
                        <td>S/ ${parseFloat(d.precio_unitario).toFixed(2)}</td>
                        <td>S/ ${subtotal.toFixed(2)}</td>
                        <td>S/ ${igv.toFixed(2)}</td>
                        <td>S/ ${total.toFixed(2)}</td>
                    </tr>`;
                });
                
                detallesHtml += '</tbody></table></div>';
                
                $('#pecosaDetallesContent').html(detallesHtml);
                $('#pecosaDetallesModal').modal('show');
            });
        };

        window.previsualizarPecosaPdf = function(id) {
            $('#pecosaPDFFrame').attr('src', `/pecosas/${id}/preview`);
            $('#pecosaPDFModal').modal('show');
        };

        window.imprimirPecosaPdf = function(id) {
            window.open(`/pecosas/${id}/imprimir`, '_blank');
        };

        window.editarPecosa = function(id) {
            $.get(`/pecosas/${id}`, function(pecosa) {
                $('#pecosaModalLabel').text('Editar PECOSA #' + pecosa.nro_documento);
                $('#pecosa_id').val(pecosa.id);
                
                // Limpiar control temporal al inicio de edición
                controlTemporalMateriales = {};
                
                // Formatear fecha
                let fecha = pecosa.fecha;
                if (fecha.includes('T')) {
                    fecha = fecha.split('T')[0];
                }
                $('#fecha_pecosa').val(fecha);
                
                $('#nro_documento_pecosa').val(pecosa.nro_documento);
                $('#observaciones_pecosa').val(pecosa.observaciones || '');
                $('#estado_pecosa').prop('checked', pecosa.estado == 1);

                // Cargar cuadrilla y empleado
                $('#cuadrilla_id').val(pecosa.cuadrilla_empleado.cuadrilla_id).trigger('change');

                setTimeout(() => {
                    $('#empleado_id').val(pecosa.cuadrilla_empleado_id).trigger('change');
                    
                    setTimeout(() => {
                        // Cargar detalles
                        $('#detallesPecosaTbody').html('');
                        detalleCounterPecosa = 0;

                        if (pecosa.detalles && pecosa.detalles.length > 0) {
                            pecosa.detalles.forEach(d => {
                                detalleCounterPecosa++;
                                const materialNombre = d.nea_detalle && d.nea_detalle.material ? d.nea_detalle.material.nombre : 'N/A';
                                const codigo = d.nea_detalle && d.nea_detalle.material ? d.nea_detalle.material.codigo_material : 'N/A';
                                const unidad = d.nea_detalle && d.nea_detalle.material && d.nea_detalle.material.unidad_medida ? d.nea_detalle.material.unidad_medida.nombre : 'UND';
                                const subtotal = parseFloat(d.cantidad) * parseFloat(d.precio_unitario);
                                const neaNumero = d.nea_detalle && d.nea_detalle.nea ? d.nea_detalle.nea.nro_documento : 'N/A';
                                
                                // Formato con número de NEA: [código] material - NEA: XXX
                                const neaDetalleName = `[${codigo}] ${materialNombre} - NEA: ${neaNumero}`;
                                
                                // En edición, necesitamos el stock DISPONIBLE original del NEA, no la cantidad guardada
                                // Buscar el stock original en el array de detalles disponibles
                                let stockDisponibleOriginal = d.cantidad; // Por defecto la cantidad guardada
                                
                                const html = `
                                    <tr data-detalle-id="detalle_${detalleCounterPecosa}">
                                        <td>
                                            <input type="hidden" class="form-detalle-id" value="${d.id}">
                                            <input type="hidden" class="form-detalle-nea-detalle-id" value="${d.nea_detalle_id}">
                                            <input type="hidden" class="form-detalle-stock-disponible-original" value="${d.nea_detalle && d.nea_detalle.stock_disponible ? d.nea_detalle.stock_disponible : d.cantidad}">
                                            <input type="hidden" class="form-detalle-cantidad-guardada" value="${d.cantidad}">
                                            <input type="hidden" class="form-detalle-unidad" value="${unidad}">
                                            ${neaDetalleName}
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm form-detalle-cantidad" value="${parseFloat(d.cantidad).toFixed(3)}" step="0.001" min="0" required>
                                        </td>
                                        <td style="text-align: center;">
                                            ${unidad}
                                        </td>
                                        <td>
                                            <strong class="form-detalle-subtotal">S/ ${subtotal.toFixed(2)}</strong>
                                        </td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove-detalle" onclick="removerDetallePecosa(this)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                `;

                                $('#detallesPecosaTbody').append(html);
                                
                                // Agregar event listeners para cambios en cantidad
                                const $fila = $(`tr[data-detalle-id="detalle_${detalleCounterPecosa}"]`);
                                $fila.find('.form-detalle-cantidad').on('change keyup blur', function() {
                                    // Valores de esta fila
                                    const neaDetalleIdRow = parseInt($fila.find('.form-detalle-nea-detalle-id').val());
                                    const cantidadActual = parseFloat($(this).val()) || 0;
                                    const cantidadGuardada = parseFloat($fila.find('.form-detalle-cantidad-guardada').val()) || 0;
                                    const stockDisponibleOriginal = parseFloat($fila.find('.form-detalle-stock-disponible-original').val()) || 0;
                                    const unidadRow = $fila.find('.form-detalle-unidad').val();
                                    
                                    // CALCULAR DIFERENCIA EN ESTA FILA
                                    // Si cambias de 20 a 17, la diferencia es -3 (se libera stock)
                                    const diferenciaestaFila = cantidadActual - cantidadGuardada;
                                    
                                    // STOCK TOTAL PARA ESTA LÍNEA = Stock disponible + Lo que ya tiene guardado
                                    // Ejemplo: 2 (disponible) + 20 (guardado) = 22 total
                                    const stockTotalDisponible = stockDisponibleOriginal + cantidadGuardada;
                                    
                                    // LÓGICA PARA EDICIÓN EN TABLA:
                                    // Contar TODAS las diferencias de otros detalles guardados + todos los nuevos
                                    let totalCambiosOtrasFilas = 0;
                                    $('#detallesPecosaTbody tr').each(function() {
                                        const $otherRow = $(this);
                                        if ($otherRow[0] !== $fila[0]) {
                                            const nea = parseInt($otherRow.find('.form-detalle-nea-detalle-id').val());
                                            
                                            if (nea === neaDetalleIdRow) {
                                                const detalleId = $otherRow.find('.form-detalle-id').val();
                                                
                                                if (detalleId) {
                                                    // Detalle GUARDADO - contar diferencia
                                                    const cantGuardada = parseFloat($otherRow.find('.form-detalle-cantidad-guardada').val()) || 0;
                                                    const cantActual = parseFloat($otherRow.find('.form-detalle-cantidad').val()) || 0;
                                                    const cambio = cantActual - cantGuardada;
                                                    totalCambiosOtrasFilas += Math.max(0, cambio); // Solo si aumentó
                                                } else {
                                                    // Detalle NUEVO - contar cantidad actual
                                                    totalCambiosOtrasFilas += parseFloat($otherRow.find('.form-detalle-cantidad').val()) || 0;
                                                }
                                            }
                                        }
                                    });
                                    
                                    // Máximo que puedo poner en esta fila:
                                    // = stock total - lo que ocupan otras filas (cambios + nuevos)
                                    let maxDisponible = stockTotalDisponible - totalCambiosOtrasFilas;
                                    
                                    console.log(`Edición: NEA ${neaDetalleIdRow}, Stock total: ${stockTotalDisponible} (${stockDisponibleOriginal}+${cantidadGuardada}), Cambios otras: ${totalCambiosOtrasFilas}, Diferencia esta: ${diferenciaestaFila}, Max: ${maxDisponible}, Actual: ${cantidadActual}`);
                                    
                                    if (cantidadActual > maxDisponible) {
                                        Swal.fire('Error', `No puede exceder stock disponible: ${Math.max(0, maxDisponible).toFixed(3)} ${unidadRow}`, 'error');
                                        $(this).val(Math.max(0, maxDisponible));
                                    }
                                    
                                    // Actualizar max attribute
                                    $(this).attr('max', Math.max(0, maxDisponible).toFixed(3));
                                    
                                    // Recalcular
                                    calcularSubtotalFilaPecosa($fila);
                                    calcularTotalesPecosa();
                                    
                                    // Limpiar stock disponible
                                    $('#formStockDisponible').val('');
                                    
                                    // IMPORTANTE: Recargar el SELECT para actualizar data-stock dinámicamente
                                    const cuadrillaEmpleadoId = $('#empleado_id').val();
                                    if (cuadrillaEmpleadoId) {
                                        cargarNeaDetalles(cuadrillaEmpleadoId);
                                    }
                                });
                            });

                            // Mostrar tabla si hay detalles
                            if (pecosa.detalles.length > 0) {
                                $('#detallesPecosaTablaContainer').show();
                                $('#resumenTotalesPecosa').show();
                            }
                        }

                        // CRÍTICO: Reconstruir control temporal DESPUÉS de cargar detalles
                        // Esto asegura que los stocks disponibles se calculen correctamente
                        actualizarControlTemporal();
                        
                        calcularTotalesPecosa();
                        
                        // Reinicializar Select2 en el modal
                        if ($('#cuadrilla_id').hasClass('select2-hidden-accessible')) {
                            $('#cuadrilla_id').select2('destroy');
                        }
                        if ($('#empleado_id').hasClass('select2-hidden-accessible')) {
                            $('#empleado_id').select2('destroy');
                        }
                        
                        $('#cuadrilla_id').select2({
                            dropdownParent: $('#pecosaModal')
                        });
                        $('#empleado_id').select2({
                            dropdownParent: $('#pecosaModal')
                        });
                        
                        $('#pecosaModal').modal('show');
                    }, 300);
                }, 300);
            }).fail(function(xhr) {
                console.error('Error al cargar PECOSA:', xhr);
                Swal.fire('Error', 'No se pudo cargar la PECOSA', 'error');
            });
        };

        window.anularPecosa = function(id) {
            Swal.fire({
                title: 'Anular PECOSA',
                input: 'textarea',
                inputLabel: 'Motivo de anulación',
                inputPlaceholder: 'Ingrese el motivo de anulación...',
                showCancelButton: true,
                confirmButtonText: 'Anular',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debe ingresar un motivo de anulación'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/pecosas/${id}/anular`, {
                        motivo_anulacion: result.value,
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        Swal.fire('¡Anulado!', res.message, 'success').then(() => {
                            $('#pecosasTable').DataTable().ajax.reload();
                        });
                    }).fail(function(xhr) {
                        let mensaje = 'Error al anular la PECOSA';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            mensaje = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', mensaje, 'error');
                    });
                }
            });
        };

        window.eliminarPecosa = function(id) {
            Swal.fire({
                title: '¿Eliminar PECOSA?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/pecosas/${id}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('¡Eliminado!', res.message, 'success').then(() => {
                                $('#pecosasTable').DataTable().ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            let mensaje = 'Error al eliminar la PECOSA';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                mensaje = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', mensaje, 'error');
                        }
                    });
                }
            });
        };

        window.editPecosa = function(id) {
            // Abrir modal de edición
            editarPecosa(id);
        };
    </script>
@endsection
