@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <style>
        .modal-content { background: #fff !important; }
        .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }
        
        /* Badge styling */
        .badge-success { background-color: #1abc9c !important; color: white !important; }
        .badge-danger { background-color: #e74c3c !important; color: white !important; }
        .badge-warning { background-color: #f39c12 !important; color: white !important; }
        .badge-secondary { background-color: #6c757d !important; color: white !important; }
    </style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de SOATs</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#soatModal" id="btnNuevoSoat">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo SOAT
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="soatsTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vehículo</th>
                                <th>Proveedor</th>
                                <th>Número SOAT</th>
                                <th>Fecha Emisión</th>
                                <th>Fecha Vencimiento</th>
                                <th>Vigencia</th>
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

<!-- Modal para crear/editar SOAT -->
<div class="modal fade" id="soatModal" tabindex="-1" aria-labelledby="soatModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="soatForm" action="" method="POST">
                @csrf
                <input type="hidden" id="soat_id" name="soat_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="soatModalLabel">Crear/Editar SOAT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_id" class="form-label">Vehículo <span class="text-danger">*</span></label>
                            <select class="form-control" id="vehiculo_id" name="vehiculo_id" required>
                                <option value="">Seleccione un vehículo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control" id="proveedor_id" name="proveedor_id" required>
                                <option value="">Seleccione un proveedor</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="numero_soat" class="form-label">Número SOAT <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="numero_soat" name="numero_soat" maxlength="200" placeholder="Ingrese el número del SOAT" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_emision" class="form-label">Fecha de Emisión <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
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
    <script>
    $(document).ready(function() {
        console.log('✅ Scripts cargados - Inicializando tabla de SOATs');
        
        var table = $('#soatsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('soats.data') }}',
                type: 'GET',
                dataSrc: function(json) { return json.data || []; }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'vehiculo_info', name: 'vehiculo_info', orderable: false },
                { data: 'proveedor_nombre', name: 'proveedor.nombre' },
                { data: 'numero_soat', name: 'numero_soat' },
                { data: 'fecha_emision_formatted', name: 'fecha_emision' },
                { data: 'fecha_vencimiento_formatted', name: 'fecha_vencimiento' },
                { data: 'vigencia_badge', name: 'vigencia_badge', orderable: false, searchable: false },
                { data: 'estado_badge', name: 'estado', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[5, "desc"]],
            columnDefs: [
                { targets: [6, 7, 8], className: "text-center" }
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
        
        console.log('✅ DataTable de SOATs cargado');
    });
    </script>
@endsection