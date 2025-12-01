<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoFichaActividad extends Model
{
    use HasFactory;

    protected $table = 'foto_ficha_actividades';

    protected $fillable = [
        'ficha_actividad_id',
        'url',
        'descripcion',
        'fecha',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id',
        'archivo_nombre',
        'archivo_ruta',
        'archivo_mime',
        'archivo_tamaño',
        'tipo_origen'
    ];

    protected $appends = ['foto_url', 'tamaño_formateado', 'fecha_formateada'];

    protected $casts = [
        'estado' => 'boolean',
        'fecha' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ==================== RELACIONES ====================

    public function fichaActividad()
    {
        return $this->belongsTo(FichaActividad::class, 'ficha_actividad_id');
    }

    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    public function usuarioActualizacion()
    {
        return $this->belongsTo(User::class, 'usuario_actualizacion_id');
    }

    // ==================== SCOPES ====================

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    public function scopePorFicha($query, $fichaId)
    {
        return $query->where('ficha_actividad_id', $fichaId);
    }

    // ==================== ACCESSORS ====================

    public function getFechaFormateadaAttribute()
    {
        return $this->fecha ? $this->fecha->format('d/m/Y H:i') : '-';
    }

    public function getFotoUrlAttribute()
    {
        // Si es URL, retornar directamente
        if ($this->tipo_origen === 'url' && $this->url) {
            return $this->url;
        }
        
        // Si es archivo, construir la ruta pública
        if ($this->tipo_origen === 'archivo' && $this->archivo_ruta) {
            return asset('storage/' . $this->archivo_ruta);
        }
        
        // Si es cámara, construir la ruta pública (igual que archivo)
        if ($this->tipo_origen === 'camara' && $this->archivo_ruta) {
            return asset('storage/' . $this->archivo_ruta);
        }
        
        // Fallback a placeholder
        return asset('images/placeholder.png');
    }

    public function getTamañoFormateadoAttribute()
    {
        if (!$this->archivo_tamaño) {
            return '-';
        }
        
        $bytes = $this->archivo_tamaño;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
