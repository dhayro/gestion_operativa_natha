<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuadrillasEmpleado extends Model
{
    use HasFactory;

    protected $table = 'cuadrillas_empleados';

    protected $fillable = [
        'cuadrilla_id',
        'empleado_id',
        'fecha_asignacion',
        'estado'
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'estado' => 'boolean'
    ];

    /**
     * Relación con Cuadrilla
     */
    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class, 'cuadrilla_id');
    }

    /**
     * Relación con Empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Relación inversa: Un cuadrilla-empleado puede tener muchos PECOSAs
     */
    public function pecosas()
    {
        return $this->hasMany(Pecosa::class, 'cuadrilla_empleado_id');
    }

    /**
     * Scope para empleados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para empleados inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('estado', false);
    }

    /**
     * Accessor para obtener nombre completo del empleado
     */
    public function getNombreCompletoEmpleadoAttribute()
    {
        return $this->empleado ? $this->empleado->nombre . ' ' . $this->empleado->apellido : 'N/A';
    }

    /**
     * Accessor para obtener nombre de la cuadrilla
     */
    public function getNombreCuadrillaAttribute()
    {
        return $this->cuadrilla ? $this->cuadrilla->nombre : 'N/A';
    }
}
