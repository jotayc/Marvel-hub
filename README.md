# Fase 2: Controladores en Laravel

En la Fase 1 creamos rutas y vistas, pero toda la lógica estaba dentro del archivo de rutas. A medida que el proyecto crece, este enfoque se vuelve difícil de mantener. Los **controladores** resuelven este problema organizando la lógica de tu aplicación.

---

## ¿Qué es un Controlador?

Un **controlador** es una clase PHP que agrupa la lógica relacionada con un tipo de recurso (como héroes, usuarios, productos, etc.).

**Analogía del restaurante:**

Imagina que las rutas son como el menú de un restaurante:
- El cliente (navegador) pide un plato del menú (hace una petición a una ruta)
- El camarero (Laravel) recibe el pedido
- El camarero lleva el pedido a la cocina (controlador)
- El chef (método del controlador) prepara el plato (procesa la lógica)
- El camarero lleva el plato terminado (vista) al cliente

Sin controladores, sería como si el camarero tuviera que cocinar cada plato él mismo mientras atiende mesas. Con controladores, cada tarea está en su lugar correcto.

---

## ¿Por qué usar Controladores?

### **Sin controladores (como en Fase 1):**

```php
// routes/web.php
Route::get('/heroes', function () {
    $heroes = [
        ['name' => 'Iron Man', 'power' => 'Tecnología avanzada'],
        ['name' => 'Thor', 'power' => 'Dios del Trueno'],
        ['name' => 'Spider-Man', 'power' => 'Sentido arácnido'],
    ];
    
    return view('heroes.index', ['heroes' => $heroes]);
});

Route::get('/heroes/{id}', function ($id) {
    // Más lógica aquí...
    return view('heroes.show');
});

Route::get('/villains', function () {
    // Lógica de villanos...
});

// El archivo crece y crece...
```

**Problemas:**
- El archivo de rutas se vuelve enorme
- La lógica está mezclada con las rutas
- Difícil de mantener y testear
- Código repetido

### **Con controladores:**

```php
// routes/web.php
Route::get('/heroes', [HeroController::class, 'index']);
Route::get('/heroes/{id}', [HeroController::class, 'show']);
```

```php
// app/Http/Controllers/HeroController.php
class HeroController extends Controller
{
    public function index()
    {
        $heroes = [
            ['name' => 'Iron Man', 'power' => 'Tecnología avanzada'],
            ['name' => 'Thor', 'power' => 'Dios del Trueno'],
        ];
        
        return view('heroes.index', ['heroes' => $heroes]);
    }
    
    public function show($id)
    {
        // Lógica del detalle...
    }
}
```

**Ventajas:**
- Rutas limpias y claras
- Lógica organizada por recurso
- Fácil de mantener y testear
- Código reutilizable

---

## ¿Qué es Artisan?

Ya usamos Artisan en la Fase 0 para comandos como `php artisan serve`. Ahora lo usaremos para **generar controladores automáticamente**.

**Artisan** es una herramienta de línea de comandos incluida en Laravel que te ayuda a realizar tareas comunes como:
- Crear controladores
- Crear modelos
- Limpiar cachés
- Ver rutas
- Y muchas más...

**Ventaja:** En lugar de crear archivos manualmente y escribir código repetitivo, Artisan genera la estructura básica por ti.

---

## Crear tu Primer Controlador

Vamos a crear un controlador para gestionar los héroes.

### Paso 1: Generar el controlador

Abre la terminal en la carpeta del proyecto y ejecuta:

```bash
php artisan make:controller HeroController
```

**Salida:**

```
INFO  Controller [app/Http/Controllers/HeroController.php] created successfully.
```

Artisan ha creado el archivo `app/Http/Controllers/HeroController.php` con la estructura básica.

### Paso 2: Ver el controlador generado

Abre el archivo `app/Http/Controllers/HeroController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeroController extends Controller
{
    //
}
```

**Desglose:**

```php
namespace App\Http\Controllers;
```
Define el **namespace** (espacio de nombres) del controlador. Esto permite organizar las clases y evitar conflictos de nombres.

```php
use Illuminate\Http\Request;
```
Importa la clase `Request` que se usa para acceder a los datos de las peticiones HTTP (formularios, parámetros, etc.). La usaremos en fases futuras.

