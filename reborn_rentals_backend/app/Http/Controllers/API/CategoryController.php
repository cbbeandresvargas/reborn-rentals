<?php

namespace App\Http\Controllers\API;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all();
        if (!$category) {
            return response()->json(['message' => 'Categorias aún no tiene datos registrados'], 404);
        }
        return response()->json($category);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $category = Category::create($validator->validated());

        return response()->json($category, 201);
    }
     /**
      *  Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return response()->json($category, 200);    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
         // Buscar la categoría por su ID
            $category = Category::find($id);

            // Si no existe, devolver error 404
            if (!$category) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }

            // Validar los datos recibidos
            $validated = $request->validate([
                'name' => [
                    'sometimes', 'required', 'string', 'max:255',
                    Rule::unique('categories', 'name')->ignore($category->id),
                ],
                'description' => ['sometimes', 'nullable', 'string'],
            ]);

            // Actualizar el modelo con los campos validados
            $category->update($validated);

            // Devolver el objeto actualizado
            return response()->json($category->fresh(), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente'], 200);
    }
}