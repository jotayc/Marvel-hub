# Fase 4: Modelos y Eloquent ORM

---

## De dónde partimos

En la **Fase 3** trabajaste con el proyecto **Marvel Hub** usando **Query Builder** para acceder a la base de datos:

```php
// HeroController.php (Fase 3)
use Illuminate\Support\Facades\DB;

public function index()
{
    $heroes = DB::table('heroes')->get();
    return view('heroes.index', ['heroes' => $heroes]);
}

public function show($id)
{
    $hero = DB::table('heroes')->find($id);
    if (!$hero) {
        abort(404);
    }
    return view('heroes.show', ['hero' => $hero]);
}
```

Esta forma funciona perfectamente, pero Laravel ofrece una herramienta más potente y elegante: **Eloquent ORM**.

En esta fase vas a transformar tu proyecto Marvel Hub para usar **modelos Eloquent** en lugar de Query Builder.

---

## Preparación: Crear una rama nueva

Antes de empezar, vamos a crear una rama nueva en Git para trabajar con Eloquent sin modificar la Fase 3.

**Si estás usando Git:**

```bash
# Asegúrate de estar en la rama de la Fase 3
git checkout 3.AccesoBBDD

# Crear la nueva rama y cambiar a ella
git checkout -b 4.Eloquent
```

**Si no usas Git:** Puedes continuar directamente modificando el proyecto.

---

## Paso 1: Crear el Modelo Hero

Un **modelo** es una clase PHP que representa una tabla de la base de datos. Laravel incluye un comando Artisan para crear modelos.

### 1.1. Generar el modelo

Abre tu terminal en la raíz del proyecto y ejecuta:

```bash
php artisan make:model Hero
```

**Salida esperada:**

```
INFO  Model [app/Models/Hero.php] created successfully.
```

**¿Dónde se crea?** En `app/Models/Hero.php`

### 1.2. Estructura del modelo generado

Abre el archivo `app/Models/Hero.php` y verás:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    //
}
```

A primera vista puede llamar la atención que la clase esté prácticamente vacía. Antes de analizar línea por línea, vale la pena entender por qué.

**¿Por qué el modelo no tiene atributos privados ni métodos getters/setters?**

En PHP orientado a objetos clásico estás acostumbrado a clases con esta estructura:

```php
class Hero {
    private string $name;
    private string $power;