```php
class HeroController extends Controller
```
Define la clase `HeroController` que **extiende** (hereda de) la clase base `Controller` de Laravel.

---

## Anatomía de un Controlador

Un controlador es simplemente una clase PHP con métodos. Cada método se encarga de una acción específica.

### Nomenclatura estándar de métodos

Laravel recomienda usar nombres de métodos que reflejen operaciones CRUD:

| Método | Acción | Ejemplo URL | Descripción |
|--------|--------|-------------|-------------|
| `index()` | Listar todos | `/heroes` | Muestra lista de todos los héroes |
| `show($id)` | Ver uno | `/heroes/1` | Muestra detalle de un héroe específico |
| `create()` | Formulario crear | `/heroes/create` | Muestra formulario para crear héroe |
| `store(Request $request)` | Guardar nuevo | `/heroes` (POST) | Procesa y guarda un héroe nuevo |
| `edit($id)` | Formulario editar | `/heroes/1/edit` | Muestra formulario para editar |
| `update(Request $request, $id)` | Guardar cambios | `/heroes/1` (PUT) | Procesa y actualiza un héroe |
| `destroy($id)` | Eliminar | `/heroes/1` (DELETE) | Elimina un héroe |

**No es obligatorio** usar estos nombres, pero es la **convención** de Laravel y facilita entender el código.

---

## Crear el Método index()

Vamos a crear el método que lista todos los héroes.

Edita `app/Http/Controllers/HeroController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeroController extends Controller
{
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
```

**¿Qué hace este método?**

1. Define un array con datos de héroes (de momento hardcodeados)
2. Retorna la vista `heroes.index` pasándole los datos
3. La vista puede usar `$heroes` para mostrar los datos

---

## Conectar Rutas con Controladores

Ahora necesitamos conectar la ruta `/heroes` con el método `index()` del controlador.

Edita `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeroController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/heroes', [HeroController::class, 'index']);
```

**Desglose:**

```php
use App\Http\Controllers\HeroController;
```
Importa el controlador para poder usarlo. **Importante:** Esta línea debe estar al principio del archivo, después de `<?php`.

```php
Route::get('/heroes', [HeroController::class, 'index']);
```

**Sintaxis:** `Route::get(ruta, [NombreControlador::class, 'nombreMetodo'])`

- `'/heroes'` → URL que el usuario visita
- `HeroController::class` → Referencia a la clase del controlador
- `'index'` → Nombre del método a ejecutar

**¿Por qué `::class`?**

En PHP 8+, `NombreClase::class` devuelve el nombre completo de la clase como string. Es más seguro que escribir el string manualmente porque:
- El IDE puede autocompletar
- Si renombras la clase, se actualiza automáticamente
- PHP detecta errores si la clase no existe

---

## Probar el Controlador

Inicia el servidor:

```bash
php artisan serve
```

Visita: http://localhost:8000/heroes

Deberías ver la lista de héroes renderizada por la vista `heroes/index.blade.php` (que creaste en Fase 1).

**Si da error "View not found":** Asegúrate de haber creado el archivo `resources/views/heroes/index.blade.php` en la Fase 1.

---

## Crear el Método show()

Ahora vamos a crear el método que muestra el detalle de un héroe específico.

Edita `app/Http/Controllers/HeroController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeroController extends Controller
{
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

    public function show($id)
    {
        $heroes = [
            1 => ['id' => 1, 'name' => 'Iron Man', 'real_name' => 'Tony Stark', 'power' => 'Tecnología avanzada', 'power_level' => 8500, 'team' => 'Vengadores'],
            2 => ['id' => 2, 'name' => 'Thor', 'real_name' => 'Thor Odinson', 'power' => 'Dios del Trueno', 'power_level' => 9000, 'team' => 'Vengadores'],
            3 => ['id' => 3, 'name' => 'Spider-Man', 'real_name' => 'Peter Parker', 'power' => 'Sentido arácnido', 'power_level' => 7000, 'team' => 'Vengadores'],
            4 => ['id' => 4, 'name' => 'Doctor Strange', 'real_name' => 'Stephen Strange', 'power' => 'Hechicería', 'power_level' => 9000, 'team' => 'Vengadores'],
            5 => ['id' => 5, 'name' => 'Black Widow', 'real_name' => 'Natasha Romanoff', 'power' => 'Espía experta', 'power_level' => 6500, 'team' => 'Vengadores'],
        ];

        // Verificar si el héroe existe
        if (!isset($heroes[$id])) {
            abort(404, 'Héroe no encontrado');
        }

        $hero = $heroes[$id];

        return view('heroes.show', ['hero' => $hero]);
    }
}
```

