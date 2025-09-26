<?php

namespace App\Http\Controllers;

use App\Models\Ubigeo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UbigeoController extends Controller
{
    public function index()
    {
        return view('admin.ubigeo.index', [
            'catName' => 'ubigeo',
            'title' => 'Gestión de Ubigeos',
            'breadcrumbs' => ['Configuración', 'Ubigeos'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Optimización: Usar JOIN para evitar N+1 queries
            $ubigeos = Ubigeo::select([
                'ubigeos.id', 
                'ubigeos.nombre', 
                'ubigeos.codigo_postal', 
                'ubigeos.dependencia_id', 
                'ubigeos.estado',
                'dependencias.nombre as dependencia_nombre'
            ])
            ->leftJoin('ubigeos as dependencias', 'ubigeos.dependencia_id', '=', 'dependencias.id');

            return DataTables::of($ubigeos)
                ->addIndexColumn()
                ->addColumn('dependencia_display', function ($row) {
                    return $row->dependencia_nombre ?? 'Sin dependencia';
                })
                ->addColumn('estado_badge', function ($row) {
                    if ($row->estado) {
                        return '<span class="badge badge-success">Activo</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactivo</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="viewUbigeo(' . $row->id . ')">Ver</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editUbigeo(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteUbigeo(' . $row->id . ')">Eliminar</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'dependencia_id' => [
                'nullable',
                'exists:ubigeos,id',
                // Evitar referencias circulares
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->has('id') && $value == $request->id) {
                        $fail('Un ubigeo no puede ser dependiente de sí mismo.');
                    }
                }
            ],
            'estado' => 'required|boolean'
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser texto.',
            'nombre.max' => 'El campo nombre no puede tener más de 100 caracteres.',
            'codigo_postal.string' => 'El código postal debe ser texto.',
            'codigo_postal.max' => 'El código postal no puede tener más de 10 caracteres.',
            'dependencia_id.exists' => 'La dependencia seleccionada no existe.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.boolean' => 'El campo estado debe ser verdadero o falso.'
        ]);

        try {
            $ubigeo = Ubigeo::create($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Ubigeo creado correctamente.',
                'data' => $ubigeo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el ubigeo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Solo cargar los campos necesarios
            $ubigeo = Ubigeo::select([
                'id', 
                'nombre', 
                'codigo_postal', 
                'dependencia_id', 
                'estado'
            ])->findOrFail($id);
            
            // Cargar dependencia solo si existe
            $dependencia_nombre = null;
            if ($ubigeo->dependencia_id) {
                $dependencia = Ubigeo::select('nombre')->find($ubigeo->dependencia_id);
                $dependencia_nombre = $dependencia ? $dependencia->nombre : null;
            }
            
            return response()->json([
                'id' => $ubigeo->id,
                'nombre' => $ubigeo->nombre,
                'codigo_postal' => $ubigeo->codigo_postal,
                'dependencia_id' => $ubigeo->dependencia_id,
                'dependencia_nombre' => $dependencia_nombre,
                'estado' => (bool) $ubigeo->estado
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
        $ubigeo = Ubigeo::findOrFail($id);
        
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'dependencia_id' => [
                'nullable',
                'exists:ubigeos,id',
                // Evitar referencias circulares
                function ($attribute, $value, $fail) use ($id) {
                    if ($value && $value == $id) {
                        $fail('Un ubigeo no puede ser dependiente de sí mismo.');
                    }
                    
                    // Verificar que no se cree una dependencia circular
                    if ($value) {
                        $this->checkCircularDependency($value, $id, $fail);
                    }
                }
            ],
            'estado' => 'required|boolean'
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser texto.',
            'nombre.max' => 'El campo nombre no puede tener más de 100 caracteres.',
            'codigo_postal.string' => 'El código postal debe ser texto.',
            'codigo_postal.max' => 'El código postal no puede tener más de 10 caracteres.',
            'dependencia_id.exists' => 'La dependencia seleccionada no existe.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.boolean' => 'El campo estado debe ser verdadero o falso.'
        ]);

        try {
            $ubigeo->update($validatedData);
            
            return response()->json([
                'success' => true,
                'message' => 'Ubigeo actualizado correctamente.',
                'data' => $ubigeo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el ubigeo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ubigeo = Ubigeo::findOrFail($id);
            
            // Verificar si tiene dependientes (optimizado)
            $dependientesCount = Ubigeo::where('dependencia_id', $id)->count();
            if ($dependientesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar este ubigeo porque tiene ' . $dependientesCount . ' dependientes asociados'
                ], 422);
            }

            $ubigeo->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Ubigeo eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el ubigeo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUbigeosForSelect()
    {
        try {
            // Optimización: Solo cargar campos necesarios y limitar resultados
            $ubigeos = Ubigeo::select(['id', 'nombre', 'dependencia_id'])
                ->where('estado', true)
                ->orderBy('nombre')
                ->limit(1000) // Limitar para evitar sobrecarga
                ->get()
                ->map(function ($ubigeo) {
                    return [
                        'id' => $ubigeo->id,
                        'text' => $ubigeo->nombre // Usar solo el nombre por ahora
                    ];
                });

            return response()->json($ubigeos);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los ubigeos'
            ], 500);
        }
    }

    /**
     * Verificar dependencias circulares
     */
    private function checkCircularDependency($dependenciaId, $ubigeoId, $fail, $visited = [])
    {
        // Evitar bucles infinitos
        if (in_array($dependenciaId, $visited)) {
            $fail('Se detectó una dependencia circular.');
            return;
        }

        $visited[] = $dependenciaId;

        // Si la dependencia tiene como dependencia el ubigeo actual, hay circularidad
        $dependencia = Ubigeo::select('dependencia_id')->find($dependenciaId);
        if ($dependencia && $dependencia->dependencia_id) {
            if ($dependencia->dependencia_id == $ubigeoId) {
                $fail('Se detectó una dependencia circular.');
                return;
            }
            
            // Verificar recursivamente pero con límite
            if (count($visited) < 10) { // Límite de profundidad
                $this->checkCircularDependency($dependencia->dependencia_id, $ubigeoId, $fail, $visited);
            }
        }
    }
}