    public function __construct(string $name, string $power) {
        $this->name = $name;
        $this->power = $power;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }
}
```

Un modelo Eloquent es diferente porque sigue el patrón **Active Record**: cada instancia del modelo representa un registro de la base de datos y, al mismo tiempo, sabe cómo leer, guardar y eliminar ese registro. No necesitas declarar atributos ni getters/setters porque Eloquent los gestiona de forma dinámica mediante los **métodos mágicos de PHP** (`__get` y `__set`). Cuando escribes `$hero->name`, Eloquent intercepta ese acceso y busca el valor en los datos que recuperó de la base de datos.

Este enfoque responde a una filosofía de Laravel llamada **Convention over Configuration** (convención sobre configuración): en lugar de escribir todo el código repetitivo, describes qué es tu modelo y Laravel se encarga del resto.

**La clave está en la herencia.** Al escribir `class Hero extends Model`, tu clase hereda cientos de líneas de código que ya están escritas en la clase `Model` de Laravel.

**¿Qué es `Model`?**

`Model` (de `Illuminate\Database\Eloquent\Model`) es la clase base que proporciona Laravel. Contiene toda la lógica necesaria para:

- Conectarse a la base de datos y ejecutar consultas
- Traducir los resultados de SQL en objetos PHP
- Gestionar los atributos del registro de forma dinámica
- Manejar automáticamente `created_at` y `updated_at`
- Convertir tipos de datos (`$casts`)
- Soportar relaciones entre tablas (próximas fases)

Tu clase `Hero` hereda todo eso. Tú solo defines **qué es específico de un héroe**: qué campos se pueden rellenar, qué tabla usa si no sigue la convención, etc.

**Análisis del código generado:**

```php
namespace App\Models;
```
- Define que esta clase vive en el namespace `App\Models`
- Laravel busca los modelos en `app/Models/`

```php
use Illuminate\Database\Eloquent\Model;
```
- Importa la clase `Model` para poder usarla en este archivo
- Sin este `use`, PHP no sabría de dónde viene `Model`

```php
class Hero extends Model
```
- `Hero` hereda de `Model`
- A partir de este momento `Hero` tiene acceso a `find()`, `all()`, `create()`, `update()`, `delete()` y muchos más métodos, sin escribir ninguno

```php
{
    //
}
```
- El cuerpo está vacío porque, con las convenciones de Laravel, no hace falta nada más para empezar a funcionar
- Aquí es donde añadirás las propiedades de configuración como `$fillable` o `$casts`

### 1.3. Convenciones de Eloquent

Laravel sigue estas reglas automáticas:

| Convención | Ejemplo |
|------------|---------|
| **Nombre del modelo** | `Hero` (singular, PascalCase) |
| **Nombre de la tabla** | `heroes` (plural, minúsculas) |
| **Clave primaria** | `id` |
| **Timestamps** | `created_at`, `updated_at` |

**¿Qué significa esto?**

Cuando escribes `Hero::all()`, Laravel automáticamente:
1. Busca la tabla `heroes` (plural del modelo)
2. Espera una columna `id` como clave primaria
3. Espera columnas `created_at` y `updated_at`

**Si tu tabla se llama diferente:**

```php
class Hero extends Model
{
    protected $table = 'mis_heroes'; // Nombre personalizado
}
```

**Si no usas timestamps:**

```php
class Hero extends Model
{
    public $timestamps = false;
}
```

En nuestro caso, la tabla `heroes` ya sigue las convenciones, así que no necesitamos configurar nada más.

### 1.4. Configurar campos permitidos ($fillable)

Por seguridad, Eloquent no permite asignar valores masivamente sin tu permiso explícito. Debes indicar qué campos se pueden rellenar.

**¿Qué es asignación masiva?**

Es crear o actualizar registros pasando un array:

```php
Hero::create([
    'name' => 'Thor',
    'power' => 'Trueno'
]);
```

Sin configurar `$fillable`, obtendrías este error:

```
MassAssignmentException
Add [name] to fillable property to allow mass assignment
```

**Solución:** Añade la propiedad `$fillable` al modelo.

Edita `app/Models/Hero.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'name',
        'real_name',
        'power',
        'power_level',
        'team',
        'bio',
        'is_active'
    ];
}
```

**Importante:** 
- NO incluyas `id`, `created_at`, `updated_at` → Se manejan automáticamente
- Solo lista campos que el usuario puede modificar
- Esto protege contra ataques de asignación masiva

**Alternativa: $guarded**

En lugar de listar lo que SÍ se puede llenar, puedes listar lo que NO:

```php
protected $guarded = ['id']; // Protege solo el ID
```

**Recomendación:** Usa `$fillable` (lista blanca) por mayor seguridad.

### 1.5. Configuraciones adicionales (opcional)

Puedes añadir más configuraciones al modelo:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    // Tabla (si no sigue convenciones)
    protected $table = 'heroes';
    
    // Campos rellenables
    protected $fillable = [
        'name',
        'real_name',
        'power',
        'power_level',
        'team',
        'bio',
        'is_active'
    ];
    
    // Convertir tipos automáticamente
    protected $casts = [
        'power_level' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
```

**¿Qué hace `$casts`?**

Convierte automáticamente los valores de la base de datos al tipo PHP correcto:
- `'is_active' => 'boolean'` → Convierte 0/1 a `false`/`true`
- `'power_level' => 'integer'` → Convierte string a número entero
- `'created_at' => 'datetime'` → Convierte a objeto Carbon (fechas)

**Por ahora, con `$fillable` es suficiente.**

---

## ¿Qué es Eloquent ORM?

Ya tienes tu modelo `Hero` creado. Ahora entendamos qué es Eloquent y por qué es mejor que Query Builder.

### Definición

**Eloquent** es el **ORM** (Object-Relational Mapping) de Laravel.

**ORM** = Puente entre tus objetos PHP y las tablas de la base de datos.

En lugar de escribir SQL o usar `DB::table()`, trabajas directamente con **objetos** que representan registros de la base de datos.

### Comparación: Query Builder vs Eloquent

**Query Builder (Fase 3):**