**Novedades:**

```php
public function show($id)
```
El parámetro `$id` recibe el valor de la URL (por ejemplo, en `/heroes/3`, `$id` será `3`).

```php
if (!isset($heroes[$id])) {
    abort(404, 'Héroe no encontrado');
}
```
Verificamos si existe ese héroe. Si no existe, `abort(404)` muestra una página de error 404.

```php
return view('heroes.show', ['hero' => $hero]);
```
Pasamos un **héroe individual** (no un array de héroes) a la vista.

---

## Conectar la Ruta show()

Edita `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeroController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/heroes', [HeroController::class, 'index']);
Route::get('/heroes/{id}', [HeroController::class, 'show']);
```

**Nota:** El orden importa. Las rutas más específicas deben ir **antes** que las genéricas. En este caso está correcto porque `/heroes/{id}` es más específica que `/heroes`.

---

## Crear la Vista show

Crea el archivo `resources/views/heroes/show.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $hero['name'] }} - Marvel Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .hero-detail {
            border: 2px solid #333;
            padding: 30px;
            border-radius: 8px;
            background: #f5f5f5;
        }
        h1 {
            color: #d32f2f;
            margin-top: 0;
        }
        .info-row {
            margin: 15px 0;
            padding: 10px;
            background: white;
            border-radius: 4px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #1976d2;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="hero-detail">
        <h1>{{ $hero['name'] }}</h1>
        
        <div class="info-row">
            <span class="label">Nombre real:</span>
            {{ $hero['real_name'] }}
        </div>
        
        <div class="info-row">
            <span class="label">Poder:</span>
            {{ $hero['power'] }}
        </div>
        
        <div class="info-row">
            <span class="label">Nivel de poder:</span>
            {{ $hero['power_level'] }}
        </div>
        
        <div class="info-row">
            <span class="label">Equipo:</span>
            {{ $hero['team'] }}
        </div>
        
        <a href="/heroes" class="back-link">← Volver al listado</a>
    </div>
</body>
</html>
```

**Diferencias con index:**

- Muestra un **solo héroe** (`$hero`) en lugar de un array
- Usa `{{ $hero['nombre_campo'] }}` para acceder a cada dato
- Incluye un enlace para volver al listado

---

## Probar la Vista de Detalle

Visita: http://localhost:8000/heroes/1

Deberías ver el detalle de Iron Man.

Prueba con otros IDs:
- http://localhost:8000/heroes/2 (Thor)
- http://localhost:8000/heroes/3 (Spider-Man)
- http://localhost:8000/heroes/99 (Error 404)

---

## Agregar Enlaces en el Listado

Para que los usuarios puedan hacer clic en un héroe y ver su detalle, modifica `resources/views/heroes/index.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Héroes Marvel - Marvel Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            color: #d32f2f;
            text-align: center;
        }
        .heroes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .hero-card {
            border: 2px solid #333;
            padding: 20px;
            border-radius: 8px;
            background: #f5f5f5;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: transform 0.2s;
        }
        .hero-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .hero-name {
            font-size: 1.4em;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 10px;
        }
        .hero-real-name {
            color: #666;
            margin-bottom: 10px;
        }
        .hero-power {
            font-style: italic;
            color: #1976d2;
        }
    </style>
</head>
<body>
    <h1>Héroes de Marvel</h1>
    
    <div class="heroes-grid">
        @foreach($heroes as $hero)
            <a href="/heroes/{{ $hero['id'] }}" class="hero-card">
                <div class="hero-name">{{ $hero['name'] }}</div>
                <div class="hero-real-name">{{ $hero['real_name'] }}</div>
                <div class="hero-power">{{ $hero['power'] }}</div>
            </a>
        @endforeach
    </div>
</body>
</html>
```

**Cambios:**

```html
<a href="/heroes/{{ $hero['id'] }}" class="hero-card">
```

Cada tarjeta ahora es un **enlace** que apunta a `/heroes/1`, `/heroes/2`, etc.

