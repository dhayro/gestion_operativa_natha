<?php

namespace App\Http\Controllers;

use App\Models\Medidor;
use App\Models\Material;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MedidorController extends Controller
{
    public function index()
    {
        return view('admin.medidor.index', [
            'catName' => 'medidor',
            'title' => 'Gestión de Medidores',
            'breadcrumbs' => ['Configuración', 'Medidores'],
            'scrollspy' => 0,
            'simplePage' => 0
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $medidores = Medidor::select([
                'id',
                'serie',
                'modelo',
                'capacidad_amperios',
                'año_fabricacion',
                'marca',
                'numero_hilos',
                'material_id',
                'fm',
                'estado'
            ])->orderBy('serie');

            return DataTables::of($medidores)
                ->addIndexColumn()
                ->addColumn('material_nombre', function ($row) {
                    return $row->material ? $row->material->nombre : '-';
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
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editMedidor(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteMedidor(' . $row->id . ')">Eliminar</a>
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
        $validatedData = $request->validate([
            'serie' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $exists = Medidor::where('serie', $value)->exists();
                    if ($exists) {
                        $fail('La serie del medidor ya está registrada.');
                    }
                }
            ],
            'modelo' => 'required|string|max:50',
            'capacidad_amperios' => 'nullable|string|max:10',
            'año_fabricacion' => 'nullable|digits:4',
            'marca' => 'nullable|string|max:50',
            'numero_hilos' => 'nullable|integer',
            'material_id' => 'nullable|exists:materials,id',
            'fm' => 'nullable|string|max:50',
            'estado' => 'required|boolean'
        ], [
            'serie.required' => 'La serie es obligatoria.',
            'serie.string' => 'La serie debe ser texto.',
            'serie.max' => 'La serie no puede tener más de 50 caracteres.',
            'modelo.required' => 'El modelo es obligatorio.',
            'modelo.string' => 'El modelo debe ser texto.',
            'modelo.max' => 'El modelo no puede tener más de 50 caracteres.',
            'capacidad_amperios.string' => 'La capacidad debe ser texto.',
            'capacidad_amperios.max' => 'La capacidad no puede tener más de 10 caracteres.',
            'año_fabricacion.digits' => 'El año debe tener 4 dígitos.',
            'marca.string' => 'La marca debe ser texto.',
            'marca.max' => 'La marca no puede tener más de 50 caracteres.',
            'numero_hilos.integer' => 'El número de hilos debe ser un número entero.',
            'material_id.exists' => 'El material seleccionado no existe.',
            'fm.string' => 'El FM debe ser texto.',
            'fm.max' => 'El FM no puede tener más de 50 caracteres.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.boolean' => 'El campo estado debe ser verdadero o falso.'
        ]);

        try {
            $medidor = Medidor::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Medidor creado correctamente.',
                'data' => $medidor
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el medidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $medidor = Medidor::findOrFail($id);

            return response()->json([
                'id' => $medidor->id,
                'serie' => $medidor->serie,
                'modelo' => $medidor->modelo,
                'capacidad_amperios' => $medidor->capacidad_amperios,
                'año_fabricacion' => $medidor->año_fabricacion,
                'marca' => $medidor->marca,
                'numero_hilos' => $medidor->numero_hilos,
                'material_id' => $medidor->material_id,
                'fm' => $medidor->fm,
                'estado' => (bool) $medidor->estado
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
        $medidor = Medidor::findOrFail($id);

        $validatedData = $request->validate([
            'serie' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = Medidor::where('serie', $value)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('La serie del medidor ya está registrada.');
                    }
                }
            ],
            'modelo' => 'required|string|max:50',
            'capacidad_amperios' => 'nullable|string|max:10',
            'año_fabricacion' => 'nullable|digits:4',
            'marca' => 'nullable|string|max:50',
            'numero_hilos' => 'nullable|integer',
            'material_id' => 'nullable|exists:materials,id',
            'fm' => 'nullable|string|max:50',
            'estado' => 'required|boolean'
        ], [
            'serie.required' => 'La serie es obligatoria.',
            'serie.string' => 'La serie debe ser texto.',
            'serie.max' => 'La serie no puede tener más de 50 caracteres.',
            'modelo.required' => 'El modelo es obligatorio.',
            'modelo.string' => 'El modelo debe ser texto.',
            'modelo.max' => 'El modelo no puede tener más de 50 caracteres.',
            'capacidad_amperios.string' => 'La capacidad debe ser texto.',
            'capacidad_amperios.max' => 'La capacidad no puede tener más de 10 caracteres.',
            'año_fabricacion.digits' => 'El año debe tener 4 dígitos.',
            'marca.string' => 'La marca debe ser texto.',
            'marca.max' => 'La marca no puede tener más de 50 caracteres.',
            'numero_hilos.integer' => 'El número de hilos debe ser un número entero.',
            'material_id.exists' => 'El material seleccionado no existe.',
            'fm.string' => 'El FM debe ser texto.',
            'fm.max' => 'El FM no puede tener más de 50 caracteres.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.boolean' => 'El campo estado debe ser verdadero o falso.'
        ]);

        try {
            $medidor->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Medidor actualizado correctamente.',
                'data' => $medidor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el medidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $medidor = Medidor::findOrFail($id);
            $medidor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medidor eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el medidor: ' . $e->getMessage()
            ], 500);
        }
    }

    // Método para obtener materiales
    public function getMateriales()
    {
        $materiales = Material::where('estado', true)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->nombre
                ];
            });

        return response()->json($materiales);
    }
}