```php
use Illuminate\Support\Facades\DB;

// Obtener todos los héroes
$heroes = DB::table('heroes')->get();

// Buscar por ID
$hero = DB::table('heroes')->find(1);

// Filtrar
$heroes = DB::table('heroes')->where('team', 'Vengadores')->get();

// Crear nuevo héroe
DB::table('heroes')->insert([
    'name' => 'Iron Man',
    'power' => 'Tecnología',
    'team' => 'Vengadores'
]);

// Los resultados son objetos stdClass genéricos
echo $hero->name; // funciona
```

**Eloquent (Fase 4):**

```php
use App\Models\Hero;

// Obtener todos los héroes
$heroes = Hero::all();

// Buscar por ID
$hero = Hero::find(1);

// Filtrar
$heroes = Hero::where('team', 'Vengadores')->get();

// Crear nuevo héroe
Hero::create([
    'name' => 'Iron Man',
    'power' => 'Tecnología',
    'team' => 'Vengadores'
]);

// Los resultados son objetos Hero (modelos)
echo $hero->name; // funciona
```

### Ventajas de Eloquent

| Query Builder | Eloquent |
|---------------|----------|
| `DB::table('heroes')` | `Hero::` (más corto y legible) |
| Objetos genéricos `stdClass` | Objetos `Hero` con lógica propia |
| Sin validaciones | Puede incluir validaciones |
| Sin relaciones | Soporta relaciones (futuro) |
| Sin métodos personalizados | Métodos personalizados en modelo |
| Acceso manual a timestamps | Timestamps automáticos |

**En resumen:**
- **Query Builder:** Más flexible, más SQL-like
- **Eloquent:** Más elegante, orientado a objetos, más Laravel

---

## Paso 2: Actualizar HeroController para usar Eloquent

Ahora vamos a transformar tu `HeroController` de la Fase 3 para usar el modelo `Hero` en lugar de `DB::table()`.

### 2.1. Cambiar el import

Abre `app/Http/Controllers/HeroController.php`.

**Antes (Fase 3):**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeroController extends Controller
{
    // ...
}
```

**Después (Fase 4):**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero;  // ← Cambio aquí

class HeroController extends Controller
{
    // ...
}
```

**Importante:** Ya NO necesitas `use Illuminate\Support\Facades\DB;`

### 2.2. Una mejora: la función compact()

Antes de actualizar los métodos, vale la pena conocer `compact()`, una función de PHP que a partir de esta fase usaremos habitualmente al pasar datos a las vistas.

Hasta ahora en la Fase 3 escribías:

```php
return view('heroes.index', ['heroes' => $heroes]);
```

Con `compact()` puedes escribir lo mismo de forma más corta:

```php
return view('heroes.index', compact('heroes'));
```

`compact('heroes')` construye automáticamente el array `['heroes' => $heroes]`: toma la variable `$heroes` que existe en ese momento y usa su nombre como clave. Cuando necesitas pasar varias variables la diferencia es más evidente:

```php
// Sin compact
return view('heroes.team', ['heroes' => $heroes, 'team' => $team]);

// Con compact
return view('heroes.team', compact('heroes', 'team'));
```

El resultado es idéntico en ambos casos. `compact()` no hace nada que no pudieras hacer con un array, simplemente elimina la repetición de escribir el nombre de la variable dos veces.

### 2.3. Método index() - Listar todos los héroes

**Antes (Query Builder):**

```php
public function index()
{
    $heroes = DB::table('heroes')->get();
    return view('heroes.index', ['heroes' => $heroes]);
}
```

**Después (Eloquent):**

```php
public function index()
{
    $heroes = Hero::all();
    return view('heroes.index', compact('heroes'));
}
```

**Cambios:**
- `DB::table('heroes')->get()` → `Hero::all()`
- Más corto y semántico

### 2.4. Método show() - Mostrar detalle de un héroe

**Antes (Query Builder):**

```php
public function show($id)
{
    $hero = DB::table('heroes')->find($id);
    
    if (!$hero) {
        abort(404);
    }
    
    return view('heroes.show', ['hero' => $hero]);
}
```

**Después (Eloquent):**

```php
public function show($id)
{
    $hero = Hero::findOrFail($id);
    return view('heroes.show', compact('hero'));
}
```