```css
.hero-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
```

Añade un efecto visual cuando pasas el ratón por encima.

---

## Usar Rutas Nombradas con Controladores

Puedes (y debes) usar rutas nombradas también con controladores.

Edita `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeroController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/heroes', [HeroController::class, 'index'])->name('heroes.index');
Route::get('/heroes/{id}', [HeroController::class, 'show'])->name('heroes.show');
```

Ahora puedes usar `route()` en las vistas:

```html
<!-- En index.blade.php -->
<a href="{{ route('heroes.show', $hero['id']) }}" class="hero-card">

<!-- En show.blade.php -->
<a href="{{ route('heroes.index') }}" class="back-link">← Volver al listado</a>
```

**Ventaja:** Si cambias la URL de `/heroes` a `/personajes`, no necesitas modificar las vistas, solo las rutas.

---

## Evitar Repetición de Datos

Habrás notado que el array de héroes está **duplicado** en los métodos `index()` y `show()`. Esto no es ideal.

Vamos a extraerlo a un método privado:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeroController extends Controller
{
    // Método privado para obtener todos los héroes
    private function getAllHeroes()
    {
        return [
            1 => ['id' => 1, 'name' => 'Iron Man', 'real_name' => 'Tony Stark', 'power' => 'Tecnología avanzada', 'power_level' => 8500, 'team' => 'Vengadores'],
            2 => ['id' => 2, 'name' => 'Thor', 'real_name' => 'Thor Odinson', 'power' => 'Dios del Trueno', 'power_level' => 9000, 'team' => 'Vengadores'],
            3 => ['id' => 3, 'name' => 'Spider-Man', 'real_name' => 'Peter Parker', 'power' => 'Sentido arácnido', 'power_level' => 7000, 'team' => 'Vengadores'],
            4 => ['id' => 4, 'name' => 'Doctor Strange', 'real_name' => 'Stephen Strange', 'power' => 'Hechicería', 'power_level' => 9000, 'team' => 'Vengadores'],
            5 => ['id' => 5, 'name' => 'Black Widow', 'real_name' => 'Natasha Romanoff', 'power' => 'Espía experta', 'power_level' => 6500, 'team' => 'Vengadores'],
        ];
    }

    public function index()
    {
        $heroes = $this->getAllHeroes();
        
        return view('heroes.index', ['heroes' => $heroes]);
    }

    public function show($id)
    {
        $heroes = $this->getAllHeroes();

        if (!isset($heroes[$id])) {
            abort(404, 'Héroe no encontrado');
        }

        $hero = $heroes[$id];

        return view('heroes.show', ['hero' => $hero]);
    }
}
```

**Ventajas:**

- Los datos están en **un solo lugar**
- Si añades un héroe, se actualiza automáticamente en todas las vistas
- Más fácil de mantener

**Nota:** En la Fase 4 reemplazaremos este array por datos reales de la base de datos.

---

## Archivo routes/web.php Completo

Tu archivo `routes/web.php` debería verse así:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeroController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/heroes', [HeroController::class, 'index'])->name('heroes.index');
Route::get('/heroes/{id}', [HeroController::class, 'show'])->name('heroes.show');
```

Limpio, claro y fácil de leer.

---

## Archivo HeroController.php Completo

