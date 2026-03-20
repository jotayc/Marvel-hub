<?php

//Importamos la clase Route
use Illuminate\Support\Facades\Route;


//-----------------------//
//-----RUTAS BÁSICAS-----//
//-----------------------//


/*
    Formato básico:

    Route::metodo('uri', acción);
*/

//Route::get('/', función o controlador) -> Define una ruta que responde a peticiones GET en la URL ráiz.
Route::get('/', function () {
    //return view('welcome'); -> Retorna la vista welcome.blade.php
    return 'Bienvenidos a Marvel Hub - Lista de Héroes';
});

//Rutas múltiples.

Route::get('/heroes', function () {
    return 'Lista de Héroes Marvel';
});

Route::get('/about', function () {
    return 'Acerca de Marvel Hub';
});

Route::get('/contact', function () {
    return 'Página de Contacto';
});

//--------------------------//
//----- VISTAS (BLADE) -----//
//--------------------------//

// La petición devuelve la vista heroes.blade.php
//usando la sentencia view(<nombreVista>) laravel busca dentro de la carpeta views.

// Por ejemplo:
/*
    Route::get('/heroes', function(){
        return view('heroes');
    });
*/

// --- Pasar datos a las vistas --- //

// Si quieres pasar un dato a  la vista
Route::get('/heroes', function(){
    $titulo = 'Superheroes de Marvel';
    return view('heroes',['titulo' => $titulo]);

});

// Si quieres pasar más de un dato a través de un array:
Route::get('/listaHeroes', function () {
    $heroes = ['Iron Man', 'Thor', 'Spider-Man', 'Hulk', 'Doctor Strange'];

    return view('listaHeroes', ['heroes' => $heroes]);
});

// -- Rutas con parámetros --- //
// Puedes definir rutas que acepten parámetros dinámicos, por ejemplo,
//para mostrar detalles de un héroe específico:
Route::get('/heroes/{id}', function ($id) {
     $heroes = [
        ['nombre' => 'Iron Man', 'poder' => 'Tecnología avanzada'],
        ['nombre' => 'Thor', 'poder' => 'Dios del Trueno'],
        ['nombre' => 'Spider-Man', 'poder' => 'Sentido arácnido'],
        ['nombre' => 'Hulk', 'poder' => 'Fuerza sobrehumana'],
        ['nombre' => 'Doctor Strange', 'poder' => 'Hechicería']
    ];

    $hero = $heroes[$id] ?? null; // Obtener el héroe por su ID, o null si no existe

    return view('heroes.show', ['hero' => $hero]);
})->name('heroes.show'); // Asignamos un nombre a la ruta para facilitar su uso en enlaces y redirecciones
// a traves de la función route('heroes.show', ['id' => $id]) en lugar de escribir la URL completa.
