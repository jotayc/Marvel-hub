# Fase 5: Layouts y Plantillas Blade

---

## De dónde partimos

Hasta la Fase 4 cada vista del proyecto Marvel Hub es un archivo independiente. Si imaginas cómo estarían escritas, todas repiten la misma estructura HTML:

```html
{{-- resources/views/heroes/index.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marvel Hub</title>
    <link rel="stylesheet" href="{{ asset('css/heroes.css') }}">
</head>
<body>
    <nav>
        <a href="{{ route('heroes.index') }}">Héroes</a>
    </nav>

    <main>
        @foreach($heroes as $hero)
            {{-- Contenido específico de esta vista --}}
        @endforeach
    </main>

    <footer>
        <p>Marvel Hub © 2025</p>
    </footer>
</body>
</html>
```

```html
{{-- resources/views/heroes/show.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marvel Hub</title>
    <link rel="stylesheet" href="{{ asset('css/heroes.css') }}">
</head>
<body>
    <nav>
        <a href="{{ route('heroes.index') }}">Héroes</a>
    </nav>

    <main>
        {{-- Contenido específico de esta vista --}}
    </main>

    <footer>
        <p>Marvel Hub © 2025</p>
    </footer>
</body>
</html>
```

El problema es evidente: `<head>`, `<nav>` y `<footer>` son idénticos en todas las vistas. Cualquier cambio en la navegación o en los estilos obliga a editar cada archivo por separado. Con cinco vistas es incómodo; con veinte, inviable.

En esta fase aprenderás a separar esa estructura HTML compartida en un único archivo llamado **layout**, de modo que cada vista solo contenga lo que la hace diferente del resto.

---

## Preparación: Crear una rama nueva

```bash
# Asegúrate de estar en la rama de la Fase 4
git checkout 4.Eloquent

# Crear la nueva rama y cambiar a ella
git checkout -b 5.Layouts
```

---

## El problema que resuelven los layouts

Cuando un sitio web tiene varias páginas con la misma cabecera, menú y pie de página, el código que construye esa estructura es siempre idéntico. Lo único que cambia de una página a otra es el **contenido principal**: la lista de héroes, el detalle de uno en concreto, los más poderosos. El resto —el `<head>`, la navegación, el footer— es exactamente el mismo.

Un **layout** es la solución a este problema. En lugar de repetir esa estructura en cada vista, se escribe una sola vez en un archivo dedicado, y se marcan los **huecos** donde cada vista insertará su contenido específico. Todas las vistas que usen ese layout heredan automáticamente su estructura.

La analogía es la de una plantilla de carta con membrete: el membrete siempre es el mismo, pero el cuerpo de la carta cambia en cada envío. El layout es el membrete; el contenido de cada vista es el cuerpo.

Blade implementa este patrón con directivas que trabajan en pareja:

- En el **layout** se declaran huecos con `@yield('nombre')`.
- En cada **vista** se indica qué layout se usa con `@extends('layouts.app')`, y se rellena cada hueco con `@section('nombre') ... @endsection`.

Cuando Blade renderiza una vista, lee primero el layout, localiza cada `@yield`, y sustituye ese marcador por el contenido que la vista ha definido en el `@section` correspondiente. El resultado es un único HTML completo que se envía al navegador.

---

## Paso 1: Crear el layout principal

Por convención, los layouts se guardan en `resources/views/layouts/`. Crea esa carpeta y dentro el archivo `app.blade.php`:

```
resources/
└── views/
    └── layouts/
        └── app.blade.php   ← nuevo
```

El nombre `app` es una convención habitual en Laravel, aunque puede llamarse de cualquier manera. Si un proyecto tuviese dos diseños distintos —por ejemplo, uno para la parte pública y otro para un panel de administración— tendría dos layouts: `app.blade.php` y `admin.blade.php`.

### Los estilos pasan a un archivo externo

