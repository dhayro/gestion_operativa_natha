@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/src/apex/apexcharts.css')}}">
<style>
    .widget-stats {
        border-left: 4px solid #0d6efd;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .widget-stats:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .widget-stats .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .widget-stats .card-header h5 {
        margin: 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .widget-stats .card-body {
        padding: 1.5rem;
        text-align: center;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0d6efd;
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-4px);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .card-header .card-title h5 {
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    .card-header .card-title small {
        font-size: 0.75rem;
        display: block;
        margin-top: 0.25rem;
    }

    .employee-list .avatar {
        flex-shrink: 0;
    }

    .employee-list small.text-muted {
        font-size: 0.75rem;
    }

    .vehicle-list .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        flex-shrink: 0;
    }

    .vehicle-list small {
        font-size: 0.875rem;
    }

    .vehicle-list small.text-muted {
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .stat-number {
            font-size: 2rem;
        }

        .card {
            margin-bottom: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row layout-top-spacing">

    <!-- Tarjetas de Estadísticas Generales -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="card widget-stats">
            <div class="card-header">
                <h5>Total Empleados</h5>
            </div>
            <div class="card-body">
                <div class="stat-number">{{ $totalEmpleados }}</div>
                <div class="stat-label">Activos en el sistema</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="card widget-stats">
            <div class="card-header">
                <h5>Cuadrillas Activas</h5>
            </div>
            <div class="card-body">
                <div class="stat-number">{{ $totalCuadrillas }}</div>
                <div class="stat-label">En operación</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="card widget-stats">
            <div class="card-header">
                <h5>Vehículos Disponibles</h5>
            </div>
            <div class="card-body">
                <div class="stat-number">{{ $totalVehiculos }}</div>
                <div class="stat-label">En la flota</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
        <div class="card widget-stats">
            <div class="card-header">
                <h5>Papeletas Activas</h5>
            </div>
            <div class="card-body">
                <div class="stat-number">{{ $papeletasActivas }}</div>
                <div class="stat-label">{{ $papeletasHoy }} creadas hoy</div>
            </div>
        </div>
    </div>

</div>

<!-- Cuadrillas Activas -->
<div class="row layout-top-spacing">
    <div class="col-12">
        <h4 class="mb-4 mt-4">
            <i class="fas fa-layer-group me-2"></i> Cuadrillas Activas
        </h4>
    </div>
</div>

<div class="row layout-spacing">
    @forelse($cuadrillas as $cuadrilla)
    <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <div class="card-title mb-0">
                    <h5 class="mb-0">{{ $cuadrilla->nombre }}</h5>
                    <small class="text-muted">
                        Desde: {{ $cuadrilla->fecha_inicio ? $cuadrilla->fecha_inicio->format('d/m/Y') : 'Sin fecha' }}
                    </small>
                </div>
                <div class="dropdown ms-auto">
                    <a href="javascript:void(0);" class="dropdown-toggle" id="dropdown-{{ $cuadrilla->id }}" data-bs-toggle="dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-{{ $cuadrilla->id }}">
                        <a class="dropdown-item" href="{{ route('cuadrillas.show', $cuadrilla->id) }}">
                            <i class="fas fa-eye me-2"></i> Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Empleados -->
                <div class="mb-3">
                    <h6 class="mb-2">
                        <i class="fas fa-users"></i> Empleados ({{ $cuadrilla->empleadosActivos->count() }})
                    </h6>
                    @if($cuadrilla->empleadosActivos->count() > 0)
                        <div class="employee-list">
                            @foreach($cuadrilla->empleadosActivos->take(5) as $empleado)
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background-color: #e3f2fd; border-radius: 50%; font-size: 12px; font-weight: bold;">
                                    {{ substr($empleado->nombre, 0, 1) }}
                                </div>
                                <div>
                                    <small class="d-block">{{ $empleado->nombre }}</small>
                                    <small class="text-muted">{{ $empleado->cargo->nombre ?? 'N/A' }}</small>
                                </div>
                            </div>
                            @endforeach
                            @if($cuadrilla->empleadosActivos->count() > 5)
                            <small class="text-muted">
                                +{{ $cuadrilla->empleadosActivos->count() - 5 }} más
                            </small>
                            @endif
                        </div>
                    @else
                        <small class="text-muted">Sin empleados asignados</small>
                    @endif
                </div>

                <hr>

                <!-- Vehículos -->
                <div>
                    <h6 class="mb-2">
                        <i class="fas fa-truck"></i> Vehículos ({{ $cuadrilla->vehiculosActivos->count() }})
                    </h6>
                    @if($cuadrilla->vehiculosActivos->count() > 0)
                        <div class="vehicle-list">
                            @foreach($cuadrilla->vehiculosActivos as $vehiculo)
                            <div class="d-flex align-items-center mb-2">
                                <div class="badge bg-primary me-2">
                                    <i class="fas fa-car"></i>
                                </div>
                                <div>
                                    <small class="d-block"><strong>{{ $vehiculo->placa }}</strong></small>
                                    <small class="text-muted">{{ $vehiculo->marca }} - {{ $vehiculo->modelo }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <small class="text-muted">Sin vehículos asignados</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            No hay cuadrillas activas en este momento.
        </div>
    </div>
    @endforelse
</div>

@endsection

@section('scripts')
<script src="{{asset('plugins/src/apex/apexcharts.min.js')}}"></script>
@endsection
