<?php

use App\Http\Controllers\CategoriaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('hola-mundo', function () {
    return Inertia::render('HolaMundo');
})->middleware(['auth', 'verified'])->name('hola-mundo');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('dashboard1', function () {
    return Inertia::render('Dashboard1');
})->middleware(['auth', 'verified'])->name('dashboard1');


Route::get('contador', function () {
    return Inertia::render('Contador');
})->middleware(['auth', 'verified'])->name('contador');



/////////
Route::get('categorias-data', [CategoriaController::class, 'listarCategoria']);

Route::get('categorias-exportar-pdf', [CategoriaController::class, 'exportarCategoriasPDF'])
    ->name('categorias.export.pdf');
//////////


Route::get('categoria', function () {
    return Inertia::render('Categoria');
})->middleware(['auth', 'verified'])->name('categoria');







require __DIR__ . '/settings.php';
