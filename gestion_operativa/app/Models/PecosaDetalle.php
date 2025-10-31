<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PecosaDetalle extends Model
{
    use HasFactory;

    protected $table = 'pecosa_detalles';

    protected $fillable = [
        'pecosa_id',
        'nea_detalle_id',
        'cantidad',
        'precio_unitario',
        'incluye_igv',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio_unitario' => 'decimal:3',
        'incluye_igv' => 'boolean',
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ===== RELACIONES =====

    public function pecosa()
    {
        return $this->belongsTo(Pecosa::class);
    }

    public function neaDetalle()
    {
        return $this->belongsTo(NeaDetalle::class, 'nea_detalle_id');
    }

    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // ===== SCOPES =====

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // ===== ACCESORES =====

    /**
     * Obtener subtotal del detalle
     */
    public function getSubtotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }

    /**
     * Obtener IGV del detalle
     */
    public function getIgvAttribute()
    {
        if ($this->incluye_igv) {
            $subTotal = $this->cantidad * $this->precio_unitario;
            return $subTotal - ($subTotal / 1.18);
        }
        return 0;
    }

    /**
     * Obtener total del detalle
     */
    public function getTotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }
}
