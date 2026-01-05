<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuadrilla extends Model
{
    use HasFactory;

    protected $table = 'cuadrillas';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'estado' => 'boolean'
    ];

    // Relación con empleados a través de la tabla pivot
    public function empleados()
    {
        return $this->belongsToMany(Empleado::class, 'cuadrillas_empleados')
                    ->withPivot('fecha_asignacion', 'estado')
                    ->withTimestamps();
    }

    // Relación con empleados activos
    public function empleadosActivos()
    {
        return $this->empleados()->wherePivot('estado', true);
    }

    // Relación directa con la tabla pivot de empleados
    public function cuadrillaEmpleados()
    {
        return $this->hasMany(CuadrillaEmpleado::class);
    }

    // Relación con vehículos a través de la tabla pivot
    public function vehiculos()
    {
        return $this->belongsToMany(Vehiculo::class, 'asignacion_vehiculos')
                    ->withPivot('fecha_asignacion', 'estado')
                    ->withTimestamps();
    }

    // Relación con vehículos activos
    public function vehiculosActivos()
    {
        return $this->vehiculos()->wherePivot('estado', true);
    }

    // Relación directa con la tabla pivot de vehículos
    public function asignacionVehiculos()
    {
        return $this->hasMany(AsignacionVehiculo::class);
    }

    // Alias para mantener consistencia con el controlador
    public function asignacionesVehiculos()
    {
        return $this->hasMany(AsignacionVehiculo::class);
    }

    // Relación con movimientos de materiales pecosa
    public function materialesPecosaMovimientos()
    {
        return $this->hasMany(MaterialPecosaMovimiento::class);
    }

    // Scope para cuadrillas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    // Scope para cuadrillas inactivas
    public function scopeInactivas($query)
    {
        return $query->where('estado', false);
    }
}
