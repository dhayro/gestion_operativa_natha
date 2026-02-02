<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedidorSuministro extends Model
{
    use HasFactory;

    protected $table = 'medidor_suministros';

    protected $fillable = [
        'suministro_id',
        'medidor_id',
        'fecha_cambio',
        'observaciones',
        'ficha_actividad_id',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_cambio' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ==================== RELACIONES ====================

    public function suministro()
    {
        return $this->belongsTo(Suministro::class, 'suministro_id');
    }

    public function medidor()
    {
        return $this->belongsTo(Medidor::class, 'medidor_id');
    }

    public function fichaActividad()
    {
        return $this->belongsTo(FichaActividad::class, 'ficha_actividad_id');
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

    public function scopePorSuministro($query, $suministroId)
    {
        return $query->where('suministro_id', $suministroId);
    }

    public function scopePorMedidor($query, $medidorId)
    {
        return $query->where('medidor_id', $medidorId);
    }

    public function scopePorFicha($query, $fichaId)
    {
        return $query->where('ficha_actividad_id', $fichaId);
    }

    // ==================== ACCESSORS ====================

    public function getFechaFormateadaAttribute()
    {
        return $this->fecha_cambio ? $this->fecha_cambio->format('d/m/Y H:i') : '-';
    }
}
