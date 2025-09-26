<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    use HasFactory;

    protected $table = 'ubigeos';
    
    protected $fillable = [
        'nombre',
        'codigo_postal',
        'dependencia_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // Relación con el padre (ubigeo dependiente)
    public function dependencia()
    {
        return $this->belongsTo(Ubigeo::class, 'dependencia_id');
    }

    // Relación con los hijos (ubigeos que dependen de este)
    public function dependientes()
    {
        return $this->hasMany(Ubigeo::class, 'dependencia_id');
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

    // Accessor para mostrar la jerarquía completa
    public function getJerarquiaAttribute()
    {
        $jerarquia = $this->nombre;
        $padre = $this->dependencia;
        
        while ($padre) {
            $jerarquia = $padre->nombre . ' > ' . $jerarquia;
            $padre = $padre->dependencia;
        }
        
        return $jerarquia;
    }
}