Tu controlador `app/Http/Controllers/HeroController.php` completo:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeroController extends Controller
{
    private function getAllHeroes()
    {
        return [
            1 => ['id' => 1, 'name' => 'Iron Man', 'real_name' => 'Tony Stark', 'power' => 'Tecnología avanzada', 'power_level' => 8500, 'team' => 'Vengadores'],
            2 => ['id' => 2, 'name' => 'Thor', 'real_name' => 'Thor Odinson', 'power' => 'Dios del Trueno', 'power_level' => 9000, 'team' => 'Vengadores'],
            3 => ['id' => 3, 'name' => 'Spider-Man', 'real_name' => 'Peter Parker', 'power' => 'Sentido arácnido', 'power_level' => 7000, 'team' => 'Vengadores'],
            4 => ['id' => 4, 'name' => 'Doctor Strange', 'real_name' => 'Stephen Strange', 'power' => 'Hechicería', 'power_level' => 9000, 'team' => 'Vengadores'],
            5 => ['id' => 5, 'name' => 'Black Widow', 'real_name' => 'Natasha Romanoff', 'power' => 'Espía experta', 'power_level' => 6500, 'team' => 'Vengadores'],
        ];
    }

    public function index()
    {
        $heroes = $this->getAllHeroes();
        
        return view('heroes.index', ['heroes' => $heroes]);
    }

    public function show($id)
    {
        $heroes = $this->getAllHeroes();

        if (!isset($heroes[$id])) {
            abort(404, 'Héroe no encontrado');
        }

        $hero = $heroes[$id];

        return view('heroes.show', ['hero' => $hero]);
    }
}
```

---

## Buenas Prácticas

### 1. Un controlador por recurso

Crea un controlador para cada tipo de recurso:
- `HeroController` para héroes
- `VillainController` para villanos
- `TeamController` para equipos

No mezcles todo en un solo controlador gigante.

### 2. Métodos pequeños y enfocados

Cada método debe hacer **una sola cosa**:

```php
// ✅ BIEN - Cada método hace una cosa
public function index() { /* Listar */ }
public function show($id) { /* Ver uno */ }

// ❌ MAL - Método que hace demasiadas cosas
public function handleEverything($action, $id = null) {
    if ($action === 'list') { /* ... */ }
    if ($action === 'show') { /* ... */ }
    if ($action === 'delete') { /* ... */ }
}
```

### 3. Nombres de métodos descriptivos

Usa los nombres estándar de Laravel cuando sea posible:

```php
// ✅ BIEN
public function index() { }
public function show($id) { }

