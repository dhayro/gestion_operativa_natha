<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'telefono',
        'email',
        'direccion',
        'estado',
        'cargo_id',
        'area_id',
        'ubigeo_id',
        'licencia',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class);
    }

    // Relación inversa: Un empleado puede tener un usuario
    public function usuario()
    {
        return $this->hasOne(User::class, 'empleado_id');
    }

    // Relación con cuadrillas a través de la tabla pivot
    public function cuadrillas()
    {
        return $this->belongsToMany(Cuadrilla::class, 'cuadrillas_empleados')
                    ->withPivot('fecha_asignacion', 'estado')
                    ->withTimestamps();
    }

    // Relación con cuadrillas activas
    public function cuadrillasActivas()
    {
        return $this->cuadrillas()->wherePivot('estado', true);
    }

    // Relación directa con la tabla pivot
    public function cuadrillaEmpleados()
    {
        return $this->hasMany(CuadrillaEmpleado::class);
    }
}
