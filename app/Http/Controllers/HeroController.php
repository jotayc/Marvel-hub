<?php
//Con el comando php artisan make:controller HeroController se crea este controlador, el cual se encuentra en la carpeta app/Http/Controllers,
//y se puede utilizar para manejar las rutas relacionadas con los heroes.

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeroController extends Controller
{

    /*
    Cada método tiene un nombre establecido, como index, show, create, store, edit, update y destroy,
    los cuales se utilizan para manejar las diferentes acciones relacionadas con los recursos (en este caso, los heroes).
    El método index() es el encargado de mostrar la lista de heroes, en este caso se crea un array de heroes
    con sus respectivos atributos.

    */
    public function index()
    {
        $heroes = [
            ['id' => 1, 'name' => 'Iron Man', 'real_name' => 'Tony Stark', 'power' => 'Tecnología avanzada'],
            ['id' => 2, 'name' => 'Thor', 'real_name' => 'Thor Odinson', 'power' => 'Dios del Trueno'],
            ['id' => 3, 'name' => 'Spider-Man', 'real_name' => 'Peter Parker', 'power' => 'Sentido arácnido'],
            ['id' => 4, 'name' => 'Doctor Strange', 'real_name' => 'Stephen Strange', 'power' => 'Hechicería'],
            ['id' => 5, 'name' => 'Black Widow', 'real_name' => 'Natasha Romanoff', 'power' => 'Espía experta'],
        ];

        return view('heroes.index', ['heroes' => $heroes]);
    }
}
