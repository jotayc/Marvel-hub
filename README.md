# FASE 1: Rutas y Vistas Básicas

---

## Introducción

En esta fase aprenderemos a crear las rutas de nuestra aplicación y a mostrar contenido en el navegador mediante vistas. Al finalizar, seremos capaces de crear páginas personalizadas que respondan a URLs específicas.

---

## ¿Qué es una Ruta?

Una **ruta** es la definición de cómo debe responder la aplicación cuando se solicita una URL específica.

**Ejemplo cotidiano:**

Imagina un hotel con un sistema de habitaciones:
- Si pides la habitación 101 → Te llevan a la habitación 101
- Si pides la recepción → Te llevan a la recepción
- Si pides el restaurante → Te llevan al restaurante

Las rutas en Laravel funcionan igual:
- Si solicitas `/` → Muestra la página de inicio
- Si solicitas `/heroes` → Muestra la lista de héroes
- Si solicitas `/about` → Muestra la página "Acerca de"

**Componentes de una ruta:**

Una ruta tiene tres elementos principales:

1. **Método HTTP:** GET, POST, PUT, DELETE, etc.
2. **URI (URL):** La dirección que se solicita (`/`, `/heroes`, `/about`)
3. **Acción:** Qué hacer cuando se solicita esa URL

**Formato básico en Laravel:**

```php
Route::metodo('uri', acción);
```

Ejemplos:

```php
Route::get('/', función o controlador);
Route::post('/heroes', función o controlador);
```

---

## Archivo de Rutas

En Laravel, todas las rutas web se definen en un archivo específico.

**Ubicación:** `routes/web.php`

Este archivo ya existe en tu proyecto Laravel. Ábrelo:

```bash
notepad C:\MAMP\htdocs\marvel-hub\routes\web.php
```

Verás algo como esto:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
```

**Explicación del código:**

- `use Illuminate\Support\Facades\Route;` → Importa la clase Route para poder usarla
- `Route::get('/', ...)` → Define una ruta que responde a peticiones GET en la URL raíz `/`
- `function () { ... }` → Función anónima que se ejecuta cuando se accede a esa ruta
- `return view('welcome');` → Retorna la vista `welcome.blade.php`

---

## Primera Ruta Simple

Vamos a crear nuestra primera ruta personalizada que muestre un mensaje simple.

**Paso 1:** Abre el archivo `routes/web.php`

**Paso 2:** Agrega esta ruta debajo de la ruta existente:

```php
Route::get('/heroes', function () {
    return 'Bienvenido a Marvel Hub - Lista de Héroes';
});
```

**Archivo completo:**

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/heroes', function () {
    return 'Bienvenido a Marvel Hub - Lista de Héroes';
});
```

**Paso 3:** Guarda el archivo

**Paso 4:** Accede desde el navegador

Asegúrate de que el servidor esté corriendo:

```bash
php artisan serve
```

Abre el navegador y ve a:
```
http://localhost:8000/heroes
```

Deberías ver el texto: **Bienvenido a Marvel Hub - Lista de Héroes**

### ¿Qué acabamos de hacer?

1. Definimos una ruta que escucha peticiones GET en `/heroes`
2. Cuando alguien accede a esa URL, Laravel ejecuta la función
3. La función retorna un texto simple
4. Laravel envía ese texto como respuesta HTTP al navegador

---

## Múltiples Rutas

Podemos definir tantas rutas como necesitemos. Vamos a crear varias:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/heroes', function () {
    return 'Lista de Héroes Marvel';
});

Route::get('/about', function () {
    return 'Acerca de Marvel Hub';
});

