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
        'estado' => 'integer', // 1 = Disponible, 2 = Asignado
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Scopes para filtrar por estado
    public function scopeDisponibles($query)
    {
        return $query->where('medidors.estado', 1);
    }
    
    public function scopeAsignados($query)
    {
        return $query->where('medidors.estado', 2);
    }
    
    // Método para cambiar estado
    public function marcarAsignado()
    {
        $this->update(['estado' => 2]);
    }
    
    public function marcarDisponible()
    {
        $this->update(['estado' => 1]);
    }
    
    // Accessor para mostrar estado como texto
    public function getEstadoTextoAttribute()
    {
        return match($this->estado) {
            1 => 'Disponible',
            2 => 'Asignado',
            default => 'Desconocido'
        };
    }

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

    // Scope para obtener solo los activos (medidores en uso)
    public function scopeActivos($query)
    {
        return $query->where('estado', '!=', 0); // Todos excepto eliminados
    }

    // Accessor para mostrar el material
    public function getMaterialNombreAttribute()
    {
        return $this->material ? $this->material->nombre : 'N/A';
    }
}
