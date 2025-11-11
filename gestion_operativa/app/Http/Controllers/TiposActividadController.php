<?php

namespace App\Http\Controllers;

use App\Models\TiposActividad;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TiposActividadController extends Controller
{
    public function index()
    {
        return view('admin.tipos_actividad.index', [
            'catName' => 'maestros',
            'title' => 'Gestión de Tipos de Actividad',
            'breadcrumbs' => ['Configuración', 'Tipos de Actividad'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Obtener tipos padres o hijos según el parámetro dependencia_id
            $query = TiposActividad::select(['id', 'nombre', 'dependencia_id', 'estado']);
            
            if ($request->has('dependencia_id') && $request->get('dependencia_id') != null) {
                // Mostrar solo hijos del padre especificado
                $query->where('dependencia_id', $request->get('dependencia_id'));
            } else {
                // Mostrar solo tipos padres (sin dependencia)
                $query->whereNull('dependencia_id');
            }
            
            $tiposActividad = $query->orderBy('nombre');
            
            return DataTables::of($tiposActividad)
                ->addIndexColumn()
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editTiposActividad(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteTiposActividad(' . $row->id . ')">Eliminar</a>
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="verSubactividades(' . $row->id . ', \'' . addslashes($row->nombre) . '\')">
                                        <i class="fas fa-eye"></i> Ver Subactividades
                                    </a>
                                </div>
                            </div>';
                    
                    return $html;
                })
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $dependencia_id = $request->get('dependencia_id') ?: null;
        
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($dependencia_id) {
                    // Validar que el nombre sea único dentro del mismo padre
                    $exists = TiposActividad::where('nombre', $value)
                        ->where('dependencia_id', $dependencia_id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Ya existe un tipo de actividad con este nombre en este nivel.');
                    }
                }
            ],
            'dependencia_id' => 'nullable|exists:tipos_actividads,id',
            'estado' => 'required|boolean',
        ]);
        
        // Obtener estado del input hidden o del checkbox
        $estado = $request->has('estado_hidden') ? (bool)$request->get('estado_hidden') : (bool)$request->get('estado');
        
        $tiposActividad = TiposActividad::create([
            'nombre' => $request->get('nombre'),
            'dependencia_id' => $dependencia_id,
            'estado' => $estado
        ]);
        return response()->json(['success' => true, 'data' => $tiposActividad]);
    }

    public function show($id)
    {
        $tiposActividad = TiposActividad::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $tiposActividad
        ]);
    }

    public function update(Request $request, $id)
    {
        $dependencia_id = $request->get('dependencia_id') ?: null;
        
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($dependencia_id, $id) {
                    // Validar que el nombre sea único dentro del mismo padre (excepto el registro actual)
                    $exists = TiposActividad::where('nombre', $value)
                        ->where('dependencia_id', $dependencia_id)
                        ->where('id', '!=', $id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Ya existe un tipo de actividad con este nombre en este nivel.');
                    }
                }
            ],
            'dependencia_id' => 'nullable|exists:tipos_actividads,id',
            'estado' => 'required|boolean',
        ]);
        
        // Obtener estado del input hidden o del checkbox
        $estado = $request->has('estado_hidden') ? (bool)$request->get('estado_hidden') : (bool)$request->get('estado');
        
        $tiposActividad = TiposActividad::findOrFail($id);
        $tiposActividad->update([
            'nombre' => $request->get('nombre'),
            'dependencia_id' => $dependencia_id,
            'estado' => $estado
        ]);
        return response()->json(['success' => true, 'data' => $tiposActividad]);
    }

    public function destroy($id)
    {
        try {
            $tiposActividad = TiposActividad::findOrFail($id);
            $tiposActividad->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'No se puede eliminar este tipo de actividad porque tiene dependencias.'], 400);
        }
    }

    public function getTiposActividadForSelect()
    {
        try {
            $tiposActividad = TiposActividad::select(['id', 'nombre'])
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($tiposActividad) {
                    return [
                        'id' => $tiposActividad->id,
                        'text' => $tiposActividad->nombre
                    ];
                });

            return response()->json($tiposActividad);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de actividad'
            ], 500);
        }
    }

    public function select()
    {
        try {
            $tiposActividad = TiposActividad::select(['id', 'nombre'])
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($tipo) {
                    return [
                        'id' => $tipo->id,
                        'nombre' => $tipo->nombre
                    ];
                });

            return response()->json($tiposActividad);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de actividad'
            ], 500);
        }
    }

    public function getPadresForSelect()
    {
        try {
            $tiposActividad = TiposActividad::select(['id', 'nombre'])
                ->whereNull('dependencia_id')
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($tiposActividad) {
                    return [
                        'id' => $tiposActividad->id,
                        'text' => $tiposActividad->nombre
                    ];
                });

            return response()->json($tiposActividad);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los tipos de actividad padre'
            ], 500);
        }
    }

    public function getTree()
    {
        try {
            $tiposActividad = TiposActividad::with('hijos')
                ->whereNull('dependencia_id')
                ->where('estado', true)
                ->orderBy('nombre')
                ->get()
                ->map(function ($tipo) {
                    return $this->buildTreeNode($tipo);
                });

            return response()->json($tiposActividad);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el árbol de tipos de actividad'
            ], 500);
        }
    }

    private function buildTreeNode($tipo)
    {
        return [
            'id' => $tipo->id,
            'nombre' => $tipo->nombre,
            'estado' => $tipo->estado,
            'dependencia_id' => $tipo->dependencia_id,
            'hijos' => $tipo->hijos->map(function ($hijo) {
                return $this->buildTreeNode($hijo);
            })->toArray()
        ];
    }

    public function selectConHijos()
    {
        $tipos = TiposActividad::whereNull('dependencia_id')
            ->where('estado', true)
            ->orderBy('nombre')
            ->get()
            ->map(function ($tipo) {
                return $this->construirArbolRecursivo($tipo);
            });

        return response()->json($tipos);
    }

    private function construirArbolRecursivo($tipo)
    {
        $hijos = TiposActividad::where('dependencia_id', $tipo->id)
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $data = [
            'id' => $tipo->id,
            'text' => $tipo->nombre,
        ];

        if ($hijos->count() > 0) {
            $data['children'] = $hijos->map(function ($hijo) {
                return $this->construirArbolRecursivo($hijo);
            })->toArray();
        }

        return $data;
    }
}