Route::get('/contact', function () {
    return 'Página de Contacto';
});
```

Prueba accediendo a:
- http://localhost:8000/heroes
- http://localhost:8000/about
- http://localhost:8000/contact

Cada URL muestra su contenido correspondiente.

---

## ¿Qué es una Vista?

Hasta ahora hemos retornado texto simple desde las rutas. Sin embargo, las aplicaciones web reales necesitan HTML completo con estilos, imágenes, etc.

Una **vista** es un archivo que contiene código HTML (y opcionalmente CSS, JavaScript) que se envía al navegador.

**¿Por qué separar las vistas?**

Imagina que tienes que mostrar una página compleja con 100 líneas de HTML. No querrás escribir todo ese HTML dentro del archivo de rutas. Sería un desastre.

**Solución:** Crear archivos separados (vistas) que contengan el HTML.

**Ventajas:**
- Código organizado y limpio
- Reutilización de plantillas
- Separación entre lógica (rutas) y presentación (vistas)
- Más fácil de mantener

---

## Sistema de Plantillas Blade

Laravel incluye un motor de plantillas llamado **Blade** que facilita la creación de vistas.

**¿Qué es Blade?**

Blade es un sistema que permite:
- Escribir HTML normal
- Insertar variables PHP de forma sencilla
- Usar estructuras de control (if, foreach, etc.)
- Crear plantillas reutilizables

**Características:**
- Los archivos Blade tienen extensión `.blade.php`
- Se guardan en `resources/views/`
- Se compilan automáticamente a PHP puro

**Sintaxis básica de Blade:**

```blade
{{ $variable }}          <!-- Muestra el valor de $variable (escapado) -->
{!! $variable !!}        <!-- Muestra HTML sin escapar (cuidado) -->
@if (condición)          <!-- Estructura if -->
@foreach ($items as $item) <!-- Bucle foreach -->
```

No te preocupes, iremos viendo cada parte progresivamente.

---

## Ubicación de las Vistas

Las vistas se guardan en la carpeta:

```
resources/views/
```

Laravel busca automáticamente en esta carpeta cuando usas `view('nombre')`.

**Convenciones:**
- Los archivos Blade terminan en `.blade.php`
- Se pueden organizar en subcarpetas
- El nombre del archivo (sin `.blade.php`) es el que usas en `view()`

**Ejemplos:**

| Archivo | Ruta en código |
|---------|----------------|
| `resources/views/welcome.blade.php` | `view('welcome')` |
| `resources/views/heroes.blade.php` | `view('heroes')` |
| `resources/views/heroes/index.blade.php` | `view('heroes.index')` |
| `resources/views/heroes/show.blade.php` | `view('heroes.show')` |

**Nota:** El punto `.` representa una carpeta.

---

## Crear la Primera Vista

Vamos a crear una vista para nuestra página de héroes.

**Paso 1:** Crear el archivo de vista

Navega a la carpeta `resources/views/` y crea un archivo llamado `heroes.blade.php`

```bash
notepad C:\MAMP\htdocs\marvel-hub\resources\views\heroes.blade.php
```

**Paso 2:** Agregar contenido HTML

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Héroes Marvel</title>
</head>
<body>
    <h1>Héroes de Marvel</h1>
    <p>Bienvenido al universo Marvel</p>
</body>
</html>
```

**Paso 3:** Guardar el archivo

**Paso 4:** Modificar la ruta para usar la vista

Abre `routes/web.php` y modifica la ruta `/heroes`:

```php
Route::get('/heroes', function () {
    return view('heroes');
});
```

**Paso 5:** Probar en el navegador

Accede a: http://localhost:8000/heroes

Ahora verás una página HTML completa en lugar de solo texto.

---

## Pasar Datos a las Vistas

Las vistas son más útiles cuando pueden mostrar datos dinámicos.

### Sintaxis para pasar datos

Existen dos formas de pasar datos desde una ruta a una vista:

**Forma 1: Array asociativo**

```php
Route::get('/ruta', function () {
    return view('vista', ['variable' => 'valor']);
});
```

**Forma 2: Método with()**

```php
Route::get('/ruta', function () {
    return view('vista')->with('variable', 'valor');
});
```

Ambas formas son válidas. Usaremos la primera por ser más concisa.

### Ejemplo práctico

**Paso 1:** Modificar la ruta para pasar un dato

Edita `routes/web.php`:

```php
Route::get('/heroes', function () {
    $titulo = 'Héroes de Marvel';
    return view('heroes', ['titulo' => $titulo]);
});
```

**Paso 2:** Usar el dato en la vista

Edita `resources/views/heroes.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <p>Bienvenido al universo Marvel</p>
</body>
</html>
```

**Explicación:**

- En la ruta: Definimos `$titulo = 'Héroes de Marvel'`
- Pasamos el dato: `['titulo' => $titulo]`
- En la vista: Usamos `{{ $titulo }}` para mostrar el valor