// ❌ Confuso
public function getAllStuff() { }
public function viewOne($id) { }
```

### 4. Validación de parámetros

Siempre verifica que los datos existan antes de usarlos:

```php
public function show($id)
{
    $heroes = $this->getAllHeroes();
    
    // ✅ BIEN - Verifica existencia
    if (!isset($heroes[$id])) {
        abort(404);
    }
    
    return view('heroes.show', ['hero' => $heroes[$id]]);
}
```

---

## Resumen de la Fase 2

En esta fase has aprendido:

✅ **¿Qué es un controlador?** - Una clase que organiza la lógica de tu aplicación

✅ **¿Por qué usarlos?** - Código más limpio, organizado y mantenible

✅ **Generar controladores** - Con `php artisan make:controller`

✅ **Métodos estándar** - `index()`, `show()`, `create()`, `store()`, etc.

✅ **Conectar rutas** - `Route::get('/ruta', [Controlador::class, 'metodo'])`

✅ **Pasar datos a vistas** - Desde el controlador con `return view('nombre', ['datos' => $datos])`

✅ **Organizar código** - Evitar repetición con métodos privados

**En la próxima fase** aprenderás a conectar con la base de datos real para reemplazar los arrays hardcodeados por datos dinámicos.

---

## Resolución de Problemas Comunes

### Error: "Target class [HeroController] does not exist"

**Causa:** Laravel no encuentra el controlador.

**Solución:**

1. Verifica que el archivo existe en `app/Http/Controllers/HeroController.php`
2. Verifica que importaste el controlador en `routes/web.php`:
   ```php
   use App\Http\Controllers\HeroController;
   ```
3. Limpia la caché:
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

### Error: "View [heroes.index] not found"

**Causa:** La vista no existe.

**Solución:**

1. Verifica que el archivo existe en `resources/views/heroes/index.blade.php`
2. Verifica que la carpeta se llama `heroes` (no `hero`)
3. Verifica que el archivo tiene extensión `.blade.php` (no solo `.php`)

### Error 404 al visitar /heroes/1

**Causa:** La ruta no está definida o está en el orden incorrecto.

**Solución:**

Asegúrate que en `routes/web.php` tengas:
```php
Route::get('/heroes/{id}', [HeroController::class, 'show']);
```

Y que está **después** de la ruta `/heroes`.

### Los cambios no se reflejan

**Solución:**

1. Guarda los archivos (Ctrl+S)
2. Refresca el navegador (F5 o Ctrl+F5)
3. Si usas caché:
   ```bash
   php artisan config:clear
   php artisan view:clear
   ```

---

## Recursos Adicionales

**Documentación oficial de Laravel:**
- Controladores: https://laravel.com/docs/11.x/controllers
- Routing: https://laravel.com/docs/11.x/routing

**Próximos pasos:**
- Fase 3: Acceder a la base de datos con consultas SQL
- Fase 4: Modelos Eloquent
- Fase 5: Crear un layout con @extends y @section

---

## Ejercicio Práctico: Biblioteca de Libros

Ahora que dominas los controladores, es momento de practicar creando algo desde cero, independiente del proyecto Marvel Hub.

### Objetivo

Crear una biblioteca virtual que muestre:
1. Un listado de libros disponibles
2. Una página de detalle para cada libro
3. Usar un controlador para gestionar la lógica
4. Aplicar todas las buenas prácticas aprendidas

### Requisitos del Ejercicio

**Datos de los libros:**

Crear un array con al menos 5 libros que incluya:
- ID
- Título
- Autor
- Género
- Año de publicación
- Páginas
- ISBN
- Sinopsis breve

**Controlador a crear:**

- `BookController` con métodos `index()` y `show()`

**Rutas a crear:**

1. `/books` → `BookController@index`
2. `/books/{id}` → `BookController@show`

**Vistas a crear:**

1. `resources/views/books/index.blade.php`
2. `resources/views/books/show.blade.php`

**Funcionalidades:**

- Método privado `getAllBooks()` para evitar duplicación de datos
- Validación de existencia con `abort(404)` si el libro no existe
- Rutas nombradas `books.index` y `books.show`
- Enlaces clicables en la lista
- Diseño atractivo con CSS

### Solución Paso a Paso

#### Paso 1: Crear el controlador

Abre la terminal y ejecuta:

```bash
php artisan make:controller BookController
```

Verás el mensaje:
```
INFO  Controller [app/Http/Controllers/BookController.php] created successfully.
```

#### Paso 2: Editar el controlador

Abre `app/Http/Controllers/BookController.php` y añade el código completo:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookController extends Controller
{
    // Método privado para obtener todos los libros
    private function getAllBooks()
    {
        return [
            1 => [
                'id' => 1,
                'titulo' => 'Cien años de soledad',
                'autor' => 'Gabriel García Márquez',
                'genero' => 'Realismo mágico',
                'año' => 1967,
                'paginas' => 471,
                'isbn' => '978-0307474728',
                'sinopsis' => 'La historia de la familia Buendía a lo largo de siete generaciones en el pueblo ficticio de Macondo.'
            ],
            2 => [
                'id' => 2,
                'titulo' => '1984',
                'autor' => 'George Orwell',
                'genero' => 'Distopía',
                'año' => 1949,
                'paginas' => 328,
                'isbn' => '978-0451524935',
                'sinopsis' => 'Una novela distópica que retrata una sociedad totalitaria bajo vigilancia constante.'
            ],
            3 => [
                'id' => 3,
                'titulo' => 'El Principito',
                'autor' => 'Antoine de Saint-Exupéry',
                'genero' => 'Fábula',
                'año' => 1943,
                'paginas' => 96,
                'isbn' => '978-0156012195',
                'sinopsis' => 'La historia de un pequeño príncipe que viaja por el universo en busca de sabiduría.'
            ],
            4 => [
                'id' => 4,
                'titulo' => 'Don Quijote de la Mancha',
                'autor' => 'Miguel de Cervantes',
                'genero' => 'Novela',
                'año' => 1605,
                'paginas' => 863,
                'isbn' => '978-8420412146',
                'sinopsis' => 'Las aventuras de un hidalgo que pierde la cordura y decide convertirse en caballero andante.'
            ],
            5 => [
                'id' => 5,
                'titulo' => 'Orgullo y prejuicio',
                'autor' => 'Jane Austen',
                'genero' => 'Romance',
                'año' => 1813,
                'paginas' => 432,
                'isbn' => '978-0141439518',
                'sinopsis' => 'La historia de Elizabeth Bennet y su relación con el orgulloso señor Darcy.'
            ]
        ];
    }

    // Método para listar todos los libros
    public function index()
    {
        $books = $this->getAllBooks();
        
        return view('books.index', ['books' => $books]);
    }

    // Método para mostrar el detalle de un libro
    public function show($id)
    {
        $books = $this->getAllBooks();
        
        // Verificar si el libro existe
        if (!isset($books[$id])) {
            abort(404, 'Libro no encontrado');
        }
        
        $book = $books[$id];
        
        return view('books.show', ['book' => $book]);
    }
}
```

