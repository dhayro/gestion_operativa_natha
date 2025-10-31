<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeaDetalle extends Model
{
    use HasFactory;

    protected $table = 'nea_detalles';

    protected $fillable = [
        'nea_id',
        'material_id',
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

    /**
     * Relación inversa con NEA
     */
    public function nea()
    {
        return $this->belongsTo(NEA::class, 'nea_id');
    }

    /**
     * Relación con Material
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    /**
     * Usuario que creó el detalle
     */
    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    /**
     * Usuario que actualizó el detalle
     */
    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    /**
     * Relación inversa: Un NEA detalle puede estar en muchas PECOSAs
     */
    public function pecosaDetalles()
    {
        return $this->hasMany(PecosaDetalle::class, 'nea_detalle_id');
    }

    /**
     * Relación con movimientos de inventario
     */
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'nea_detalle_id');
    }

    // ===== ACCESORES Y MUTADORES =====

    /**
     * Calcular subtotal sin IGV
     */
    public function getSubtotalSinIgvAttribute()
    {
        if ($this->incluye_igv && $this->precio_unitario) {
            return ($this->cantidad * $this->precio_unitario) / 1.18;
        }
        return $this->cantidad * $this->precio_unitario;
    }

    /**
     * Calcular IGV del detalle
     */
    public function getIgvAttribute()
    {
        $subtotal = $this->cantidad * $this->precio_unitario;
        if ($this->incluye_igv) {
            return $subtotal - ($subtotal / 1.18);
        }
        return $subtotal * 0.18;
    }

    /**
     * Calcular total con IGV
     */
    public function getSubtotalConIgvAttribute()
    {
        if ($this->incluye_igv) {
            return $this->cantidad * $this->precio_unitario;
        }
        return ($this->cantidad * $this->precio_unitario) * 1.18;
    }

    /**
     * Obtener nombre del material
     */
    public function getMaterialNombreAttribute()
    {
        return $this->material ? $this->material->nombre : 'N/A';
    }

    /**
     * Obtener código del material
     */
    public function getMaterialCodigoAttribute()
    {
        return $this->material ? $this->material->codigo_material : 'N/A';
    }

    /**
     * Obtener unidad de medida del material
     */
    public function getMaterialUnidadAttribute()
    {
        if ($this->material && $this->material->unidadMedida) {
            return $this->material->unidadMedida->nombre;
        }
        return 'N/A';
    }

    // ===== SCOPES =====

    /**
     * Scope para filtrar detalles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para filtrar por NEA
     */
    public function scopePorNea($query, $neaId)
    {
        return $query->where('nea_id', $neaId);
    }

    /**
     * Scope para filtrar por Material
     */
    public function scopePorMaterial($query, $materialId)
    {
        return $query->where('material_id', $materialId);
    }
}
