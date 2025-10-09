<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'marca',
        'nombre', 
        'year',
        'modelo',
        'color',
        'placa',
        'tipo_combustible_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'year' => 'integer'
    ];

    // Relación con TipoCombustible
    public function tipoCombustible()
    {
        return $this->belongsTo(TipoCombustible::class);
    }

    // Relación con SOATs
    public function soats()
    {
        return $this->hasMany(Soat::class);
    }

    // Relación con SOAT activo más reciente
    public function soatActivo()
    {
        return $this->hasOne(Soat::class)
                    ->where('estado', true)
                    ->orderBy('fecha_vencimiento', 'desc');
    }

    // Relación con cuadrillas a través de la tabla pivot
    public function cuadrillas()
    {
        return $this->belongsToMany(Cuadrilla::class, 'asignacion_vehiculos')
                    ->withPivot('fecha_asignacion', 'estado')
                    ->withTimestamps();
    }

    // Relación con cuadrillas activas
    public function cuadrillasActivas()
    {
        return $this->cuadrillas()->wherePivot('estado', true);
    }

    // Relación directa con la tabla pivot de asignaciones
    public function asignacionVehiculos()
    {
        return $this->hasMany(AsignacionVehiculo::class);
    }

    // Scope para vehículos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // Scope para vehículos inactivos
    public function scopeInactivos($query)
    {
        return $query->where('estado', false);
    }
}
