<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Importamos la clase DB para realizar consultas a la base de datos
use Illuminate\Support\Facades\DB;

class HeroController extends Controller
{

    /* *** ELIMINAMOS EL MÉTODO getAllHeroes ***

    /*
    Cada método tiene un nombre establecido, como index, show, create, store, edit, update y destroy,
    los cuales se utilizan para manejar las diferentes acciones relacionadas con los recursos (en este caso, los heroes).
    El método index() es el encargado de mostrar la lista de heroes, en este caso se crea un array de heroes
    con sus respectivos atributos.

    */
    public function index()

    {   // Obtenemos todos los héroes de la base de datos
        $heroes = DB::table('heroes')->get();

        return view('heroes.index', ['heroes' => $heroes]);
    }

    // El método show() es el encargado de mostrar los detalles de un héroe específico,
    //en este caso se recibe el id del héroe como parámetro,
    public function show($id)
    {

        ///-- OPCION 1 -- ///

        //DB::table('heroes') hace referencia a la tabla 'heroes' en la base de datos
        // where() se utiliza para filtrar los resultados por el campo 'id' igual al valor de $id
        // El método first() devuelve el primer resultado que coincide con la condición, o null si no se encuentra ningún resultado.
        $hero = DB::table('heroes')->where('id', $id)->first();

       ///-- OPCION 2 -- ///
       // El método find() se utiliza para buscar un registro por su clave primaria
       //(en este caso, el campo 'id').
       // $hero = DB::table('heroes')->find($id);

        if (!$hero) {
            abort(404, 'Héroe no encontrado');
        }

        return view('heroes.show', ['hero' => $hero]);
    }
}
