@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
<link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
<style>
    .modal-content { background: #fff !important; }
    .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }
</style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="">Gestión de Unidades de Medida</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unidadMedidaModal" onclick="openCreateUnidadMedidaModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nueva Unidad de Medida
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="unidadMedidasTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán dinámicamente con DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar unidad de medida -->
<div class="modal fade" id="unidadMedidaModal" tabindex="-1" aria-labelledby="unidadMedidaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="unidadMedidaForm" action="" method="POST">
                @csrf
                <input type="hidden" id="unidadMedidaId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="unidadMedidaModalLabel">Crear/Editar Unidad de Medida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="estado" name="estado" checked>
                        <label class="form-check-label" for="estado">Activo</label>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/spanish.js')}}"></script>
<script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
<script>
$(document).ready(function() {
    window.unidadMedidasTable = $('#unidadMedidasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('unidad_medidas.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: "5%" },
            { data: 'nombre', name: 'nombre', width: "60%" },
            { data: 'estado_badge', name: 'estado_badge', orderable: false, searchable: false, width: "15%" },
            { data: 'action', name: 'action', orderable: false, searchable: false, width: "20%" }
        ],
        order: [[1, "asc"]],
        language: { url: "{{ asset('plugins/src/table/datatable/spanish.js') }}" },
        createdRow: function(row, data) {
            $('td:eq(2)', row).html(data.estado_badge);
            $('td:eq(3)', row).html(data.action);
        }
    });
});

// Abrir modal para crear
window.openCreateUnidadMedidaModal = function() {
    $('#unidadMedidaForm')[0].reset();
    $('#unidadMedidaId').val('');
    $('#unidadMedidaModalLabel').text('Nueva Unidad de Medida');
    $('#unidadMedidaForm').attr('action', '{{ route("unidad_medidas.store") }}');
    $('#method-field').remove();
    $('#unidadMedidaModal').modal('show');
}

// Editar
window.editUnidadMedida = function(id) {
    $.get('{{ url("unidad_medidas") }}/' + id, function(data) {
        $('#unidadMedidaId').val(data.id);
        $('#nombre').val(data.nombre);
        $('#estado').prop('checked', !!data.estado);
        $('#unidadMedidaModalLabel').text('Editar Unidad de Medida');
        $('#unidadMedidaForm').attr('action', '{{ url("unidad_medidas") }}/' + id);
        $('#method-field').remove();
        $('#unidadMedidaForm').append('<input type="hidden" name="_method" value="PUT" id="method-field">');
        $('#unidadMedidaModal').modal('show');
    });
}

// Guardar
$('#unidadMedidaForm').on('submit', function(e) {
    e.preventDefault();
    var $submitBtn = $(this).find('button[type="submit"]');
    $submitBtn.prop('disabled', true);
    var id = $('#unidadMedidaId').val();
    var url = id ? ('{{ url("unidad_medidas") }}/' + id) : '{{ url("unidad_medidas") }}';
    var method = id ? 'POST' : 'POST';
    var formData = $(this).serializeArray();
    if (id) formData.push({name: '_method', value: 'PUT'});
    formData.push({name: 'estado', value: $('#estado').is(':checked') ? 1 : 0});
    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function() {
            $('#unidadMedidaModal').modal('hide');
            window.unidadMedidasTable.ajax.reload();
            Swal.fire('Éxito', 'Unidad de Medida guardada correctamente', 'success');
        },
        error: function(xhr) {
            Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
        }
    }).always(function() {
        setTimeout(function() { $submitBtn.prop('disabled', false); }, 300);
    });
});

// Eliminar
window.deleteUnidadMedida = function(id) {
    Swal.fire({
        title: '¿Está seguro?',
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
                url: '{{ url("unidad_medidas") }}/' + id,
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                success: function() {
                    window.unidadMedidasTable.ajax.reload();
                    Swal.fire('Eliminado', 'La unidad de medida ha sido eliminada', 'success');
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo eliminar', 'error');
                }
            });
        }
    });
}
</script>
@endsection
