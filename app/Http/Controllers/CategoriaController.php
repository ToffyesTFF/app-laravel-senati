<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CategoriaController extends Controller
{
    public function listarCategoria()
    {
        try {
            $categorias = Categoria::orderBy('created_at', 'asc')->get();
            return response()->json(
                [
                    'success' => true,
                    'nombre' => "Andree Contreras",
                    'data' => $categorias
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar categorías: ' . $e->getMessage(),
                'data' => []
            ], 500); 
        }
    }

    public function agregarCategoria(Request $request)
    {
        $validated = $request->validate([
            'nombre_categoria' => 'required|string|max:255|unique:categorias,nombre_categoria',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|boolean',
        ]);

        Categoria::create($validated);

        return Redirect::route('categoria')
            ->with('success', 'Categoría creada con éxito.');
    }

    public function editarCategoria(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre_categoria' => 'required|string|max:255|unique:categorias,nombre_categoria,' . $categoria->id,
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|boolean',
        ]);

        $categoria->update($validated);

        return Redirect::route('categoria')
            ->with('success', 'Categoría actualizada con éxito.');
    }

    public function eliminarCategoria(Categoria $categoria)
    {
        $categoria->delete();

        return Redirect::route('categoria')
            ->with('success', 'Categoría eliminada con éxito.');
    }

    public function exportarCategoriasPDF()
    {
        $categorias = Categoria::all();
        $data = ['categorias' => $categorias];
        $pdf = PDF::loadView('reports.categorias_pdf', $data);
        return $pdf->download('reporte-categorias-' . time() . '.pdf');
    }
}
?>