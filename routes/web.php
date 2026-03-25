<?php

use Illuminate\Support\Facades\Route;
// Importamos el controlador HeroController para poder utilizarlo en las rutas.
use App\Http\Controllers\HeroController;

Route::get('/', function () {
    return view('welcome');
});

// Definimos una ruta para mostrar la lista de heroes, utilizando el método index del HeroController.
Route::get('/heroes', [HeroController::class, 'index']);
