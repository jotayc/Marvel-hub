<?php

use Illuminate\Support\Facades\Route;
// Importamos el controlador HeroController para poder utilizarlo en las rutas.
use App\Http\Controllers\HeroController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/heroes', [HeroController::class, 'index'])->name('heroes.index');

//Es importante colocar las rutas específicas (como active y powerful)
//antes de la ruta general (show),porque Laravel evalúa las rutas en el
// orden en que están definidas y podría interpretar "active" o "powerful"
//como un ID si la ruta show está definida antes, lo que causaría un error 404.
Route::get('/heroes/active', [HeroController::class, 'active'])->name('heroes.active');
Route::get('/heroes/powerful', [HeroController::class, 'powerful'])->name('heroes.powerful');
Route::get('/heroes/create', [HeroController::class, 'create'])->name('heroes.create');
Route::get('/heroes/{id}', [HeroController::class, 'show'])->name('heroes.show');

// La ruta para almacenar un nuevo héroe utilizando el método POST,
//que se corresponde con la función store del controlador.
// Es importante que esta ruta esté definida después de la ruta GET para crear un nuevo héroe,
//ya que ambas rutas comparten el mismo URI base (/heroes) pero utilizan diferentes métodos HTTP
// (GET para mostrar el formulario y POST para procesar el formulario).
Route::post('/heroes', [HeroController::class, 'store'])->name('heroes.store');
