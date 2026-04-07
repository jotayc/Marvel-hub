<?php

use Illuminate\Support\Facades\Route;
// Importamos el controlador HeroController para poder utilizarlo en las rutas.
use App\Http\Controllers\HeroController;

Route::get('/', function () {
    return view('welcome');
});

// Definimos una ruta para mostrar la lista de heroes, utilizando el método index del HeroController.
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes.index');

// La función name() se utiliza para asignar un nombre a la ruta,
//lo que facilita su referencia en otras partes de la aplicación, como en las vistas o en redirecciones.

// Definimos una ruta para mostrar los detalles de un héroe específico,
//utilizando el método show del HeroController y pasando el id del héroe como pará metro.
Route::get('/heroes/{id}', [HeroController::class, 'show'])->name('heroes.show');