**Cambios:**
- `DB::table('heroes')->find($id)` → `Hero::findOrFail($id)`
- Ya NO necesitas el `if (!$hero)` → `findOrFail()` lanza 404 automáticamente

### 2.5. Método active() - Héroes activos

**Antes (Query Builder):**

```php
public function active()
{
    $heroes = DB::table('heroes')
        ->where('is_active', 1)
        ->get();
    
    return view('heroes.active', ['heroes' => $heroes]);
}
```

**Después (Eloquent):**

```php
public function active()
{
    $heroes = Hero::where('is_active', true)->get();
    return view('heroes.active', compact('heroes'));
}
```

**Cambios:**
- `DB::table('heroes')` → `Hero::`
- `->where('is_active', 1)` → `->where('is_active', true)`
  - Gracias a `$casts`, puedes usar booleanos directamente

### 2.6. Método powerful() - Héroes poderosos

**Antes (Query Builder):**

```php
public function powerful()
{
    $heroes = DB::table('heroes')
        ->where('power_level', '>', 8000)
        ->orderBy('power_level', 'desc')
        ->get();
    
    return view('heroes.powerful', ['heroes' => $heroes]);
}
```

**Después (Eloquent):**

```php
public function powerful()
{
    $heroes = Hero::where('power_level', '>', 8000)
        ->orderBy('power_level', 'desc')
        ->get();
    
    return view('heroes.powerful', compact('heroes'));
}
```

**Cambios:**
- `DB::table('heroes')` → `Hero::`
- El resto es idéntico (Eloquent usa los mismos métodos de Query Builder)

### 2.7. Método team() - Héroes por equipo

**Antes (Query Builder):**

```php
public function team($team)
{
    $heroes = DB::table('heroes')
        ->where('team', $team)
        ->get();
    
    return view('heroes.team', ['heroes' => $heroes, 'team' => $team]);
}
```

**Después (Eloquent):**

```php
public function team($team)
{
    $heroes = Hero::where('team', $team)->get();
    return view('heroes.team', compact('heroes', 'team'));
}
```

**Cambios:**
- `DB::table('heroes')` → `Hero::`

### 2.8. HeroController completo con Eloquent

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hero;

class HeroController extends Controller
{
    // Listar todos los héroes
    public function index()
    {
        $heroes = Hero::all();
        return view('heroes.index', compact('heroes'));
    }
    
    // Mostrar detalle de un héroe
    public function show($id)
    {
        $hero = Hero::findOrFail($id);
        return view('heroes.show', compact('hero'));
    }
    
    // Héroes activos
    public function active()
    {
        $heroes = Hero::where('is_active', true)->get();
        return view('heroes.active', compact('heroes'));
    }
    
    // Héroes poderosos (power_level > 8000)
    public function powerful()
    {
        $heroes = Hero::where('power_level', '>', 8000)
            ->orderBy('power_level', 'desc')
            ->get();
        
        return view('heroes.powerful', compact('heroes'));
    }
    
    // Héroes por equipo
    public function team($team)
    {
        $heroes = Hero::where('team', $team)->get();
        return view('heroes.team', compact('heroes', 'team'));
    }
}
```

### 2.9. Probar los cambios

**No necesitas modificar:**
- ✅ Las rutas (`routes/web.php`)
- ✅ Las vistas (`resources/views/heroes/`)
- ✅ La base de datos

**Solo cambiaste:**
- ✅ Creaste el modelo `Hero`
- ✅ Actualizaste el controlador para usar `Hero::` en lugar de `DB::table()`

**Prueba tu aplicación:**

```bash
php artisan serve
```

Accede a:
- `http://localhost:8000/heroes` → Debe funcionar igual
- `http://localhost:8000/heroes/1` → Debe mostrar detalle
- `http://localhost:8000/heroes/activos` → Debe funcionar

**Si todo funciona, ¡felicidades!** Has migrado de Query Builder a Eloquent.

---

## Métodos Eloquent más comunes

Ahora que tienes tu modelo funcionando, veamos los métodos más útiles de Eloquent.

### Consultar datos

