@extends('layouts.app')

@section('content')
<style>
    .modal-content {
        background-color: #ffffff !important;
        border: 1px solid #e0e0e0;
    }
    
    .modal-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .modal-body {
        background-color: #ffffff !important;
        color: #333333;
        padding: 25px;
    }
    
    .modal-footer {
        background-color: #ffffff !important;
        border-top: 1px solid #e0e0e0;
    }
    
    .roles-list {
        max-height: 500px;
        overflow-y: auto;
        padding-right: 5px;
    }
    
    .roles-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .roles-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .roles-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .roles-list::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
    
    .form-check {
        padding: 12px;
        border-radius: 6px;
        background-color: #f8f9fa;
        border-left: 4px solid #dee2e6;
        transition: all 0.3s ease;
    }
    
    .form-check:hover {
        background-color: #e9ecef;
        border-left-color: #007bff;
    }
    
    .form-check-input {
        width: 20px;
        height: 20px;
        cursor: pointer;
        margin-top: 4px;
        accent-color: #007bff;
    }
    
    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .form-check-label {
        cursor: pointer;
        margin-left: 12px;
        user-select: none;
    }
    
    .form-check-label strong {
        display: block;
        color: #333;
        font-size: 15px;
        margin-bottom: 4px;
    }
    
    .form-check-label small {
        display: block;
        color: #6c757d;
        font-size: 12px;
    }
    
    /* Responsive para móviles */
    @media (max-width: 576px) {
        .modal-dialog {
            margin: 10px;
        }
        
        .modal-body {
            padding: 15px;
        }
        
        .form-check {
            padding: 10px;
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
        }
    }
</style>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">
                Gestión de Usuarios
            </h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table id="usuariosTable" class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Empleado</th>
                    <th>Roles</th>
                    <th>Perfil</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para asignar roles -->
<div class="modal fade" id="rolesModal" tabindex="-1" role="dialog" aria-labelledby="rolesModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="background-color: #ffffff;">
            <div class="modal-header" style="background-color: #ffffff; border-bottom: 1px solid #dee2e6;">
                <h5 class="modal-title" id="rolesModalLabel">Asignar Roles a Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #ffffff;">
                <div id="rolesContent">
                    <p class="text-center text-muted">Cargando...</p>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #ffffff; border-top: 1px solid #dee2e6;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarRolesBtn">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentUserId = null;
let usuariosTable;

$(document).ready(function() {
    // Inicializar DataTable
    usuariosTable = $('#usuariosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('usuarios.data') }}",
            type: "GET"
        },
        columns: [
            { data: 'id', name: 'id', visible: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'empleado_nombre', name: 'empleado_nombre' },
            { data: 'roles', name: 'roles', orderable: false, searchable: false },
            { data: 'perfil', name: 'perfil' },
            { data: 'estado', name: 'estado', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
        },
        columnDefs: [
            {
                targets: -1,
                className: 'text-center'
            }
        ]
    });
});

function asignarRoles(userId) {
    currentUserId = userId;
    
    // Obtener datos de roles para este usuario
    $.ajax({
        url: `/admin/usuarios/${userId}/roles`,
        type: 'GET',
        success: function(data) {
            const user = data.user;
            const userRoles = data.userRoles;
            const allRoles = data.allRoles;
            
            let content = `
                <h6 class="mb-3">Usuario: <strong>${user.name}</strong></h6>
                <p class="text-muted mb-4">Selecciona los roles que deseas asignar a este usuario:</p>
                <div class="roles-list">
            `;
            
            allRoles.forEach(role => {
                const isChecked = userRoles.includes(role.id);
                content += `
                    <div class="form-check mb-3 p-3" style="background-color: #f8f9fa; border-radius: 6px; border-left: 4px solid ${isChecked ? '#007bff' : '#dee2e6'};">
                        <div class="d-flex align-items-start">
                            <input class="form-check-input role-checkbox mt-1" type="checkbox" value="${role.id}" id="role_${role.id}" ${isChecked ? 'checked' : ''} style="width: 20px; height: 20px; cursor: pointer;">
                            <label class="form-check-label ms-3" for="role_${role.id}" style="cursor: pointer; flex: 1;">
                                <strong style="font-size: 15px; color: #333;">${role.nombre}</strong>
                                <small class="text-muted d-block mt-1" style="font-size: 13px;">${role.descripcion || 'Sin descripción'}</small>
                            </label>
                        </div>
                    </div>
                `;
            });
            
            content += `</div>`;
            
            $('#rolesContent').html(content);
            $('#rolesModal').modal('show');
        },
        error: function() {
            alert('Error al cargar los roles del usuario');
        }
    });
}

$('#guardarRolesBtn').click(function() {
    const rolesSeleccionados = [];
    $('.role-checkbox:checked').each(function() {
        rolesSeleccionados.push($(this).val());
    });
    
    $.ajax({
        url: `/admin/usuarios/${currentUserId}/roles`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            roles: rolesSeleccionados
        },
        success: function(data) {
            if (data.success) {
                alert(data.message);
                $('#rolesModal').modal('hide');
                usuariosTable.ajax.reload();
            }
        },
        error: function() {
            alert('Error al asignar roles');
        }
    });
});
</script>
@endsection
