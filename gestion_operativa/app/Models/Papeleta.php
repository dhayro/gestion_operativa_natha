<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Papeleta extends Model
{
    use HasFactory;

    protected $table = 'papeletas';

    protected $fillable = [
        'correlativo',
        'asignacion_vehiculo_id',
        'chofer_id',
        'miembros_cuadrilla',
        'personal_adicional',
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
        'observaciones',
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
        'km_llegada' => 'decimal:3',
        'miembros_cuadrilla' => 'array'
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
     * Generar correlativo por período (año con reinicio)
     */
    public static function generarCorrelativo($fecha)
    {
        $anio = date('Y', strtotime($fecha));
        
        // Buscar el último correlativo del año con formato YYYY-XXXX
        $ultimaPapeleta = self::where('correlativo', 'LIKE', $anio . '-%')
            ->where('correlativo', 'REGEXP', '^[0-9]{4}-[0-9]{4}$')
            ->whereNotNull('correlativo')
            ->where('correlativo', '!=', '')
            ->orderByRaw('CAST(SUBSTRING(correlativo, 6) AS UNSIGNED) DESC')
            ->first();

        if ($ultimaPapeleta) {
            // Extraer el número secuencial del correlativo (últimos 4 dígitos)
            $ultimoNumero = (int) substr($ultimaPapeleta->correlativo, -4);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return $anio . '-' . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
    }

    // Relación con asignación de vehículo
    public function asignacionVehiculo()
    {
        return $this->belongsTo(AsignacionVehiculo::class, 'asignacion_vehiculo_id');
    }

    // Relación con vehículo a través de asignación
    public function vehiculo()
    {
        return $this->hasOneThrough(
            Vehiculo::class,
            AsignacionVehiculo::class,
            'id',
            'id',
            'asignacion_vehiculo_id',
            'vehiculo_id'
        );
    }

    // Relación con cuadrilla a través de asignación
    public function cuadrilla()
    {
        return $this->hasOneThrough(
            Cuadrilla::class,
            AsignacionVehiculo::class,
            'id',
            'id',
            'asignacion_vehiculo_id',
            'cuadrilla_id'
        );
    }

    // Usuario que creó la papeleta
    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    // Usuario que actualizó la papeleta
    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // Relación con el chofer específico de esta papeleta
    public function chofer()
    {
        return $this->belongsTo(Empleado::class, 'chofer_id');
    }

    // Método para obtener los miembros de cuadrilla seleccionados
    public function miembrosCuadrillaEmpleados()
    {
        if (empty($this->miembros_cuadrilla)) {
            return collect();
        }
        
        return Empleado::whereIn('id', $this->miembros_cuadrilla)->get();
    }

    // Scope para papeletas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    // Scope para papeletas anuladas
    public function scopeAnuladas($query)
    {
        return $query->where('estado', false);
    }

    // Scope para filtrar por usuario a través de empleado y cuadrilla
    public function scopeParaUsuario($query, $userId)
    {
        return $query->whereHas('asignacionVehiculo.cuadrilla.cuadrillaEmpleados', function ($q) use ($userId) {
            $q->whereHas('empleado.usuario', function ($emp) use ($userId) {
                $emp->where('id', $userId);
            })->where('estado', true);
        });
    }

    // Scope para filtrar por fecha
    public function scopeFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    // Scope para filtrar por rango de fechas
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    // Accessor para calcular kilómetros recorridos
    public function getKmRecorridosAttribute()
    {
        if ($this->km_llegada && $this->km_salida) {
            return $this->km_llegada - $this->km_salida;
        }
        return null;
    }

    // Accessor para verificar si está en curso
    public function getEnCursoAttribute()
    {
        return $this->fecha_hora_salida && !$this->fecha_hora_llegada && $this->estado;
    }

    // Accessor para verificar si está completada
    public function getCompletadaAttribute()
    {
        return $this->fecha_hora_salida && $this->fecha_hora_llegada && $this->estado;
    }

    // Mutator para fecha de anulación
    public function setFechaAnulacionAttribute($value)
    {
        if ($value && !$this->estado) {
            $this->attributes['fecha_anulacion'] = $value;
        }
    }
}
