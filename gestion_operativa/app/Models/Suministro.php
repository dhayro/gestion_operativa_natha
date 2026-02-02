<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suministro extends Model
{
    use HasFactory;

    protected $table = 'suministros';

    protected $fillable = [
        'codigo',
        'nombre',
        'ruta',
        'direccion',
        'ubigeo_id',
        'referencia',
        'caja',
        'tarifa',
        'latitud',
        'longitud',
        'serie',
        'medidor_id',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con Medidor
    public function medidor()
    {
        return $this->belongsTo(Medidor::class, 'medidor_id');
    }

    // Relación muchos a muchos con Medidor a través de MedidorSuministro (historial)
    public function medidoresHistorial()
    {
        return $this->hasMany(MedidorSuministro::class, 'suministro_id');
    }

    // Relación con Ubigeo (Distrito)
    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id');
    }

    // Relación con Usuario de Creación
    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    // Relación con Usuario de Actualización
    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // Scope para obtener solo los activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // Accessor para mostrar el estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    // Accessor para mostrar el medidor
    public function getMedidorSerieAttribute()
    {
        return $this->medidor ? $this->medidor->serie : 'N/A';
    }

    // Accessor para mostrar el ubigeo
    public function getUbigeoNombreAttribute()
    {
        return $this->ubigeo ? $this->ubigeo->nombre : 'N/A';
    }
}
