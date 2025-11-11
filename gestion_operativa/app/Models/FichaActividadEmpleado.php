<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaActividadEmpleado extends Model
{
    use HasFactory;

    protected $table = 'ficha_actividad_empleados';

    protected $fillable = [
        'ficha_actividad_id',
        'cuadrilla_empleado_id',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ==================== RELACIONES ====================

    public function fichaActividad()
    {
        return $this->belongsTo(FichaActividad::class, 'ficha_actividad_id');
    }

    public function cuadrillaEmpleado()
    {
        return $this->belongsTo(CuadrillaEmpleado::class, 'cuadrilla_empleado_id');
    }

    public function empleado()
    {
        return $this->hasManyThrough(
            Empleado::class,
            CuadrillaEmpleado::class,
            'id',
            'id',
            'cuadrilla_empleado_id',
            'empleado_id'
        );
    }

    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // ==================== SCOPES ====================

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopePorFicha($query, $fichaId)
    {
        return $query->where('ficha_actividad_id', $fichaId);
    }

    // ==================== ACCESSORS ====================

    public function getEmpleadoNombreAttribute()
    {
        if ($this->cuadrillaEmpleado && $this->cuadrillaEmpleado->empleado) {
            return $this->cuadrillaEmpleado->empleado->nombre;
        }
        return 'N/A';
    }

    public function getCuadrillaNombreAttribute()
    {
        if ($this->cuadrillaEmpleado && $this->cuadrillaEmpleado->cuadrilla) {
            return $this->cuadrillaEmpleado->cuadrilla->nombre;
        }
        return 'N/A';
    }
}
