<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pecosa extends Model
{
    use HasFactory;

    protected $table = 'pecosas';

    protected $fillable = [
        'cuadrilla_empleado_id',
        'fecha',
        'nro_documento',
        'observaciones',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'total_sin_igv',
        'igv_total',
        'total_con_igv'
    ];

    /**
     * Generar número de PECOSA automáticamente
     * Formato: PECOSA-YYYY-00001
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pecosa) {
            if (empty($pecosa->nro_documento)) {
                $pecosa->nro_documento = self::generarNumeroPecosa($pecosa->fecha);
            }
        });
    }

    /**
     * Generar número de PECOSA por año
     */
    public static function generarNumeroPecosa($fecha)
    {
        $anio = date('Y', strtotime($fecha));
        
        // Buscar el último número de PECOSA del año
        $ultimaPecosa = self::where('nro_documento', 'LIKE', "PECOSA-{$anio}-%")
            ->orderByRaw('CAST(SUBSTRING(nro_documento, -5) AS UNSIGNED) DESC')
            ->first();

        $numeroSecuencial = 1;
        if ($ultimaPecosa) {
            preg_match('/PECOSA-\d{4}-(\d+)/', $ultimaPecosa->nro_documento, $matches);
            if (isset($matches[1])) {
                $numeroSecuencial = intval($matches[1]) + 1;
            }
        }

        return "PECOSA-{$anio}-" . str_pad($numeroSecuencial, 5, '0', STR_PAD_LEFT);
    }

    // ===== RELACIONES =====

    public function cuadrillaEmpleado()
    {
        return $this->belongsTo(CuadrillaEmpleado::class);
    }

    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    /**
     * Relación con usuario que anuló la PECOSA
     */
    public function usuarioAnulacion()
    {
        return $this->belongsTo(User::class, 'usuario_anulacion_id');
    }

    /**
     * Relación uno a muchos con detalles de PECOSA
     */
    public function detalles()
    {
        return $this->hasMany(PecosaDetalle::class, 'pecosa_id');
    }

    /**
     * Obtener todos los materiales asociados a esta PECOSA
     */
    public function materiales()
    {
        return $this->hasManyThrough(
            Material::class,
            PecosaDetalle::class,
            'pecosa_id',
            'id',
            'id',
            'nea_detalle_id'
        );
    }

    /**
     * Relación con movimientos de materiales (historial)
     */
    public function materialMovimientos()
    {
        return $this->hasMany(MaterialPecosaMovimiento::class, 'pecosa_id');
    }

    // ===== SCOPES =====

    /**
     * Scope para filtrar PECOSAs activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para filtrar PECOSAs inactivas
     */
    public function scopeInactivas($query)
    {
        return $query->where('estado', false);
    }

    /**
     * Scope para filtrar PECOSAs anuladas
     */
    public function scopeAnuladas($query)
    {
        return $query->where('anulada', true);
    }

    /**
     * Scope para filtrar PECOSAs no anuladas
     */
    public function scopeNoAnuladas($query)
    {
        return $query->where('anulada', false);
    }

    /**
     * Scope para filtrar por cuadrilla empleado
     */
    public function scopePorCuadrillaEmpleado($query, $cuadrillaEmpleadoId)
    {
        return $query->where('cuadrilla_empleado_id', $cuadrillaEmpleadoId);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para filtrar por año
     */
    public function scopePorAnio($query, $anio)
    {
        return $query->whereYear('fecha', $anio);
    }

    // ===== ACCESORES Y MUTADORES =====

    public function getFechaFormattedAttribute()
    {
        return $this->fecha ? $this->fecha->format('d/m/Y') : '';
    }

    /**
     * Calcular total sin IGV de todos los detalles
     */
    public function getTotalSinIgvAttribute()
    {
        if (!$this->detalles || $this->detalles->isEmpty()) {
            return 0;
        }
        
        return $this->detalles->sum(function ($detalle) {
            $subTotal = $detalle->cantidad * ($detalle->precio_unitario ?? 0);
            return $detalle->incluye_igv ? $subTotal / 1.18 : $subTotal;
        });
    }

    /**
     * Calcular IGV total
     */
    public function getIgvTotalAttribute()
    {
        if (!$this->detalles || $this->detalles->isEmpty()) {
            return 0;
        }
        
        return $this->detalles->sum(function ($detalle) {
            if ($detalle->incluye_igv) {
                $subTotal = $detalle->cantidad * ($detalle->precio_unitario ?? 0);
                return $subTotal - ($subTotal / 1.18);
            }
            return 0;
        });
    }

    /**
     * Calcular total con IGV
     */
    public function getTotalConIgvAttribute()
    {
        if (!$this->detalles || $this->detalles->isEmpty()) {
            return 0;
        }
        
        return $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad * ($detalle->precio_unitario ?? 0);
        });
    }

    /**
     * Cantidad de detalles/ítems
     */
    public function getCantidadDetallesAttribute()
    {
        return $this->detalles()->count();
    }

    // ===== MÉTODOS AUXILIARES =====

    /**
     * Verificar si la PECOSA tiene detalles
     */
    public function tieneDetalles()
    {
        return $this->detalles()->exists();
    }
}
