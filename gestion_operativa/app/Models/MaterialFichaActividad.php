<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialFichaActividad extends Model
{
    use HasFactory;

    protected $table = 'material_ficha_actividades';

    protected $fillable = [
        'ficha_actividad_id',
        'material_id',
        'cantidad',
        'observacion',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ==================== RELACIONES ====================

    public function fichaActividad()
    {
        return $this->belongsTo(FichaActividad::class, 'ficha_actividad_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // ==================== SCOPES ====================

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopePorFicha($query, $fichaId)
    {
        return $query->where('ficha_actividad_id', $fichaId);
    }

    // ==================== ACCESSORS ====================

    public function getMaterialInfoAttribute()
    {
        return $this->material ? $this->material->nombre : 'N/A';
    }

    public function getCantidadFormateadaAttribute()
    {
        return "{$this->cantidad} " . ($this->material ? $this->material->unidad_medida : '');
    }
}
