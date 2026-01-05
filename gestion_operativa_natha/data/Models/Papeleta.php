<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Papeleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'correlativo',
        'asignacion_vehiculo_id',
        'fecha',
        'destino',
        'motivo',
        'km_salida',
        'km_llegada',
        'fecha_hora_salida',
        'fecha_hora_llegada',
        'estado',
        'fecha_anulacion',
        'motivo_anulacion',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_hora_salida' => 'datetime',
        'fecha_hora_llegada' => 'datetime',
        'fecha_anulacion' => 'datetime',
        'estado' => 'boolean',
        'km_salida' => 'decimal:3',
        'km_llegada' => 'decimal:3'
    ];

    // Generar correlativo automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($papeleta) {
            if (empty($papeleta->correlativo)) {
                $papeleta->correlativo = self::generarCorrelativo($papeleta->fecha);
            }
        });
    }

    /**
     * Generar correlativo por período (año-mes)
     */
    public static function generarCorrelativo($fecha)
    {
        $periodo = date('Y-m', strtotime($fecha));
        
        // Buscar el último correlativo del período
        $ultimaPapeleta = self::where('correlativo', 'LIKE', $periodo . '%')
            ->orderBy('correlativo', 'desc')
            ->first();

        if ($ultimaPapeleta) {
            // Extraer el número secuencial del correlativo
            $ultimoNumero = (int) substr($ultimaPapeleta->correlativo, -4);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return $periodo . '-' . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
    }

    // Relaciones
    public function asignacionVehiculo()
    {
        return $this->belongsTo(AsignacionVehiculo::class);
    }

    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // Accessor para obtener información del vehículo
    public function getVehiculoAttribute()
    {
        return $this->asignacionVehiculo->vehiculo ?? null;
    }

    // Accessor para obtener información del empleado
    public function getEmpleadoAttribute()
    {
        return $this->asignacionVehiculo->empleado ?? null;
    }
}
