<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medidor extends Model
{
    use HasFactory;

    protected $table = 'medidors';

    protected $fillable = [
        'serie',
        'modelo',
        'capacidad_amperios',
        'año_fabricacion',
        'marca',
        'numero_hilos',
        'material_id',
        'fm',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con Material
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    // Relación con Usuario de Creación
    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    // Relación con Usuario de Actualización
    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // Scope para obtener solo los activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // Accessor para mostrar el estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    // Accessor para mostrar el material
    public function getMaterialNombreAttribute()
    {
        return $this->material ? $this->material->nombre : 'N/A';
    }
}
