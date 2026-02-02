@extends('layouts.app')

@section('content')
<div class="seperator-header layout-top-spacing">
    <div class="d-flex justify-content-between align-items-center">
        <h4>Crear Nuevo Permiso</h4>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="row layout-spacing">
    <div class="col-lg-6">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Permiso</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" name="nombre" placeholder="ej: ver_usuarios" value="{{ old('nombre') }}" required>
                        <small class="text-muted">Use snake_case (ej: administrar_usuarios)</small>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="modulo" class="form-label">Módulo</label>
                        <input type="text" class="form-control @error('modulo') is-invalid @enderror" 
                               id="modulo" name="modulo" placeholder="ej: usuarios" value="{{ old('modulo') }}" required>
                        <small class="text-muted">Categoría a la que pertenece este permiso</small>
                        @error('modulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" name="descripcion" rows="3" placeholder="Descripción del permiso">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Crear Permiso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
