<?php

namespace App\Http\Controllers;

use App\Models\Papeleta;
use App\Models\DotacionCombustible;
use App\Models\TipoCombustible;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DotacionCombustibleController extends Controller
{
    /**
     * Obtener todas las dotaciones de una papeleta (AJAX)
     */
    public function index($papeletaId)
    {
        $papeleta = Papeleta::findOrFail($papeletaId);
        
        if (request()->ajax()) {
            $dotaciones = DotacionCombustible::where('papeleta_id', $papeletaId)
                ->with('tipoCombustible', 'usuarioCreacion')
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($dotaciones)
                ->addIndexColumn()
                ->addColumn('fecha_carga_formatted', function ($row) {
                    return $row->fecha_carga ? $row->fecha_carga->format('d/m/Y') : $row->created_at->format('d/m/Y');
                })
                ->addColumn('tipo_nombre', function ($row) {
                    return $row->tipoCombustible->nombre ?? 'N/A';
                })
                ->addColumn('cantidad_gl', function ($row) {
                    return number_format($row->cantidad_gl, 2, '.', ',');
                })
                ->addColumn('precio_unitario', function ($row) {
                    return $row->precio_unitario ? 'S/. ' . number_format($row->precio_unitario, 2, '.', ',') : 'S/. 0.00';
                })
                ->addColumn('costo_total', function ($row) {
                    $costo = ($row->cantidad_gl ?? 0) * ($row->precio_unitario ?? 0);
                    return 'S/. ' . number_format($costo, 2, '.', ',');
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('action', function ($row) use ($papeletaId) {
                    $btns = '';
                    $btns .= '<button class="btn btn-sm btn-info" onclick="previsualizarVale(' . $row->id . ')" title="Previsualizar Vale">
                                <i class="fas fa-file-pdf"></i>
                              </button> ';
                    $btns .= '<button class="btn btn-sm btn-primary" onclick="editarDotacion(' . $row->id . ', ' . $papeletaId . ')" title="Editar">
                                <i class="fas fa-edit"></i>
                              </button> ';
                    $btns .= '<button class="btn btn-sm btn-danger" onclick="eliminarDotacion(' . $row->id . ', ' . $papeletaId . ')" title="Eliminar">
                                <i class="fas fa-trash"></i>
                              </button>';
                    return $btns;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json($dotaciones);
    }

    /**
     * Almacenar una nueva dotación de combustible
     */
    public function store(Request $request, $papeletaId)
    {
        $papeleta = Papeleta::findOrFail($papeletaId);

        // Validación
        $validated = $request->validate([
            'cantidad_gl' => 'required|numeric|gt:0',
            'precio_unitario' => 'nullable|numeric|gte:0',
            'numero_vale' => 'required|string|max:200',
            'tipo_combustible_id' => 'required|exists:tipo_combustibles,id',
            'fecha_carga' => 'nullable|date'
        ], [
            'cantidad_gl.required' => 'La cantidad en galones es requerida',
            'cantidad_gl.gt' => 'La cantidad debe ser mayor a 0',
            'numero_vale.required' => 'El número de vale es requerido',
            'tipo_combustible_id.required' => 'El tipo de combustible es requerido',
            'tipo_combustible_id.exists' => 'El tipo de combustible no existe'
        ]);

        // Agregar datos adicionales
        $validated['papeleta_id'] = $papeletaId;
        $validated['usuario_creacion_id'] = Auth::id();
        $validated['estado'] = true;

        // Crear la dotación
        $dotacion = DotacionCombustible::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dotación de combustible agregada correctamente',
            'dotacion' => $dotacion->load('tipoCombustible')
        ], 201);
    }

    /**
     * Obtener una dotación específica
     */
    public function show($papeletaId, $dotacionId)
    {
        $dotacion = DotacionCombustible::where('papeleta_id', $papeletaId)
            ->where('id', $dotacionId)
            ->with('tipoCombustible', 'usuarioCreacion')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'dotacion' => $dotacion
        ]);
    }

    /**
     * Actualizar una dotación de combustible
     */
    public function update(Request $request, $papeletaId, $dotacionId)
    {
        $dotacion = DotacionCombustible::where('papeleta_id', $papeletaId)
            ->where('id', $dotacionId)
            ->firstOrFail();

        // Validación
        $validated = $request->validate([
            'cantidad_gl' => 'required|numeric|gt:0',
            'precio_unitario' => 'nullable|numeric|gte:0',
            'numero_vale' => 'required|string|max:200',
            'tipo_combustible_id' => 'required|exists:tipo_combustibles,id',
            'fecha_carga' => 'nullable|date'
        ], [
            'cantidad_gl.required' => 'La cantidad en galones es requerida',
            'cantidad_gl.gt' => 'La cantidad debe ser mayor a 0',
            'numero_vale.required' => 'El número de vale es requerido',
            'tipo_combustible_id.required' => 'El tipo de combustible es requerido',
        ]);

        // Actualizar usuario de actualización
        $validated['usuario_actualizacion_id'] = Auth::id();

        $dotacion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dotación actualizada correctamente',
            'dotacion' => $dotacion->load('tipoCombustible')
        ]);
    }

    /**
     * Eliminar una dotación
     */
    public function destroy($papeletaId, $dotacionId)
    {
        $dotacion = DotacionCombustible::where('papeleta_id', $papeletaId)
            ->where('id', $dotacionId)
            ->firstOrFail();

        $dotacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dotación eliminada correctamente'
        ]);
    }

    /**
     * Obtener resumen de dotaciones por papeleta
     */
    public function resumen($papeletaId)
    {
        $papeleta = Papeleta::findOrFail($papeletaId);

        $dotaciones = DotacionCombustible::where('papeleta_id', $papeletaId)
            ->activas()
            ->with('tipoCombustible')
            ->get();

        $totalGalones = $dotaciones->sum('cantidad_gl');
        $totalCosto = $dotaciones->sum(function ($dot) {
            return $dot->costo_total;
        });

        return response()->json([
            'success' => true,
            'total_galones' => $totalGalones,
            'total_costo' => $totalCosto,
            'cantidad_cargas' => $dotaciones->count(),
            'dotaciones' => $dotaciones
        ]);
    }

    /**
     * Obtener tipos de combustible disponibles (para Select2)
     */
    public function tiposCombustible()
    {
        $tipos = TipoCombustible::where('estado', true)
            ->select('id', 'nombre')
            ->get();

        return response()->json([
            'success' => true,
            'tipos' => $tipos
        ]);
    }
}
