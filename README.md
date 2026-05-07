# Fase 8: Relaciones entre Modelos

---

## De dónde partimos

A lo largo del curso el campo `team` de la tabla `heroes` ha sido siempre un string: `'Vengadores'`, `'Guardianes'`, `'X-Men'`. Esto funciona, pero tiene un problema evidente: si quisieras cambiar el nombre de un equipo tendrías que actualizar todos los héroes que lo tienen. Y no hay forma de guardar información sobre el equipo en sí, como su descripción o su año de fundación, sin añadir más columnas a la tabla `heroes`.

La solución correcta desde el punto de vista del modelo relacional es extraer los equipos a su propia tabla y conectar ambas mediante una clave foránea. En lugar de almacenar `'Vengadores'` en cada héroe, cada héroe almacena el ID del equipo al que pertenece.

En esta fase vas a:

1. Introducir las migraciones de Laravel para gestionar los cambios de base de datos
2. Crear la tabla `teams` mediante una migración
3. Añadir la columna `team_id` a la tabla `heroes` mediante otra migración
4. Definir las relaciones `belongsTo` y `hasMany` entre los modelos
5. Actualizar el controlador y las vistas para usar la relación

---

## Preparación: Crear una rama nueva

```bash
git checkout 7.CRUD
git checkout -b 8.Relaciones
```

---

## ¿Qué es una migración?

Hasta ahora todos los cambios en la base de datos se han hecho manualmente desde phpMyAdmin: crear tablas, añadir columnas, insertar datos. Esto funciona en local, pero tiene un problema: esos cambios no quedan registrados en ningún sitio junto con el código. Si otro desarrollador clona el proyecto, o si cambias de rama en Git, la base de datos no se actualiza automáticamente.

Una **migración** es un archivo PHP que describe un cambio en la estructura de la base de datos. En lugar de ejecutar SQL directamente en phpMyAdmin, describes el cambio en código y Laravel lo ejecuta cuando tú le indiques. Las migraciones viajan con el código en Git, de modo que el historial de cambios de la base de datos queda versionado junto con el historial del proyecto.

Cada migración tiene dos métodos:

- **`up()`**: aplica el cambio (crear tabla, añadir columna, etc.)
- **`down()`**: revierte el cambio (eliminar tabla, eliminar columna, etc.)

Esto permite avanzar y retroceder en el historial de cambios de la base de datos igual que Git permite avanzar y retroceder en el historial del código.

Los comandos principales son:

```bash
# Ejecutar todas las migraciones pendientes
php artisan migrate

# Ver el estado de todas las migraciones
php artisan migrate:status

# Revertir la última tanda de migraciones
php artisan migrate:rollback

# Crear un nuevo archivo de migración
php artisan make:migration nombre_descriptivo
```

Laravel guarda en la tabla `migrations` de la base de datos un registro de qué migraciones se han ejecutado y cuándo. Así sabe cuáles están pendientes en cada momento.

---

## Paso 1: Migración para la tabla teams

```bash
php artisan make:migration create_teams_table
```

Laravel crea el archivo en `database/migrations/` con un nombre como `2025_01_01_000000_create_teams_table.php`. El prefijo de fecha y hora garantiza que las migraciones se ejecuten en el orden en que fueron creadas.

