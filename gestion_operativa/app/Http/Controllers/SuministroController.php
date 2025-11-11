<?php

namespace App\Http\Controllers;

use App\Models\Suministro;
use App\Models\Medidor;
use App\Models\Ubigeo;
use App\Models\MedidorSuministro;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SuministroController extends Controller
{
    public function index()
    {
        return view('admin.suministro.index', [
            'catName' => 'suministro',
            'title' => 'Gestión de Suministros',
            'breadcrumbs' => ['Configuración', 'Suministros'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $suministros = Suministro::select([
                'id',
                'codigo',
                'nombre',
                'ruta',
                'medidor_id',
                'ubigeo_id',
                'estado'
            ])->orderBy('codigo');

            return DataTables::of($suministros)
                ->addIndexColumn()
                ->addColumn('medidor_serie', function ($row) {
                    return $row->medidor ? $row->medidor->serie : '-';
                })
                ->addColumn('ubigeo_nombre', function ($row) {
                    return $row->ubigeo ? $row->ubigeo->nombre : '-';
                })
                ->addColumn('estado_badge', function ($row) {
                    return $row->estado
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editSuministro(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteSuministro(' . $row->id . ')">Eliminar</a>
                                </div>
                            </div>';
                    return $html;
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
    }

    public function getForSelect()
    {
        try {
            $suministros = Suministro::select(['id', 'nombre'])
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($suministro) {
                    return [
                        'id' => $suministro->id,
                        'nombre' => $suministro->nombre
                    ];
                });

            return response()->json($suministros);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener suministros'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $exists = Suministro::where('codigo', $value)->exists();
                    if ($exists) {
                        $fail('El código del suministro ya está registrado.');
                    }
                }
            ],
            'nombre' => 'required|string',
            'ruta' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'referencia' => 'nullable|string',
            'caja' => 'nullable|string|max:50',
            'tarifa' => 'nullable|string|max:50',
            'latitud' => 'nullable|string|max:50',
            'longitud' => 'nullable|string|max:50',
            'serie' => 'nullable|string|max:50',
            'medidor_id' => 'nullable|exists:medidors,id',
            'ubigeo_id' => 'nullable|exists:ubigeos,id',
            'estado' => 'required|boolean'
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'El código ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'medidor_id.exists' => 'El medidor seleccionado no existe.',
            'ubigeo_id.exists' => 'El ubigeo seleccionado no existe.'
        ]);

        try {
            $validatedData['usuario_creacion_id'] = auth()->id() ?? 1;
            $suministro = Suministro::create($validatedData);

            // ====== NUEVO: Registrar en medidor_suministros ======
            if ($validatedData['medidor_id']) {
                MedidorSuministro::create([
                    'suministro_id' => $suministro->id,
                    'medidor_id' => $validatedData['medidor_id'],
                    'fecha_cambio' => now(),
                    'observaciones' => "Medidor asignado al crear el suministro",
                    'estado' => 1,
                    'usuario_creacion_id' => auth()->id() ?? 1
                ]);
            }
            // ====== FIN: Registrar en medidor_suministros ======

            return response()->json([
                'success' => true,
                'message' => 'Suministro creado correctamente.',
                'data' => $suministro
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el suministro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $suministro = Suministro::findOrFail($id);

            return response()->json([
                'id' => $suministro->id,
                'codigo' => $suministro->codigo,
                'nombre' => $suministro->nombre,
                'ruta' => $suministro->ruta,
                'direccion' => $suministro->direccion,
                'referencia' => $suministro->referencia,
                'caja' => $suministro->caja,
                'tarifa' => $suministro->tarifa,
                'latitud' => $suministro->latitud,
                'longitud' => $suministro->longitud,
                'serie' => $suministro->serie,
                'medidor_id' => $suministro->medidor_id,
                'ubigeo_id' => $suministro->ubigeo_id,
                'estado' => (bool) $suministro->estado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $suministro = Suministro::findOrFail($id);

        $validatedData = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = Suministro::where('codigo', $value)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('El código del suministro ya está registrado.');
                    }
                }
            ],
            'nombre' => 'required|string',
            'ruta' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'referencia' => 'nullable|string',
            'caja' => 'nullable|string|max:50',
            'tarifa' => 'nullable|string|max:50',
            'latitud' => 'nullable|string|max:50',
            'longitud' => 'nullable|string|max:50',
            'serie' => 'nullable|string|max:50',
            'medidor_id' => 'nullable|exists:medidors,id',
            'ubigeo_id' => 'nullable|exists:ubigeos,id',
            'estado' => 'required|boolean'
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'El código ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'medidor_id.exists' => 'El medidor seleccionado no existe.',
            'ubigeo_id.exists' => 'El ubigeo seleccionado no existe.'
        ]);

        try {
            $validatedData['usuario_actualizacion_id'] = auth()->id() ?? 1;
            
            // ====== NUEVO: Registrar cambio de medidor en medidor_suministros ======
            // Si cambió el medidor, crear un nuevo registro histórico
            if ($suministro->medidor_id != $validatedData['medidor_id']) {
                // Obtener datos del medidor anterior
                $medidorAnterior = $suministro->medidor;
                $medidorAnteriorInfo = $medidorAnterior 
                    ? "ID: {$suministro->medidor_id}, Serie: {$medidorAnterior->serie}, Modelo: {$medidorAnterior->modelo}"
                    : "N/A";
                
                // Crear registro histórico del nuevo medidor
                if ($validatedData['medidor_id']) {
                    MedidorSuministro::create([
                        'suministro_id' => $suministro->id,
                        'medidor_id' => $validatedData['medidor_id'],
                        'fecha_cambio' => now(),
                        'observaciones' => "Cambio de medidor - Anterior: {$medidorAnteriorInfo}",
                        'estado' => 1,
                        'usuario_creacion_id' => auth()->id() ?? 1
                    ]);
                }
                
                // Marcar el registro anterior como inactivo
                if ($suministro->medidor_id) {
                    MedidorSuministro::where('suministro_id', $suministro->id)
                        ->where('medidor_id', $suministro->medidor_id)
                        ->latest()
                        ->first()?->update([
                            'estado' => 0,
                            'observaciones' => "Medidor reemplazado por: {$medidorAnteriorInfo}",
                            'usuario_actualizacion_id' => auth()->id() ?? 1
                        ]);
                }
            }
            // ====== FIN: Registrar cambio de medidor ======
            
            $suministro->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Suministro actualizado correctamente.',
                'data' => $suministro
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el suministro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $suministro = Suministro::findOrFail($id);
            
            // ====== NUEVO: Registrar eliminación en medidor_suministros ======
            if ($suministro->medidor_id) {
                // Obtener datos del medidor
                $medidorActual = $suministro->medidor;
                $medidorInfo = $medidorActual 
                    ? "ID: {$suministro->medidor_id}, Serie: {$medidorActual->serie}, Modelo: {$medidorActual->modelo}"
                    : "N/A";
                
                MedidorSuministro::where('suministro_id', $suministro->id)
                    ->where('medidor_id', $suministro->medidor_id)
                    ->latest()
                    ->first()?->update([
                        'estado' => 0,
                        'observaciones' => "Suministro eliminado - Medidor: {$medidorInfo}",
                        'usuario_actualizacion_id' => auth()->id() ?? 1
                    ]);
            }
            // ====== FIN: Registrar eliminación ======
            
            $suministro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Suministro eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el suministro: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener Departamentos (Ubigeos padres)
    public function getDepartamentos()
    {
        $departamentos = Ubigeo::whereNull('dependencia_id')
            ->where('estado', true)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nombre
                ];
            });

        return response()->json($departamentos);
    }

    // Obtener Provincias (Hijos del Departamento)
    public function getProvincias($departamento_id)
    {
        $provincias = Ubigeo::where('dependencia_id', $departamento_id)
            ->where('estado', true)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nombre
                ];
            });

        return response()->json($provincias);
    }

    // Obtener Distritos (Hijos de la Provincia)
    public function getDistritos($provincia_id)
    {
        $distritos = Ubigeo::where('dependencia_id', $provincia_id)
            ->where('estado', true)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nombre
                ];
            });

        return response()->json($distritos);
    }

    // Obtener la jerarquía completa de ubigeo (Departamento, Provincia, Distrito) a partir del ID del distrito
    public function getUbigeoHierarquia($ubigeo_id)
    {
        try {
            $ubigeo = Ubigeo::findOrFail($ubigeo_id);
            
            // Inicializamos los niveles
            $distrito = $ubigeo;
            $provincia = null;
            $departamento = null;
            
            // Recorremos hacia arriba en la jerarquía
            if ($distrito->dependencia) {
                $provincia = $distrito->dependencia;
                
                if ($provincia->dependencia) {
                    $departamento = $provincia->dependencia;
                }
            }
            
            return response()->json([
                'success' => true,
                'departamento_id' => $departamento ? $departamento->id : null,
                'provincia_id' => $provincia ? $provincia->id : null,
                'distrito_id' => $distrito->id,
                'departamento_nombre' => $departamento ? $departamento->nombre : null,
                'provincia_nombre' => $provincia ? $provincia->nombre : null,
                'distrito_nombre' => $distrito->nombre
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la jerarquía del ubigeo: ' . $e->getMessage()
            ], 404);
        }
    }

    // ====== NUEVO: Obtener historial de medidores de un suministro ======
    public function getMedidoresHistorial($suministroId)
    {
        try {
            $suministro = Suministro::findOrFail($suministroId);
            
            $historial = MedidorSuministro::with('medidor', 'fichaActividad')
                ->where('suministro_id', $suministroId)
                ->orderBy('fecha_cambio', 'desc')
                ->get()
                ->map(function ($registro) {
                    return [
                        'id' => $registro->id,
                        'medidor_id' => $registro->medidor_id,
                        'medidor_serie' => $registro->medidor?->serie ?? 'N/A',
                        'fecha_cambio' => $registro->fecha_cambio->format('d/m/Y H:i'),
                        'observaciones' => $registro->observaciones,
                        'estado' => $registro->estado ? 'Activo' : 'Inactivo',
                        'ficha_id' => $registro->fichaActividad?->id,
                        'ficha_descripcion' => $registro->fichaActividad?->descripcion
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $historial
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el historial: ' . $e->getMessage()
            ], 500);
        }
    }
    // ====== FIN: Obtener historial ======

    // Obtener Medidores (que no estén asignados a otros suministros)
    public function getMedidores(Request $request)
    {
        $suministro_id = $request->query('suministro_id'); // Para edición, excluir el actual
        
        // Obtener IDs de medidores ya asignados a suministros
        $medidoresAsignados = Suministro::whereNotNull('medidor_id')
            ->when($suministro_id, function ($q) use ($suministro_id) {
                // Si es edición, excluir el suministro actual para permitir su medidor
                return $q->where('id', '!=', $suministro_id);
            })
            ->pluck('medidor_id')
            ->toArray();
        
        // Obtener medidores disponibles (no asignados y activos)
        $medidores = Medidor::where('estado', true)
            ->whereNotIn('id', $medidoresAsignados)
            ->select('id', 'serie')
            ->orderBy('serie')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->serie
                ];
            });

        return response()->json($medidores);
    }
}
