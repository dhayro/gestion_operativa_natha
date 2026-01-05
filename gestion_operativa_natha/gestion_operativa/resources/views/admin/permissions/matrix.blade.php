@extends('layouts.app')

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4>Matriz de Permisos por Rol</h4>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-12">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto; overflow-x: auto;">
                    <table class="table table-striped table-bordered table-sm">
                        <thead style="position: sticky; top: 0; background: #f5f5f5;">
                            <tr>
                                <th style="min-width: 200px;">Permiso / Módulo</th>
                                @foreach($roles as $role)
                                <th style="min-width: 120px; text-align: center;">{{ $role->nombre }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $permisosPorModulo = $permissions->groupBy('modulo');
                            @endphp
                            @foreach($permisosPorModulo as $modulo => $permisos)
                            <tr class="table-info">
                                <td colspan="{{ $roles->count() + 1 }}" style="font-weight: bold; background: #e7f3ff;">
                                    {{ strtoupper($modulo) }}
                                </td>
                            </tr>
                            @foreach($permisos as $permiso)
                            <tr>
                                <td>{{ $permiso->nombre }}</td>
                                @foreach($roles as $role)
                                <td style="text-align: center;">
                                    <div class="form-check" style="display: flex; justify-content: center;">
                                        <input 
                                            class="form-check-input permission-checkbox" 
                                            type="checkbox" 
                                            data-role-id="{{ $role->id }}"
                                            data-permission-id="{{ $permiso->id }}"
                                            @if($role->permissions->contains('id', $permiso->id)) checked @endif
                                            @if(in_array($role->nombre, ['admin', 'supervisor', 'tecnico', 'operario'])) @endif>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 alert alert-info">
                    <strong>Nota:</strong> Los cambios se guardan automáticamente al cambiar los checkboxes
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const roleId = this.getAttribute('data-role-id');
        const permissionId = this.getAttribute('data-permission-id');
        
        // Obtener todos los permisos chequeados para este rol
        const permissions = Array.from(
            document.querySelectorAll(`.permission-checkbox[data-role-id="${roleId}"]:checked`)
        ).map(cb => cb.getAttribute('data-permission-id'));
        
        fetch('{{ route("permissions.matrix.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                role_id: roleId,
                permissions: permissions
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Permisos actualizados');
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>
@endsection
