<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposActividad extends Model
{
    use HasFactory;

    protected $table = 'tipos_actividads';

    protected $fillable = [
        'nombre',
        'dependencia_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación: Actividad padre
    public function padre()
    {
        return $this->belongsTo(TiposActividad::class, 'dependencia_id');
    }

    // Relación: Actividades hijo
    public function hijos()
    {
        return $this->hasMany(TiposActividad::class, 'dependencia_id');
    }

    // Scope para obtener solo activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // Scope para obtener solo padres (sin dependencia)
    public function scopePadres($query)
    {
        return $query->whereNull('dependencia_id');
    }

    // Accessor para formatear el estado
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    // Accessor para nombre del padre
    public function getNombrePadreAttribute()
    {
        return $this->padre ? $this->padre->nombre : 'N/A';
    }
}