```php
// Todos los registros
$heroes = Hero::all();

// Primer registro
$hero = Hero::first();

// Buscar por ID
$hero = Hero::find(1);

// Buscar por ID o error 404
$hero = Hero::findOrFail(1);

// Con condiciones
$heroes = Hero::where('team', 'Vengadores')->get();
$heroes = Hero::where('power_level', '>', 8000)->get();

// Primera coincidencia con condición
$hero = Hero::where('name', 'Thor')->first();

// Primera coincidencia o error 404
$hero = Hero::where('name', 'Thor')->firstOrFail();

// Ordenar
$heroes = Hero::orderBy('power_level', 'desc')->get();

// Limitar resultados
$heroes = Hero::orderBy('power_level', 'desc')->limit(3)->get();

// Contar
$total = Hero::count();
$totalActivos = Hero::where('is_active', true)->count();
```

### Crear registros

```php
// Forma 1: create() - Asignación masiva
$hero = Hero::create([
    'name' => 'Iron Man',
    'real_name' => 'Tony Stark',
    'power' => 'Tecnología',
    'power_level' => 8500,
    'team' => 'Vengadores',
    'bio' => 'Genio, millonario, playboy, filántropo',
    'is_active' => true
]);

// Forma 2: new + save()
$hero = new Hero();
$hero->name = 'Captain America';
$hero->real_name = 'Steve Rogers';
$hero->power = 'Super soldado';
$hero->power_level = 8000;
$hero->team = 'Vengadores';
$hero->bio = 'El primer Vengador';
$hero->is_active = true;
$hero->save();
```

### Actualizar registros

```php
// Forma 1: Buscar y actualizar
$hero = Hero::find(1);
$hero->power_level = 9000;
$hero->save();

// Forma 2: update() - Asignación masiva
$hero = Hero::find(1);
$hero->update([
    'power_level' => 9000,
    'bio' => 'Nueva biografía'
]);

// Forma 3: where()->update() - Actualizar múltiples
Hero::where('team', 'Vengadores')
    ->update(['is_active' => true]);
```

### Eliminar registros

```php
// Buscar y eliminar
$hero = Hero::find(1);
$hero->delete();

// Eliminar por ID
Hero::destroy(1);

// Eliminar múltiples IDs
Hero::destroy([1, 2, 3]);

// Eliminar con condición
Hero::where('power_level', '<', 1000)->delete();
```

---

## Diferencias clave: Query Builder vs Eloquent

### Métodos terminales

Ambos comparten muchos métodos, pero hay diferencias:

| Query Builder | Eloquent | Diferencia |
|--------------|----------|----------|
| `->get()` | `->get()` o `::all()` | Eloquent tiene `all()` |
| `->find($id)` | `::find($id)` | Sintaxis distinta |
| NO existe | `::findOrFail($id)` | Solo Eloquent |
| `->first()` | `->first()` | Igual |
| NO existe | `->firstOrFail()` | Solo Eloquent |

### Creación de registros

| Query Builder | Eloquent |
|--------------|----------|
| `DB::table('heroes')->insert([...])` | `Hero::create([...])` |
| NO retorna el objeto creado | Retorna el modelo creado |

### Actualización

| Query Builder | Eloquent |
|--------------|----------|
| `DB::table('heroes')->where(...)->update([...])` | `$hero->update([...])` |
| Actualiza múltiples siempre | Puede actualizar uno o múltiples |

### Objetos retornados

| Query Builder | Eloquent |
|--------------|----------|
| Objetos `stdClass` | Objetos `Hero` (modelo) |
| Sin métodos personalizados | Puede tener métodos personalizados |

---

## Probar con Tinker

Tinker es perfecto para experimentar con Eloquent.

```bash
php artisan tinker
```

### Ejemplos:

```php
// Importar modelo
use App\Models\Hero;

// Ver todos
Hero::all();

// Buscar uno
$hero = Hero::find(1);
$hero->name;
$hero->power;

// Crear
$hero = Hero::create([
    'name' => 'Black Widow',
    'real_name' => 'Natasha Romanoff',
    'power' => 'Espionaje',
    'power_level' => 7500,
    'team' => 'Vengadores',
    'bio' => 'Espía de élite',
    'is_active' => true
]);

// Actualizar
$hero = Hero::find(1);
$hero->power_level = 9500;
$hero->save();

// Eliminar
$hero = Hero::find(5);
$hero->delete();

// Consultas
Hero::where('team', 'Vengadores')->count();
Hero::where('power_level', '>', 8000)->get();
Hero::orderBy('power_level', 'desc')->first();
```