Hasta la Fase 4 cada vista incluía sus estilos dentro de un bloque `<style>` en el propio archivo. Al crear el layout, ese bloque se repetiría una sola vez —lo cual ya es una mejora— pero podemos ir un paso más lejos: mover los estilos a un archivo CSS independiente en `public/css/heroes.css` y enlazarlo desde el layout con una etiqueta `<link>`.

Esto tiene dos ventajas concretas. La primera es que el layout queda limpio y legible, sin cientos de líneas de CSS mezcladas con HTML. La segunda es que el navegador puede cachear el archivo CSS y no necesita descargarlo en cada página, lo que mejora el rendimiento.

El archivo `public/css/heroes.css` ya contiene los estilos del proyecto. Para enlazarlo desde el layout se usa la función `asset()` de Laravel, que genera la URL correcta al directorio `public/`:

```html
<link rel="stylesheet" href="{{ asset('css/heroes.css') }}">
```

A partir de esta fase, ninguna vista necesita un bloque `<style>` propio. Todos los estilos viven en `heroes.css` y se cargan automáticamente a través del layout.

### El layout completo

```html
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Marvel Hub')</title>
    <link rel="stylesheet" href="{{ asset('css/heroes.css') }}">
</head>
<body>

    <nav>
        <a href="{{ route('heroes.index') }}">Héroes</a>
        <a href="{{ route('heroes.active') }}">Activos</a>
        <a href="{{ route('heroes.powerful') }}">Más poderosos</a>
    </nav>

    <main>
        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @yield('contenido')
    </main>

    <footer>
        <p>Marvel Hub &copy; 2025</p>
    </footer>

</body>
</html>
```

### Análisis del layout

**El hueco del título:**

```html
<title>@yield('titulo', 'Marvel Hub')</title>
```

`@yield('titulo', 'Marvel Hub')` define un hueco llamado `titulo` dentro de la etiqueta `<title>`. El segundo parámetro es el **valor por defecto**: si una vista no define su propio título, el navegador mostrará `Marvel Hub`. Cuando una vista sí lo define, mostrará el valor que ella indique. Esto permite que cada página tenga un título descriptivo en la pestaña del navegador sin que el layout imponga uno fijo para todas.

**El hueco del contenido:**

```html
<main>
    @yield('contenido')
</main>
```

Este es el hueco principal. Todo el HTML específico de cada vista —la lista de héroes, el detalle de uno, los filtros— se insertará aquí. A diferencia del título, este hueco no tiene valor por defecto, porque no tiene sentido mostrar una página sin contenido.

**La navegación usa rutas nombradas:**

```html
<a href="{{ route('heroes.index') }}">Héroes</a>
```

Al usar `route()` en lugar de URLs escritas a mano, si en algún momento cambias la URL de una ruta en `web.php`, los enlaces del layout se actualizan automáticamente en todas las páginas sin necesitar modificar el HTML.

---

## Paso 2: Adaptar las vistas para usar el layout

Con el layout creado, cada vista puede eliminar todo el HTML estructural y contener únicamente su contenido propio. El mecanismo son `@extends` y `@section`.

### Vista index.blade.php

**Antes** (estructura repetida, sin layout):

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Marvel Hub</title>
    <style>/* estilos repetidos */</style>
</head>
<body>
    <nav>...</nav>
    <main>
        <h1>Héroes</h1>
        @foreach($heroes as $hero)
            <div>{{ $hero->name }}</div>
        @endforeach
    </main>
    <footer>...</footer>
