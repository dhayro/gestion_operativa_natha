<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaActividad extends Model
{
    use HasFactory;

    protected $table = 'ficha_actividads';

    protected $fillable = [
        'tipo_actividad_id',
        'suministro_id',
        'tipo_propiedad_id',
        'construccion_id',
        'servicio_electrico_id',
        'uso_id',
        'numero_piso',
        'situacion_id',
        'situacion_detalle',
        'suministro_derecho',
        'suministro_izquierdo',
        'direccion',
        'latitud',
        'longitud',
        'observacion',
        'documento',
        'fecha',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ==================== RELACIONES ====================

    /**
     * Relación con Suministro (Propietario)
     */
    public function suministro()
    {
        return $this->belongsTo(Suministro::class, 'suministro_id');
    }

    /**
     * Relación con TiposActividad
     */
    public function tipoActividad()
    {
        return $this->belongsTo(TiposActividad::class, 'tipo_actividad_id');
    }

    /**
     * Relación con TipoPropiedad
     */
    public function tipoPropiedad()
    {
        return $this->belongsTo(TipoPropiedad::class, 'tipo_propiedad_id');
    }

    /**
     * Relación con Construccion
     */
    public function construccion()
    {
        return $this->belongsTo(Construccion::class, 'construccion_id');
    }

    /**
     * Relación con ServicioElectrico
     */
    public function servicioElectrico()
    {
        return $this->belongsTo(ServicioElectrico::class, 'servicio_electrico_id');
    }

    /**
     * Relación con Uso
     */
    public function uso()
    {
        return $this->belongsTo(Uso::class, 'uso_id');
    }

    /**
     * Relación con Situacion
     */
    public function situacion()
    {
        return $this->belongsTo(Situacion::class, 'situacion_id');
    }

    /**
     * Relación con Usuario de Creación
     */
    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    /**
     * Relación con Usuario de Actualización
     */
    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    /**
     * Relación con Fotos
     */
    public function fotos()
    {
        return $this->hasMany(FotoFichaActividad::class, 'ficha_actividad_id');
    }

    /**
     * Relación con Empleados (a través de ficha_actividad_empleados y cuadrillas_empleados)
     */
    public function empleados()
    {
        return $this->hasManyThrough(
            Empleado::class,
            FichaActividadEmpleado::class,
            'ficha_actividad_id',  // Foreign key on ficha_actividad_empleados
            'id',  // Foreign key on empleados
            'id',  // Local key on ficha_actividads
            'cuadrilla_empleado_id'  // Local key on ficha_actividad_empleados
        )->join('cuadrillas_empleados', 'ficha_actividad_empleados.cuadrilla_empleado_id', '=', 'cuadrillas_empleados.id')
         ->select('empleados.*');
    }

    /**
     * Relación con Medidores
     */
    public function medidores()
    {
        return $this->belongsToMany(Medidor::class, 'medidor_ficha_actividades', 'ficha_actividad_id', 'medidor_id');
    }

    /**
     * Relación con Precintos
     */
    public function precintos()
    {
        return $this->hasMany(PrecintoFichaActividad::class, 'ficha_actividad_id');
    }

    /**
     * Relación con Materiales
     */
    public function materiales()
    {
        return $this->belongsToMany(Material::class, 'material_ficha_actividades', 'ficha_actividad_id', 'material_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Obtener fichas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope: Obtener fichas de un suministro
     */
    public function scopePorSuministro($query, $suministroId)
    {
        return $query->where('suministro_id', $suministroId);
    }

    /**
     * Scope: Obtener fichas por tipo de actividad
     */
    public function scopePorTipoActividad($query, $tipoActividadId)
    {
        return $query->where('tipo_actividad_id', $tipoActividadId);
    }

    /**
     * Scope: Obtener fichas entre fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Obtener estado como texto
     */
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    /**
     * Obtener información del suministro
     */
    public function getSuministroInfoAttribute()
    {
        return $this->suministro ? "{$this->suministro->codigo} - {$this->suministro->nombre}" : 'N/A';
    }

    /**
     * Obtener tipo actividad nombre
     */
    public function getTipoActividadNombreAttribute()
    {
        return $this->tipoActividad ? $this->tipoActividad->nombre : 'N/A';
    }

    /**
     * Obtener coordenadas
     */
    public function getCoordenadasAttribute()
    {
        if ($this->latitud && $this->longitud) {
            return "{$this->latitud}, {$this->longitud}";
        }
        return 'Sin coordenadas';
    }
}
