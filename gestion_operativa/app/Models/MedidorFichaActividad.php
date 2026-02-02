<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedidorFichaActividad extends Model
{
    use HasFactory;

    protected $table = 'medidor_ficha_actividades';

    protected $fillable = [
        'ficha_actividad_id',
        'medidor_id',
        'tipo',
        'digitos_enteros',
        'digitos_decimales',
        'lectura',
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

    public function medidor()
    {
        return $this->belongsTo(Medidor::class, 'medidor_id');
    }

    public function precintos()
    {
        return $this->hasMany(PrecintoFichaActividad::class, 'medidor_ficha_actividad_id');
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

    // ==================== ACCESSORS ====================

    public function getTipoNombreAttribute()
    {
        $tipos = [
            'nuevo' => '✨ Nuevo',
            'retirado' => '🗑️ Retirado',
            'existente' => '📌 Existente'
        ];
        return $tipos[$this->tipo] ?? $this->tipo;
    }

    public function getMedidorInfoAttribute()
    {
        return $this->medidor ? "{$this->medidor->numero} ({$this->medidor->marca})" : 'N/A';
    }
}
