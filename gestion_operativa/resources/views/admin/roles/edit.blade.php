@extends('layouts.app')

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4>Editar Rol: {{ $role->nombre }}</h4>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-8">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" name="nombre" value="{{ $role->nombre }}" required 
                               @if(in_array($role->nombre, ['admin', 'supervisor', 'tecnico', 'operario'])) disabled @endif>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" name="descripcion" rows="3" 
                                  @if(in_array($role->nombre, ['admin', 'supervisor', 'tecnico', 'operario'])) disabled @endif>{{ $role->descripcion }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="card">
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                @php
                                    $modulos = $permissions->groupBy('modulo');
                                    $rolePermissions = $role->permissions->pluck('id')->toArray();
                                @endphp
                                
                                @foreach($modulos as $modulo => $permisos)
                                <div class="mb-3">
                                    <h6 class="text-primary mb-2">{{ ucfirst($modulo) }}</h6>
                                    <div class="row">
                                        @foreach($permisos as $permiso)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                       value="{{ $permiso->id }}" id="perm_{{ $permiso->id }}"
                                                       @if(in_array($permiso->id, $rolePermissions)) checked @endif
                                                       @if(in_array($role->nombre, ['admin', 'supervisor', 'tecnico', 'operario'])) disabled @endif>
                                                <label class="form-check-label" for="perm_{{ $permiso->id }}">
                                                    {{ $permiso->nombre }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if(!in_array($role->nombre, ['admin', 'supervisor', 'tecnico', 'operario']))
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar Rol</button>
                    </div>
                    @else
                    <div class="alert alert-info">
                        Este es un rol del sistema y no puede ser editado
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Volver</a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
