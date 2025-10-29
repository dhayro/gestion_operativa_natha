<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DotacionCombustible extends Model
{
    use HasFactory;

    protected $table = 'dotacion_combustibles';

    protected $fillable = [
        'papeleta_id',
        'cantidad_gl',
        'precio_unitario',
        'fecha_carga',
        'numero_vale',
        'tipo_combustible_id',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'cantidad_gl' => 'decimal:3',
        'precio_unitario' => 'decimal:3',
        'fecha_carga' => 'date',
        'estado' => 'boolean'
    ];

    /**
     * Relación: Pertenece a una Papeleta
     */
    public function papeleta()
    {
        return $this->belongsTo(Papeleta::class, 'papeleta_id');
    }

    /**
     * Relación: Pertenece a un Tipo de Combustible
     */
    public function tipoCombustible()
    {
        return $this->belongsTo(TipoCombustible::class, 'tipo_combustible_id');
    }

    /**
     * Relación: Usuario que creó el registro
     */
    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    /**
     * Relación: Usuario que actualizó el registro
     */
    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    /**
     * Scope: Obtener dotaciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope: Obtener dotaciones inactivas
     */
    public function scopeInactivas($query)
    {
        return $query->where('estado', false);
    }

    /**
     * Accessor: Obtener costo total (cantidad * precio_unitario)
     */
    public function getCostoTotalAttribute()
    {
        return ($this->cantidad_gl ?? 0) * ($this->precio_unitario ?? 0);
    }

    /**
     * Mutator: Establecer valores por defecto
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dotacion) {
            // Asignar usuario que crea el registro
            if (!$dotacion->usuario_creacion_id) {
                $dotacion->usuario_creacion_id = auth()->id();
            }
            
            // Estado por defecto: activo
            if (!isset($dotacion->estado)) {
                $dotacion->estado = true;
            }

            // Fecha de carga por defecto: hoy
            if (!$dotacion->fecha_carga) {
                $dotacion->fecha_carga = now();
            }
        });

        static::updating(function ($dotacion) {
            // Actualizar usuario que modifica
            $dotacion->usuario_actualizacion_id = auth()->id();
        });
    }
}
