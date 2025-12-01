@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .modal-content { background: #fff !important; }
        .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }
        
        /* Badge styling */
        .badge-success { background-color: #1abc9c !important; color: white !important; }
        .badge-danger { background-color: #e74c3c !important; color: white !important; }
        .badge-warning { background-color: #f39c12 !important; color: white !important; }
        .badge-secondary { background-color: #6c757d !important; color: white !important; }
        
        /* Select2 styling */
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
                <input type="hidden" id="vehiculo_id_hidden" name="vehiculo_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="soatModalLabel">Crear/Editar SOAT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_id" class="form-label">Vehículo <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="vehiculo_id" required>
                                <option value="">Seleccione un vehículo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="proveedor_id" name="proveedor_id" required>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
    <script>
    let soatsTable = null;

    $(document).ready(function() {
        console.log('✅ Scripts cargados - Inicializando tabla de SOATs');
        
        // Inicializar Select2
        $('#vehiculo_id').select2({
            dropdownParent: $('#soatModal'),
            placeholder: 'Seleccione un vehículo',
            allowClear: true
        });
        
        $('#proveedor_id').select2({
            dropdownParent: $('#soatModal'),
            placeholder: 'Seleccione un proveedor',
            allowClear: true
        });
        
        // Cargar vehículos sin SOAT
        loadVehiculos();
        loadProveedores();
        
        // Cargar tabla de SOATs
        loadSoatsTable();
        
        // Evento del botón Nuevo SOAT
        $('#btnNuevoSoat').click(function() {
            $('#soatForm')[0].reset();
            $('#soat_id').val('');
            $('#soatModalLabel').text('Nuevo SOAT');
            $('#vehiculo_id').prop('disabled', false); // Habilitar cuando es nuevo
            loadVehiculos();
            loadProveedores();
        });
        
        // Submit del formulario
        $('#soatForm').on('submit', function(e) {
            e.preventDefault();
            
            // Sincronizar vehiculo_id del select al campo oculto antes de enviar
            $('#vehiculo_id_hidden').val($('#vehiculo_id').val());
            
            const soatId = $('#soat_id').val();
            const url = soatId ? '/soats/' + soatId : '/soats';
            const method = soatId ? 'PUT' : 'POST';
            
            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(res) {
                    Swal.fire('¡Guardado!', 'El SOAT se guardó correctamente', 'success');
                    $('#soatModal').modal('hide');
                    $('#soatForm')[0].reset();
                    loadSoatsTable();
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Hubo un problema al guardar el SOAT', 'error');
                }
            });
        });
    });

    function loadVehiculos() {
        $.get('{{ route('soats.vehiculos.sin-soat') }}', function(data) {
            const select = $('#vehiculo_id');
            select.empty().append('<option value="">Seleccione un vehículo</option>');
            $.each(data, function(index, item) {
                const claseEstado = item.estado === 'sin_soat' ? ' (⚠️ Sin SOAT)' : ' (✅)';
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
            select.trigger('change');
        });
    }

    function loadProveedores() {
        $.get('{{ route('soats.proveedores') }}', function(data) {
            const select = $('#proveedor_id');
            select.empty().append('<option value="">Seleccione un proveedor</option>');
            $.each(data, function(index, item) {
                select.append(`<option value="${item.id}">${item.text}</option>`);
            });
            select.trigger('change');
        });
    }

    function loadSoatsTable() {
        if ($.fn.DataTable.isDataTable('#soatsTable')) {
            $('#soatsTable').DataTable().destroy();
        }
        
        soatsTable = $('#soatsTable').DataTable({
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
            order: [[6, "asc"]],
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
    }

    window.editSoat = function(id) {
        $.get('/soats/' + id, function(data) {
            $('#soat_id').val(data.id);
            
            // Cargar datos del vehículo y deshabilitarlo
            const vehiculoInfo = data.vehiculo.marca + ' ' + data.vehiculo.nombre + ' (' + data.vehiculo.placa + ')';
            $('#vehiculo_id').html('<option value="' + data.vehiculo_id + '">' + vehiculoInfo + '</option>')
                .val(data.vehiculo_id)
                .prop('disabled', true) // Deshabilitar vehículo en edición
                .trigger('change');
            
            $('#proveedor_id').val(data.proveedor_id).trigger('change');
            $('#numero_soat').val(data.numero_soat);
            $('#fecha_emision').val(data.fecha_emision);
            $('#fecha_vencimiento').val(data.fecha_vencimiento);
            $('#estado').prop('checked', data.estado ? true : false);
            
            $('#soatModalLabel').text('Editar SOAT');
            $('#soatModal').modal('show');
        });
    };

    window.deleteSoat = function(id) {
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
                $.ajax({
                    url: '/soats/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('¡Eliminado!', 'El SOAT se eliminó correctamente', 'success');
                        loadSoatsTable();
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo eliminar el SOAT', 'error');
                    }
                });
            }
        });
    };

    window.agregarSoat = function(vehiculoId) {
        $('#soatForm')[0].reset();
        $('#soat_id').val('');
        
        // Cargar todos los vehículos pero preseleccionar el especificado y deshabilitarlo
        loadVehiculos();
        
        setTimeout(function() {
            $('#vehiculo_id').val(vehiculoId).trigger('change').prop('disabled', true);
        }, 500); // Pequeño delay para asegurar que Select2 está listo
        
        $('#soatModalLabel').text('Agregar SOAT al Vehículo');
        $('#soatModal').modal('show');
    };
    </script>
@endsection