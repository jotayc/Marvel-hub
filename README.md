# Fase 6: Formularios y Validación

---

## De dónde partimos

Hasta la Fase 5 el proyecto Marvel Hub solo permite **leer** datos: listar héroes, ver el detalle de uno, filtrar por equipo o nivel de poder. Toda esa información entró en la base de datos a través de phpMyAdmin o Tinker. No existe ninguna forma de que un usuario cree un registro nuevo desde el navegador.

En esta fase se cierra esa parte del ciclo: aprenderás a crear un formulario HTML en una vista Blade, recoger los datos que el usuario introduce, validarlos en el controlador antes de tocarlos, y guardar el nuevo registro en la base de datos.

El flujo completo que vas a construir es el siguiente:

```
Navegador                  Laravel
   |                          |
   |--- GET /heroes/create --> |  Muestra el formulario vacío
   |<-- Vista create.blade --- |
   |                          |
   |--- POST /heroes ----------|  Envía los datos del formulario
   |                          |  Valida los datos
   |                          |  Guarda el héroe
   |<-- Redirección ---------- |  Redirige al listado
```

---

## Preparación: Crear una rama nueva

```bash
git checkout 5.Layouts
git checkout -b 6.Formularios
```

---

## El método POST y la protección CSRF

Hasta ahora todas las rutas que has definido usan `Route::get()`. Los formularios HTML que envían datos al servidor usan el método **POST**, que requiere su propia ruta con `Route::post()`.

La diferencia entre GET y POST no es solo semántica. GET transporta los datos en la URL (visibles, cacheables, marcables como favoritos), lo que lo hace adecuado para consultas y filtros. POST transporta los datos en el cuerpo de la petición (no visibles en la URL), lo que lo hace adecuado para operaciones que crean o modifican datos.

### Qué es CSRF y por qué importa

**CSRF** (Cross-Site Request Forgery, falsificación de petición en sitios cruzados) es un tipo de ataque que explota la confianza que un servidor tiene en el navegador de un usuario autenticado.

El escenario es el siguiente: imagina que un usuario ha iniciado sesión en una aplicación web. Mientras tiene esa sesión abierta, visita una página maliciosa en otra pestaña. Esa página podría contener un formulario oculto que apunta a la aplicación legítima:

```html
{{-- Formulario oculto en una página maliciosa --}}
<form action="https://tu-app.com/heroes" method="POST">
    <input type="hidden" name="name" value="Héroe falso">
</form>
<script>document.forms[0].submit();</script>
```

Como el navegador del usuario todavía tiene la sesión activa, ese formulario se enviaría con sus credenciales sin que él lo sepa ni lo autorice. El servidor recibiría una petición aparentemente legítima.

### Cómo lo resuelve Laravel

Laravel protege contra esto generando un **token único por sesión**: una cadena aleatoria que solo conoce el servidor y el formulario legítimo. El flujo es el siguiente:

1. El usuario abre el formulario de creación (`GET /heroes/create`)
2. Laravel genera un token secreto y lo guarda en la sesión del usuario
3. El formulario se envía al navegador con ese token incluido en un campo oculto
4. Cuando el usuario envía el formulario (`POST /heroes`), el token viaja junto con los datos
5. Laravel compara el token recibido con el que guardó en la sesión
6. Si coinciden, la petición es legítima y se procesa
7. Si no coinciden o no hay token, Laravel rechaza la petición con un **error 419**

La página maliciosa no puede reproducir este ataque porque no conoce el token: es secreto, cambia con cada sesión y nunca se expone públicamente.

### @csrf en Blade

En Blade, añadir el token al formulario es una sola línea:

```html
<form action="{{ route('heroes.store') }}" method="POST">
    @csrf
    {{-- campos del formulario --}}
</form>
```

`@csrf` genera automáticamente el campo oculto con el token de la sesión actual:

```html
<input type="hidden" name="_token" value="xK9mP2...token-secreto...Qr4n">
```

No necesitas gestionar ese token manualmente. Laravel lo genera al crear el formulario, lo inserta con `@csrf`, y lo verifica automáticamente al recibir la petición POST. Si olvidas `@csrf` en un formulario, Laravel devolverá un error 419 al intentar enviarlo.

---

## Paso 1: Añadir las rutas

El formulario de creación necesita dos rutas que trabajan juntas:

```php
// routes/web.php

// GET: muestra el formulario vacío
Route::get('/heroes/create', [HeroController::class, 'create'])->name('heroes.create');

// POST: recibe los datos y guarda el héroe
Route::post('/heroes', [HeroController::class, 'store'])->name('heroes.store');
```

