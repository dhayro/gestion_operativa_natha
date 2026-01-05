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
        <h4 class="">Gestión de Categorías</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoriaModal" onclick="openCreateCategoriaModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nueva Categoría
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="categoriasTable" class="table table-striped table-bordered table-hover" style="width:100%">
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

<!-- Modal para crear/editar categoría -->
<div class="modal fade" id="categoriaModal" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="categoriaForm" action="" method="POST">
                @csrf
                <input type="hidden" id="categoriaId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoriaModalLabel">Crear/Editar Categoría</h5>
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
    window.categoriasTable = $('#categoriasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('categorias.data') }}',
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
window.openCreateCategoriaModal = function() {
    $('#categoriaForm')[0].reset();
    $('#categoriaId').val('');
    $('#categoriaModalLabel').text('Nueva Categoría');
    $('#categoriaForm').attr('action', '{{ route("categorias.store") }}');
    $('#method-field').remove();
    $('#categoriaModal').modal('show');
}

// Editar
window.editCategoria = function(id) {
    $.get('{{ url("categorias") }}/' + id, function(data) {
        $('#categoriaId').val(data.id);
        $('#nombre').val(data.nombre);
        $('#estado').prop('checked', !!data.estado);
        $('#categoriaModalLabel').text('Editar Categoría');
        $('#categoriaForm').attr('action', '{{ url("categorias") }}/' + id);
        $('#method-field').remove();
        $('#categoriaForm').append('<input type="hidden" name="_method" value="PUT" id="method-field">');
        $('#categoriaModal').modal('show');
    });
}

// Guardar
$('#categoriaForm').on('submit', function(e) {
    e.preventDefault();
    var $submitBtn = $(this).find('button[type="submit"]');
    $submitBtn.prop('disabled', true);
    var id = $('#categoriaId').val();
    var url = id ? ('{{ url("categorias") }}/' + id) : '{{ url("categorias") }}';
    var method = id ? 'POST' : 'POST';
    var formData = $(this).serializeArray();
    if (id) formData.push({name: '_method', value: 'PUT'});
    formData.push({name: 'estado', value: $('#estado').is(':checked') ? 1 : 0});
    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function() {
            $('#categoriaModal').modal('hide');
            window.categoriasTable.ajax.reload();
            Swal.fire('Éxito', 'Categoría guardada correctamente', 'success');
        },
        error: function(xhr) {
            Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
        }
    }).always(function() {
        setTimeout(function() { $submitBtn.prop('disabled', false); }, 300);
    });
});

// Eliminar
window.deleteCategoria = function(id) {
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
                url: '{{ url("categorias") }}/' + id,
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                success: function() {
                    window.categoriasTable.ajax.reload();
                    Swal.fire('Eliminado', 'La categoría ha sido eliminada', 'success');
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