---

## Ejercicio Práctico: Sistema de Películas

Ahora que dominas Eloquent con el modelo `Hero`, vamos a crear un sistema independiente para gestionar películas.

### Contexto

Eres desarrollador en una plataforma de streaming. Te piden crear un sistema para mostrar el catálogo de películas clásicas.

**Requisitos del cliente:**

1. Mostrar listado completo de películas
2. Ver ficha detallada de cada película
3. Filtrar películas disponibles para ver
4. Mostrar las mejor valoradas (calificación ≥ 8.5)
5. Filtrar películas por género

### Datos de ejemplo

Vas a trabajar con estas 10 películas clásicas:

| Título | Director | Año | Género | Duración | Calificación | Disponible |
|--------|----------|-----|--------|----------|--------------|------------|
| El Padrino | Francis Ford Coppola | 1972 | Drama | 175 | 9.2 | Sí |
| Pulp Fiction | Quentin Tarantino | 1994 | Crimen | 154 | 8.9 | Sí |
| El Caballero Oscuro | Christopher Nolan | 2008 | Acción | 152 | 9.0 | No |
| 12 Hombres sin Piedad | Sidney Lumet | 1957 | Drama | 96 | 9.0 | Sí |
| La Lista de Schindler | Steven Spielberg | 1993 | Drama | 195 | 9.0 | Sí |
| El Señor de los Anillos: El Retorno del Rey | Peter Jackson | 2003 | Fantasía | 201 | 9.0 | No |
| Forrest Gump | Robert Zemeckis | 1994 | Drama | 142 | 8.8 | Sí |
| Inception | Christopher Nolan | 2010 | Ciencia Ficción | 148 | 8.8 | Sí |
| Matrix | Lana Wachowski | 1999 | Ciencia Ficción | 136 | 8.7 | No |
| Goodfellas | Martin Scorsese | 1990 | Crimen | 145 | 8.7 | Sí |

---

## Tareas a realizar

### Tarea 1: Crear base de datos y tabla

Usa phpMyAdmin para crear la base de datos y tabla.

**Requisitos:**
- Base de datos: `cine`
- Tabla: `peliculas`
- Collation: `utf8mb4_unicode_ci`
- Campos:
  - `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
  - `titulo` (VARCHAR 150)
  - `director` (VARCHAR 100)
  - `año` (INT)
  - `genero` (VARCHAR 50)
  - `duracion` (INT, minutos)
  - `sinopsis` (TEXT)
  - `calificacion` (DECIMAL 3,1)
  - `disponible` (TINYINT 1, default 1)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)

Inserta las 10 películas de la tabla anterior.

### Tarea 2: Configurar Laravel para la base de datos

Edita el archivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cine
DB_USERNAME=root
DB_PASSWORD=root
DB_COLLATION=utf8mb4_unicode_ci
```

Limpia la caché:

```bash
php artisan config:clear
```

### Tarea 3: Crear el modelo Movie

Crea el modelo con Artisan y configúralo correctamente.

**Requisitos:**
- Nombre del modelo: `Movie`
- Ubicación: `app/Models/Movie.php`
- Configurar `$table` si es necesario
- Configurar `$fillable` con todos los campos
- Configurar `$casts` para tipos correctos

### Tarea 4: Probar con Tinker

Antes de crear el controlador, verifica que el modelo funciona.

```bash
php artisan tinker
```

Prueba:
- Obtener todas las películas
- Buscar una película por ID
- Filtrar por género
- Contar películas disponibles

### Tarea 5: Crear MovieController

Crea el controlador con 5 métodos:

1. `index()` - Listar todas las películas
2. `show($id)` - Mostrar detalle de una película
3. `disponibles()` - Películas disponibles
4. `mejores()` - Películas con calificación ≥ 8.5
5. `genero($genero)` - Películas por género

### Tarea 6: Crear las rutas

Define las rutas en `routes/web.php`:

```
GET /peliculas → index()
GET /peliculas/{id} → show()
GET /peliculas/disponibles → disponibles()
GET /peliculas/mejores → mejores()
GET /peliculas/genero/{genero} → genero()
```