La ruta GET sirve el formulario. La ruta POST recibe los datos cuando el usuario pulsa el botón de envío. Fíjate en que ambas comparten el mismo recurso (`heroes`) pero con métodos HTTP distintos, lo que las hace rutas independientes.

El orden en `web.php` también importa aquí. La ruta `GET /heroes/create` debe estar **antes** de `GET /heroes/{id}`, de lo contrario Laravel interpretaría `create` como un ID:

```php
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes.index');
Route::get('/heroes/create', [HeroController::class, 'create'])->name('heroes.create');  // ← antes de {id}
Route::get('/heroes/active', [HeroController::class, 'active'])->name('heroes.active');
Route::get('/heroes/powerful', [HeroController::class, 'powerful'])->name('heroes.powerful');
Route::get('/heroes/{id}', [HeroController::class, 'show'])->name('heroes.show');
Route::post('/heroes', [HeroController::class, 'store'])->name('heroes.store');
```

---

## Paso 2: Añadir los métodos al controlador

### El método create()

`create()` tiene una única responsabilidad: devolver la vista con el formulario vacío. No consulta nada en la base de datos ni hace ningún cálculo.

```php
public function create()
{
    return view('heroes.create');
}
```

### El objeto Request

Antes de escribir `store()`, conviene entender cómo Laravel pone los datos del formulario a disposición del controlador.

Cuando el navegador envía el formulario, todos los campos viajan en el cuerpo de la petición HTTP. Laravel encapsula esa petición completa en un objeto de la clase `Illuminate\Http\Request`. Para acceder a ese objeto desde un método del controlador, basta con declararlo como parámetro:

```php
use Illuminate\Http\Request;

public function store(Request $request)
{
    // $request contiene todos los datos de la petición
}
```

Laravel inyecta el objeto automáticamente cuando ve ese parámetro tipado. Dentro del método puedes acceder a cualquier campo del formulario con `$request->nombre_del_campo`:

```php
$request->name;        // Valor del campo <input name="name">
$request->power;       // Valor del campo <input name="power">
$request->power_level; // Valor del campo <input name="power_level">
```

También puedes obtener todos los campos a la vez como array con `$request->all()`, aunque en la práctica es mejor pedir solo los que necesitas.

### La validación con validate()

Antes de guardar cualquier dato en la base de datos, es imprescindible validar que los datos recibidos tienen el formato y los valores esperados. Un campo que se espera numérico podría llegar vacío o con texto; un campo obligatorio podría no venir.

El método `validate()` del objeto `$request` recibe un array de reglas y comprueba que los datos del formulario las cumplen:

```php
$request->validate([
    'name'        => 'required|string|max:100',
    'power'       => 'required|string|max:150',
    'power_level' => 'required|integer|min:1|max:10000',
    'team'        => 'required|string|max:100',
    'real_name'   => 'nullable|string|max:100',
    'bio'         => 'nullable|string',
    'is_active'   => 'boolean',
]);
```

Cada clave es el nombre del campo y el valor es una cadena de reglas separadas por `|`. Las reglas más comunes son:

| Regla | Qué comprueba |
|-------|--------------|
| `required` | El campo no puede estar vacío |
| `nullable` | El campo puede estar vacío |
| `string` | El valor debe ser texto |
| `integer` | El valor debe ser un número entero |
| `boolean` | El valor debe ser true o false |
| `min:n` | Valor mínimo (en números) o longitud mínima (en texto) |
| `max:n` | Valor máximo (en números) o longitud máxima (en texto) |
| `email` | El valor debe tener formato de email |
| `unique:tabla` | El valor no debe existir ya en esa tabla |

**¿Qué ocurre si la validación falla?**

Laravel interrumpe la ejecución del método y redirige automáticamente al formulario de origen. Los errores de validación se almacenan en la sesión y quedan disponibles en la vista a través de la variable `$errors`. El usuario ve el mismo formulario con los mensajes de error, sin perder los datos que ya había introducido.

**¿Qué ocurre si la validación pasa?**

La ejecución continúa con la línea siguiente a `validate()`. Los datos han sido verificados y se puede proceder a guardarlos.

### El método store() completo

```php
public function store(Request $request)
{
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

    return redirect()->route('heroes.index');
}
```

`$request->boolean('is_active')` merece una nota. Los checkboxes HTML solo envían su valor cuando están marcados: si el checkbox no está marcado, el campo `is_active` directamente no llega en la petición. `boolean()` gestiona ese comportamiento devolviendo `true` si el campo está presente y marcado, y `false` en cualquier otro caso.

`redirect()->route('heroes.index')` redirige al usuario al listado de héroes tras guardar. Es una práctica estándar en formularios: después de un POST exitoso siempre se redirige, nunca se devuelve una vista directamente. Esto evita que al recargar la página el navegador pregunte si se quiere reenviar el formulario.

