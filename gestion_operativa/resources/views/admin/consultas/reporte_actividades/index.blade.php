@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="card-title">{{ $title }}</div>
                                    <p class="card-category mb-0">Fichas de actividad realizadas en un rango de fechas, por cuadrilla o por empleado</p>
                                </div>
                                <a class="btn btn-success btn-sm" href="{{ route('reporte_actividades.exportar', request()->query()) }}">
                                    <i class="fas fa-file-excel"></i> Exportar a Excel
                                </a>
                            </div>

                            <div class="card-body">
                                <form method="GET" action="{{ route('reporte_actividades.index') }}" class="row g-3 align-items-end mb-4">
                                    <div class="col-md-2">
                                        <label for="fecha_desde" class="form-label">Desde</label>
                                        <input type="date" id="fecha_desde" name="fecha_desde" class="form-control" value="{{ $filtros['fecha_desde'] }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="fecha_hasta" class="form-label">Hasta</label>
                                        <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control" value="{{ $filtros['fecha_hasta'] }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cuadrilla_id" class="form-label">Cuadrilla</label>
                                        <select id="cuadrilla_id" name="cuadrilla_id" class="form-select">
                                            <option value="">Todas las cuadrillas</option>
                                            @foreach($cuadrillas as $cuadrilla)
                                                <option value="{{ $cuadrilla->id }}" @selected($filtros['cuadrilla_id'] == $cuadrilla->id)>{{ $cuadrilla->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="empleado_id" class="form-label">Empleado</label>
                                        <select id="empleado_id" name="empleado_id" class="form-select">
                                            <option value="">Todos los empleados</option>
                                            @foreach($empleados as $empleado)
                                                <option value="{{ $empleado->id }}" @selected($filtros['empleado_id'] == $empleado->id)>{{ $empleado->nombre }} {{ $empleado->apellido }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="vista" class="form-label">Vista</label>
                                        <select id="vista" name="vista" class="form-select">
                                            <option value="resumen" @selected($filtros['vista'] === 'resumen')>Resumen</option>
                                            <option value="detalle" @selected($filtros['vista'] === 'detalle')>Detalle</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Generar reporte</button>
                                        <a href="{{ route('reporte_actividades.index') }}" class="btn btn-light">Limpiar</a>
                                    </div>
                                </form>

                                @error('fecha_hasta')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <p class="text-muted small mb-3">
                                    Mostrando actividades del <strong>{{ \Carbon\Carbon::parse($filtros['fecha_desde'])->format('d/m/Y') }}</strong>
                                    al <strong>{{ \Carbon\Carbon::parse($filtros['fecha_hasta'])->format('d/m/Y') }}</strong>
                                    @if($filtros['cuadrilla_id'])
                                        · Cuadrilla: <strong>{{ $cuadrillas->firstWhere('id', $filtros['cuadrilla_id'])?->nombre }}</strong>
                                    @endif
                                    @if($filtros['empleado_id'])
                                        · Empleado: <strong>{{ $empleados->firstWhere('id', $filtros['empleado_id'])?->nombre }} {{ $empleados->firstWhere('id', $filtros['empleado_id'])?->apellido }}</strong>
                                    @endif
                                </p>

                                <!-- Totales -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6 col-md-2">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="fs-4 fw-bold">{{ $totales['actividades'] }}</div>
                                            <small class="text-muted">Actividades</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="fs-4 fw-bold">{{ $totales['empleados'] }}</div>
                                            <small class="text-muted">Empleados</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="fs-4 fw-bold">{{ $totales['medidores'] }}</div>
                                            <small class="text-muted">Medidores</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="fs-4 fw-bold">{{ $totales['materiales'] }}</div>
                                            <small class="text-muted">Materiales (ítems)</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="fs-4 fw-bold">{{ $totales['precintos'] }}</div>
                                            <small class="text-muted">Precintos</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="fs-4 fw-bold">{{ $totales['fotos'] }}</div>
                                            <small class="text-muted">Fotos</small>
                                        </div>
                                    </div>
                                </div>

                                @if($totales['actividades'] === 0)
                                    <div class="alert alert-info mb-0">No hay actividades registradas en el rango seleccionado.</div>

                                @elseif($filtros['vista'] === 'resumen')
                                    <h6 class="mb-2">Resumen por cuadrilla</h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-sm table-striped table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Cuadrilla</th>
                                                    <th class="text-end">Actividades</th>
                                                    <th class="text-end">Empleados</th>
                                                    <th class="text-end">Medidores</th>
                                                    <th class="text-end">Materiales (ítems)</th>
                                                    <th class="text-end">Precintos</th>
                                                    <th class="text-end">Fotos</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($resumenCuadrillas as $fila)
                                                    <tr>
                                                        <td>{{ $fila['cuadrilla'] }}</td>
                                                        <td class="text-end">{{ $fila['actividades'] }}</td>
                                                        <td class="text-end">{{ $fila['empleados'] }}</td>
                                                        <td class="text-end">{{ $fila['medidores'] }}</td>
                                                        <td class="text-end">{{ $fila['materiales'] }}</td>
                                                        <td class="text-end">{{ $fila['precintos'] }}</td>
                                                        <td class="text-end">{{ $fila['fotos'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <h6 class="mb-2">Actividades por tipo</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered mb-0" style="max-width: 480px;">
                                            <thead>
                                                <tr>
                                                    <th>Tipo de actividad</th>
                                                    <th class="text-end">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($resumenTipos as $tipo => $cantidad)
                                                    <tr>
                                                        <td>{{ $tipo }}</td>
                                                        <td class="text-end">{{ $cantidad }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                @else
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Cuadrilla</th>
                                                    <th>Tipo de actividad</th>
                                                    <th>Suministro</th>
                                                    <th>Dirección</th>
                                                    <th>Empleados</th>
                                                    <th class="text-end">Medidores</th>
                                                    <th class="text-end">Materiales</th>
                                                    <th class="text-end">Precintos</th>
                                                    <th class="text-end">Fotos</th>
                                                    <th>Creado por</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($fichas as $ficha)
                                                    <tr>
                                                        <td class="text-nowrap">{{ $ficha->fecha?->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $ficha->cuadrilla_nombre }}</td>
                                                        <td>{{ $ficha->tipoActividad?->nombre }}</td>
                                                        <td>{{ trim(($ficha->suministro?->codigo ?? '') . ' ' . ($ficha->suministro?->nombre ?? '')) }}</td>
                                                        <td>{{ $ficha->direccion }}</td>
                                                        <td>{{ $ficha->participantes_nombres }}</td>
                                                        <td class="text-end">{{ $ficha->medidorFichas->count() }}</td>
                                                        <td class="text-end">{{ $ficha->materialFichas->count() }}</td>
                                                        <td class="text-end">{{ $ficha->precintos->count() }}</td>
                                                        <td class="text-end">{{ $ficha->fotos->count() }}</td>
                                                        <td>{{ $ficha->usuarioCreacion?->name }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