**Puntos clave del código:**

- ✅ Método privado `getAllBooks()` para evitar duplicación
- ✅ Método `index()` que obtiene todos los libros y los pasa a la vista
- ✅ Método `show($id)` con validación de existencia
- ✅ Uso de `abort(404)` cuando el libro no existe
- ✅ Estructura clara y organizada

#### Paso 3: Crear las rutas

Edita `routes/web.php` y añade:

```php
use App\Http\Controllers\BookController;

// Rutas para la biblioteca
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');
```

**Importante:** No olvides el `use` al principio del archivo.

#### Paso 4: Crear la carpeta de vistas

```bash
mkdir C:\MAMP\htdocs\marvel-hub\resources\views\books
```

#### Paso 5: Crear la vista de listado

Crea `resources/views/books/index.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Virtual</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #FFF8DC;
            text-align: center;
            margin-bottom: 20px;
            font-size: 3em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .subtitle {
            color: #FFE4B5;
            text-align: center;
            margin-bottom: 40px;
            font-size: 1.2em;
            font-style: italic;
        }
        
        .books-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .book-card {
            background: #FFF8DC;
            border-radius: 10px;
            padding: 25px;
            text-decoration: none;
            color: #333;
            display: block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            border: 3px solid #8B4513;
        }
        
        .book-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
            border-color: #D2691E;
        }
        
        .book-title {
            font-size: 1.5em;
            font-weight: bold;
            color: #8B4513;
            margin-bottom: 10px;
            border-bottom: 2px solid #D2691E;
            padding-bottom: 10px;
        }
        
        .book-author {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 15px;
            font-style: italic;
        }
        
        .book-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #D2691E;
        }
        
        .book-genre {
            background: #8B4513;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
        }
        
        .book-year {
            color: #8B4513;
            font-weight: bold;
        }
        
        .stats {
            background: #FFF8DC;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .stats-number {
            font-size: 2em;
            font-weight: bold;
            color: #8B4513;
        }
        
        .stats-label {
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Biblioteca Virtual</h1>
        <p class="subtitle">Explora nuestra colección de clásicos literarios</p>
        
        <div class="stats">
            <div class="stats-number">{{ count($books) }}</div>
            <div class="stats-label">Libros en la colección</div>
        </div>
        
        <div class="books-container">
            @foreach($books as $book)
                <a href="{{ route('books.show', $book['id']) }}" class="book-card">
                    <div class="book-title">{{ $book['titulo'] }}</div>
                    <div class="book-author">por {{ $book['autor'] }}</div>
                    <div class="book-meta">
                        <span class="book-genre">{{ $book['genero'] }}</span>
                        <span class="book-year">{{ $book['año'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</body>
</html>
```

#### Paso 6: Crear la vista de detalle

Crea `resources/views/books/show.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $book['titulo'] }} - Biblioteca Virtual</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .book-detail {
            background: #FFF8DC;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            border: 3px solid #8B4513;
        }
        
        .book-header {
            border-bottom: 3px solid #8B4513;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        h1 {
            color: #8B4513;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .author {
            font-size: 1.4em;
            color: #666;
            font-style: italic;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #8B4513;
        }
        
        .info-label {
            font-weight: bold;
            color: #8B4513;
            font-size: 0.9em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #333;
            font-size: 1.1em;
        }
        
        .synopsis {
            background: white;
            padding: 25px;
            border-radius: 8px;
            line-height: 1.8;
            color: #333;
            font-size: 1.1em;
            border-left: 5px solid #D2691E;
        }
        
        .synopsis-title {
            font-weight: bold;
            color: #8B4513;
            font-size: 1.3em;
            margin-bottom: 15px;
        }
        
        .back-link {
            display: inline-block;
            background: #8B4513;
            color: #FFF8DC;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            margin-top: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .back-link:hover {
            background: #D2691E;
            transform: translateX(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="book-detail">
            <div class="book-header">
                <h1>{{ $book['titulo'] }}</h1>
                <div class="author">por {{ $book['autor'] }}</div>
            </div>
            
            <div class="info-section">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Género</div>
                        <div class="info-value">{{ $book['genero'] }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Año de Publicación</div>
                        <div class="info-value">{{ $book['año'] }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Páginas</div>
                        <div class="info-value">{{ $book['paginas'] }} páginas</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">ISBN</div>
                        <div class="info-value">{{ $book['isbn'] }}</div>
                    </div>
                </div>
                
                <div class="synopsis">
                    <div class="synopsis-title">📖 Sinopsis</div>
                    {{ $book['sinopsis'] }}
                </div>
            </div>
            
            <a href="{{ route('books.index') }}" class="back-link">
                ← Volver a la biblioteca
            </a>
        </div>
    </div>
</body>
</html>
```

