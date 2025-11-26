<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    //CRUD
    //LISTAR
    public function listarCategoria()
    {

        //$categorias = Categoria::all();
        $categorias = Categoria::orderBy('created_at', 'asc')->get();
        //$categorias = Categoria::first();

        return response()->json(

            [
                'success' => true,
                'data' => $categorias
            ]
        );
    }

    public function agregarCategoria() {}

    public function editarCategoria() {}

    public function eliminarCategoria() {}


    // En CategoriaController.php

    public function exportarCategoriasPDF()
    {
        // 1. Obtener los datos
        $categorias = Categoria::all();

        // AÑADE ESTO PARA VERIFICAR SI HAY DATOS:
        // Detiene la ejecución y muestra los datos
        // Si la matriz está vacía ([]), el problema está en la base de datos (Paso 3).
        // Si ves los datos, puedes continuar (Paso 2).
        // dd($categorias); 

        // 2. Cargar la vista con los datos
        $data = ['categorias' => $categorias];

        // 3. Generar el PDF
        $pdf = PDF::loadView('reports.categorias_pdf', $data);

        // 4. Descargar el archivo
        return $pdf->download('reporte-categorias-' . time() . '.pdf');
    }
}
