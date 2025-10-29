<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NEA extends Model
{
    use HasFactory;

    protected $table = 'neas';

    protected $fillable = [
        'proveedor_id',
        'fecha',
        'nro_documento',
        'tipo_comprobante_id',
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
}