---

## Paso 3: Crear la vista del formulario

Antes de crear la vista, hay que añadir los estilos del formulario a `public/css/heroes.css`. Los formularios tienen sus propias clases para mantener coherencia visual con el resto del proyecto:

```css
/* ========================================
   FORMULARIOS
   ======================================== */
.form-card {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 10px;
    border-top: 5px solid #e23636;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    max-width: 700px;
    margin: 30px auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 6px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group textarea {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 5px;
    font-size: 1rem;
    font-family: inherit;
    color: #1a1a1a;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #e23636;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.form-error {
    display: block;
    color: #e23636;
    font-size: 0.85rem;
    margin-top: 5px;
    font-weight: 600;
}

.form-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 25px;
}

.form-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #e23636;
}

.form-checkbox label {
    font-weight: 600;
    color: #1a1a1a;
}

.btn-submit {
    display: inline-block;
    padding: 12px 30px;
    background-color: #e23636;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-submit:hover {
    background-color: #c42e2e;
    transform: translateY(-2px);
}

.btn-new {
    display: inline-block;
    padding: 10px 24px;
    background-color: #e23636;
    color: #ffffff;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 700;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.btn-new:hover {
    background-color: #c42e2e;
    transform: translateY(-2px);
}
```

Con los estilos añadidos, la vista queda así:

```html
{{-- resources/views/heroes/create.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Nuevo Héroe — Marvel Hub')

@section('contenido')
    <a href="{{ route('heroes.index') }}" class="back-link">&larr; Volver al listado</a>

    <div class="form-card">
        <h1>Nuevo Héroe</h1>

        <form action="{{ route('heroes.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="real_name">Nombre real</label>
                <input type="text" id="real_name" name="real_name" value="{{ old('real_name') }}">
                @error('real_name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="power">Poder</label>
                <input type="text" id="power" name="power" value="{{ old('power') }}">
                @error('power')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="power_level">Nivel de poder</label>
                <input type="number" id="power_level" name="power_level" min="1" max="10000" value="{{ old('power_level') }}">
                @error('power_level')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="team">Equipo</label>
                <input type="text" id="team" name="team" value="{{ old('team') }}">
                @error('team')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="bio">Biografía</label>
                <textarea id="bio" name="bio">{{ old('bio') }}</textarea>
                @error('bio')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-checkbox">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                <label for="is_active">Activo</label>
            </div>

            <button type="submit" class="btn-submit">Guardar héroe</button>
        </form>
    </div>
@endsection
```

### Análisis de la vista

**`.form-card` y `.form-group`**

`.form-card` envuelve el formulario y le da la misma estructura visual que `.hero-detail` en la vista de detalle: fondo blanco, borde superior rojo y sombra. `.form-group` agrupa cada campo con su etiqueta y su posible mensaje de error, manteniendo el espaciado uniforme entre campos.

**`action="{{ route('heroes.store') }}"`**

El atributo `action` indica la URL a la que se enviarán los datos al pulsar el botón. Se usa `route()` en lugar de una URL estática por la misma razón que en la navegación: si la ruta cambia, el formulario se actualiza solo.

**`method="POST"`**

Indica a HTML que los datos viajen en el cuerpo de la petición, no en la URL.

**`@csrf`**

Inserta el campo oculto con el token de seguridad. Sin esta línea, Laravel rechazará la petición con error 419.

**`value="{{ old('name') }}"`**

`old('name')` recupera el valor que el usuario había introducido en ese campo antes de que la validación fallara. Si el formulario se envía, la validación rechaza un campo y el usuario vuelve al formulario, encontrará los campos rellenos con los valores que ya había escrito, no un formulario vacío. Si el formulario se abre por primera vez, `old()` devuelve `null` y el campo aparece vacío.

**`@error('name') ... @enderror`**

Esta directiva comprueba si existe un error de validación para el campo `name`. Si existe, ejecuta el bloque y dentro pone a disposición la variable `$message` con el texto del error. El `<span class="form-error">` lo muestra en rojo bajo el campo. Si no existe ningún error para ese campo, el bloque se ignora completamente.

**`.form-checkbox` y `.btn-submit`**

`.form-checkbox` alinea horizontalmente el checkbox con su etiqueta. `.btn-submit` aplica al botón de envío el mismo estilo rojo que los botones de acción del resto del proyecto.

---

## Paso 4: Enlazar el formulario desde el listado

Para que el usuario pueda acceder al formulario, añade un enlace en `index.blade.php`:

```html
{{-- resources/views/heroes/index.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Héroes — Marvel Hub')

@section('contenido')
    <h1>Héroes</h1>

    <a href="{{ route('heroes.create') }}" class="btn-new">+ Nuevo héroe</a>

    @foreach($heroes as $hero)
        @include('partials.hero-card')
    @endforeach
@endsection
```

