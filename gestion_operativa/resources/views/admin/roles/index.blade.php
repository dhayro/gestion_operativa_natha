@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
<link rel="stylesheet" href="{{asset('plugins/src/sweetalerts2/sweetalerts2.css')}}">
<style>
    .modal-content { background: #fff !important; }
    .full-height-row { min-height: 75vh; display: flex; align-items: stretch; }
    .badge-secondary { background-color: #6c757d; }
    .permissions-list { max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px; }
    .permission-group { margin-bottom: 15px; }
    .permission-group h6 { color: #0d6efd; font-weight: 600; margin-bottom: 8px; }
</style>
@endsection

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4>Gestión de Roles</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="openCreateRoleModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Nuevo Rol
        </button>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive full-height-row">
                    <table id="rolesTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Permisos</th>
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

<!-- Modal para crear/editar rol -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="roleForm" action="" method="POST">
                @csrf
                <input type="hidden" id="roleId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Crear/Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="255" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="permissions-list" id="permissionsList">
                            <p class="text-muted">Cargando permisos...</p>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/spanish.js')}}"></script>
<script src="{{asset('plugins/src/sweetalerts2/sweetalerts2.min.js')}}"></script>
<script>
var allPermissions = [];

$(document).ready(function() {
    // Cargar permisos disponibles al cargar la página
    loadAllPermissions();

    window.rolesTable = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('roles.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: "5%" },
            { data: 'nombre', name: 'nombre', width: "20%" },
            { data: 'descripcion', name: 'descripcion', width: "40%" },
            { data: 'permisos_count', name: 'permisos_count', orderable: false, searchable: false, width: "15%" },
            { data: 'action', name: 'action', orderable: false, searchable: false, width: "20%" }
        ],
        order: [[1, "asc"]],
        language: { url: "{{ asset('plugins/src/table/datatable/spanish.js') }}" },
        createdRow: function(row, data) {
            $(row).find('td:eq(1)').html('<span class="badge badge-secondary">' + data.nombre + '</span>');
        }
    });
});

function loadAllPermissions() {
    $.ajax({
        url: '{{ route("permissions.index") }}',
        method: 'GET',
        dataType: 'html',
        success: function(data) {
            // Extraer los permisos de la página HTML (alternativa: crear un endpoint JSON)
            // Por ahora, cargaremos los permisos desde la estructura
        }
    });
}

function renderPermissionsCheckboxes(selectedPermissionIds = []) {
    // Permisos agrupados por módulo (hardcoded por ahora)
    const permissionsData = {
        'dashboard': [
            { id: 1, nombre: 'ver_dashboard', descripcion: 'Ver dashboard' }
        ],
        'cargos': [
            { id: 2, nombre: 'ver_cargos', descripcion: 'Ver cargos' },
            { id: 3, nombre: 'crear_cargo', descripcion: 'Crear cargo' },
            { id: 4, nombre: 'editar_cargo', descripcion: 'Editar cargo' },
            { id: 5, nombre: 'eliminar_cargo', descripcion: 'Eliminar cargo' }
        ],
        'areas': [
            { id: 6, nombre: 'ver_areas', descripcion: 'Ver áreas' },
            { id: 7, nombre: 'crear_area', descripcion: 'Crear área' },
            { id: 8, nombre: 'editar_area', descripcion: 'Editar área' },
            { id: 9, nombre: 'eliminar_area', descripcion: 'Eliminar área' }
        ],
        'empleados': [
            { id: 10, nombre: 'ver_empleados', descripcion: 'Ver empleados' },
            { id: 11, nombre: 'crear_empleado', descripcion: 'Crear empleado' },
            { id: 12, nombre: 'editar_empleado', descripcion: 'Editar empleado' },
            { id: 13, nombre: 'eliminar_empleado', descripcion: 'Eliminar empleado' }
        ],
        'admin': [
            { id: 96, nombre: 'administrar_roles', descripcion: 'Administrar roles y permisos' },
            { id: 97, nombre: 'administrar_permisos', descripcion: 'Gestionar matriz de permisos' }
        ]
    };

    let html = '';
    for (const [modulo, permisos] of Object.entries(permissionsData)) {
        html += '<div class="permission-group">';
        html += '<h6>' + modulo.charAt(0).toUpperCase() + modulo.slice(1) + '</h6>';
        html += '<div class="row">';
        permisos.forEach(permiso => {
            const isChecked = selectedPermissionIds.includes(permiso.id) ? 'checked' : '';
            html += '<div class="col-md-6">';
            html += '<div class="form-check">';
            html += '<input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="' + permiso.id + '" id="perm_' + permiso.id + '" ' + isChecked + '>';
            html += '<label class="form-check-label" for="perm_' + permiso.id + '">' + permiso.nombre + '</label>';
            html += '</div>';
            html += '</div>';
        });
        html += '</div>';
        html += '</div>';
    }
    return html;
}

// Abrir modal para crear
window.openCreateRoleModal = function() {
    $('#roleForm')[0].reset();
    $('#roleId').val('');
    $('#roleModalLabel').text('Nuevo Rol');
    $('#roleForm').attr('action', '{{ route("roles.store") }}');
    $('#method-field').remove();
    $('#permissionsList').html(renderPermissionsCheckboxes());
    $('#roleModal').modal('show');
}

// Editar
window.editRole = function(id) {
    $.get('{{ url("admin/roles") }}/' + id, function(data) {
        $('#roleId').val(data.id);
        $('#nombre').val(data.nombre);
        $('#descripcion').val(data.descripcion || '');
        $('#roleModalLabel').text('Editar Rol');
        $('#roleForm').attr('action', '{{ url("admin/roles") }}/' + id);
        $('#method-field').remove();
        $('#roleForm').append('<input type="hidden" name="_method" value="PUT" id="method-field">');
        
        // Cargar permisos del rol
        var selectedPermissions = data.permissions ? data.permissions.map(p => p.id) : [];
        $('#permissionsList').html(renderPermissionsCheckboxes(selectedPermissions));
        
        $('#roleModal').modal('show');
    });
}

// Guardar
$('#roleForm').on('submit', function(e) {
    e.preventDefault();
    var $submitBtn = $(this).find('button[type="submit"]');
    $submitBtn.prop('disabled', true);
    var id = $('#roleId').val();
    var url = id ? ('{{ url("admin/roles") }}/' + id) : '{{ route("roles.store") }}';
    var formData = $(this).serializeArray();
    if (id) formData.push({name: '_method', value: 'PUT'});
    
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function() {
            $('#roleModal').modal('hide');
            window.rolesTable.ajax.reload();
            Swal.fire('Éxito', 'Rol guardado correctamente', 'success');
        },
        error: function(xhr) {
            Swal.fire('Error', 'Ocurrió un error al guardar: ' + (xhr.responseJSON?.message || 'Intenta de nuevo'), 'error');
        }
    }).always(function() {
        setTimeout(function() { $submitBtn.prop('disabled', false); }, 300);
    });
});

// Eliminar
window.deleteRole = function(id) {
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
                url: '{{ url("admin/roles") }}/' + id,
                method: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
                success: function() {
                    window.rolesTable.ajax.reload();
                    Swal.fire('Eliminado', 'El rol ha sido eliminado', 'success');
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
