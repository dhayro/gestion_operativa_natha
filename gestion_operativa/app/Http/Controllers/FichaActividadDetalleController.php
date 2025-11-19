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

        // Validar que sea URL o archivo, pero no ambos (30MB = 30720 KB)
        $validated = $request->validate([
            'tipo_origen' => 'required|in:url,archivo',
            'url' => 'required_if:tipo_origen,url|nullable|url',
            'archivo' => 'required_if:tipo_origen,archivo|nullable|image|mimes:jpeg,png,gif,webp|max:30720',
            'descripcion' => 'nullable|string|max:500'
        ]);

        try {
            $fotoData = [
                'ficha_actividad_id' => $fichaId,
                'usuario_creacion_id' => auth()->id() ?? 1,
                'fecha' => now(),
                'descripcion' => $validated['descripcion'] ?? null,
                'tipo_origen' => $validated['tipo_origen']
            ];

            // Si es URL
            if ($validated['tipo_origen'] === 'url') {
                $fotoData['url'] = $validated['url'];
                
                Log::info('Foto URL agregada', [
                    'ficha_id' => $fichaId,
                    'url' => $validated['url']
                ]);
            }
            // Si es archivo
            else if ($validated['tipo_origen'] === 'archivo') {
                $archivo = $request->file('archivo');
                
                // Crear directorio si no existe
                $rutaDir = "fotos/ficha_{$fichaId}";
                
                // Generar nombre único
                $nombreArchivo = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
                
                // Guardar archivo
                $rutaGuardada = \Storage::disk('public')->putFileAs($rutaDir, $archivo, $nombreArchivo);
                
                $fotoData['archivo_nombre'] = $archivo->getClientOriginalName();
                $fotoData['archivo_ruta'] = $rutaGuardada;
                $fotoData['archivo_mime'] = $archivo->getClientMimeType();
                $fotoData['archivo_tamaño'] = $archivo->getSize();
                
                Log::info('Foto archivo subida', [
                    'ficha_id' => $fichaId,
                    'archivo_nombre' => $fotoData['archivo_nombre'],
                    'archivo_ruta' => $rutaGuardada,
                    'archivo_tamaño' => $fotoData['archivo_tamaño']
                ]);
            }

            $foto = FotoFichaActividad::create($fotoData);

            return response()->json([
                'success' => true,
                'message' => 'Foto agregada exitosamente.',
                'data' => $foto->load('usuarioCreacion')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al agregar foto', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fotosUpdate(Request $request, $fichaId, $fotoId)
    {
        $foto = FotoFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($fotoId);

        // Usar tipo_origen actual si no se proporciona
        $tipoOrigen = $request->input('tipo_origen', $foto->tipo_origen);

        // Validación flexible (30MB = 30720 KB)
        $rules = [
            'tipo_origen' => 'nullable|in:url,archivo',
            'url' => 'nullable|url',
            'archivo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:30720',
            'descripcion' => 'nullable|string|max:500'
        ];

        // Si está cambiando a URL, requiere URL
        if ($tipoOrigen === 'url' && $request->has('tipo_origen')) {
            $rules['url'] = 'required|url';
        }

        // Si está cambiando a archivo, requiere archivo
        if ($tipoOrigen === 'archivo' && $request->has('tipo_origen')) {
            $rules['archivo'] = 'required|image|mimes:jpeg,png,gif,webp|max:30720';
        }

        $validated = $request->validate($rules);

        try {
            $updateData = [
                'descripcion' => $validated['descripcion'] ?? null,
                'usuario_actualizacion_id' => auth()->id() ?? 1
            ];

            // Si cambió de archivo a URL
            if ($foto->tipo_origen === 'archivo' && $tipoOrigen === 'url') {
                if ($foto->archivo_ruta && \Storage::disk('public')->exists($foto->archivo_ruta)) {
                    \Storage::disk('public')->delete($foto->archivo_ruta);
                }
                
                $updateData['tipo_origen'] = 'url';
                $updateData['archivo_nombre'] = null;
                $updateData['archivo_ruta'] = null;
                $updateData['archivo_mime'] = null;
                $updateData['archivo_tamaño'] = null;
                $updateData['url'] = $validated['url'] ?? null;
                
                Log::info('Foto actualizada: Archivo → URL', [
                    'foto_id' => $fotoId,
                    'ficha_id' => $fichaId,
                    'nueva_url' => $validated['url'] ?? null
                ]);
            }
            // Si cambió de URL a archivo
            else if ($foto->tipo_origen === 'url' && $tipoOrigen === 'archivo' && $request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $rutaDir = "fotos/ficha_{$fichaId}";
                $nombreArchivo = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
                $rutaGuardada = \Storage::disk('public')->putFileAs($rutaDir, $archivo, $nombreArchivo);
                
                $updateData['tipo_origen'] = 'archivo';
                $updateData['url'] = null;
                $updateData['archivo_nombre'] = $archivo->getClientOriginalName();
                $updateData['archivo_ruta'] = $rutaGuardada;
                $updateData['archivo_mime'] = $archivo->getClientMimeType();
                $updateData['archivo_tamaño'] = $archivo->getSize();
                
                Log::info('Foto actualizada: URL → Archivo', [
                    'foto_id' => $fotoId,
                    'ficha_id' => $fichaId,
                    'archivo_ruta' => $rutaGuardada
                ]);
            }
            // Si es archivo reemplazando archivo
            else if ($tipoOrigen === 'archivo' && $request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                
                // Eliminar archivo anterior
                if ($foto->archivo_ruta && \Storage::disk('public')->exists($foto->archivo_ruta)) {
                    \Storage::disk('public')->delete($foto->archivo_ruta);
                }
                
                $rutaDir = "fotos/ficha_{$fichaId}";
                $nombreArchivo = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
                $rutaGuardada = \Storage::disk('public')->putFileAs($rutaDir, $archivo, $nombreArchivo);
                
                $updateData['tipo_origen'] = 'archivo';
                $updateData['archivo_nombre'] = $archivo->getClientOriginalName();
                $updateData['archivo_ruta'] = $rutaGuardada;
                $updateData['archivo_mime'] = $archivo->getClientMimeType();
                $updateData['archivo_tamaño'] = $archivo->getSize();
                
                Log::info('Foto actualizada: Archivo reemplazado', [
                    'foto_id' => $fotoId,
                    'ficha_id' => $fichaId,
                    'archivo_ruta' => $rutaGuardada
                ]);
            }
            // Si es URL reemplazando URL (o solo cambio de descripción en URL)
            else if ($tipoOrigen === 'url' && $validated['url']) {
                $updateData['tipo_origen'] = 'url';
                $updateData['url'] = $validated['url'];
                
                Log::info('Foto actualizada: URL reemplazada', [
                    'foto_id' => $fotoId,
                    'ficha_id' => $fichaId,
                    'nueva_url' => $validated['url']
                ]);
            }
            // Solo cambio de descripción
            else {
                $updateData['tipo_origen'] = $foto->tipo_origen;
                Log::info('Foto actualizada: Solo descripción', [
                    'foto_id' => $fotoId,
                    'ficha_id' => $fichaId
                ]);
            }

            $foto->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Foto actualizada exitosamente.',
                'data' => $foto->fresh()->load('usuarioActualizacion')
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al actualizar foto', [
                'foto_id' => $fotoId,
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fotosDestroy($fichaId, $fotoId)
    {
        try {
            $foto = FotoFichaActividad::where('ficha_actividad_id', $fichaId)->findOrFail($fotoId);
            
            // Si es archivo, eliminar del almacenamiento
            if ($foto->tipo_origen === 'archivo' && $foto->archivo_ruta) {
                if (\Storage::disk('public')->exists($foto->archivo_ruta)) {
                    \Storage::disk('public')->delete($foto->archivo_ruta);
                    
                    Log::info('Archivo de foto eliminado', [
                        'foto_id' => $fotoId,
                        'archivo_ruta' => $foto->archivo_ruta
                    ]);
                }
            }
            
            $foto->delete();
            
            Log::info('Foto eliminada', [
                'foto_id' => $fotoId,
                'ficha_id' => $fichaId,
                'tipo_origen' => $foto->tipo_origen
            ]);

            return response()->json(['success' => true, 'message' => 'Foto eliminada.'], 200);
        } catch (\Exception $e) {
            Log::error('Error al eliminar foto', [
                'foto_id' => $fotoId,
                'ficha_id' => $fichaId,
                'error' => $e->getMessage()
            ]);
            
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
            $fotos = FotoFichaActividad::with('usuarioCreacion', 'usuarioActualizacion')
                ->where('ficha_actividad_id', $fichaId)
                ->get()
                ->map(function ($foto) {
                    return [
                        'id' => $foto->id,
                        'descripcion' => $foto->descripcion,
                        'tipo_origen' => $foto->tipo_origen,
                        'url' => $foto->url,
                        'archivo_nombre' => $foto->archivo_nombre,
                        'archivo_ruta' => $foto->archivo_ruta,
                        'archivo_mime' => $foto->archivo_mime,
                        'archivo_tamaño' => $foto->archivo_tamaño,
                        'archivo_tamaño_formateado' => $foto->tamaño_formateado,
                        'foto_url' => $foto->foto_url, // Accessor para obtener URL correcta
                        'fecha' => $foto->fecha,
                        'fecha_formateada' => $foto->fecha_formateada,
                        'usuario_creacion' => $foto->usuarioCreacion?->nombre ?? 'Sistema',
                        'usuario_actualizacion' => $foto->usuarioActualizacion?->nombre ?? null
                    ];
                });

            return response()->json(['success' => true, 'data' => $fotos], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getFoto($fichaId, $fotoId)
    {
        try {
            $foto = FotoFichaActividad::with('usuarioCreacion', 'usuarioActualizacion')
                ->where('id', $fotoId)
                ->where('ficha_actividad_id', $fichaId)
                ->firstOrFail();

            return response()->json([
                'id' => $foto->id,
                'descripcion' => $foto->descripcion,
                'tipo_origen' => $foto->tipo_origen,
                'url' => $foto->url,
                'archivo_nombre' => $foto->archivo_nombre,
                'archivo_ruta' => $foto->archivo_ruta,
                'archivo_mime' => $foto->archivo_mime,
                'archivo_tamaño' => $foto->archivo_tamaño,
                'archivo_tamaño_formateado' => $foto->tamaño_formateado,
                'foto_url' => $foto->foto_url,
                'fecha' => $foto->fecha,
                'fecha_formateada' => $foto->fecha_formateada,
                'usuario_creacion' => $foto->usuarioCreacion?->nombre ?? 'Sistema',
                'usuario_actualizacion' => $foto->usuarioActualizacion?->nombre ?? null
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Foto no encontrada'], 404);
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
