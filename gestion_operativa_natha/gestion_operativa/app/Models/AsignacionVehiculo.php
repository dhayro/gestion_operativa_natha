<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionVehiculo extends Model
{
    use HasFactory;

    protected $table = 'asignacion_vehiculos';

    protected $fillable = [
        'cuadrilla_id',
        'vehiculo_id',
        'empleado_id',
        'fecha_asignacion',
        'estado'
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'estado' => 'boolean'
    ];

    /**
     * Relación con Cuadrilla
     */
    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class);
    }

    /**
     * Relación con Vehiculo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Relación con Empleado (conductor responsable)
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /**
     * Relación con Papeletas
     */
    public function papeletas()
    {
        return $this->hasMany(Papeleta::class, 'asignacion_vehiculo_id');
    }
}
