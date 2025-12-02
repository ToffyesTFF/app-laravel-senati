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
            $categorias = Categoria::orderBy('created_at', 'desc')->get();
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

    public function guardarCategoria(Request $request)
    {
        $validated = $request->validate([
            'nombre_categoria' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'estado' => 'required|boolean',
        ]);
        $categoria = Categoria::create($request->all());
        return response()->json(
            [
                'success' => true,
                'nombre' => "Andree Contreras",
                'data' => $categoria
            ]
        );
    }

    public function editarCategoria(Request $request, $id_categoria)
    {
        $categoria = Categoria::findOrFail($id_categoria);

        $validated = $request->validate([
            'nombre_categoria' => 'required|string|max:255|unique:categorias,nombre_categoria,' . $categoria->id,
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|boolean',
        ]);

        $categoria->update($validated);

        return response()->json(
            [
                'success' => true,
                'nombre' => "Andree Contreras",
                'data' => $categoria
            ]
        );
    }

    public function eliminarCategoria($id_categoria)
    {
        $categoria = Categoria::findOrFail($id_categoria);
        $categoria->delete();
        return response()->json(
            [
                'success' => true,
                'nombre' => "Andree Contreras",
                'data' => $categoria
            ]
        );
    }

    public function exportarCategoriasPDF()
    {
        $categorias = Categoria::all();
        $data = ['categorias' => $categorias];
        $pdf = PDF::loadView('reports.categorias_pdf', $data);
        return $pdf->download('reporte-categorias-' . time() . '.pdf');
    }
}
