<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

//Importamos el modelo Hero para poder interactuar con la base de datos
use App\Models\Hero;

class HeroController extends Controller
{



    public function index()

    {   // Ahora utilizamos el modelo Hero para obtener todos los héroes de la base de datos
        //  y pasarlos a la vista en lugar de usar DB::table('heroes') directamente.
        $heroes = Hero::all();

        // La función compact() se utiliza para crear un array asociativo con el nombre de la variable
        //como clave y su valor como valor.
        //Es equivalente a escribir ['heroes' => $heroes], pero es más conciso y legible.
        return view('heroes.index', compact('heroes'));
    }


    public function show($id)
    {
        // La función findOrFail() intenta encontrar un registro por su ID.
        //Si no lo encuentra, lanza una excepción que generalmente se traduce
        //en una página de error 404.
        $hero = Hero::findOrFail($id);
        return view('heroes.show', compact('hero'));
    }

    public function active()
    {
        // Aquí utilizamos el modelo Hero para obtener solo los
        //héroes activos de la base de datos

        //Gracias a $casts en el modelo Hero, Laravel sabe que is_active es un booleano,
        //así que podemos usar true directamente en la consulta.
        $heroes = Hero::where('is_active', true)->get();

        //Reutilizamos la misma vista index para mostrar
        //los héroes activos, ya que la estructura de datos
        //es la misma.
        return view('heroes.active', compact('heroes'));
    }

    public function powerful()
    {
        $heroes = Hero::where('power_level', '>', 8500)
            ->orderBy('power_level', 'desc')
            ->get();
        // Aquí también reutilizamos la vista index para mostrar
        //los héroes más poderosos.
        return view('heroes.index', compact('heroes'));
    }
}
