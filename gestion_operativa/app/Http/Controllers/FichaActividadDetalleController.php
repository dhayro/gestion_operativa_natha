<?php

namespace App\Http\Controllers;

use App\Models\FichaActividad;
use App\Models\MedidorFichaActividad;
use App\Models\FotoFichaActividad;
use App\Models\MaterialFichaActividad;
use App\Models\PrecintoFichaActividad;
use App\Models\FichaActividadEmpleado;
use App\Models\Medidor;
use App\Models\Material;
use App\Models\CuadrillaEmpleado;
use App\Models\MedidorSuministro;
use App\Models\Suministro;
use App\Models\MaterialPecosaMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FichaActividadDetalleController extends Controller
{
    /**
     * MEDIDORES
     */

    public function medidoresStore(Request $request, $fichaId)
    {
        $ficha = FichaActividad::findOrFail($fichaId);

        $validated = $request->validate([
            'medidor_id' => 'required|exists:medidors,id',
            'tipo' => 'required|in:nuevo,retirado,existente',
            'digitos_enteros' => 'nullable|integer',
            'digitos_decimales' => 'nullable|integer',
            'lectura' => 'nullable|integer'
        ]);

        try {
            $validated['ficha_actividad_id'] = $fichaId;
            $validated['usuario_creacion_id'] = auth()->id() ?? 1;

            $medidor = MedidorFichaActividad::create($validated);

            // ====== NUEVO: Registrar en medidor_suministros y actualizar suministro ======
            // Obtener el suministro de la ficha
            $ficha = FichaActividad::findOrFail($fichaId);
            $suministro = Suministro::findOrFail($ficha->suministro_id);
            
            // Obtener el medidor anterior si existía
            $medidorAnterior = $suministro->medidor_id;
            $medidorAnteriorInfo = '';
            
            if ($medidorAnterior) {
                $med = Medidor::find($medidorAnterior);
                $medidorAnteriorInfo = $med ? "ID: {$medidorAnterior}, Serie: {$med->serie}, Modelo: {$med->modelo}" : "N/A";
            }
            
            // Actualizar el suministro según el tipo
            if ($validated['tipo'] === 'retirado') {
                // Si es retirado, poner medidor_id en null y desactivar el medidor
                $suministro->update(['medidor_id' => null]);
                Medidor::where('id', $validated['medidor_id'])->update(['estado' => 0]);
                $observacion = "Medidor retirado de suministro - Anterior: {$medidorAnteriorInfo}";
            } else {
                // Si es nuevo o existente, asignar el nuevo medidor
                $suministro->update(['medidor_id' => $validated['medidor_id']]);
                $medidorNuevo = Medidor::find($validated['medidor_id']);
                $medidorNuevoInfo = $medidorNuevo ? "Serie: {$medidorNuevo->serie}, Modelo: {$medidorNuevo->modelo}" : "N/A";
                $observacion = "Medidor asignado a suministro - Tipo: {$validated['tipo']} - Anterior: {$medidorAnteriorInfo} - Nuevo: {$medidorNuevoInfo}";
            }
            
            // Crear registro en medidor_suministros para historial
            MedidorSuministro::create([
                'suministro_id' => $suministro->id,
                'medidor_id' => $validated['medidor_id'],
                'ficha_actividad_id' => $fichaId,
                'fecha_cambio' => now(),
                'observaciones' => $observacion,
                'estado' => 1,
                'usuario_creacion_id' => auth()->id() ?? 1
            ]);
            // ====== FIN: Registrar en medidor_suministros y actualizar suministro ======

            return response()->json([
                'success' => true,
                'message' => 'Medidor agregado exitosamente.',
                'data' => $medidor->load('medidor')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function medidoresUpdate(Request $request, $fichaId, $medidorId)
    {
        $medidor = MedidorFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($medidorId);

        $validated = $request->validate([
            'medidor_id' => 'required|exists:medidors,id',
            'tipo' => 'required|in:nuevo,retirado,existente',
            'digitos_enteros' => 'nullable|integer',
            'digitos_decimales' => 'nullable|integer',
            'lectura' => 'nullable|integer'
        ]);

        try {
            $validated['usuario_actualizacion_id'] = auth()->id() ?? 1;
            $medidor->update($validated);

            // ====== NUEVO: Actualizar medidor_id del suministro si cambió ======
            $ficha = FichaActividad::findOrFail($fichaId);
            $suministro = Suministro::findOrFail($ficha->suministro_id);
            
            // Obtener datos del medidor anterior
            $medidorAnterior = Medidor::find($medidor->medidor_id);
            $medidorAnteriorInfo = $medidorAnterior 
                ? "Serie: {$medidorAnterior->serie}, Modelo: {$medidorAnterior->modelo}"
                : "N/A";
            
            // Actualizar el suministro según el tipo
            if ($validated['tipo'] === 'retirado') {
                // Si es retirado, poner medidor_id en null y desactivar el medidor
                $suministro->update(['medidor_id' => null]);
                Medidor::where('id', $validated['medidor_id'])->update(['estado' => 0]);
                $observacion = "Cambio a retirado en ficha - Anterior: {$medidorAnteriorInfo}";
            } else {
                // Si es nuevo o existente, asignar el nuevo medidor
                $suministro->update(['medidor_id' => $validated['medidor_id']]);
                $medidorNuevo = Medidor::find($validated['medidor_id']);
                $medidorNuevoInfo = $medidorNuevo 
                    ? "Serie: {$medidorNuevo->serie}, Modelo: {$medidorNuevo->modelo}"
                    : "N/A";
                $observacion = "Actualización en ficha - Tipo: {$validated['tipo']} - Anterior: {$medidorAnteriorInfo} - Nuevo: {$medidorNuevoInfo}";
            }
            
            // Crear nuevo registro histórico en medidor_suministros
            MedidorSuministro::create([
                'suministro_id' => $suministro->id,
                'medidor_id' => $validated['medidor_id'],
                'ficha_actividad_id' => $fichaId,
                'fecha_cambio' => now(),
                'observaciones' => $observacion,
                'estado' => 1,
                'usuario_creacion_id' => auth()->id() ?? 1
            ]);
            // ====== FIN: Actualizar medidor_id del suministro ======

            return response()->json([
                'success' => true,
                'message' => 'Medidor actualizado exitosamente.',
                'data' => $medidor->load('medidor')
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function medidoresDestroy($fichaId, $medidorId)
    {
        try {
            $medidor = MedidorFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($medidorId);
            
            // ====== NUEVO: Actualizar suministro y registrar en medidor_suministros ======
            $ficha = FichaActividad::findOrFail($fichaId);
            $suministro = Suministro::findOrFail($ficha->suministro_id);
            
            // Si el medidor eliminado coincide con el del suministro, poner en null
            if ($suministro->medidor_id == $medidor->medidor_id) {
                $suministro->update(['medidor_id' => null]);
            }
            
            // Obtener datos del medidor eliminado
            $medidorEliminado = Medidor::find($medidor->medidor_id);
            $medidorInfo = $medidorEliminado 
                ? "Serie: {$medidorEliminado->serie}, Modelo: {$medidorEliminado->modelo}"
                : "N/A";
            
            // Crear registro de eliminación en medidor_suministros
            MedidorSuministro::create([
                'suministro_id' => $suministro->id,
                'medidor_id' => $medidor->medidor_id,
                'ficha_actividad_id' => $fichaId,
                'fecha_cambio' => now(),
                'observaciones' => "Medidor eliminado de ficha de actividad - Medidor: {$medidorInfo}",
                'estado' => 0,
                'usuario_creacion_id' => auth()->id() ?? 1
            ]);
            // ====== FIN: Actualizar suministro y registrar eliminación ======
            
            $medidor->delete();

            return response()->json(['success' => true, 'message' => 'Medidor eliminado.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * MATERIALES
     */

    public function materialesStore(Request $request, $fichaId)
    {
        $ficha = FichaActividad::findOrFail($fichaId);

        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'cantidad' => 'required|numeric|min:0.001',
            'observacion' => 'nullable|string'
        ]);

        try {
            $validated['ficha_actividad_id'] = $fichaId;
            $validated['usuario_creacion_id'] = auth()->id() ?? 1;

            // Crear el MaterialFichaActividad
            $material = MaterialFichaActividad::create($validated);
            
            Log::info("=== MATERIAL FICHA ACTIVIDAD CREADO ===", [
                'material_ficha_actividad_id' => $material->id,
                'material_id' => $material->material_id,
                'cantidad' => $material->cantidad,
                'ficha_id' => $fichaId
            ]);

            // ====== NUEVO: Registrar salida en material_pecosa_movimientos ======
            // Cuando se agrega un material a una ficha, se está sacando de la PECOSA
            if ($ficha->pecosa_id && $material->material_id) {
                Log::info("✅ Condiciones cumplidas - Creando movimiento SALIDA", [
                    'pecosa_id' => $ficha->pecosa_id,
                    'material_ficha_actividades_id' => $material->id
                ]);
                
                $movimiento = MaterialPecosaMovimiento::create([
                    'pecosa_id' => $ficha->pecosa_id,
                    'material_id' => $material->material_id,
                    'ficha_actividad_id' => $fichaId,
                    'material_ficha_actividades_id' => $material->id,  // ← Traceabilidad
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $material->cantidad,
                    'observaciones' => 'Material asignado a ficha',
                    'estado' => true,
                    'usuario_creacion_id' => auth()->id() ?? 1
                ]);
                
                Log::info("✅ Movimiento SALIDA creado", [
                    'movimiento_id' => $movimiento->id,
                    'material_ficha_actividades_id' => $movimiento->material_ficha_actividades_id
                ]);
            } else {
                Log::warning("⚠️ Condiciones NO cumplidas", [
                    'pecosa_id_exists' => !empty($ficha->pecosa_id),
                    'material_id_exists' => !empty($material->material_id),
                    'pecosa_id' => $ficha->pecosa_id,
                    'material_id' => $material->material_id
                ]);
            }
            // ====== FIN: Registrar salida en material_pecosa_movimientos ======

            return response()->json([
                'success' => true,
                'message' => 'Material agregado exitosamente.',
                'data' => $material->load('material')
            ], 201);
        } catch (\Exception $e) {
            Log::error("❌ Error en materialesStore", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function materialesUpdate(Request $request, $fichaId, $materialId)
    {
        $material = MaterialFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($materialId);

        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'cantidad' => 'required|numeric|min:0.001',
            'observacion' => 'nullable|string'
        ]);

        try {
            $validated['usuario_actualizacion_id'] = auth()->id() ?? 1;
            $material->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Material actualizado exitosamente.',
                'data' => $material->load('material')
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function materialesDestroy($fichaId, $materialId)
    {
        try {
            Log::info("=== INICIANDO SOFT-DELETE DE MATERIAL ===", [
                'fichaId' => $fichaId,
                'materialId' => $materialId
            ]);
            
            $material = MaterialFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($materialId);
            $ficha = FichaActividad::findOrFail($fichaId);
            
            Log::info("Material y Ficha encontrados", [
                'material_id' => $material->material_id,
                'cantidad' => $material->cantidad,
                'pecosa_id' => $ficha->pecosa_id,
                'ficha_id' => $ficha->id
            ]);
            
            // ====== ACTUALIZADO: Soft-delete y registrar entrada en material_pecosa_movimientos ======
            // Cambio de: hard delete → soft delete (estado = false)
            // Cuando se elimina un material de una ficha, se está devolviendo a la PECOSA
            
            // 1. Realizar soft-delete del material (marcar como inactivo)
            $material->update(['estado' => false]);
            Log::info("✅ Material marcado como inactivo (soft-delete)");
            
            // 2. Registrar entrada en material_pecosa_movimientos si PECOSA existe
            if ($ficha->pecosa_id && $material->material_id) {
                Log::info("✅ Condiciones cumplidas - Creando movimiento de entrada (devolución)");
                
                $movimiento = MaterialPecosaMovimiento::create([
                    'pecosa_id' => $ficha->pecosa_id,
                    'material_id' => $material->material_id,
                    'ficha_actividad_id' => $fichaId,
                    'material_ficha_actividades_id' => $material->id,  // ← NUEVO: Traceabilidad
                    'tipo_movimiento' => 'entrada',
                    'cantidad' => $material->cantidad,
                    'observaciones' => 'Devolución de material - Eliminado de ficha',
                    'estado' => true,
                    'usuario_creacion_id' => auth()->id() ?? 1
                ]);
                
                Log::info("✅ Movimiento de entrada creado exitosamente", [
                    'movimiento_id' => $movimiento->id,
                    'pecosa_id' => $movimiento->pecosa_id,
                    'material_id' => $movimiento->material_id,
                    'material_ficha_actividades_id' => $movimiento->material_ficha_actividades_id,
                    'cantidad' => $movimiento->cantidad
                ]);
            } else {
                Log::warning("⚠️ No se pudo registrar entrada en PECOSA", [
                    'pecosa_id_is_null' => is_null($ficha->pecosa_id),
                    'material_id_is_null' => is_null($material->material_id),
                    'pecosa_id' => $ficha->pecosa_id,
                    'material_id' => $material->material_id
                ]);
            }
            // ====== FIN: Soft-delete y movimiento de entrada ======

            return response()->json(['success' => true, 'message' => 'Material marcado como inactivo y devuelto a PECOSA.'], 200);
        } catch (\Exception $e) {
            Log::error("❌ Error en materialesDestroy", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * FOTOS
     */

    public function fotosStore(Request $request, $fichaId)
    {
        $ficha = FichaActividad::findOrFail($fichaId);

        $validated = $request->validate([
            'url' => 'required|url',
            'descripcion' => 'nullable|string|max:500'
        ]);

        try {
            $validated['ficha_actividad_id'] = $fichaId;
            $validated['usuario_creacion_id'] = auth()->id() ?? 1;
            $validated['fecha'] = now();

            $foto = FotoFichaActividad::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Foto agregada exitosamente.',
                'data' => $foto
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fotosUpdate(Request $request, $fichaId, $fotoId)
    {
        $foto = FotoFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($fotoId);

        $validated = $request->validate([
            'url' => 'required|url',
            'descripcion' => 'nullable|string|max:500'
        ]);

        try {
            $validated['usuario_actualizacion_id'] = auth()->id() ?? 1;
            $foto->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Foto actualizada exitosamente.',
                'data' => $foto
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fotosDestroy($fichaId, $fotoId)
    {
        try {
            $foto = FotoFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($fotoId);
            $foto->delete();

            return response()->json(['success' => true, 'message' => 'Foto eliminada.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PRECINTOS
     */

    public function precintosStore(Request $request, $fichaId)
    {
        $ficha = FichaActividad::findOrFail($fichaId);

        $validated = $request->validate([
            'medidor_ficha_actividad_id' => 'required|exists:medidor_ficha_actividades,id',
            'material_id' => 'required|exists:materials,id',
            'tipo' => 'required|in:tapa,caja,bornera',
            'numero_precinto' => 'required|string|max:50|unique:precinto_ficha_actividades,numero_precinto'
        ]);

        try {
            $validated['ficha_actividad_id'] = $fichaId;
            $validated['usuario_creacion_id'] = auth()->id() ?? 1;

            $precinto = PrecintoFichaActividad::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Precinto agregado exitosamente.',
                'data' => $precinto->load('medidorFichaActividad', 'material')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function precintosUpdate(Request $request, $fichaId, $precintoId)
    {
        $precinto = PrecintoFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($precintoId);

        $validated = $request->validate([
            'medidor_ficha_actividad_id' => 'required|exists:medidor_ficha_actividades,id',
            'material_id' => 'required|exists:materials,id',
            'tipo' => 'required|in:tapa,caja,bornera',
            'numero_precinto' => 'required|string|max:50|unique:precinto_ficha_actividades,numero_precinto,' . $precintoId
        ]);

        try {
            $validated['usuario_actualizacion_id'] = auth()->id() ?? 1;
            $precinto->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Precinto actualizado exitosamente.',
                'data' => $precinto->load('medidorFichaActividad', 'material')
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function precintosDestroy($fichaId, $precintoId)
    {
        try {
            $precinto = PrecintoFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($precintoId);
            $precinto->delete();

            return response()->json(['success' => true, 'message' => 'Precinto eliminado.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * EMPLEADOS
     */

    public function empleadosStore(Request $request, $fichaId)
    {
        $ficha = FichaActividad::findOrFail($fichaId);

        $validated = $request->validate([
            'cuadrilla_empleado_id' => 'required|exists:cuadrillas_empleados,id'
        ]);

        try {
            $validated['ficha_actividad_id'] = $fichaId;
            $validated['usuario_creacion_id'] = auth()->id() ?? 1;

            $empleado = FichaActividadEmpleado::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Empleado agregado exitosamente.',
                'data' => $empleado->load('cuadrillaEmpleado.empleado')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function empleadosDestroy($fichaId, $empleadoId)
    {
        try {
            $empleado = FichaActividadEmpleado::where('ficha_actividad_id', $fichaId)->findOrFail($empleadoId);
            $empleado->delete();

            return response()->json(['success' => true, 'message' => 'Empleado eliminado.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * GET ENDPOINTS PARA CARGAR DATOS EN FORMULARIOS
     */

    public function getMedidores($fichaId)
    {
        try {
            $medidores = MedidorFichaActividad::with('medidor')
                ->where('ficha_actividad_id', $fichaId)
                ->get();

            return response()->json(['success' => true, 'data' => $medidores], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getMateriales($fichaId)
    {
        try {
            $materiales = MaterialFichaActividad::with('material')
                ->where('ficha_actividad_id', $fichaId)
                ->where('estado', true)  // ← NUEVO: Solo mostrar materiales activos
                ->get();

            return response()->json(['success' => true, 'data' => $materiales], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getPrecintos($fichaId)
    {
        try {
            $precintos = PrecintoFichaActividad::with('medidorFichaActividad.medidor', 'material')
                ->where('ficha_actividad_id', $fichaId)
                ->get();

            return response()->json(['success' => true, 'data' => $precintos], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getFotos($fichaId)
    {
        try {
            $fotos = FotoFichaActividad::where('ficha_actividad_id', $fichaId)->get();

            return response()->json(['success' => true, 'data' => $fotos], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getEmpleados($fichaId)
    {
        try {
            $empleados = FichaActividadEmpleado::with('cuadrillaEmpleado.empleado', 'cuadrillaEmpleado.cuadrilla')
                ->where('ficha_actividad_id', $fichaId)
                ->get();

            return response()->json(['success' => true, 'data' => $empleados], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