---

## Paso 5: Mostrar un mensaje tras guardar

Es buena práctica informar al usuario de que la operación se completó correctamente. Laravel permite pasar mensajes a través de la redirección usando `with()`:

```php
// En el controlador, al redirigir:
return redirect()->route('heroes.index')->with('success', 'Héroe creado correctamente.');
```

El mensaje viaja en la sesión y está disponible en la siguiente petición a través de la variable `session()`. En el layout, añade un bloque para mostrarlo cuando exista:

```html
{{-- En resources/views/layouts/app.blade.php, dentro de <main> --}}
<main>
    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    @yield('contenido')
</main>
```

Al colocarlo en el layout, cualquier redirección con `->with('success', '...')` mostrará automáticamente el mensaje en todas las páginas, sin necesitar añadir el bloque en cada vista.

---

## El controlador completo hasta esta fase

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero;

class HeroController extends Controller
{
    public function index()
    {
        $heroes = Hero::all();
        return view('heroes.index', compact('heroes'));
    }

    public function create()
    {
        return view('heroes.create');
    }

    public function store(Request $request)
    {
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
        $hero = Hero::findOrFail($id);
        return view('heroes.show', compact('hero'));
    }

    public function active()
    {
        $heroes = Hero::where('is_active', true)->get();
        return view('heroes.active', compact('heroes'));
    }

    public function powerful()
    {
        $heroes = Hero::where('power_level', '>', 8000)
            ->orderBy('power_level', 'desc')
            ->get();
        return view('heroes.powerful', compact('heroes'));
    }

    public function team($team)
    {
        $heroes = Hero::where('team', $team)->get();
        return view('heroes.team', compact('heroes', 'team'));
    }
}
```

---

## Ejercicio Práctico: Plataforma de Música

Añade la funcionalidad de crear nuevos álbumes al ejercicio de la Fase 5.

### Requisitos

1. Formulario accesible desde el listado principal
2. Campos: título, artista, año, género, número de canciones, valoración, disponible
3. Validaciones:
   - `titulo`, `artista`, `genero`: obligatorios, texto, máximo 150 caracteres
   - `año`: obligatorio, entero, entre 1900 y el año actual
   - `canciones`: obligatorio, entero, mínimo 1
   - `valoracion`: obligatorio, numérico, entre 0 y 10
   - `disponible`: booleano
4. Si la validación falla, el formulario muestra los errores y conserva los valores introducidos
5. Si la validación pasa, guarda el álbum y redirige al listado con mensaje de confirmación

### Tareas a realizar

**Tarea 1:** Añade las dos rutas necesarias a `web.php` en el orden correcto.

**Tarea 2:** Añade los métodos `create()` y `store()` a `AlbumController`.

**Tarea 3:** Crea la vista `resources/views/albumes/create.blade.php` con el formulario, `@csrf`, `old()` en cada campo y `@error` para los mensajes.

**Tarea 4:** Añade el enlace al formulario desde la vista `index.blade.php`.

**Tarea 5:** Añade el bloque de mensaje de éxito en el layout.

---

## Pistas y recordatorios

### Sobre la regla `numeric` para decimales

```php
'valoracion' => 'required|numeric|min:0|max:10',
```

Para campos decimales usa `numeric` en lugar de `integer`. `integer` rechazaría valores como `9.2`.

### Sobre el año máximo dinámico

```php
'año' => 'required|integer|min:1900|max:' . date('Y'),
```

`date('Y')` devuelve el año actual en PHP, de modo que la regla siempre valida hasta el año en curso sin necesitar actualizar el código.

### Sobre la estructura del formulario

```html
<form action="{{ route('albumes.store') }}" method="POST">
    @csrf

    <div>
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" value="{{ old('titulo') }}">
        @error('titulo')
            <span>{{ $message }}</span>
        @enderror
    </div>

    {{-- resto de campos --}}

    <button type="submit">Guardar álbum</button>
</form>
```

---

## Verificación

- [ ] Ruta `GET /albumes/create` definida antes de `GET /albumes/{id}`
- [ ] Ruta `POST /albumes` definida
- [ ] Método `create()` devuelve la vista del formulario
- [ ] Método `store()` valida antes de guardar
- [ ] El formulario incluye `@csrf`
- [ ] Cada campo usa `old()` para recuperar el valor previo
- [ ] Cada campo tiene su bloque `@error`
- [ ] Si la validación falla, el formulario muestra los errores con los campos rellenos
- [ ] Si la validación pasa, el álbum aparece en el listado
- [ ] Tras guardar se muestra un mensaje de confirmación