</body>
</html>
```

**Después** (usando el layout):

```html
{{-- resources/views/heroes/index.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Héroes — Marvel Hub')

@section('contenido')
    <h1>Héroes</h1>

    @foreach($heroes as $hero)
        <div class="hero-card">
            <h2>{{ $hero->name }}</h2>
            <p>{{ $hero->power }}</p>
            <p>Nivel: {{ $hero->power_level }}</p>
            <a href="{{ route('heroes.show', $hero->id) }}">Ver detalle</a>
        </div>
    @endforeach
@endsection
```

La reducción es drástica. La vista pasa de tener toda la estructura HTML a contener únicamente lo que la hace única: su título y su listado.

**`@extends('layouts.app')`**

Esta directiva debe aparecer en la primera línea de la vista. Le indica a Blade que esta vista no es un HTML autónomo, sino que forma parte del layout `resources/views/layouts/app.blade.php`. La ruta sigue la misma convención que `view()`: puntos en lugar de barras y sin extensión, por lo que `layouts.app` apunta a `resources/views/layouts/app.blade.php`.

**`@section('titulo', 'Héroes — Marvel Hub')`**

Rellena el hueco `titulo` del layout con el texto `Héroes — Marvel Hub`. Esta forma de una sola línea es válida cuando el contenido es texto simple. El HTML resultante será `<title>Héroes — Marvel Hub</title>`.

**`@section('contenido') ... @endsection`**

Todo lo que escribas entre `@section('contenido')` y `@endsection` se insertará donde el layout tiene `@yield('contenido')`. No hay límite en la complejidad de este bloque: puede contener HTML, directivas Blade, bucles y condicionales.

---

### Vista show.blade.php

```html
{{-- resources/views/heroes/show.blade.php --}}
@extends('layouts.app')

@section('titulo', $hero->name . ' — Marvel Hub')

@section('contenido')
    <a href="{{ route('heroes.index') }}" class="back-link">&larr; Volver al listado</a>

    <div class="hero-detail">
        <h1>{{ $hero->name }}</h1>

        <div class="info-row">
            <span class="label">Nombre real</span>
            <span class="value">{{ $hero->real_name }}</span>
        </div>

        <div class="info-row">
            <span class="label">Poder</span>
            <span class="value">{{ $hero->power }}</span>
        </div>

        <div class="info-row">
            <span class="label">Nivel de poder</span>
            <span class="value">{{ $hero->power_level }}</span>
        </div>

        <div class="info-row">
            <span class="label">Equipo</span>
            <span class="value">{{ $hero->team }}</span>
        </div>

        <div class="info-row">
            <span class="label">Biografía</span>
            <span class="value">{{ $hero->bio }}</span>
        </div>

        <div class="info-row">
            <span class="label">Estado</span>
            <span class="value">
                @if($hero->is_active)
                    Activo
                @else
                    Inactivo
                @endif
            </span>
        </div>
    </div>
@endsection
```

Aquí el título del hueco es una expresión PHP en lugar de texto fijo:

```html
@section('titulo', $hero->name . ' — Marvel Hub')
```

Blade evalúa la expresión en el momento de renderizar, de modo que si el héroe es Thor, el título resultante será `Thor — Marvel Hub`. Las variables que el controlador ha pasado a la vista —en este caso `$hero`— están disponibles dentro de todos los `@section`.

---

### Vista active.blade.php

```html
{{-- resources/views/heroes/active.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Héroes Activos — Marvel Hub')

@section('contenido')
    <h1>Héroes Activos</h1>

    @forelse($heroes as $hero)
        <div class="hero-card">
            <h2>{{ $hero->name }}</h2>
            <p>{{ $hero->power }}</p>
            <a href="{{ route('heroes.show', $hero->id) }}">Ver detalle</a>
        </div>
    @empty
        <p>No hay héroes activos en este momento.</p>
    @endforelse
@endsection
```

Aquí aparece `@forelse`, una directiva de Blade que merece atención. Es una variante de `@foreach` que incorpora un bloque `@empty` para gestionar el caso en que la colección esté vacía.

Sin `@forelse`, manejar una colección vacía requería combinar dos directivas:

```html
{{-- Sin @forelse --}}
@if($heroes->isEmpty())
    <p>No hay héroes activos.</p>
@else
    @foreach($heroes as $hero)
        <div>{{ $hero->name }}</div>
    @endforeach
@endif
```

Con `@forelse` el mismo resultado es más limpio:

```html
{{-- Con @forelse --}}
@forelse($heroes as $hero)
    <div>{{ $hero->name }}</div>
@empty
    <p>No hay héroes activos.</p>
@endforelse
```

El bloque `@empty` solo se ejecuta cuando la colección no tiene ningún elemento. Si tiene al menos uno, se ejecuta el bucle y `@empty` se ignora por completo.

---

### Vista powerful.blade.php

```html
{{-- resources/views/heroes/powerful.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Héroes Más Poderosos — Marvel Hub')

@section('contenido')
    <h1>Héroes Más Poderosos</h1>
    <p>Héroes con nivel de poder superior a 8000, ordenados de mayor a menor.</p>

    @forelse($heroes as $hero)
        <div class="hero-card">
            <h2>{{ $hero->name }}</h2>
            <p>Nivel: {{ $hero->power_level }}</p>
            <a href="{{ route('heroes.show', $hero->id) }}">Ver detalle</a>
        </div>
    @empty
        <p>No hay héroes con ese nivel de poder.</p>
    @endforelse
@endsection
```

---

## Paso 3: Extraer partes reutilizables con @include

Fíjate en que `index.blade.php`, `active.blade.php` y `powerful.blade.php` repiten el mismo bloque HTML para mostrar cada héroe:

```html
<div class="hero-card">
    <h2>{{ $hero->name }}</h2>
    <p>{{ $hero->power }}</p>
    <a href="{{ route('heroes.show', $hero->id) }}">Ver detalle</a>
</div>
```

El mismo problema de antes, pero a menor escala: si necesitas cambiar el aspecto de la tarjeta de héroe, tienes que editar tres archivos. Blade ofrece `@include` para resolver esto.

Un **parcial** es un fragmento de vista almacenado en su propio archivo que puede insertarse en cualquier otra vista. No tiene `@extends` ni `@section`: es simplemente un trozo de HTML reutilizable.

### Crear el parcial

La convención habitual es guardar los parciales en `resources/views/partials/`. La estructura de carpetas queda así:

```
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php
    ├── heroes/
    │   ├── index.blade.php
    │   ├── show.blade.php
    │   ├── active.blade.php
    │   └── powerful.blade.php
    └── partials/
        └── hero-card.blade.php   ← nuevo
```

```html
{{-- resources/views/partials/hero-card.blade.php --}}
<div class="hero-card">
    <h2>{{ $hero->name }}</h2>
    <p>{{ $hero->power }}</p>
    <p>Nivel: {{ $hero->power_level }}</p>
    <a href="{{ route('heroes.show', $hero->id) }}">Ver detalle</a>
</div>
```

El parcial usa `$hero` directamente sin recibirla como parámetro. Esto es posible porque `@include` comparte automáticamente el contexto de variables de la vista que lo llama: si en ese punto del bucle la vista tiene acceso a `$hero`, el parcial también lo tiene.

### Usar el parcial en las vistas

```html
{{-- resources/views/heroes/index.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Héroes — Marvel Hub')

@section('contenido')
    <h1>Héroes</h1>

    @foreach($heroes as $hero)
        @include('partials.hero-card')
    @endforeach
@endsection
```

`@include('partials.hero-card')` sigue la misma convención de puntos: busca `resources/views/partials/hero-card.blade.php` y lo inserta en ese punto del HTML. En cada iteración del `@foreach`, la variable `$hero` tiene el valor del héroe actual, y el parcial la usa para renderizar esa tarjeta concreta.

Ahora si necesitas cambiar el diseño de la tarjeta de héroe, solo tienes que editar `partials/hero-card.blade.php` y el cambio se aplica en todas las vistas que lo incluyen.

### @section vs @include

Ambos insertan contenido en la vista, pero su mecanismo es distinto y responden a necesidades diferentes.

`@section` rellena un hueco que el layout ha declarado con `@yield`. Es el layout quien decide dónde va ese contenido: la vista simplemente lo proporciona. Solo puede existir un `@section('contenido')` por vista, y aparecerá exactamente donde el layout tiene su `@yield('contenido')`.

`@include` inserta un parcial en el punto exacto donde tú lo escribes, dentro del contenido que ya estás construyendo. No hay ningún `@yield` esperando ese fragmento en ningún sitio. Tú decides dónde, cuándo y cuántas veces lo insertas.

```html
@section('contenido')
    <h1>Héroes</h1>                        {{-- va al @yield del layout --}}

    @foreach($heroes as $hero)
        @include('partials.hero-card')     {{-- se inserta aquí mismo, en cada iteración --}}
    @endforeach
@endsection
```

En resumen: `@section` comunica la vista con su layout, `@include` reutiliza fragmentos dentro del contenido que la vista ya está construyendo.

---

## Cómo funciona el proceso completo

Cuando el navegador solicita `/heroes`, los pasos que ocurren son los siguientes:

**1.** El router recibe la petición y llama a `HeroController@index`.

**2.** El controlador consulta la base de datos y retorna la vista:

```php
$heroes = Hero::all();
return view('heroes.index', compact('heroes'));
```

**3.** Blade lee `resources/views/heroes/index.blade.php` y encuentra `@extends('layouts.app')`. En ese momento sabe que esta vista no es autónoma y necesita el layout para construir el HTML completo.

**4.** Blade lee `resources/views/layouts/app.blade.php` y toma nota de todos los `@yield` que contiene: `titulo` y `contenido`.

**5.** Blade vuelve a la vista y recoge el contenido de cada `@section`. Donde encuentre `@include`, inserta el parcial correspondiente compartiendo el contexto de variables.

**6.** Blade construye el HTML final sustituyendo cada `@yield` del layout por el `@section` correspondiente de la vista.

**7.** El HTML resultante —completo, con `<head>`, navegación, contenido y footer— se envía al navegador.

El controlador no sabe nada de este proceso: su trabajo termina en el paso 2. Toda la composición de la página ocurre dentro de la capa de vistas, lo que mantiene cada capa con una responsabilidad clara y separada.

---

## Resumen de directivas

| Directiva | Archivo | Qué hace |
|-----------|---------|----------|
| `@yield('nombre')` | Layout | Marca un hueco que las vistas rellenarán |
| `@yield('nombre', 'defecto')` | Layout | Hueco con valor cuando la vista no lo define |
| `@extends('layouts.app')` | Vista | Indica qué layout usa esta vista |
| `@section('nombre', 'valor')` | Vista | Rellena un hueco con una línea de texto o expresión |
| `@section('nombre')` / `@endsection` | Vista | Rellena un hueco con un bloque HTML completo |
| `@include('ruta.parcial')` | Vista | Inserta un parcial compartiendo el contexto de variables |
| `@forelse` / `@empty` / `@endforelse` | Vista | Bucle con bloque alternativo para colección vacía |

---

## Ejercicio Práctico: Plataforma de Música

Eres desarrollador en una plataforma de streaming de música. Te piden crear un sistema para mostrar el catálogo de álbumes clásicos con un diseño coherente en todas las páginas.

### Datos de ejemplo

Crea la base de datos `musica` con la tabla `albumes` y estos 10 registros:

| Título | Artista | Año | Género | Canciones | Valoración | Disponible |
|--------|---------|-----|--------|-----------|------------|------------|
| Thriller | Michael Jackson | 1982 | Pop | 9 | 9.5 | Sí |
| Back in Black | AC/DC | 1980 | Rock | 10 | 9.2 | Sí |
| The Dark Side of the Moon | Pink Floyd | 1973 | Rock | 10 | 9.4 | No |
| Abbey Road | The Beatles | 1969 | Rock | 17 | 9.3 | Sí |
| Rumours | Fleetwood Mac | 1977 | Pop Rock | 11 | 9.1 | Sí |
| Born to Run | Bruce Springsteen | 1975 | Rock | 8 | 8.9 | No |
| Purple Rain | Prince | 1984 | Pop | 9 | 9.0 | Sí |
| Nevermind | Nirvana | 1991 | Grunge | 12 | 9.2 | Sí |
| What's Going On | Marvin Gaye | 1971 | Soul | 9 | 9.3 | No |
| Kind of Blue | Miles Davis | 1959 | Jazz | 5 | 9.4 | Sí |

### Requisitos del cliente

1. Listado completo de álbumes
2. Ficha detallada de cada álbum
3. Álbumes disponibles para escuchar
4. Mejores álbumes (valoración ≥ 9.2)
5. Filtro por género

Todos los apartados deben compartir el mismo layout con navegación entre secciones.

### Tareas a realizar

**Tarea 1:** Crea la base de datos `musica` con la tabla `albumes` e inserta los 10 registros. Configura `.env` y verifica con Tinker que los datos son accesibles.

**Tarea 2:** Crea el modelo `Album` con `$table`, `$fillable` y `$casts` correctos.

**Tarea 3:** Crea `AlbumController` con los métodos: `index()`, `show()`, `disponibles()`, `mejores()`, `genero($genero)`.

**Tarea 4:** Define las rutas en `routes/web.php`. Recuerda el orden correcto: rutas específicas antes de rutas con parámetros.

**Tarea 5:** Crea el layout en `resources/views/layouts/app.blade.php` con navegación que enlace todas las secciones del catálogo.

**Tarea 6:** Crea las 5 vistas usando `@extends` y `@section`. Usa `@forelse` en los listados con un mensaje en `@empty`.

**Tarea 7:** Extrae la tarjeta de álbum en un parcial `resources/views/partials/album-card.blade.php` e inclúyelo con `@include` en las vistas que listan álbumes.

---

## Pistas y recordatorios

### Sobre el modelo

```php
class Album extends Model
{
    protected $table = 'albumes';

    protected $fillable = [
        'titulo',
        'artista',
        'año',
        'genero',
        'canciones',
        'valoracion',
        'disponible'
    ];

    protected $casts = [
        'año'        => 'integer',
        'canciones'  => 'integer',
        'valoracion' => 'decimal:1',
        'disponible' => 'boolean'
    ];
}
```

### Sobre el orden de rutas

```php
// ✅ Las rutas específicas siempre antes que las rutas con parámetros
Route::get('/albumes', [AlbumController::class, 'index'])->name('albumes.index');
Route::get('/albumes/disponibles', [AlbumController::class, 'disponibles'])->name('albumes.disponibles');
Route::get('/albumes/mejores', [AlbumController::class, 'mejores'])->name('albumes.mejores');
Route::get('/albumes/genero/{genero}', [AlbumController::class, 'genero'])->name('albumes.genero');
Route::get('/albumes/{id}', [AlbumController::class, 'show'])->name('albumes.show');
```

### Sobre la estructura de cada vista

```html
@extends('layouts.app')

@section('titulo', 'Catálogo — Música')

@section('contenido')
    {{-- Solo el contenido específico de esta vista --}}
@endsection
```

### Sobre @forelse

```html
@forelse($albumes as $album)
    @include('partials.album-card')
@empty
    <p>No hay álbumes disponibles.</p>
@endforelse
```

---

## Verificación

- [ ] Base de datos `musica` creada con 10 álbumes
- [ ] Modelo `Album` con `$fillable` y `$casts` correctos
- [ ] `AlbumController` con 5 métodos funcionales
- [ ] Rutas definidas en el orden correcto (específicas antes de `{id}`)
- [ ] Layout creado en `resources/views/layouts/app.blade.php`
- [ ] Las 5 vistas comienzan con `@extends('layouts.app')`
- [ ] El título del navegador cambia en cada página
- [ ] Los listados usan `@forelse` con mensaje en `@empty`
- [ ] Parcial `album-card.blade.php` creado e incluido con `@include`
- [ ] La navegación del layout enlaza todas las secciones
- [ ] Modificar el layout afecta a todas las páginas simultáneamente
