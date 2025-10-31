<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $table = 'movimientos';
    
    protected $fillable = [
        'material_id',
        'tipo_movimiento',
        'nea_detalle_id',
        'pecosa_detalle_id',
        'cantidad',
        'precio_unitario',
        'incluye_igv',
        'fecha',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'incluye_igv' => 'boolean',
        'estado' => 'boolean'
    ];

    // Relaciones
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function neaDetalle()
    {
        return $this->belongsTo(NeaDetalle::class);
    }

    public function pecosaDetalle()
    {
        return $this->belongsTo(PecosaDetalle::class);
    }

    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // Scopes
    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'salida');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopePorMaterial($query, $materialId)
    {
        return $query->where('material_id', $materialId);
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }
}