Abre el archivo y escribe el siguiente contenido:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('founded')->nullable();
            $table->timestamps();
        });

        // Insertar los equipos iniciales
        DB::table('teams')->insert([
            ['name' => 'Vengadores',  'description' => 'El equipo de superhéroes más poderoso de la Tierra', 'founded' => 1963],
            ['name' => 'Guardianes',  'description' => 'Protectores del universo desde el espacio exterior',  'founded' => 1969],
            ['name' => 'X-Men',       'description' => 'Mutantes que luchan por la paz entre humanos y mutantes', 'founded' => 1963],
            ['name' => 'Sin equipo',  'description' => 'Héroes independientes sin afiliación', 'founded' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
```

**Análisis del método `up()`:**

```php
$table->id();
```
Crea la columna `id` como entero sin signo, auto-incremental y clave primaria. Es el equivalente de `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`. En Laravel 11 `id()` genera `BIGINT UNSIGNED`, no `INT UNSIGNED`, lo que explica por qué la clave foránea `team_id` también debe ser `unsignedBigInteger`.

```php
$table->string('name', 100);
```
Crea una columna VARCHAR de 100 caracteres. `string()` es el método de Blueprint para VARCHAR.

```php
$table->text('description')->nullable();
```
Crea una columna TEXT. `nullable()` permite que la columna acepte valores NULL.

```php
$table->integer('founded')->nullable();
```
Crea una columna INT que puede ser NULL.

```php
$table->timestamps();
```
Crea automáticamente las columnas `created_at` y `updated_at` como TIMESTAMP. Eloquent las gestiona solo.

**El método `down()`** revierte la migración eliminando la tabla. `dropIfExists()` no lanza error si la tabla no existe.

---

## Paso 2: Migración para añadir team_id a heroes

```bash
php artisan make:migration add_team_id_to_heroes_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('team');
            $table->foreign('team_id')->references('id')->on('teams');
        });

        // Asignar team_id según el valor actual del campo team
        DB::table('heroes')->where('team', 'Vengadores')->update(['team_id' => 1]);
        DB::table('heroes')->where('team', 'Guardianes')->update(['team_id' => 2]);
        DB::table('heroes')->where('team', 'X-Men')->update(['team_id' => 3]);
        DB::table('heroes')->where(function ($query) {
            $query->whereNull('team')->orWhere('team', '');
        })->update(['team_id' => 4]);
    }

    public function down(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
```

**Diferencia respecto a la migración anterior:**

```php
Schema::create('teams', ...)   // Crea una tabla nueva
Schema::table('heroes', ...)   // Modifica una tabla existente
```

`Schema::table()` se usa cuando la tabla ya existe y solo quieres añadir, modificar o eliminar columnas.

```php
$table->unsignedBigInteger('team_id')->nullable()->after('team');
```

`unsignedBigInteger` coincide con el tipo que usa `$table->id()`, que también genera un `BIGINT UNSIGNED`. La clave foránea debe ser del mismo tipo que la clave primaria a la que apunta. `after('team')` coloca la columna justo después de la columna `team` existente.

```php
$table->foreign('team_id')->references('id')->on('teams');
```

Declara la restricción de clave foránea: `team_id` en `heroes` apunta a `id` en `teams`.

**El método `down()`** revierte en orden inverso: primero elimina la clave foránea, luego la columna.

---

## Paso 3: Ejecutar las migraciones

```bash
php artisan migrate
```

Laravel detecta las dos nuevas migraciones y las ejecuta en orden. La salida esperada:

```
INFO  Running migrations.

2025_01_01_000000_create_teams_table .............. 45ms DONE
2025_01_01_000001_add_team_id_to_heroes_table ..... 32ms DONE
```

Puedes verificar el estado de todas las migraciones con:

```bash
php artisan migrate:status
```

### Migraciones y ramas de Git

Las migraciones resuelven el problema de sincronizar la base de datos con el código cuando se trabaja con ramas. El código viaja con Git, pero la base de datos no: si cambias de rama sin hacer nada, el código de esa rama no encontrará las tablas o columnas que espera y aparecerán errores.

La regla es que **el orden importa**: hay que sincronizar la base de datos antes de cambiar de rama, no después.

**Para bajar a una rama anterior** (por ejemplo volver a `7.CRUD`):

```bash
# 1. Revertir la base de datos estando en 8.Relaciones
php artisan migrate:rollback

# 2. Cambiar de rama
git checkout 7.CRUD
```

`migrate:rollback` ejecuta los métodos `down()` de las migraciones del último batch: elimina la columna `team_id` de `heroes` y borra la tabla `teams`. La base de datos vuelve al estado que espera el código de esa rama.

**Para volver a la Fase 8:**

```bash
# 1. Cambiar de rama
git checkout 8.Relaciones

# 2. Aplicar las migraciones pendientes
php artisan migrate
```

`migrate` detecta las migraciones pendientes y las ejecuta: recrea `teams` con sus datos e incorpora `team_id` a `heroes`.

En resumen:

```
Bajar de rama  →  migrate:rollback primero, luego git checkout
Subir de rama  →  git checkout primero, luego migrate
```

---

## ¿Qué es una relación en Eloquent?

### Cardinalidad y claves foráneas

Antes de ver los métodos de Eloquent, conviene tener claro el concepto que los sustenta.

Una **relación** entre dos tablas tiene una **cardinalidad** que describe cuántos registros de una tabla pueden estar asociados con cuántos de la otra. En este proyecto la relación entre héroes y equipos es de **uno a muchos (1:N)**: un equipo puede tener muchos héroes, pero cada héroe pertenece a exactamente un equipo.

En una relación 1:N, la **clave foránea** siempre vive en la tabla del lado "muchos". En este caso `team_id` está en la tabla `heroes` porque es el héroe quien apunta al equipo, no al revés. Esa columna es lo que implementa la relación en la base de datos: contiene el `id` del equipo al que pertenece cada héroe.

Eloquent traduce esta estructura a objetos PHP mediante dos métodos que se colocan en los modelos siguiendo la misma lógica: `belongsTo` va en el modelo cuya tabla tiene la clave foránea, y `hasMany` va en el modelo del lado "uno".

### Los métodos de relación

**`belongsTo` (pertenece a)**

Se declara en el modelo que tiene la clave foránea. Un héroe pertenece a un equipo, y `team_id` vive en `heroes`, por eso `belongsTo` va en `Hero`.

```php
// En el modelo Hero:
public function team()
{
    return $this->belongsTo(Team::class);
}

// Uso:
$hero->team->name  // Nombre del equipo del héroe
```

**`hasMany` (tiene muchos)**

Se declara en el modelo del lado "uno". Un equipo tiene muchos héroes, por eso `hasMany` va en `Team`.

```php
// En el modelo Team:
public function heroes()
{
    return $this->hasMany(Hero::class);
}

// Uso:
$team->heroes  // Collection con todos los héroes del equipo
```

Ambas se definen como métodos públicos en el modelo. Eloquent detecta automáticamente el nombre de la clave foránea siguiendo convenciones: `belongsTo(Team::class)` busca una columna `team_id` en la tabla `heroes` (nombre del modelo en minúsculas más `_id`), y `hasMany(Hero::class)` busca esa misma columna para construir la relación inversa.

### ¿Qué ocurre en una relación N:N?

En una relación muchos a muchos, por ejemplo si un héroe pudiera pertenecer a varios equipos simultáneamente y un equipo pudiera compartir héroes con otros, la clave foránea ya no puede vivir en ninguna de las dos tablas. Ninguna es el lado "uno", así que no hay un lugar natural donde colocarla.

La solución en bases de datos relacionales es una **tabla pivote** o tabla intermedia que almacena pares de IDs: en este caso `hero_id` y `team_id`. Cada fila de esa tabla representa una asociación concreta entre un héroe y un equipo.

En Eloquent, las relaciones N:N no usan `belongsTo` ni `hasMany` sino un tercer método llamado `belongsToMany`, que se declara en ambos modelos apuntándose mutuamente. Eloquent gestiona la tabla pivote de forma transparente, sin que el controlador ni las vistas necesiten conocerla.

En esta fase se trabaja exclusivamente con la relación 1:N entre héroes y equipos. La relación N:N con `belongsToMany` es el paso siguiente natural una vez dominada la relación 1:N.

---

## Paso 4: Crear el modelo Team

```bash
php artisan make:model Team
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Hero;

class Team extends Model
{
    protected $fillable = [
        'name',
        'description',
        'founded'
    ];

    protected $casts = [
        'founded' => 'integer'
    ];

    // Un equipo tiene muchos héroes
    public function heroes()
    {
        return $this->hasMany(Hero::class);
    }
}
```

---

## Paso 5: Actualizar el modelo Hero

Añade la relación `belongsTo` y actualiza `$fillable` para incluir `team_id`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Team;

class Hero extends Model
{
    protected $fillable = [
        'name',
        'real_name',
        'power',
        'power_level',
        'team_id',
        'bio',
        'is_active'
    ];

    protected $casts = [
        'power_level' => 'integer',
        'is_active'   => 'boolean',
    ];

    // Un héroe pertenece a un equipo
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
```

**Importante:** el método `team()` ahora es una relación, no un campo directo. Cuando se accede a `$hero->team`, Eloquent ejecuta la consulta de la relación y devuelve el objeto `Team` completo. Para acceder al nombre del equipo hay que escribir `$hero->team->name`.

---

## Paso 6: Verificar las relaciones con Tinker

```bash
php artisan tinker
```

```php
use App\Models\Hero;
use App\Models\Team;

// Acceder al equipo de un héroe
$hero = Hero::find(1);
$hero->team;            // Devuelve el objeto Team completo
$hero->team->name;      // Devuelve 'Vengadores'

// Acceder a todos los héroes de un equipo
$team = Team::find(1);
$team->heroes;           // Devuelve una Collection de objetos Hero
$team->heroes->count();  // Número de héroes del equipo

// Todos los equipos con su número de héroes (sin N+1)
Team::withCount('heroes')->get()->each(function($team) {
    echo $team->name . ': ' . $team->heroes_count . " héroes\n";
});
```

---

## Paso 7: Eager Loading — evitar el problema N+1

Antes de actualizar el controlador hay que entender un problema habitual con las relaciones: la **consulta N+1**.

Observa qué ocurre si en el controlador haces esto:

```php
$heroes = Hero::all();
```

Y en la vista iteras los héroes mostrando su equipo:

```html
@foreach($heroes as $hero)
    <p>{{ $hero->team->name }}</p>
@endforeach
```

Para cada héroe, Eloquent ejecuta una consulta SQL para cargar su equipo. Si hay 5 héroes, se ejecutan 6 consultas: 1 para obtener todos los héroes y 5 más para obtener el equipo de cada uno. Con 100 héroes serían 101 consultas. Esto se llama el **problema N+1**.

La solución es el **Eager Loading**: indicarle a Eloquent que cargue los equipos en la misma consulta inicial usando `with()`:

```php
// Sin Eager Loading: 1 + N consultas
$heroes = Hero::all();

// Con Eager Loading: 2 consultas en total
$heroes = Hero::with('team')->get();
```

`with('team')` le dice a Eloquent que cuando cargue los héroes, cargue también sus equipos en una segunda consulta y los vincule automáticamente. El resultado en la vista es idéntico, pero la diferencia de rendimiento con colecciones grandes es muy significativa.

Siempre que cargues héroes que vayan a mostrar su equipo en la vista, usa `with('team')`.

El Paso 7 no cambia la lógica de ningún método: los controladores siguen devolviendo las mismas vistas con los mismos datos. El único cambio es añadir `with('team')` a las consultas donde la vista vaya a acceder a `$hero->team`. La funcionalidad es idéntica; la diferencia es de rendimiento.

---

## Paso 8: Actualizar HeroController

### Qué ocurre con el método team() y su ruta

En fases anteriores el controlador tenía un método `team($team)` que filtraba héroes por el string del equipo:

```php
// Antes — ya no funciona
public function team($team)
{
    $heroes = Hero::where('team', $team)->get();
    return view('heroes.team', compact('heroes', 'team'));
}
```

Y en `web.php` existía la ruta:

```php
Route::get('/heroes/team/{team}', [HeroController::class, 'team'])->name('heroes.team');
```

Con la Fase 8 este enfoque queda obsoleto por dos razones: el campo `team` ya no contiene el nombre del equipo (contiene un string vacío o sigue existiendo como columna heredada), y la funcionalidad de listar héroes por equipo se cubre ahora con `TeamController@show`, que es más completa.

**Elimina** el método `team()` del controlador y **elimina** su ruta de `web.php`. A partir de esta fase la navegación por equipos se hace a través de `/teams/{id}`.

---

Antes de ver el controlador completo, conviene aclarar un cambio que puede resultar inesperado en los métodos `create()` y `edit()`.

En la Fase 6 el formulario de creación tenía un campo de texto libre donde el usuario escribía el nombre del equipo a mano. Con la Fase 8 ese campo desaparece y se reemplaza por un `<select>` con los equipos disponibles. Para que ese desplegable tenga opciones, la vista necesita recibir la lista de equipos desde el controlador, de la misma forma que recibe los héroes o cualquier otro dato. Por eso `create()` consulta `Team::orderBy('name')->get()` y pasa el resultado a la vista como `$teams`, y lo mismo hace `edit()` junto con el héroe a editar.

No se está buscando el equipo de ningún héroe concreto. Se están cargando todos los equipos disponibles para construir las opciones del desplegable.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero;
use App\Models\Team;

class HeroController extends Controller
{
    public function index()
    {
        $heroes = Hero::with('team')->get();
        return view('heroes.index', compact('heroes'));
    }

    public function create()
    {
        $teams = Team::orderBy('name')->get();
        return view('heroes.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'real_name'   => 'nullable|string|max:100',
            'power'       => 'required|string|max:150',
            'power_level' => 'required|integer|min:1|max:10000',
            'team_id'     => 'required|integer',
            'bio'         => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        Hero::create([
            'name'        => $request->name,
            'real_name'   => $request->real_name,
            'power'       => $request->power,
            'power_level' => $request->power_level,
            'team_id'     => $request->team_id,
            'bio'         => $request->bio,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('heroes.index')
            ->with('success', 'Héroe creado correctamente.');
    }

    public function show($id)
    {
        $hero = Hero::with('team')->findOrFail($id);
        return view('heroes.show', compact('hero'));
    }

    public function edit($id)
    {
        $hero  = Hero::findOrFail($id);
        $teams = Team::orderBy('name')->get();
        return view('heroes.edit', compact('hero', 'teams'));
    }

    public function update(Request $request, $id)
    {
        $hero = Hero::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:100',
            'real_name'   => 'nullable|string|max:100',
            'power'       => 'required|string|max:150',
            'power_level' => 'required|integer|min:1|max:10000',
            'team_id'     => 'required|integer',
            'bio'         => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $hero->update([
            'name'        => $request->name,
            'real_name'   => $request->real_name,
            'power'       => $request->power,
            'power_level' => $request->power_level,
            'team_id'     => $request->team_id,
            'bio'         => $request->bio,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('heroes.show', $hero->id)
            ->with('success', 'Héroe actualizado correctamente.');
    }

    public function destroy($id)
    {
        $hero = Hero::findOrFail($id);
        $hero->delete();

        return redirect()->route('heroes.index')
            ->with('success', 'Héroe eliminado correctamente.');
    }

    public function active()
    {
        $heroes = Hero::with('team')->where('is_active', true)->get();
        return view('heroes.active', compact('heroes'));
    }

    public function powerful()
    {
        $heroes = Hero::with('team')
            ->where('power_level', '>', 8000)
            ->orderBy('power_level', 'desc')
            ->get();
        return view('heroes.powerful', compact('heroes'));
    }
}
```

---

## Paso 9: Actualizar las vistas

### Formularios: reemplazar el campo de texto por un select

El campo `team` de texto libre se reemplaza por un `<select>` que lista los equipos disponibles. Este cambio afecta a `create.blade.php` y `edit.blade.php`.

Añade primero el estilo del `<select>` en `public/css/heroes.css`:

```css
/* ========================================
   SELECT
   ======================================== */
.form-group select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 5px;
    font-size: 1rem;
    font-family: inherit;
    color: #1a1a1a;
    background-color: #ffffff;
    transition: border-color 0.3s;
    cursor: pointer;
}

.form-group select:focus {
    outline: none;
    border-color: #e23636;
}
```

En `create.blade.php`, sustituye el campo de texto de equipo por:

```html
<div class="form-group">
    <label for="team_id">Equipo</label>
    <select id="team_id" name="team_id">
        <option value="">— Selecciona un equipo —</option>
        @foreach($teams as $team)
            <option value="{{ $team->id }}"
                {{ old('team_id') == $team->id ? 'selected' : '' }}>
                {{ $team->name }}
            </option>
        @endforeach
    </select>
    @error('team_id')
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
```

`old('team_id') == $team->id` compara el valor previo de la sesión con el ID de cada opción para marcar la seleccionada si la validación falla.

En `edit.blade.php`, el `<select>` también necesita preseleccionar el equipo actual del héroe:

```html
<div class="form-group">
    <label for="team_id">Equipo</label>
    <select id="team_id" name="team_id">
        <option value="">— Selecciona un equipo —</option>
        @foreach($teams as $team)
            <option value="{{ $team->id }}"
                {{ old('team_id', $hero->team_id) == $team->id ? 'selected' : '' }}>
                {{ $team->name }}
            </option>
        @endforeach
    </select>
    @error('team_id')
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
```

`old('team_id', $hero->team_id)` sigue la misma lógica que en los demás campos: si hay datos de sesión usa esos, y si el formulario se abre por primera vez usa el `team_id` actual del héroe.

### Vistas de listado y detalle

En `index.blade.php`, `active.blade.php`, `powerful.blade.php` y `partials/hero-card.blade.php`, donde antes mostrabas el equipo con `{{ $hero->team }}`, ahora accedes al objeto relacionado:

```html
{{-- Antes --}}
<p>{{ $hero->team }}</p>

{{-- Ahora --}}
<p>{{ $hero->team->name }}</p>
```

En `show.blade.php`:

```html
<div class="info-row">
    <span class="label">Equipo</span>
    <span class="value">{{ $hero->team->name }}</span>
</div>
```

---

## Paso 10: Añadir TeamController y sus vistas

Con los equipos en su propia tabla, tiene sentido crear una sección que los liste y permita ver los héroes de cada uno.

```bash
php artisan make:controller TeamController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('heroes')->get();
        return view('teams.index', compact('teams'));
    }

    public function show($id)
    {
        $team = Team::with('heroes')->findOrFail($id);
        return view('teams.show', compact('team'));
    }
}
```

**`withCount('heroes')`** carga el número de héroes de cada equipo en una sola consulta añadiendo el atributo `heroes_count` a cada objeto `Team`. Es más eficiente que cargar todos los héroes solo para contarlos.

### Rutas de equipos

```php
// routes/web.php — añadir al final
use App\Http\Controllers\TeamController;

Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/teams/{id}', [TeamController::class, 'show'])->name('teams.show');
```

### Vista teams/index.blade.php

```html
{{-- resources/views/teams/index.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Equipos — Marvel Hub')

@section('contenido')
    <h1>Equipos</h1>

    @forelse($teams as $team)
        <div class="hero-card">
            <h2>{{ $team->name }}</h2>
            <p>{{ $team->description }}</p>
            <p><strong>Héroes:</strong> {{ $team->heroes_count }}</p>
            @if($team->founded)
                <p><strong>Fundado:</strong> {{ $team->founded }}</p>
            @endif
            <a href="{{ route('teams.show', $team->id) }}">Ver héroes</a>
        </div>
    @empty
        <p>No hay equipos registrados.</p>
    @endforelse
@endsection
```

### Vista teams/show.blade.php

```html
{{-- resources/views/teams/show.blade.php --}}
@extends('layouts.app')

@section('titulo', $team->name . ' — Marvel Hub')

@section('contenido')
    <a href="{{ route('teams.index') }}" class="back-link">&larr; Volver a equipos</a>

    <div class="hero-detail">
        <h1>{{ $team->name }}</h1>

        @if($team->description)
            <div class="info-row">
                <span class="label">Descripción</span>
                <span class="value">{{ $team->description }}</span>
            </div>
        @endif

        @if($team->founded)
            <div class="info-row">
                <span class="label">Fundado</span>
                <span class="value">{{ $team->founded }}</span>
            </div>
        @endif
    </div>

    <h2 style="color: #e23636; margin: 30px 0 15px 0;">Héroes del equipo</h2>

    @forelse($team->heroes as $hero)
        @include('partials.hero-card')
    @empty
        <p>Este equipo no tiene héroes asignados.</p>
    @endforelse
@endsection
```

### Actualizar el layout

Añade el enlace a equipos en la navegación de `resources/views/layouts/app.blade.php`:

```html
<nav>
    <a href="{{ route('heroes.index') }}">Héroes</a>
    <a href="{{ route('heroes.active') }}">Activos</a>
    <a href="{{ route('heroes.powerful') }}">Más poderosos</a>
    <a href="{{ route('teams.index') }}">Equipos</a>
</nav>
```

---

## Ejercicio Práctico: Plataforma de Música

Añade relaciones al ejercicio de música usando migraciones. Actualmente el campo `genero` de la tabla `albumes` es un string. Extráelo a su propia tabla y conecta ambas con una relación.

### Requisitos

1. Crea una migración para la tabla `generos` con los datos iniciales
2. Crea una migración para añadir `genero_id` a `albumes` y asignar los valores correctos
3. Ejecuta las migraciones y verifica con `migrate:status`
4. Crea el modelo `Genero` con `hasMany(Album::class)`
5. Actualiza el modelo `Album` con `belongsTo(Genero::class)` y `genero_id` en `$fillable`
6. Verifica las relaciones con Tinker
7. Actualiza `AlbumController` para usar `with('genero')` y pasar géneros a los formularios
8. Reemplaza el campo de texto de género por un `<select>` en los formularios
9. Actualiza las vistas para mostrar `$album->genero->name`
10. Crea `GeneroController` con `index()` usando `withCount` y `show()` con `with('albumes')`

### Comandos de migración

```bash
php artisan make:migration create_generos_table
php artisan make:migration add_genero_id_to_albumes_table
php artisan migrate
php artisan migrate:status
```

---

## Pistas y recordatorios

### Estructura de la migración para tabla nueva

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// ...

public function up(): void
{
    Schema::create('generos', function (Blueprint $table) {
        $table->id();
        $table->string('name', 100);
        $table->text('description')->nullable();
        $table->timestamps();
    });

    DB::table('generos')->insert([
        ['name' => 'Rock',  'description' => 'Rock clásico'],
        ['name' => 'Pop',   'description' => 'Música popular'],
        // ...
    ]);
}

public function down(): void
{
    Schema::dropIfExists('generos');
}
```

### Estructura de la migración para añadir columna

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// ...

public function up(): void
{
    Schema::table('albumes', function (Blueprint $table) {
        $table->unsignedBigInteger('genero_id')->nullable()->after('genero');
        $table->foreign('genero_id')->references('id')->on('generos');
    });

    DB::table('albumes')->where('genero', 'Rock')->update(['genero_id' => 1]);
    // ...
}

public function down(): void
{
    Schema::table('albumes', function (Blueprint $table) {
        $table->dropForeign(['genero_id']);
        $table->dropColumn('genero_id');
    });
}
```

### Select con valor preseleccionado en edición

```html
<select name="genero_id">
    @foreach($generos as $genero)
        <option value="{{ $genero->id }}"
            {{ old('genero_id', $album->genero_id) == $genero->id ? 'selected' : '' }}>
            {{ $genero->name }}
        </option>
    @endforeach
</select>
```

---

## Verificación

- [ ] Migración `create_teams_table` ejecutada correctamente
- [ ] Migración `add_team_id_to_heroes_table` ejecutada correctamente
- [ ] `php artisan migrate:status` muestra ambas migraciones como `Ran`
- [ ] Modelo `Team` con `hasMany(Hero::class)` y `$fillable`
- [ ] Modelo `Hero` con `belongsTo(Team::class)` y `team_id` en `$fillable`
- [ ] Tinker confirma que `$hero->team->name` devuelve el nombre correcto
- [ ] Tinker confirma que `$team->heroes` devuelve la colección de héroes
- [ ] `HeroController` usa `with('team')` en `index()`, `show()`, `active()` y `powerful()`
- [ ] Los formularios de crear y editar usan `<select>` con los equipos disponibles
- [ ] La validación usa `team_id` en lugar de `team`
- [ ] Las vistas muestran `$hero->team->name`
- [ ] `TeamController` con `index()` usando `withCount` y `show()` con `with('heroes')`
- [ ] Vistas de equipos creadas y funcionales
- [ ] El layout incluye el enlace a equipos en la navegación
- [ ] Al ejecutar `migrate:rollback` la base de datos vuelve al estado anterior