### Tarea 7: Crear las vistas

Crea vistas Blade en `resources/views/peliculas/`:

1. `index.blade.php` - Tarjetas con todas las películas
2. `show.blade.php` - Ficha detallada
3. `disponibles.blade.php` - Solo disponibles
4. `mejores.blade.php` - Mejor valoradas
5. `genero.blade.php` - Por género

Incluye en las vistas:
- Título, director, año
- Género y duración
- Calificación con estrellas o badge
- Indicador visual si está disponible
- Enlaces entre vistas

---

## Pistas y recordatorios

### Sobre el Modelo

```php
class Movie extends Model
{
    protected $table = 'peliculas'; // Si no sigue convención
    
    protected $fillable = [
        'titulo',
        'director',
        'año',
        // ... resto de campos
    ];
    
    protected $casts = [
        'año' => 'integer',
        'duracion' => 'integer',
        'calificacion' => 'decimal:1',
        'disponible' => 'boolean'
    ];
}
```

### Sobre el Controlador

```php
use App\Models\Movie;

public function index()
{
    $peliculas = Movie::all();
    return view('peliculas.index', compact('peliculas'));
}

public function show($id)
{
    $pelicula = Movie::findOrFail($id);
    return view('peliculas.show', compact('pelicula'));
}

public function mejores()
{
    $peliculas = Movie::where('calificacion', '>=', 8.5)
        ->orderBy('calificacion', 'desc')
        ->get();
    
    return view('peliculas.mejores', compact('peliculas'));
}
```

### Sobre las Rutas

```php
use App\Http\Controllers\MovieController;

Route::get('/peliculas', [MovieController::class, 'index'])->name('peliculas.index');
Route::get('/peliculas/{id}', [MovieController::class, 'show'])->name('peliculas.show');
// ... resto de rutas
```

### Sobre las Vistas

```blade
@foreach($peliculas as $pelicula)
    <div class="pelicula">
        <h3>{{ $pelicula->titulo }}</h3>
        <p>Director: {{ $pelicula->director }}</p>
        <p>Año: {{ $pelicula->año }}</p>
        <p>Calificación: {{ $pelicula->calificacion }}/10</p>
        
        @if($pelicula->disponible)
            <span class="badge disponible">Disponible</span>
        @else
            <span class="badge no-disponible">No disponible</span>
        @endif
        
        <a href="{{ route('peliculas.show', $pelicula->id) }}">Ver detalles</a>
    </div>
@endforeach
```

---

## Verificación

Antes de consultar las soluciones, verifica:

- [ ] Base de datos `cine` creada con 10 películas
- [ ] Archivo `.env` configurado correctamente
- [ ] Modelo `Movie` creado con `$fillable` y `$casts`
- [ ] Tinker muestra películas correctamente
- [ ] `MovieController` con 5 métodos funcionales
- [ ] 5 rutas definidas y nombradas
- [ ] 5 vistas creadas con Blade
- [ ] Al acceder a `/peliculas` se muestran todas las películas
- [ ] Al acceder a `/peliculas/1` se muestra detalle
- [ ] Filtros de disponibles, mejores y género funcionan
- [ ] Las vistas tienen enlaces de navegación entre ellas

---

## Reflexión Final

### Comparación de enfoques

**Fase 2:** Datos en arrays dentro del controlador  
**Fase 3:** Datos en base de datos con Query Builder  
**Fase 4:** Datos en base de datos con Eloquent ORM

**Evolución:**

```php
// Fase 2: Arrays
$heroes = [
    ['name' => 'Thor', 'power' => 'Trueno'],
    // ...
];

// Fase 3: Query Builder
$heroes = DB::table('heroes')->get();

// Fase 4: Eloquent
$heroes = Hero::all();
```

### Ventajas evidentes de Eloquent

- ✅ Código más corto y expresivo
- ✅ Modelos orientados a objetos
- ✅ Métodos automáticos como `findOrFail()`
- ✅ Timestamps gestionados automáticamente
- ✅ Casting de tipos automático
- ✅ Preparado para relaciones (próximas fases)

Eloquent no reemplaza Query Builder, ambos conviven en Laravel. Usa Eloquent para la mayoría de casos y Query Builder para consultas muy complejas o específicas.