#### Paso 7: Probar el ejercicio

1. Asegúrate que el servidor está corriendo:
   ```bash
   php artisan serve
   ```

2. Accede a:
   - http://localhost:8000/books (lista de libros)
   - http://localhost:8000/books/1 (Cien años de soledad)
   - http://localhost:8000/books/3 (El Principito)
   - http://localhost:8000/books/99 (Error 404)

3. Verifica las rutas creadas:
   ```bash
   php artisan route:list
   ```

### Verificación

Comprueba que todo funciona correctamente:

- ✅ El controlador se creó con `php artisan make:controller`
- ✅ El controlador tiene método privado `getAllBooks()`
- ✅ El método `index()` muestra todos los libros
- ✅ El método `show($id)` muestra un libro específico
- ✅ Valida existencia con `abort(404)`
- ✅ Las rutas están conectadas al controlador
- ✅ Las rutas tienen nombres (`books.index`, `books.show`)
- ✅ Los enlaces usan `route()` helper
- ✅ Las tarjetas son clicables
- ✅ La página de detalle muestra toda la información
- ✅ El enlace "Volver" funciona correctamente
- ✅ Al acceder a ID inexistente muestra error 404

### Reto Extra (Opcional)

Si quieres seguir practicando, añade estas funcionalidades:

1. **Método `authors()`:** Crear una ruta `/books/authors` que liste todos los autores únicos
2. **Filtrar por género:** Añadir método `byGenre($genero)` y ruta `/books/genre/{genero}`
3. **Libros más largos:** Método `longest()` que muestre los 3 libros con más páginas
4. **Búsqueda simple:** Método `search()` que busque libros por título
5. **Estadísticas:** Método `stats()` que muestre total de libros, total de páginas, libro más antiguo, etc.

### Conceptos Practicados

Con este ejercicio has trabajado:

- ✅ Comando `php artisan make:controller`
- ✅ Estructura de un controlador (namespace, use, class)
- ✅ Métodos públicos (`index`, `show`)
- ✅ Método privado para evitar duplicación (`getAllBooks`)
- ✅ Validación con `abort(404)`
- ✅ Conectar rutas con controladores: `[Controller::class, 'metodo']`
- ✅ Rutas nombradas con `->name()`
- ✅ Pasar datos desde controlador a vista
- ✅ Usar `route()` helper en vistas
- ✅ Parámetros en métodos del controlador `show($id)`
- ✅ Verificación de existencia antes de usar datos
- ✅ Organización de código en controladores
- ✅ Buenas prácticas: un controlador por recurso, métodos enfocados

### Comparación: Con y Sin Controladores

**Sin controladores (Fase 1):**
```php
// routes/web.php - Todo mezclado
Route::get('/books', function () {
    $books = [/* array gigante */];
    return view('books.index', ['books' => $books]);
});

Route::get('/books/{id}', function ($id) {
    $books = [/* array duplicado */];
    // validación, lógica...
    return view('books.show', ['book' => $book]);
});
```

**Con controladores (Fase 2):**
```php
// routes/web.php - Limpio y claro
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);

// BookController.php - Lógica organizada
class BookController {
    private function getAllBooks() { /* datos */ }
    public function index() { /* lógica lista */ }
    public function show($id) { /* lógica detalle */ }
}
```

**Ventajas evidentes:**
- ✅ Rutas más limpias (2 líneas vs 20+ líneas)
- ✅ Datos centralizados (método privado)
- ✅ Lógica organizada por recurso
- ✅ Fácil de mantener y testear
- ✅ Escalable para más métodos

**¡Felicidades!** Has completado el ejercicio de controladores. Ahora entiendes por qué los controladores son fundamentales en Laravel y cómo organizan el código de forma profesional.
