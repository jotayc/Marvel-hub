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

    public function create()
    {
        return view('heroes.create');
    }

    public function store(Request $request)
    {

    // Validamos los datos del formulario para asegurarnos de que cumplen con los requisitos antes de guardarlos en la base de datos.
    // Aquí estamos utilizando el método validate() del objeto Request para definir las reglas de validación para cada campo del formulario.
    // En caso de que los datos no cumplan con las reglas, Laravel redirigirá automáticamente al usuario de vuelta al formulario
    //con los errores de validación. Se puede acceder a través de la variable $errors en la vista para mostrar los mensajes de error correspondientes.
        $request->validate([
            'name'        => 'required|string|max:100',
            'real_name'   => 'nullable|string|max:100',
            'power'       => 'required|string|max:150',
            'power_level' => 'required|integer|min:1|max:10000',
            'team'        => 'required|string|max:100',
            'bio'         => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        Hero::create([
            'name'        => $request->name,
            'real_name'   => $request->real_name,
            'power'       => $request->power,
            'power_level' => $request->power_level,
            'team'        => $request->team,
            'bio'         => $request->bio,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('heroes.index')->with('success', 'Héroe creado correctamente.');
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
