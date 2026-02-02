<?php

namespace App\Repositories;

use App\Models\FichaActividad;
use App\Repositories\Contracts\FichaActividadRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FichaActividadRepository implements FichaActividadRepositoryContract
{
    protected $model;

    public function __construct(FichaActividad $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener todas las fichas de actividad paginadas
     */
    public function obtenerTodas($pagina = 15)
    {
        return $this->model
            ->with([
                'suministro',
                'tipoActividad',
                'servicioElectrico',
                'usuarioCreacion',
                'usuarioActualizacion'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($pagina);
    }

    /**
     * Obtener ficha por ID con todas sus relaciones
     */
    public function obtenerPorId($id)
    {
        $ficha = $this->model
            ->with([
                'suministro',
                'tipoActividad',
                'tipoPropiedad',
                'construccion',
                'servicioElectrico',
                'uso',
                'situacion',
                'usuarioCreacion',
                'usuarioActualizacion',
                'fotos',
                'empleados',
                'medidores',
                'precintos',
                'materiales'
            ])
            ->find($id);

        if (!$ficha) {
            throw new ModelNotFoundException("Ficha de Actividad con ID {$id} no encontrada");
        }

        return $ficha;
    }

    /**
     * Crear nueva ficha de actividad
     */
    public function crear($datos)
    {
        // Validar que suministro_id exista
        if (!isset($datos['suministro_id'])) {
            throw new \InvalidArgumentException('suministro_id es requerido');
        }

        $datos['usuario_creacion_id'] = auth()->id() ?? 1;
        $datos['fecha'] = $datos['fecha'] ?? now();

        return $this->model->create($datos);
    }

    /**
     * Actualizar ficha de actividad
     */
    public function actualizar($id, $datos)
    {
        $ficha = $this->obtenerPorId($id);

        $datos['usuario_actualizacion_id'] = auth()->id() ?? 1;

        $ficha->update($datos);

        return $ficha->refresh();
    }

    /**
     * Eliminar ficha de actividad
     */
    public function eliminar($id)
    {
        $ficha = $this->obtenerPorId($id);
        return $ficha->delete();
    }

    /**
     * Obtener fichas por suministro
     */
    public function obtenerPorSuministro($suministroId, $pagina = 15)
    {
        return $this->model
            ->where('suministro_id', $suministroId)
            ->with([
                'tipoActividad',
                'servicioElectrico',
                'usuarioCreacion'
            ])
            ->orderBy('fecha', 'desc')
            ->paginate($pagina);
    }

    /**
     * Obtener fichas por tipo de actividad
     */
    public function obtenerPorTipoActividad($tipoActividadId, $pagina = 15)
    {
        return $this->model
            ->where('tipo_actividad_id', $tipoActividadId)
            ->with([
                'suministro',
                'servicioElectrico',
                'usuarioCreacion'
            ])
            ->orderBy('fecha', 'desc')
            ->paginate($pagina);
    }

    /**
     * Obtener fichas entre fechas
     */
    public function obtenerEntreFechas($fechaInicio, $fechaFin, $pagina = 15)
    {
        return $this->model
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with([
                'suministro',
                'tipoActividad',
                'usuarioCreacion'
            ])
            ->orderBy('fecha', 'desc')
            ->paginate($pagina);
    }

    /**
     * Obtener fichas activas
     */
    public function obtenerActivas($pagina = 15)
    {
        return $this->model
            ->where('estado', true)
            ->with([
                'suministro',
                'tipoActividad',
                'servicioElectrico',
                'usuarioCreacion'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($pagina);
    }

    /**
     * Cambiar estado de una ficha
     */
    public function cambiarEstado($id, $estado)
    {
        $ficha = $this->obtenerPorId($id);
        $ficha->update([
            'estado' => $estado,
            'usuario_actualizacion_id' => auth()->id() ?? 1
        ]);

        return $ficha;
    }

    /**
     * Buscar fichas por término
     */
    public function buscar($termino, $pagina = 15)
    {
        return $this->model
            ->whereHas('suministro', function ($query) use ($termino) {
                $query->where('codigo', 'like', "%{$termino}%")
                      ->orWhere('nombre', 'like', "%{$termino}%");
            })
            ->orWhere('numero_piso', 'like', "%{$termino}%")
            ->orWhere('situacion_detalle', 'like', "%{$termino}%")
            ->orWhere('observacion', 'like', "%{$termino}%")
            ->with([
                'suministro',
                'tipoActividad',
                'usuarioCreacion'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($pagina);
    }

    /**
     * Obtener fichas con relaciones específicas
     */
    public function obtenerConRelaciones($id, $relaciones = [])
    {
        $relacionesDefault = [
            'suministro',
            'tipoActividad',
            'servicioElectrico',
            'usuarioCreacion'
        ];

        $relaciones = !empty($relaciones) ? $relaciones : $relacionesDefault;

        return $this->model
            ->with($relaciones)
            ->findOrFail($id);
    }

    /**
     * Contar total de fichas
     */
    public function contar()
    {
        return $this->model->count();
    }

    /**
     * Contar fichas activas
     */
    public function contarActivas()
    {
        return $this->model->where('estado', true)->count();
    }

    /**
     * Método auxiliar para obtener el modelo
     */
    public function getModel()
    {
        return $this->model;
    }
}
