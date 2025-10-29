<?php

namespace App\Repositories\Contracts;

interface FichaActividadRepositoryContract
{
    /**
     * Obtener todas las fichas de actividad
     */
    public function obtenerTodas($pagina = 15);

    /**
     * Obtener ficha por ID
     */
    public function obtenerPorId($id);

    /**
     * Crear nueva ficha de actividad
     */
    public function crear($datos);

    /**
     * Actualizar ficha de actividad
     */
    public function actualizar($id, $datos);

    /**
     * Eliminar ficha de actividad
     */
    public function eliminar($id);

    /**
     * Obtener fichas por suministro
     */
    public function obtenerPorSuministro($suministroId, $pagina = 15);

    /**
     * Obtener fichas por tipo de actividad
     */
    public function obtenerPorTipoActividad($tipoActividadId, $pagina = 15);

    /**
     * Obtener fichas entre fechas
     */
    public function obtenerEntreFechas($fechaInicio, $fechaFin, $pagina = 15);

    /**
     * Obtener fichas activas
     */
    public function obtenerActivas($pagina = 15);

    /**
     * Cambiar estado de una ficha
     */
    public function cambiarEstado($id, $estado);

    /**
     * Buscar fichas
     */
    public function buscar($termino, $pagina = 15);

    /**
     * Obtener fichas con sus relaciones
     */
    public function obtenerConRelaciones($id, $relaciones = []);

    /**
     * Contar total de fichas
     */
    public function contar();

    /**
     * Contar fichas activas
     */
    public function contarActivas();
}
