<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MaterialController extends Controller
{
    public function index()
    {
    $categorias = Categoria::where('estado', 1)->get();
    $unidades = UnidadMedida::where('estado', 1)->get();
        return view('admin.materiales.index', [
            'catName' => 'materiales',
            'title' => 'Gestión de Materiales',
            'breadcrumbs' => ['Configuración', 'Materiales'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'categorias' => $categorias,
            'unidades' => $unidades,
        ]);
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            // Consulta principal a materials con relaciones
            $materiales = Material::select(
                    'materials.id',
                    'materials.categoria_id',
                    'materials.nombre',
                    'materials.descripcion',
                    'materials.unidad_medida_id',
                    'materials.precio_unitario',
                    'materials.stock_minimo',
                    'materials.codigo_material',
                    'materials.estado',
                    'materials.created_at',
                    'materials.updated_at',
                    'categorias.nombre as categoria_nombre',
                    'unidad_medidas.nombre as unidad_nombre'
                )
                ->leftJoin('categorias', 'materials.categoria_id', '=', 'categorias.id')
                ->leftJoin('unidad_medidas', 'materials.unidad_medida_id', '=', 'unidad_medidas.id');
            
            return DataTables::of($materiales)
                ->addIndexColumn()
                ->addColumn('categoria', function ($row) {
                    return $row->categoria_nombre ?? '';
                })
                ->addColumn('unidad', function ($row) {
                    return $row->unidad_nombre ?? '';
                })
                ->addColumn('estado_badge', function ($row) {
                    return $row->estado
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink' . $row->id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink' . $row->id . '">
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="editMaterial(' . $row->id . ')">Editar</a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="deleteMaterial(' . $row->id . ')">Eliminar</a>
                                </div>
                            </div>';
                })
                ->filterColumn('categoria', function($query, $keyword) {
                    $query->whereRaw("LOWER(categorias.nombre) LIKE ?", ["%{$keyword}%"]);
                })
                ->filterColumn('unidad', function($query, $keyword) {
                    $query->whereRaw("LOWER(unidad_medidas.nombre) LIKE ?", ["%{$keyword}%"]);
                })
                ->orderColumn('categoria', 'categorias.nombre $1')
                ->orderColumn('unidad', 'unidad_medidas.nombre $1')
                ->rawColumns(['estado_badge', 'action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'unidad_medida_id' => 'required|exists:unidad_medidas,id',
            'precio_unitario' => 'nullable|numeric',
            'stock_minimo' => 'required|integer',
            'codigo_material' => 'required|string|max:50|unique:materials,codigo_material',
            'estado' => 'nullable|boolean',
        ]);
        
        $data = $request->all();
        $data['estado'] = $request->has('estado') ? 1 : 0;
        
        $material = Material::create($data);
        return response()->json(['success' => true, 'data' => $material]);
    }

    public function show($id)
    {
        $material = Material::with(['categoria', 'unidadMedida'])->findOrFail($id);
        return response()->json($material);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'unidad_medida_id' => 'required|exists:unidad_medidas,id',
            'precio_unitario' => 'nullable|numeric',
            'stock_minimo' => 'required|integer',
            'codigo_material' => 'required|string|max:50|unique:materials,codigo_material,' . $id,
            'estado' => 'nullable|boolean',
        ]);
        
        $data = $request->all();
        $data['estado'] = $request->has('estado') ? 1 : 0;
        
        $material = Material::findOrFail($id);
        $material->update($data);
        return response()->json(['success' => true, 'data' => $material]);
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();
        return response()->json(['success' => true]);
    }

    public function select()
    {
        $materiales = Material::select('id', 'nombre')
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return response()->json($materiales);
    }
}