**Importante:** Las llaves dobles `{{ }}` son sintaxis de Blade y equivalen a `<?php echo htmlspecialchars($titulo); ?>`

**Paso 3:** Recargar el navegador

El título de la página ahora viene desde la ruta, no está fijo en la vista.

---

## Pasar Múltiples Datos

Podemos pasar varios datos a la vez.

**Modificar la ruta:**

```php
Route::get('/heroes', function () {
    $titulo = 'Héroes de Marvel';
    $descripcion = 'Los vengadores más poderosos de la Tierra';
    $total = 5;
    
    return view('heroes', [
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'total' => $total
    ]);
});
```

**Modificar la vista:**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <p>{{ $descripcion }}</p>
    <p>Total de héroes: {{ $total }}</p>
</body>
</html>
```

Todas las variables pasadas están disponibles en la vista.

---

## Pasar Arrays de Datos

Podemos pasar arrays y recorrerlos en la vista.

**Modificar la ruta:**

```php
Route::get('/heroes', function () {
    $heroes = ['Iron Man', 'Thor', 'Spider-Man', 'Hulk', 'Doctor Strange'];
    
    return view('heroes', ['heroes' => $heroes]);
});
```

**Modificar la vista con @foreach:**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Héroes Marvel</title>
</head>
<body>
    <h1>Héroes de Marvel</h1>
    
    <ul>
        @foreach($heroes as $hero)
            <li>{{ $hero }}</li>
        @endforeach
    </ul>
</body>
</html>
```

**Explicación de @foreach:**

- `@foreach($heroes as $hero)` → Inicia el bucle
- `{{ $hero }}` → Muestra cada héroe
- `@endforeach` → Cierra el bucle

**Resultado en el navegador:**

```
Héroes de Marvel

• Iron Man
• Thor
• Spider-Man
• Hulk
• Doctor Strange
```

---

## Directivas Blade Básicas

Blade ofrece directivas que facilitan el trabajo con estructuras de control.

### @if, @else, @endif

**Sintaxis:**

```blade
@if (condición)
    <!-- código si es verdadero -->
@else
    <!-- código si es falso -->
@endif
```

**Ejemplo en la vista:**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Héroes Marvel</title>
</head>
<body>
    <h1>Héroes de Marvel</h1>
    
    @if (count($heroes) > 0)
        <ul>
            @foreach($heroes as $hero)
                <li>{{ $hero }}</li>
            @endforeach
        </ul>
    @else
        <p>No hay héroes disponibles</p>
    @endif
</body>
</html>
```

### @foreach

Ya lo vimos anteriormente. Recorre arrays.

```blade
@foreach($items as $item)
    {{ $item }}
@endforeach
```

### @for

Bucle tradicional for.

```blade
@for ($i = 0; $i < 10; $i++)
    <p>Iteración {{ $i }}</p>
@endfor
```

### Comentarios en Blade

```blade
{{-- Este es un comentario en Blade --}}
{{-- No se mostrará en el HTML final --}}
```

---

## Escapado de HTML

Por seguridad, Blade escapa automáticamente el contenido de las variables.

**¿Qué significa "escapar"?**

Convertir caracteres especiales HTML en entidades HTML para evitar inyección de código.

**Ejemplo:**

Si `$texto = '<script>alert("Hack")</script>';`

```blade
{{ $texto }}
<!-- Resultado: &lt;script&gt;alert("Hack")&lt;/script&gt; -->
<!-- Se muestra como texto, no se ejecuta -->
```

**¿Cuándo NO escapar?**

Solo cuando confías 100% en el contenido y necesitas mostrar HTML.

```blade
{!! $contenidoHTML !!}
```

**Advertencia:** Usar `{!! !!}` con datos de usuario puede causar vulnerabilidades XSS. Úsalo solo con contenido confiable.

---

## Ejemplo Completo: Lista de Héroes con Datos

Vamos a crear una ruta y vista más completa.

**Ruta en `routes/web.php`:**

```php
Route::get('/heroes', function () {
    $heroes = [
        ['nombre' => 'Iron Man', 'poder' => 'Tecnología avanzada'],
        ['nombre' => 'Thor', 'poder' => 'Dios del Trueno'],
        ['nombre' => 'Spider-Man', 'poder' => 'Sentido arácnido'],
        ['nombre' => 'Hulk', 'poder' => 'Fuerza sobrehumana'],
        ['nombre' => 'Doctor Strange', 'poder' => 'Hechicería']
    ];
    
    return view('heroes', ['heroes' => $heroes]);
});
```

**Vista en `resources/views/heroes.blade.php`:**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Héroes Marvel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #e23636;
        }
        .hero-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .hero-name {
            font-weight: bold;
            font-size: 1.2em;
            color: #333;
        }
        .hero-power {
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Héroes de Marvel</h1>
    
    @if (count($heroes) > 0)
        @foreach($heroes as $hero)
            <div class="hero-card">
                <div class="hero-name">{{ $hero['nombre'] }}</div>
                <div class="hero-power">Poder: {{ $hero['poder'] }}</div>
            </div>
        @endforeach
    @else
        <p>No hay héroes disponibles</p>
    @endif
</body>
</html>
```

