<?php
//Con el comando php artisan make:controller HeroController se crea este controlador, el cual se encuentra en la carpeta app/Http/Controllers,
//y se puede utilizar para manejar las rutas relacionadas con los heroes.

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeroController extends Controller
{

    public function getAllHeroes()
    {
        $heroes = [
             ['id' => 0, 'name' => 'Iron Man', 'real_name' => 'Tony Stark', 'power' => 'Tecnología avanzada', 'power_level' => 8500, 'team' => 'Vengadores'],
             ['id' => 1, 'name' => 'Thor', 'real_name' => 'Thor Odinson', 'power' => 'Dios del Trueno', 'power_level' => 9000, 'team' => 'Vengadores'],
             ['id' => 2, 'name' => 'Spider-Man', 'real_name' => 'Peter Parker', 'power' => 'Sentido arácnido', 'power_level' => 7000, 'team' => 'Vengadores'],
             ['id' => 3, 'name' => 'Doctor Strange', 'real_name' => 'Stephen Strange', 'power' => 'Hechicería', 'power_level' => 9000, 'team' => 'Vengadores'],
             ['id' => 4, 'name' => 'Black Widow', 'real_name' => 'Natasha Romanoff', 'power' => 'Espía experta', 'power_level' => 6500, 'team' => 'Vengadores'],
        ];

        return $heroes;

    }

    /*
    Cada método tiene un nombre establecido, como index, show, create, store, edit, update y destroy,
    los cuales se utilizan para manejar las diferentes acciones relacionadas con los recursos (en este caso, los heroes).
    El método index() es el encargado de mostrar la lista de heroes, en este caso se crea un array de heroes
    con sus respectivos atributos.

    */
    public function index()
    {
        $heroes = $this->getAllHeroes();

        return view('heroes.index', ['heroes' => $heroes]);
    }

    // El método show() es el encargado de mostrar los detalles de un héroe específico,
    //en este caso se recibe el id del héroe como parámetro,
    public function show($id)
    {
        $heroes = $this->getAllHeroes();


        if (!isset($heroes[$id])) {
            abort(404, 'Héroe no encontrado');
        }

        $hero = $heroes[$id]; // Ajustamos el índice para que coincida con el array (que comienza en 0)

        return view('heroes.show', ['hero' => $hero]);
    }
}
