<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Soat extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehiculo_id',
        'proveedor_id', 
        'numero_soat',
        'fecha_emision',
        'fecha_vencimiento',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date'
    ];

    // Relación con Vehículo
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    // Relación con Proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    // Scope para SOATs activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // Scope para SOATs inactivos
    public function scopeInactivos($query)
    {
        return $query->where('estado', false);
    }

    // Scope para SOATs vigentes (no vencidos)
    public function scopeVigentes($query)
    {
        return $query->where('fecha_vencimiento', '>=', Carbon::now('America/Lima')->format('Y-m-d'));
    }

    // Scope para SOATs vencidos
    public function scopeVencidos($query)
    {
        return $query->where('fecha_vencimiento', '<', Carbon::now('America/Lima')->format('Y-m-d'));
    }

    // Scope para SOATs por vencer (próximos 30 días)
    public function scopePorVencer($query, $dias = 30)
    {
        $fechaLimite = Carbon::now('America/Lima')->addDays($dias)->format('Y-m-d');
        return $query->where('fecha_vencimiento', '<=', $fechaLimite)
                    ->where('fecha_vencimiento', '>=', Carbon::now('America/Lima')->format('Y-m-d'));
    }

    // Accessor para verificar si está vigente
    public function getEsVigenteAttribute()
    {
        return $this->fecha_vencimiento >= Carbon::now('America/Lima')->format('Y-m-d');
    }

    // Accessor para verificar si está vencido
    public function getEsVencidoAttribute()
    {
        return $this->fecha_vencimiento < Carbon::now('America/Lima')->format('Y-m-d');
    }

    // Accessor para días restantes
    public function getDiasRestantesAttribute()
    {
        return Carbon::now('America/Lima')->diffInDays(Carbon::parse($this->fecha_vencimiento), false);
    }
}