**Resultado:** Una página con 5 tarjetas mostrando cada héroe y su poder.

---

## Organizar Vistas en Subcarpetas

A medida que la aplicación crece, es útil organizar las vistas en carpetas.

**Estructura recomendada:**

```
resources/views/
├── welcome.blade.php
├── heroes/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── create.blade.php
└── about.blade.php
```

**Crear la carpeta heroes:**

```bash
mkdir C:\MAMP\htdocs\marvel-hub\resources\views\heroes
```

**Mover la vista heroes.blade.php:**

Renombra y mueve:
- De: `resources/views/heroes.blade.php`
- A: `resources/views/heroes/index.blade.php`

**Actualizar la ruta:**

```php
Route::get('/heroes', function () {
    $heroes = [
        ['nombre' => 'Iron Man', 'poder' => 'Tecnología avanzada'],
        ['nombre' => 'Thor', 'poder' => 'Dios del Trueno'],
        ['nombre' => 'Spider-Man', 'poder' => 'Sentido arácnido'],
        ['nombre' => 'Hulk', 'poder' => 'Fuerza sobrehumana'],
        ['nombre' => 'Doctor Strange', 'poder' => 'Hechicería']
    ];
    
    return view('heroes.index', ['heroes' => $heroes]);
});
```

**Nota:** Usamos `heroes.index` en lugar de `heroes` para indicar la subcarpeta.

---

## Rutas con Parámetros

Las rutas pueden recibir parámetros dinámicos.

**Sintaxis:**

```php
Route::get('/ruta/{parametro}', function ($parametro) {
    // Usar $parametro
});
```

**Ejemplo: Ver detalle de un héroe por ID**

```php
Route::get('/heroes/{id}', function ($id) {
    return "Mostrando héroe con ID: " . $id;
});
```

Prueba accediendo a:
- http://localhost:8000/heroes/1 → Muestra: "Mostrando héroe con ID: 1"
- http://localhost:8000/heroes/5 → Muestra: "Mostrando héroe con ID: 5"

**Con vista:**

```php
Route::get('/heroes/{id}', function ($id) {
    $heroes = [
        1 => ['nombre' => 'Iron Man', 'poder' => 'Tecnología avanzada'],
        2 => ['nombre' => 'Thor', 'poder' => 'Dios del Trueno'],
        3 => ['nombre' => 'Spider-Man', 'poder' => 'Sentido arácnido']
    ];
    
    $hero = $heroes[$id] ?? null;
    
    return view('heroes.show', ['hero' => $hero]);
});
```

**Vista `resources/views/heroes/show.blade.php`:**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Héroe</title>
</head>
<body>
    @if ($hero)
        <h1>{{ $hero['nombre'] }}</h1>
        <p>Poder: {{ $hero['poder'] }}</p>
    @else
        <p>Héroe no encontrado</p>
    @endif
</body>
</html>
```

---

## Rutas Nombradas

Podemos asignar nombres a las rutas para referenciarlas fácilmente.

**Sintaxis:**

```php
Route::get('/ruta', función)->name('nombre.ruta');
```

**Ejemplo:**

```php
Route::get('/heroes', function () {
    // ...
})->name('heroes.index');

