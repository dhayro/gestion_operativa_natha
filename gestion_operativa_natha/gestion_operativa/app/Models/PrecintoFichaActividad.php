<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrecintoFichaActividad extends Model
{
    use HasFactory;

    protected $table = 'precinto_ficha_actividades';

    protected $fillable = [
        'ficha_actividad_id',
        'material_id',
        'medidor_ficha_actividad_id',
        'tipo',
        'numero_precinto',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ==================== RELACIONES ====================

    public function fichaActividad()
    {
        return $this->belongsTo(FichaActividad::class, 'ficha_actividad_id');
    }

    public function medidorFichaActividad()
    {
        return $this->belongsTo(MedidorFichaActividad::class, 'medidor_ficha_actividad_id');
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

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorMedidor($query, $medidorFichaActividadId)
    {
        return $query->where('medidor_ficha_actividad_id', $medidorFichaActividadId);
    }

    // ==================== ACCESSORS ====================

    public function getTipoNombreAttribute()
    {
        $tipos = [
            'tapa' => '🔴 Tapa',
            'caja' => '📦 Caja',
            'bornera' => '⚡ Bornera'
        ];
        return $tipos[$this->tipo] ?? $this->tipo;
    }
}
