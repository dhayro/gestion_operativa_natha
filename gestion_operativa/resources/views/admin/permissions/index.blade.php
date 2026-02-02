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
        <h4>Gestión de Permisos</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#permissionModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Nuevo Permiso
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="permissionsTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Módulo</th>
                                <th>Descripción</th>
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

<!-- Modal para crear/editar permiso -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="permissionForm" action="" method="POST">
                @csrf
                <input type="hidden" id="permission_id" name="permission_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="permissionModalLabel">Crear/Editar Permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Permiso *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                        <small class="text-muted">Ej: ver_reportes, crear_usuario</small>
                    </div>

                    <div class="mb-3">
                        <label for="modulo" class="form-label">Módulo *</label>
                        <select class="form-control" id="modulo" name="modulo" required>
                            <option value="">Seleccione</option>
                            <option value="dashboard">Dashboard</option>
                            <option value="cargos">Cargos</option>
                            <option value="areas">Áreas</option>
                            <option value="empleados">Empleados</option>
                            <option value="materiales">Materiales</option>
                            <option value="proveedores">Proveedores</option>
                            <option value="cuadrillas">Cuadrillas</option>
                            <option value="vehiculos">Vehículos</option>
                            <option value="medidores">Medidores</option>
                            <option value="suministros">Suministros</option>
                            <option value="admin">Administración</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
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
$(document).ready(function() {
    var table = $('#permissionsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route('permissions.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nombre', name: 'nombre' },
            { data: 'modulo', name: 'modulo' },
            { data: 'descripcion', name: 'descripcion' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[1, "asc"]],
        language: {
            url: "{{ asset('plugins/src/table/datatable/language/es.json') }}"
        },
        columnDefs: [
            { targets: 'no-sort', orderable: false }
        ]
    });

    // Manejo del formulario
    $('#permissionForm').on('submit', function(e) {
        e.preventDefault();
        
        var permissionId = $('#permission_id').val();
        var url = permissionId ? '/admin/permissions/' + permissionId : '/admin/permissions';
        var method = permissionId ? 'PUT' : 'POST';

        var formData = {
            nombre: $('#nombre').val(),
            modulo: $('#modulo').val(),
            descripcion: $('#descripcion').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        var $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true);

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
                $('#permissionModal').modal('hide');
                table.ajax.reload();
                $('#permissionForm')[0].reset();
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

    $('#permissionModal').on('hidden.bs.modal', function () {
        $('#permissionForm')[0].reset();
        $('#permission_id').val('');
        $('#permissionModalLabel').text('Crear/Editar Permiso');
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();
    });

    window.editPermission = function(id) {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        $.get('/admin/permissions/' + id, function(data) {
            Swal.close();
            $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
            $('.invalid-feedback').remove();
            $('#permission_id').val(data.id);
            $('#nombre').val(data.nombre);
            $('#modulo').val(data.modulo);
            $('#descripcion').val(data.descripcion || '');
            $('#permissionModalLabel').text('Editar Permiso');
            $('#permissionModal').modal('show');
        }).fail(function() {
            Swal.fire('Error', 'No se pudo cargar el permiso', 'error');
        });
    };

    window.deletePermission = function(id) {
        Swal.fire({
            title: '¿Eliminar Permiso?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                
                $.ajax({
                    url: '/admin/permissions/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire('Eliminado!', 'El permiso ha sido eliminado correctamente.', 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'No se pudo eliminar el permiso.', 'error');
                    }
                });
            }
        });
    };
});
</script>
@endsection