Route::get('/heroes/{id}', function ($id) {
    // ...
})->name('heroes.show');
```

**Ventaja:** Si cambias la URL, no necesitas cambiar todos los enlaces en las vistas.

**Generar URL en vistas:**

```blade
<a href="{{ route('heroes.index') }}">Ver todos los héroes</a>
<a href="{{ route('heroes.show', ['id' => 1]) }}">Ver Iron Man</a>
```

---

## Ver Todas las Rutas Definidas

Laravel incluye un comando Artisan para ver todas las rutas.

```bash
php artisan route:list
```

Verás una tabla con:
- Método HTTP (GET, POST, etc.)
- URI (la URL)
- Nombre de la ruta
- Acción (función o controlador)

Útil para verificar que las rutas están correctamente definidas.

---

## Archivo routes/web.php Completo

Aquí está el archivo completo con todas las rutas de ejemplo:

```php
<?php

use Illuminate\Support\Facades\Route;

// Ruta de inicio
Route::get('/', function () {
    return view('welcome');
});

// Lista de héroes
Route::get('/heroes', function () {
    $heroes = [
        ['nombre' => 'Iron Man', 'poder' => 'Tecnología avanzada'],
        ['nombre' => 'Thor', 'poder' => 'Dios del Trueno'],
        ['nombre' => 'Spider-Man', 'poder' => 'Sentido arácnido'],
        ['nombre' => 'Hulk', 'poder' => 'Fuerza sobrehumana'],
        ['nombre' => 'Doctor Strange', 'poder' => 'Hechicería']
    ];
    
    return view('heroes.index', ['heroes' => $heroes]);
})->name('heroes.index');

// Detalle de héroe
Route::get('/heroes/{id}', function ($id) {
    $heroes = [
        1 => ['nombre' => 'Iron Man', 'poder' => 'Tecnología avanzada'],
        2 => ['nombre' => 'Thor', 'poder' => 'Dios del Trueno'],
        3 => ['nombre' => 'Spider-Man', 'poder' => 'Sentido arácnido']
    ];
    
    $hero = $heroes[$id] ?? null;
    
    return view('heroes.show', ['hero' => $hero]);
})->name('heroes.show');

// Página acerca de
Route::get('/about', function () {
    return view('about');
})->name('about');
```

---

## Resumen de la Fase 1

### ¿Qué hemos aprendido?

1. **Rutas**
   - Qué son y para qué sirven
   - Archivo `routes/web.php`
   - Definir rutas con `Route::get()`
   - Rutas con parámetros dinámicos
   - Rutas nombradas con `->name()`

2. **Vistas**
   - Qué son las vistas
   - Sistema de plantillas Blade
   - Ubicación: `resources/views/`
   - Extensión: `.blade.php`
   - Organización en subcarpetas

3. **Blade**
   - Sintaxis básica: `{{ $variable }}`
   - Directivas: `@if`, `@foreach`, `@for`
   - Comentarios: `{{-- --}}`
   - Escapado de HTML: `{{ }}` vs `{!! !!}`

4. **Pasar datos**
   - Desde rutas a vistas
   - Arrays asociativos
   - Múltiples variables
   - Arrays de datos

---

## Resolución de Problemas Comunes

### Error: "View [nombre] not found"

**Causa:** Laravel no encuentra la vista.

**Solución:**
- Verifica que el archivo existe en `resources/views/`
- Verifica que el nombre es correcto (sin `.blade.php`)
- Revisa la estructura de carpetas (usa `.` para subcarpetas)

### Error: "Undefined variable $variable"

**Causa:** Intentas usar una variable en la vista que no fue pasada desde la ruta.

**Solución:**
- Verifica que pasas la variable en el array: `['variable' => $valor]`
- Revisa que el nombre coincide exactamente

### La ruta no responde

**Causa:** Varios motivos posibles.

**Solución:**
- Verifica que el servidor esté corriendo: `php artisan serve`
- Ejecuta `php artisan route:clear` para limpiar caché de rutas
- Revisa que la URL coincide exactamente con la ruta definida

### Cambios en la vista no se reflejan

**Causa:** Caché de vistas.

**Solución:**
```bash
php artisan view:clear
```

### CSS o estilos no se ven

**Causa:** Los estilos inline en las vistas deberían funcionar. Si usas archivos externos, verifica la ruta.

**Solución:**
- Guarda archivos CSS en `public/css/`
- Enlaza con ruta absoluta: `<link rel="stylesheet" href="/css/style.css">`

---

## Recursos Adicionales

- **Documentación de Rutas:** https://laravel.com/docs/routing
- **Documentación de Blade:** https://laravel.com/docs/blade
- **Documentación de Vistas:** https://laravel.com/docs/views

---

## Ejercicio Práctico: Catálogo de Videojuegos

Ahora que has aprendido sobre rutas y vistas, es momento de practicar creando algo desde cero, independiente del proyecto Marvel Hub.

### Objetivo

Crear un pequeño catálogo de videojuegos que muestre:
1. Una lista de videojuegos disponibles
2. Una página de detalle para cada videojuego
3. Usar rutas con parámetros
4. Usar directivas Blade
5. Organizar vistas en subcarpetas

### Requisitos del Ejercicio

**Datos de los videojuegos:**

Crea un array con al menos 5 videojuegos que incluya:
- ID
- Nombre del juego
- Género
- Plataforma
- Año de lanzamiento
- Precio

**Rutas a crear:**

1. `/games` - Lista todos los videojuegos
2. `/games/{id}` - Muestra detalle de un videojuego específico

**Vistas a crear:**

1. `resources/views/games/index.blade.php` - Lista de juegos
2. `resources/views/games/show.blade.php` - Detalle de un juego

**Funcionalidades:**

- En la lista, mostrar todos los juegos con su nombre, género y precio
- Hacer que cada juego sea clicable y lleve a su página de detalle
- En la página de detalle, mostrar toda la información del juego
- Si un juego no existe, mostrar mensaje "Juego no encontrado"
- Usar rutas nombradas para los enlaces

### Estructura de Archivos

```
resources/views/
└── games/
    ├── index.blade.php
    └── show.blade.php
