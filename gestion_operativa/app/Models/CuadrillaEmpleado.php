<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuadrillaEmpleado extends Model
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
        'fecha_asignacion' => 'datetime',
        'estado' => 'boolean'
    ];

    // Relación con Cuadrilla
    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class);
    }

    // Relación con Empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    // Scope para asignaciones activas
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    // Scope para asignaciones inactivas
    public function scopeInactivas($query)
    {
        return $query->where('estado', false);
    }
}