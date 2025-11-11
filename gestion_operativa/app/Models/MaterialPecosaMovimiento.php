<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialPecosaMovimiento extends Model
{
    use HasFactory;

    protected $table = 'material_pecosa_movimientos';

    protected $fillable = [
        'pecosa_id',
        'material_id',
        'cantidad',
        'tipo_movimiento',
        'ficha_actividad_id',
        'material_ficha_actividades_id',
        'cuadrilla_id',
        'observaciones',
        'estado',
        'usuario_creacion_id'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ===== RELACIONES =====

    public function pecosa()
    {
        return $this->belongsTo(Pecosa::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function fichaActividad()
    {
        return $this->belongsTo(FichaActividad::class);
    }

    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class);
    }

    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    // ===== SCOPES =====

    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'entrada');
    }

    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'salida');
    }

    public function scopePorPecosa($query, $pecosaId)
    {
        return $query->where('pecosa_id', $pecosaId);
    }

    public function scopePorMaterial($query, $materialId)
    {
        return $query->where('material_id', $materialId);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopePorFicha($query, $fichaId)
    {
        return $query->where('ficha_actividad_id', $fichaId);
    }

    // ===== MÉTODOS AUXILIARES =====

    /**
     * Obtener saldo actual de un material en una pecosa
     * (Entradas - Salidas)
     */
    public static function getSaldoMaterial($pecosaId, $materialId)
    {
        $entradas = self::porPecosa($pecosaId)
            ->porMaterial($materialId)
            ->entradas()
            ->sum('cantidad');

        $salidas = self::porPecosa($pecosaId)
            ->porMaterial($materialId)
            ->salidas()
            ->sum('cantidad');

        return $entradas - $salidas;
    }

    /**
     * Obtener historial de movimientos para una pecosa
     */
    public static function getHistorialPecosa($pecosaId)
    {
        return self::porPecosa($pecosaId)
            ->with(['material', 'fichaActividad', 'usuarioCreacion'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener movimientos de salida para una ficha
     */
    public static function getMovimientosFicha($fichaId)
    {
        return self::porFicha($fichaId)
            ->salidas()
            ->with(['material', 'pecosa', 'usuarioCreacion'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Registrar salida de material de pecosa a ficha
     */
    public static function registrarSalida($pecosaId, $materialId, $cantidad, $fichaId, $usuarioId, $observaciones = null)
    {
        // Verificar que hay suficiente saldo
        $saldo = self::getSaldoMaterial($pecosaId, $materialId);
        
        if ($saldo < $cantidad) {
            throw new \Exception("Saldo insuficiente. Disponible: {$saldo}, Solicitado: {$cantidad}");
        }

        return self::create([
            'pecosa_id' => $pecosaId,
            'material_id' => $materialId,
            'cantidad' => $cantidad,
            'tipo_movimiento' => 'salida',
            'ficha_actividad_id' => $fichaId,
            'observaciones' => $observaciones,
            'estado' => true,
            'usuario_creacion_id' => $usuarioId
        ]);
    }

    /**
     * Registrar entrada de material a pecosa
     */
    public static function registrarEntrada($pecosaId, $materialId, $cantidad, $usuarioId, $observaciones = null)
    {
        return self::create([
            'pecosa_id' => $pecosaId,
            'material_id' => $materialId,
            'cantidad' => $cantidad,
            'tipo_movimiento' => 'entrada',
            'observaciones' => $observaciones,
            'estado' => true,
            'usuario_creacion_id' => $usuarioId
        ]);
    }
}
