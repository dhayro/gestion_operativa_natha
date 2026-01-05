<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedors';

    protected $fillable = [
        'razon_social',
        'ruc',
        'contacto',
        'email',
        'telefono',
        'direccion',
        'ubigeo_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // Relación con Ubigeo
    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class);
    }
}
