<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NEA extends Model
{
    use HasFactory;

    protected $table = 'neas';

    protected $fillable = [
        'proveedor_id',
        'fecha',
        'nro_documento',
        'tipo_comprobante_id',
        'numero_comprobante',
        'observaciones',
        'estado',
        'anulada',
        'motivo_anulacion',
        'usuario_anulacion_id',
        'fecha_anulacion',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'boolean',
        'anulada' => 'boolean',
        'fecha_anulacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'total_sin_igv',
        'igv_total',
        'total_con_igv'
    ];

    /**
     * Generar número de NEA automáticamente
     * Formato: NEA-YYYY-00001
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($nea) {
            if (empty($nea->nro_documento)) {
                $nea->nro_documento = self::generarNumeroNea($nea->fecha);
            }
        });
    }

    /**
     * Generar número de NEA por año
     */
    public static function generarNumeroNea($fecha)
    {
        $anio = date('Y', strtotime($fecha));
        
        // Buscar el último número de NEA del año
        $ultimaNea = self::where('nro_documento', 'LIKE', "NEA-{$anio}-%")
            ->orderByRaw('CAST(SUBSTRING(nro_documento, -5) AS UNSIGNED) DESC')
            ->first();

        $numeroSecuencial = 1;
        if ($ultimaNea) {
            preg_match('/NEA-\d{4}-(\d+)/', $ultimaNea->nro_documento, $matches);
            if (isset($matches[1])) {
                $numeroSecuencial = intval($matches[1]) + 1;
            }
        }

        return "NEA-{$anio}-" . str_pad($numeroSecuencial, 5, '0', STR_PAD_LEFT);
    }

    // ===== RELACIONES =====

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function tipoComprobante()
    {
        return $this->belongsTo(TipoComprobante::class);
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
     * Relación con usuario que anuló la NEA
     */
    public function usuarioAnulacion()
    {
        return $this->belongsTo(User::class, 'usuario_anulacion_id');
    }

    /**
     * Relación uno a muchos con detalles de NEA
     */
    public function detalles()
    {
        return $this->hasMany(NeaDetalle::class, 'nea_id');
    }

    /**
     * Obtener todos los materiales asociados a esta NEA
     */
    public function materiales()
    {
        return $this->hasManyThrough(
            Material::class,
            NeaDetalle::class,
            'nea_id',
            'id',
            'id',
            'material_id'
        );
    }

    // ===== SCOPES =====

    /**
     * Scope para filtrar NEAs activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para filtrar NEAs inactivas
     */
    public function scopeInactivas($query)
    {
        return $query->where('estado', false);
    }

    /**
     * Scope para filtrar NEAs anuladas
     */
    public function scopeAnuladas($query)
    {
        return $query->where('anulada', true);
    }

    /**
     * Scope para filtrar NEAs no anuladas
     */
    public function scopeNoAnuladas($query)
    {
        return $query->where('anulada', false);
    }

    /**
     * Scope para filtrar por proveedor
     */
    public function scopePorProveedor($query, $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
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

    /**
     * Accesores para obtener información formateada
     */
    public function getProveedorNombreAttribute()
    {
        return $this->proveedor ? $this->proveedor->razon_social : 'N/A';
    }

    public function getTipoComprobanteNombreAttribute()
    {
        return $this->tipoComprobante ? $this->tipoComprobante->nombre : 'N/A';
    }

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
            $subTotal = $detalle->cantidad * $detalle->precio_unitario;
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
            // Solo calcular IGV si el detalle está marcado como "incluye_igv"
            if ($detalle->incluye_igv) {
                $subTotal = $detalle->cantidad * $detalle->precio_unitario;
                return $subTotal - ($subTotal / 1.18);
            }
            return 0; // Si no incluye IGV, no hay IGV
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
            $subTotal = $detalle->cantidad * $detalle->precio_unitario;
            // El precio ya es el final (con o sin IGV según lo que el usuario haya especificado)
            return $subTotal;
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
     * Verificar si la NEA tiene detalles
     */
    public function tieneDetalles()
    {
        return $this->detalles()->exists();
    }

    /**
     * Obtener información resumida para DataTable
     */
    public function getInfoResumidaAttribute()
    {
        return [
            'id' => $this->id,
            'nro_documento' => $this->nro_documento,
            'fecha' => $this->fecha_formatted,
            'proveedor' => $this->proveedor_nombre,
            'tipo_comprobante' => $this->tipo_comprobante_nombre,
            'cantidad_detalles' => $this->cantidad_detalles,
            'total_con_igv' => number_format($this->total_con_igv, 2, '.', ','),
            'estado' => $this->estado ? 'Activo' : 'Inactivo'
        ];
    }
}