```

### Solución Paso a Paso

#### Paso 1: Crear las rutas

Añade estas rutas a `routes/web.php`:

```php
// Lista de videojuegos
Route::get('/games', function () {
    $games = [
        1 => [
            'id' => 1,
            'nombre' => 'The Legend of Zelda: Breath of the Wild',
            'genero' => 'Aventura',
            'plataforma' => 'Nintendo Switch',
            'año' => 2017,
            'precio' => 59.99
        ],
        2 => [
            'id' => 2,
            'nombre' => 'God of War',
            'genero' => 'Acción',
            'plataforma' => 'PlayStation 5',
            'año' => 2018,
            'precio' => 49.99
        ],
        3 => [
            'id' => 3,
            'nombre' => 'Minecraft',
            'genero' => 'Sandbox',
            'plataforma' => 'Multiplataforma',
            'año' => 2011,
            'precio' => 26.95
        ],
        4 => [
            'id' => 4,
            'nombre' => 'Red Dead Redemption 2',
            'genero' => 'Acción-Aventura',
            'plataforma' => 'Xbox Series X',
            'año' => 2018,
            'precio' => 59.99
        ],
        5 => [
            'id' => 5,
            'nombre' => 'Hollow Knight',
            'genero' => 'Metroidvania',
            'plataforma' => 'PC',
            'año' => 2017,
            'precio' => 14.99
        ]
    ];
    
    return view('games.index', ['games' => $games]);
})->name('games.index');

