{{-- Modal para agregar/editar dotación de combustible --}}
<div class="modal fade" id="dotacionModal" tabindex="-1" aria-labelledby="dotacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="dotacionModalLabel">
                    <i class="fas fa-gas-pump me-2"></i>Gestionar Dotación de Combustible
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dotacionForm" name="dotacionForm">
                    <input type="hidden" id="dotacion_id" name="dotacion_id">
                    <input type="hidden" id="papeleta_id_dotacion" name="papeleta_id_dotacion">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tipo_combustible_id" class="form-label">Tipo de Combustible <span class="text-danger">*</span></label>
                            <select class="form-control" id="tipo_combustible_id" name="tipo_combustible_id" required>
                                <option value="">Seleccione un tipo</option>
                            </select>
                            <small class="text-muted">Tipos disponibles en el sistema</small>
                        </div>
                        <div class="col-md-6">
                            <label for="cantidad_gl" class="form-label">Cantidad (galones) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cantidad_gl" name="cantidad_gl" step="0.001" min="0" placeholder="0.000" required>
                            <small class="text-muted">Hasta 3 decimales</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="precio_unitario" class="form-label">Precio Unitario (S/.)</label>
                            <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" step="0.01" min="0" placeholder="0.00">
                            <small class="text-muted">Opcional - para cálculo de costo total</small>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_carga" class="form-label">Fecha de Carga</label>
                            <input type="date" class="form-control" id="fecha_carga" name="fecha_carga">
                            <small class="text-muted">Se asigna la fecha actual por defecto</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="numero_vale" class="form-label">Número de Vale <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="numero_vale" name="numero_vale" maxlength="200" placeholder="Ingrese número de vale" required>
                            <small class="text-muted">Identificador único de la carga de combustible</small>
                        </div>
                    </div>

                    <div class="alert alert-info" id="costTotalAlert" style="display: none;">
                        <small>
                            <strong>Costo Total:</strong> <span id="costoTotalValue">0.00</span> S/.
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-save me-1"></i>Guardar
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal para visualizar dotaciones --}}
<div class="modal fade" id="dotacionesListModal" tabindex="-1" aria-labelledby="dotacionesListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="dotacionesListModalLabel">
                    <i class="fas fa-gas-pump me-2"></i>Dotaciones de Combustible - Papeleta: <span id="papeletaNumero"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-primary btn-sm" id="btnAgregarDotacion">
                        <i class="fas fa-plus me-1"></i>Agregar Dotación
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="btnRefrescarDotaciones">
                        <i class="fas fa-sync me-1"></i>Actualizar
                    </button>
                </div>

                {{-- Tabla de dotaciones --}}
                <div class="table-responsive">
                    <table id="dotacionesTable" class="table table-striped table-bordered table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha Carga</th>
                                <th>Tipo</th>
                                <th>Cantidad (gl)</th>
                                <th>P. Unitario</th>
                                <th>Costo Total</th>
                                <th>Vale #</th>
                                <th>Creado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                {{-- Resumen de dotaciones --}}
                <div class="row mt-4" id="resumenDotaciones">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">Total Galones</h6>
                                <h3 class="text-primary" id="totalGalones">0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">Costo Total (S/.)</h6>
                                <h3 class="text-success" id="totalCosto">0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">Cantidad Cargas</h6>
                                <h3 class="text-warning" id="cantidadCargas">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">P. Promedio/gl</h6>
                                <h3 class="text-info" id="precioPromedio">0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Nota: Las funciones de dotaciones se han movido al index.blade.php para evitar conflictos con las secciones @section('scripts')
});
</script>