// Detalle de videojuego
Route::get('/games/{id}', function ($id) {
    $games = [
        1 => [
            'id' => 1,
            'nombre' => 'The Legend of Zelda: Breath of the Wild',
            'genero' => 'Aventura',
            'plataforma' => 'Nintendo Switch',
            'año' => 2017,
            'precio' => 59.99
        ],
        2 => [
            'id' => 2,
            'nombre' => 'God of War',
            'genero' => 'Acción',
            'plataforma' => 'PlayStation 5',
            'año' => 2018,
            'precio' => 49.99
        ],
        3 => [
            'id' => 3,
            'nombre' => 'Minecraft',
            'genero' => 'Sandbox',
            'plataforma' => 'Multiplataforma',
            'año' => 2011,
            'precio' => 26.95
        ],
        4 => [
            'id' => 4,
            'nombre' => 'Red Dead Redemption 2',
            'genero' => 'Acción-Aventura',
            'plataforma' => 'Xbox Series X',
            'año' => 2018,
            'precio' => 59.99
        ],
        5 => [
            'id' => 5,
            'nombre' => 'Hollow Knight',
            'genero' => 'Metroidvania',
            'plataforma' => 'PC',
            'año' => 2017,
            'precio' => 14.99
        ]
    ];
    
    $game = $games[$id] ?? null;
    
    return view('games.show', ['game' => $game]);
})->name('games.show');
```

#### Paso 2: Crear la carpeta games

```bash
mkdir C:\MAMP\htdocs\marvel-hub\resources\views\games
```

#### Paso 3: Crear la vista de lista (index)

Crea el archivo `resources/views/games/index.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Videojuegos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .game-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .game-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.2);
        }
        
        .game-title {
            font-size: 1.3em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 12px;
        }
        
        .game-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #f0f0f0;
        }
        
        .game-genre {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .game-price {
            font-size: 1.4em;
            font-weight: bold;
            color: #4caf50;
        }
        
        .game-count {
            background: white;
            color: #667eea;
            padding: 12px 25px;
            border-radius: 25px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Catálogo de Videojuegos</h1>
        
        <div class="game-count">
            Total de juegos disponibles: {{ count($games) }}
        </div>
        
        <div class="games-grid">
            @foreach($games as $game)
                <a href="{{ route('games.show', $game['id']) }}" class="game-card">
                    <div class="game-title">{{ $game['nombre'] }}</div>
                    <div class="game-info">
                        <span class="game-genre">{{ $game['genero'] }}</span>
                        <span class="game-price">${{ number_format($game['precio'], 2) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</body>
</html>
```

#### Paso 4: Crear la vista de detalle (show)

Crea el archivo `resources/views/games/show.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($game)
            {{ $game['nombre'] }} - Detalle
        @else
            Juego no encontrado
        @endif
    </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .game-detail {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 30px;
            font-size: 2.2em;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        
        .info-grid {
            display: grid;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-row {
            display: flex;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            min-width: 140px;
        }
        
        .info-value {
            color: #333;
        }
        
        .price-tag {
            font-size: 2.5em;
            color: #4caf50;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            background: #e8f5e9;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .back-link {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: #764ba2;
            transform: translateX(-5px);
        }
        
        .not-found {
            text-align: center;
            padding: 60px 20px;
        }
        
        .not-found h2 {
            color: #f44336;
            font-size: 2em;
            margin-bottom: 20px;
        }
        
        .not-found p {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        @if($game)
            <div class="game-detail">
                <h1>{{ $game['nombre'] }}</h1>
                
                <div class="price-tag">
                    ${{ number_format($game['precio'], 2) }}
                </div>
                
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Género:</div>
                        <div class="info-value">{{ $game['genero'] }}</div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Plataforma:</div>
                        <div class="info-value">{{ $game['plataforma'] }}</div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">Año de lanzamiento:</div>
                        <div class="info-value">{{ $game['año'] }}</div>
                    </div>
                </div>
                
                <a href="{{ route('games.index') }}" class="back-link">
                    ← Volver al catálogo
                </a>
            </div>
        @else
            <div class="game-detail">
                <div class="not-found">
                    <h2>🎮 Juego no encontrado</h2>
                    <p>El videojuego que buscas no existe en nuestro catálogo.</p>
                    <a href="{{ route('games.index') }}" class="back-link">
                        Volver al catálogo
                    </a>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
```

#### Paso 5: Probar el ejercicio

1. Inicia el servidor:
   ```bash
   php artisan serve
   ```

2. Accede a:
   - http://localhost:8000/games (lista de juegos)
   - http://localhost:8000/games/1 (detalle de Zelda)
   - http://localhost:8000/games/3 (detalle de Minecraft)
   - http://localhost:8000/games/99 (juego no encontrado)



### Reto Extra (Opcional)

Si quieres practicar más, añade estas funcionalidades:

1. **Filtro por precio:** Crea una ruta `/games/cheap` que muestre solo juegos menores a $30
2. **Filtro por género:** Crea rutas como `/games/genre/aventura`
3. **Contador de juegos:** Muestra cuántos juegos hay de cada género
4. **Destacados:** Añade un campo `destacado` y muestra esos juegos primero
5. **Búsqueda:** Añade un formulario simple que busque por nombre

