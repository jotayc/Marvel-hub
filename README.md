# Fase 3: Acceso a la Base de Datos

En la Fase 2 creamos controladores que devuelven datos, pero esos datos están "hardcodeados" en arrays dentro del código. Cada vez que quieras añadir un héroe, tendrías que modificar el código del controlador. Esto no es práctico.

En esta fase aprenderás a conectar con la base de datos real que creaste en la Fase 0 y a obtener datos dinámicos.

---

## ¿Qué es una Base de Datos Relacional?

Una **base de datos relacional** es un sistema que almacena información organizada en **tablas** (como hojas de Excel). Cada tabla tiene:

- **Columnas**: Definen qué tipo de información se guarda (nombre, poder, nivel_poder, etc.)
- **Filas**: Cada fila es un registro individual (un héroe específico)

**Ventajas sobre arrays en código:**

| Arrays en código | Base de datos |
|-----------------|---------------|
| ❌ Datos fijos en archivos | ✅ Datos separados del código |
| ❌ Cambios requieren modificar código | ✅ Cambios sin tocar código |
| ❌ Difícil gestionar muchos datos | ✅ Optimizado para millones de registros |
| ❌ Un usuario no puede añadir datos | ✅ Usuarios pueden crear/editar datos |
| ❌ Sin búsquedas complejas | ✅ Búsquedas, filtros, ordenación |

---

## Repaso: Base de Datos marvel_hub

En la Fase 0 creaste la base de datos `marvel_hub` con la tabla `heroes` y datos de ejemplo.

**Estructura de la tabla heroes:**

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | INT | Identificador único (clave primaria) |
| `name` | VARCHAR(100) | Nombre del héroe |
| `real_name` | VARCHAR(100) | Nombre real |
| `power` | VARCHAR(255) | Descripción del poder |
| `power_level` | INT | Nivel de poder (0-10000) |
| `team` | VARCHAR(100) | Equipo al que pertenece |
| `bio` | TEXT | Biografía del héroe |
| `is_active` | TINYINT(1) | 1 = activo, 0 = inactivo |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Fecha de última actualización |

**Datos de ejemplo insertados:**
- Iron Man (Tony Stark)
- Thor (Thor Odinson)
- Spider-Man (Peter Parker)
- Doctor Strange (Stephen Strange)
- Black Widow (Natasha Romanoff)

---

## Verificar Configuración de la Base de Datos

Antes de acceder a la BD desde Laravel, verifica que la configuración en `.env` es correcta.

Abre el archivo `.env` en la raíz del proyecto:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marvel_hub
DB_USERNAME=root
DB_PASSWORD=root
```

**Parámetros:**

- `DB_CONNECTION=mysql` → Tipo de base de datos (MySQL)
- `DB_HOST=127.0.0.1` → Dirección del servidor (localhost)
- `DB_PORT=3306` → Puerto de MySQL
- `DB_DATABASE=marvel_hub` → Nombre de la base de datos
- `DB_USERNAME=root` → Usuario de MySQL
- `DB_PASSWORD=root` → Contraseña (en MAMP por defecto es `root`)

**Si modificas `.env`, limpia la caché:**

```bash
php artisan config:clear
```

---

## Probar Conexión con Tinker

Antes de modificar código, verifica que Laravel puede conectarse a la BD.

**¿Qué es Tinker?**

Tinker es una herramienta interactiva de Laravel (ya la usaste en Fase 0) que te permite ejecutar código PHP y consultas a la BD en tiempo real, sin crear archivos.

**Iniciar Tinker:**

```bash
php artisan tinker
```

**Ejecutar una consulta de prueba:**

```php
DB::table('heroes')->count();
```

Debería devolver `5` (el número de héroes insertados en Fase 0).

**Salir de Tinker:**

```php
exit
```

**Si da error:**
- Verifica que la BD `marvel_hub` existe en phpMyAdmin
- Verifica que la tabla `heroes` tiene datos
- Verifica las credenciales en `.env`

---

## ¿Qué es el Query Builder?

El **Query Builder** es una herramienta de Laravel que te permite construir consultas SQL de forma segura usando métodos PHP en lugar de escribir SQL directamente.

**Ventajas:**

1. **Más seguro** - Protege contra inyección SQL automáticamente
2. **Más legible** - Código PHP en lugar de strings SQL
3. **Independiente de la BD** - Funciona igual en MySQL, PostgreSQL, SQLite, etc.
4. **Autocompletado** - El IDE puede sugerirte métodos

**Comparación:**

```php
// SQL tradicional (string)
$sql = "SELECT * FROM heroes WHERE power_level > 8000";
$heroes = DB::select($sql);

// Query Builder (métodos PHP)
$heroes = DB::table('heroes')->where('power_level', '>', 8000)->get();
```

**¿Qué es DB?**

`DB` es una **facade** de Laravel. Una facade es una forma simplificada de acceder a funcionalidades complejas de Laravel. En este caso, `DB` te da acceso al Query Builder.

---

## Primera Consulta: Obtener Todos los Héroes

Vamos a modificar el método `index()` del `HeroController` para obtener datos de la BD.

### Paso 1: Importar la facade DB

Edita `app/Http/Controllers/HeroController.php` y agrega el `use` al principio:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  // ← NUEVA LÍNEA

class HeroController extends Controller
{
    // ...
}
```

**¿Por qué importar?**

Para poder usar `DB::table()` necesitas importar la clase. El `use` le dice a PHP dónde encontrar la clase `DB`.

### Paso 2: Modificar el método index()

Reemplaza el método `index()`:

```php
public function index()
{
    // Obtener todos los héroes de la base de datos
    $heroes = DB::table('heroes')->get();
    
    return view('heroes.index', ['heroes' => $heroes]);
}
```

**Desglose:**

```php
DB::table('heroes')
```
Indica que vamos a consultar la tabla `heroes`.

```php
->get()
```
Ejecuta la consulta y devuelve **todos** los registros de la tabla.

**Resultado:**

`$heroes` ahora es una **colección** de objetos, no un array. Cada objeto representa una fila de la tabla.

### Paso 3: Adaptar la vista

La vista espera acceder a los datos con `$hero['name']`, pero ahora `$hero` es un objeto, no un array.

Edita `resources/views/heroes/index.blade.php`:

**Antes (con arrays):**
```html
<div class="hero-name">{{ $hero['name'] }}</div>
<div class="hero-real-name">{{ $hero['real_name'] }}</div>
<div class="hero-power">{{ $hero['power'] }}</div>
```

**Ahora (con objetos):**
```html
<div class="hero-name">{{ $hero->name }}</div>
<div class="hero-real-name">{{ $hero->real_name }}</div>
<div class="hero-power">{{ $hero->power }}</div>
```

**Cambio:** `$hero['campo']` → `$hero->campo`

También cambia el enlace:

**Antes:**
```html
<a href="{{ route('heroes.show', $hero['id']) }}" class="hero-card">
```

**Ahora:**
```html
<a href="{{ route('heroes.show', $hero->id) }}" class="hero-card">
```

### Paso 4: Probar

Inicia el servidor:

```bash
php artisan serve
```

Visita: http://localhost:8000/heroes

Deberías ver los 5 héroes de la base de datos.

---

## Consulta con WHERE: Obtener un Héroe Específico

Ahora vamos a modificar el método `show()` para obtener un héroe por su ID.

### Opción 1: Usando where() + first()

Edita el método `show()` en `HeroController.php`:

```php
public function show($id)
{
    // Buscar héroe por ID
    $hero = DB::table('heroes')->where('id', $id)->first();
    
    // Verificar si existe
    if (!$hero) {
        abort(404, 'Héroe no encontrado');
    }
    
    return view('heroes.show', ['hero' => $hero]);
}
```

**Desglose:**

```php
->where('id', $id)
```
Filtra los resultados: "donde la columna `id` sea igual a `$id`".

```php
->first()
```
Devuelve **el primer resultado** que cumpla la condición. Si no hay resultados, devuelve `null`.

```php
if (!$hero) {
    abort(404, 'Héroe no encontrado');
}
```
Si no existe, muestra error 404.

### Opción 2: Usando find() (más corto)

El método `find()` es un atajo cuando buscas por clave primaria (ID):

```php
public function show($id)
{
    $hero = DB::table('heroes')->find($id);
    
    if (!$hero) {
        abort(404, 'Héroe no encontrado');
    }
    
    return view('heroes.show', ['hero' => $hero]);
}
```

**Diferencia:**
- `find($id)` busca por la clave primaria automáticamente
- Es más corto y claro cuando buscas por ID

### Adaptar la vista show

Edita `resources/views/heroes/show.blade.php`:

**Antes (con arrays):**
```html
<h1>{{ $hero['name'] }}</h1>
<div class="info-row">
    <span class="label">Nombre real:</span>
    {{ $hero['real_name'] }}
</div>
```

**Ahora (con objetos):**
```html
<h1>{{ $hero->name }}</h1>
<div class="info-row">
    <span class="label">Nombre real:</span>
    {{ $hero->real_name }}
</div>
```

Cambia todos los `$hero['campo']` por `$hero->campo` en el archivo.

### Probar

Visita:
- http://localhost:8000/heroes/1 (Iron Man)
- http://localhost:8000/heroes/2 (Thor)
- http://localhost:8000/heroes/99 (Error 404)

---

## Eliminar el Método getAllHeroes()

Ya no necesitamos el método privado `getAllHeroes()` porque los datos vienen de la BD.

Elimínalo del controlador:

```php
// ❌ ELIMINAR este método
private function getAllHeroes()
{
    return [
        1 => ['id' => 1, 'name' => 'Iron Man', ...],
        // ...
    ];
}
```

---

## Controlador Completo con Base de Datos

Tu `app/Http/Controllers/HeroController.php` debería verse así:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeroController extends Controller
{
    public function index()
    {
        $heroes = DB::table('heroes')->get();
        
        return view('heroes.index', ['heroes' => $heroes]);
    }

    public function show($id)
    {
        $hero = DB::table('heroes')->find($id);
        
        if (!$hero) {
            abort(404, 'Héroe no encontrado');
        }
        
        return view('heroes.show', ['hero' => $hero]);
    }
}
```

**Cambios principales:**
1. ✅ Importamos `DB` facade
2. ✅ Usamos `DB::table('heroes')->get()` para obtener todos
3. ✅ Usamos `DB::table('heroes')->find($id)` para obtener uno
4. ✅ Eliminamos arrays hardcodeados
5. ✅ Mantenemos validación de existencia

---

## Más Métodos del Query Builder

### Seleccionar columnas específicas

En lugar de traer todas las columnas:

```php
// Traer solo algunas columnas
$heroes = DB::table('heroes')
    ->select('id', 'name', 'power')
    ->get();

// Traer columnas con alias
$heroes = DB::table('heroes')
    ->select('name as hero_name', 'power_level')
    ->get();
```

### Filtrar resultados

```php
// Héroes con power_level mayor a 8000
$strongHeroes = DB::table('heroes')
    ->where('power_level', '>', 8000)
    ->get();

// Héroes del equipo Vengadores
$avengers = DB::table('heroes')
    ->where('team', 'Vengadores')
    ->get();

// Múltiples condiciones (AND)
$activeAvengers = DB::table('heroes')
    ->where('team', 'Vengadores')
    ->where('is_active', 1)
    ->get();

// Condición OR
$heroes = DB::table('heroes')
    ->where('team', 'Vengadores')
    ->orWhere('team', 'X-Men')
    ->get();
```

### Ordenar resultados

```php
// Ordenar por poder (ascendente)
$heroes = DB::table('heroes')
    ->orderBy('power_level', 'asc')
    ->get();

// Ordenar por poder (descendente)
$heroes = DB::table('heroes')
    ->orderBy('power_level', 'desc')
    ->get();

// Ordenar por nombre alfabéticamente
$heroes = DB::table('heroes')
    ->orderBy('name')
    ->get();
```

### Limitar resultados

```php
// Los 3 héroes más poderosos
$top3 = DB::table('heroes')
    ->orderBy('power_level', 'desc')
    ->limit(3)
    ->get();

// También se puede usar take()
$top3 = DB::table('heroes')
    ->orderBy('power_level', 'desc')
    ->take(3)
    ->get();
```

### Contar registros

```php
// Contar todos los héroes
$total = DB::table('heroes')->count();

// Contar héroes activos
$activos = DB::table('heroes')
    ->where('is_active', 1)
    ->count();
```

### Verificar si existe

```php
// Verificar si existe un héroe con ID 10
$existe = DB::table('heroes')->where('id', 10)->exists();

if ($existe) {
    echo "El héroe existe";
}
```

---

## Ejemplo: Listar Solo Héroes Activos

Modifica el método `index()` para mostrar solo héroes activos:

```php
public function index()
{
    $heroes = DB::table('heroes')
        ->where('is_active', 1)
        ->orderBy('name')
        ->get();
    
    return view('heroes.index', ['heroes' => $heroes]);
}
```

---

## Ejemplo: Top 3 Héroes Más Poderosos

Crea un nuevo método en el controlador:

```php
public function top()
{
    $heroes = DB::table('heroes')
        ->orderBy('power_level', 'desc')
        ->limit(3)
        ->get();
    
    return view('heroes.top', ['heroes' => $heroes]);
}
```

Agrega la ruta en `routes/web.php`:

```php
Route::get('/heroes/top', [HeroController::class, 'top'])->name('heroes.top');
```

**Importante:** Esta ruta debe ir **antes** de `/heroes/{id}` para evitar que Laravel interprete "top" como un ID.

```php
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes.index');
Route::get('/heroes/top', [HeroController::class, 'top'])->name('heroes.top');  // ← ANTES
Route::get('/heroes/{id}', [HeroController::class, 'show'])->name('heroes.show'); // ← DESPUÉS
```

Crea la vista `resources/views/heroes/top.blade.php` (similar a index pero con título "Top 3 Más Poderosos").

---

## Depuración con dd() y dump()

Cuando trabajas con bases de datos, a veces necesitas ver exactamente qué datos estás obteniendo.

### dd() - Die and Dump

Muestra el contenido de una variable y **detiene** la ejecución:

```php
public function index()
{
    $heroes = DB::table('heroes')->get();
    
    dd($heroes); // ← Muestra $heroes y para aquí
    
    return view('heroes.index', ['heroes' => $heroes]); // Nunca llega aquí
}
```

**Útil para:** Ver rápidamente qué contiene una variable.

### dump()

Muestra el contenido pero **continúa** la ejecución:

```php
public function index()
{
    $heroes = DB::table('heroes')->get();
    
    dump($heroes); // ← Muestra $heroes en pantalla
    
    return view('heroes.index', ['heroes' => $heroes]); // Sigue ejecutándose
}
```

**Útil para:** Ver múltiples variables sin detener el flujo.

### Ver la consulta SQL generada

Para ver el SQL que Laravel está ejecutando:

```php
public function index()
{
    $heroes = DB::table('heroes')
        ->where('power_level', '>', 8000)
        ->get();
    
    // Ver SQL generado
    $query = DB::table('heroes')
        ->where('power_level', '>', 8000)
        ->toSql();
    
    dd($query); // Muestra: "select * from `heroes` where `power_level` > ?"
}
```

---

## Objetos vs Arrays

Los resultados de `DB::table()->get()` devuelven **objetos**, no arrays.

**Acceso con objetos:**
```php
$hero = DB::table('heroes')->find(1);
echo $hero->name;        // ✅ Correcto
echo $hero['name'];      // ❌ Error
```

**Si necesitas convertir a array:**

```php
// Un objeto a array
$hero = DB::table('heroes')->find(1);
$heroArray = (array) $hero;
echo $heroArray['name']; // ✅ Ahora funciona

// Colección de objetos a array
$heroes = DB::table('heroes')->get();
$heroesArray = $heroes->toArray();
```

**Recomendación:** Usa objetos. Es la forma estándar de Laravel y más eficiente.

---

## Diferencias: Query Builder vs Eloquent

**Query Builder (lo que usamos ahora):**
- ✅ Consultas SQL con sintaxis PHP
- ✅ Más control sobre las consultas
- ✅ Resultados como objetos genéricos
- ❌ No tiene relaciones automáticas
- ❌ No tiene eventos (creating, updating, etc.)

**Eloquent (lo verás en Fase 4):**
- ✅ Modelos que representan tablas
- ✅ Relaciones automáticas (belongsTo, hasMany)
- ✅ Eventos y validaciones
- ✅ Más expresivo y legible
- ❌ Ligeramente menos eficiente (negligible)

**¿Cuándo usar cada uno?**

- **Query Builder:** Consultas complejas, reportes, operaciones masivas
- **Eloquent:** CRUD normal, aplicaciones con relaciones entre tablas

**En este curso:** Fase 3 usa Query Builder, Fase 4 introducirá Eloquent para simplificar el código.

---

## Buenas Prácticas

### 1. Siempre validar existencia

```php
// ✅ BIEN - Verifica antes de usar
public function show($id)
{
    $hero = DB::table('heroes')->find($id);
    
    if (!$hero) {
        abort(404);
    }
    
    return view('heroes.show', ['hero' => $hero]);
}

// ❌ MAL - Puede causar errores si no existe
public function show($id)
{
    $hero = DB::table('heroes')->find($id);
    return view('heroes.show', ['hero' => $hero]); // Si $hero es null, da error
}
```

### 2. No traer datos innecesarios

```php
// ❌ MAL - Trae todas las columnas aunque no las uses
$heroes = DB::table('heroes')->get();

// ✅ BIEN - Solo trae lo necesario
$heroes = DB::table('heroes')
    ->select('id', 'name', 'power')
    ->get();
```

### 3. Usar métodos específicos

```php
// ❌ Menos claro
$hero = DB::table('heroes')->where('id', $id)->first();

// ✅ Más claro cuando buscas por ID
$hero = DB::table('heroes')->find($id);
```

### 4. Ordenar resultados

```php
// ✅ BIEN - Orden predecible
$heroes = DB::table('heroes')
    ->orderBy('name')
    ->get();

// ❌ Orden aleatorio (depende de la BD)
$heroes = DB::table('heroes')->get();
```

### 5. No hacer consultas en vistas

```php
<!-- ❌ MAL - Consulta en la vista -->
@foreach(DB::table('heroes')->get() as $hero)
    ...
@endforeach

<!-- ✅ BIEN - Consulta en el controlador -->
// En el controlador:
$heroes = DB::table('heroes')->get();
return view('index', ['heroes' => $heroes]);

// En la vista:
@foreach($heroes as $hero)
    ...
@endforeach
```

---

## Resumen de la Fase 3

En esta fase has aprendido:

✅ **¿Qué es una base de datos relacional?** - Tablas, columnas, filas

✅ **Verificar conexión** - Configuración en `.env` y pruebas con Tinker

✅ **Query Builder** - Herramienta para construir consultas SQL seguras

✅ **DB facade** - Acceso simplificado al Query Builder

✅ **Consultas básicas** - `get()`, `find()`, `first()`, `where()`

✅ **Modificar controladores** - Reemplazar arrays por consultas a BD

✅ **Adaptar vistas** - Objetos en lugar de arrays (`$hero->campo`)

✅ **Métodos útiles** - `select()`, `orderBy()`, `limit()`, `count()`

✅ **Depuración** - `dd()`, `dump()`, `toSql()`

✅ **Buenas prácticas** - Validación, eficiencia, orden

**En la próxima fase** aprenderás sobre **Eloquent ORM**, que simplifica aún más el trabajo con bases de datos usando modelos.

---

## Resolución de Problemas Comunes

### Error: "SQLSTATE[HY000] [1045] Access denied"

**Causa:** Credenciales incorrectas en `.env`.

**Solución:**

1. Verifica usuario y contraseña en `.env`:
   ```env
   DB_USERNAME=root
   DB_PASSWORD=root
   ```
2. Verifica que coincidan con tus credenciales de MySQL en MAMP
3. Limpia la caché:
   ```bash
   php artisan config:clear
   ```

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Causa:** MySQL no está corriendo o está en un puerto diferente.

**Solución:**

1. Abre MAMP PRO y verifica que MySQL esté corriendo (luz verde)
2. Verifica el puerto en MAMP PRO (debería ser 3306)
3. Si el puerto es diferente, actualiza `.env`:
   ```env
   DB_PORT=8889  # O el puerto que uses
   ```

### Error: "Base table or view not found: 1146 Table 'marvel_hub.heroes' doesn't exist"

**Causa:** La tabla no existe en la base de datos.

**Solución:**

1. Abre phpMyAdmin (http://localhost/phpMyAdmin)
2. Selecciona la BD `marvel_hub`
3. Verifica que existe la tabla `heroes`
4. Si no existe, ejecuta de nuevo los scripts SQL de la Fase 0

### Error: "Trying to get property 'name' of non-object"

**Causa:** Intentas acceder a propiedades de `$hero` pero es `null` (no existe).

**Solución:**

Añade validación antes de usar el objeto:

```php
public function show($id)
{
    $hero = DB::table('heroes')->find($id);
    
    // ✅ Añade esta validación
    if (!$hero) {
        abort(404, 'Héroe no encontrado');
    }
    
    return view('heroes.show', ['hero' => $hero]);
}
```

### No se ven los cambios después de modificar .env

**Solución:**

Laravel cachea la configuración. Limpia la caché:

```bash
php artisan config:clear
php artisan cache:clear
```

### Error: "Call to undefined method"

**Causa:** Olvidaste importar `DB`.

**Solución:**

Añade al principio del controlador:

```php
use Illuminate\Support\Facades\DB;
```

---

## Recursos Adicionales

**Documentación oficial de Laravel:**
- Query Builder: https://laravel.com/docs/11.x/queries
- Database: https://laravel.com/docs/11.x/database

**Próximos pasos:**
- Fase 4: Eloquent ORM y Modelos
- Fase 5: Layout con @extends y @section
- Fase 6: Ver detalle de héroe
- Fase 7: Crear nuevo héroe (formularios)

---

## Ejercicio Práctico: Tienda de Productos

Ahora que dominas Query Builder, es momento de poner en práctica todo lo aprendido creando un sistema completo desde cero.

---

## 🎯 ENUNCIADO DEL PROBLEMA

Debes crear un catálogo de productos para una tienda online que permita:

1. **Listar todos los productos** con información básica
2. **Ver detalle completo** de cada producto
3. **Filtrar productos destacados** (los más recomendados)
4. **Filtrar productos económicos** (menores a 100€)
5. **Filtrar por categoría** (Informática, Audio, etc.)

El sistema debe usar **Query Builder** (`DB::table()`) para todas las consultas a la base de datos.

---

## 📋 REQUISITOS TÉCNICOS

### Base de Datos

**Nombre:** `tienda`
**Tabla:** `productos`

**Estructura de la tabla:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | Identificador único |
| `nombre` | VARCHAR(150) | Nombre del producto |
| `descripcion` | TEXT | Descripción del producto |
| `precio` | DECIMAL(10,2) | Precio en euros |
| `stock` | INT | Unidades disponibles |
| `categoria` | VARCHAR(50) | Categoría (Informática, Audio, etc.) |
| `marca` | VARCHAR(50) | Marca del producto |
| `destacado` | TINYINT(1) | 1 = destacado, 0 = normal |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Fecha de actualización |

**Datos mínimos:** 10 productos de ejemplo con diferentes categorías y precios.

### Controlador

**Nombre:** `ProductoController`

**Métodos requeridos:**

1. `index()` - Listar todos los productos ordenados por nombre
2. `show($id)` - Mostrar detalle de un producto específico
3. `destacados()` - Listar solo productos destacados
4. `economicos()` - Listar productos con precio < 100€
5. `porCategoria($categoria)` - Listar productos de una categoría

### Rutas

**Rutas necesarias:**

- `/productos` → listado completo
- `/productos/destacados` → solo destacados
- `/productos/economicos` → solo económicos
- `/productos/categoria/{categoria}` → filtrado por categoría
- `/productos/{id}` → detalle de producto

**⚠️ Importante:** El orden de las rutas importa para evitar conflictos.

### Vistas

**Carpeta:** `resources/views/productos/`

**Archivos necesarios:**

1. `index.blade.php` - Listado general
2. `show.blade.php` - Detalle del producto
3. `destacados.blade.php` - Productos destacados
4. `economicos.blade.php` - Productos económicos
5. `categoria.blade.php` - Productos por categoría

---

## ✏️ TAREAS A REALIZAR

### Tarea 1: Preparar la Base de Datos

**1.1** Abre phpMyAdmin y crea la base de datos `tienda` con collation `utf8mb4_unicode_ci`

**1.2** Crea la tabla `productos` con todos los campos especificados arriba

**1.3** Inserta al menos 10 productos de ejemplo:
- Mínimo 3 productos destacados
- Mínimo 3 productos económicos (< 100€)
- Al menos 3 categorías diferentes (Informática, Audio, Periféricos, etc.)
- Variedad de precios, marcas y stock

**1.4** Verifica los datos ejecutando: `SELECT * FROM productos;`

### Tarea 2: Configurar Conexión

**2.1** Si usas un proyecto separado, edita `.env` con los datos de conexión a la BD `tienda`

**2.2** Si usas el proyecto `marvel-hub`, puedes cambiar temporalmente la BD en `.env`

**2.3** No olvides añadir las líneas de collation:
```env
DB_COLLATION=utf8mb4_unicode_ci
DB_CHARSET=utf8mb4
```

**2.4** Limpia la caché de configuración

### Tarea 3: Probar con Tinker

Antes de escribir código, verifica que la conexión funciona:

**3.1** Abre Tinker

**3.2** Ejecuta estas consultas de prueba:
- Contar todos los productos
- Obtener el primer producto
- Listar productos destacados
- Listar productos con precio < 100
- Listar productos de una categoría específica

**3.3** Si alguna consulta falla, revisa la configuración antes de continuar

### Tarea 4: Crear el Controlador

**4.1** Usa Artisan para generar `ProductoController`

**4.2** Importa la facade `DB` en el controlador

**4.3** Implementa el método `index()`:
- Obtener todos los productos
- Ordenarlos alfabéticamente por nombre
- Pasar los datos a la vista `productos.index`

**4.4** Implementa el método `show($id)`:
- Buscar el producto por ID
- Si no existe, lanzar error 404
- Pasar el producto a la vista `productos.show`

**4.5** Implementa el método `destacados()`:
- Filtrar productos donde `destacado = 1`
- Ordenar por precio descendente
- Pasar a la vista `productos.destacados`

**4.6** Implementa el método `economicos()`:
- Filtrar productos con precio menor a 100
- Ordenar por precio ascendente
- Pasar a la vista `productos.economicos`

**4.7** Implementa el método `porCategoria($categoria)`:
- Filtrar productos de esa categoría
- Ordenar por precio ascendente
- Pasar productos, categoría y total a la vista

### Tarea 5: Crear las Rutas

**5.1** Abre `routes/web.php`

**5.2** Importa el `ProductoController`

**5.3** Define las 5 rutas necesarias con nombres (usa `->name()`)

**5.4** Recuerda: las rutas específicas deben ir ANTES de las rutas con parámetros

### Tarea 6: Crear las Vistas

**6.1** Crea la carpeta `resources/views/productos/`

**6.2** Crea `index.blade.php`:
- Mostrar título y contador de productos
- Botones de filtro (Todos, Destacados, Económicos, categorías)
- Grid de tarjetas con: nombre, marca, género, precio, stock
- Badge si es destacado
- Cada tarjeta debe ser un enlace al detalle
- Diseño atractivo con CSS

**6.3** Crea `show.blade.php`:
- Mostrar toda la información del producto
- Precio destacado visualmente
- Grid con: categoría, stock, código, fecha de registro
- Sinopsis o descripción
- Enlace para volver al catálogo

**6.4** Crea `destacados.blade.php`:
- Similar a index pero con diseño especial para destacados
- Mostrar contador de productos destacados
- Color/diseño diferente al listado normal

**6.5** Crea `economicos.blade.php`:
- Similar a index pero con diseño orientado a ofertas
- Mostrar "productos por menos de 100€"
- Énfasis visual en el precio

**6.6** Crea `categoria.blade.php`:
- Mostrar el nombre de la categoría
- Contador de productos en esa categoría
- Listado de productos
- Manejo de categorías vacías con `@forelse`

### Tarea 7: Probar el Sistema

**7.1** Inicia el servidor con Artisan

**7.2** Verifica cada ruta en el navegador:
- `/productos` - ¿Se ven todos los productos?
- `/productos/destacados` - ¿Solo muestra destacados?
- `/productos/economicos` - ¿Solo productos < 100€?
- `/productos/categoria/Audio` - ¿Filtra correctamente?
- `/productos/1` - ¿Muestra el detalle?
- `/productos/999` - ¿Muestra error 404?

**7.3** Verifica las rutas definidas con: `php artisan route:list --name=productos`

**7.4** Comprueba que todos los enlaces funcionan (especialmente los botones de filtro)

---

## 💡 PISTAS Y RECORDATORIOS

### Sobre Query Builder

```php
// Estructura básica
DB::table('nombre_tabla')
    ->where('campo', 'valor')
    ->orderBy('campo', 'asc')
    ->get();
```

**Métodos clave que necesitarás:**

- `get()` - Obtener todos los resultados
- `find($id)` - Buscar por ID
- `where('campo', 'valor')` - Filtrar
- `where('campo', '<', 100)` - Comparaciones
- `orderBy('campo', 'asc')` - Ordenar
- `count()` - Contar registros

### Sobre el Controlador

```php
// Importar DB
use Illuminate\Support\Facades\DB;

// Estructura de método
public function nombreMetodo($parametro)
{
    $datos = DB::table('productos')->where(...)->get();
    return view('vista', ['datos' => $datos]);
}

// Manejo de errores 404
$producto = DB::table('productos')->find($id);
if (!$producto) {
    abort(404, 'Producto no encontrado');
}
```

### Sobre las Vistas

```php
// Usar datos de objetos (no arrays)
{{ $producto->nombre }}
{{ $producto->precio }}

// Directivas útiles
@foreach($productos as $producto)
    ...
@endforeach

@if($producto->destacado)
    <span>⭐ Destacado</span>
@endif

@forelse($productos as $producto)
    ...
@empty
    <p>No hay productos</p>
@endforelse
```

### Sobre las Rutas

```php
// Importar controlador
use App\Http\Controllers\ProductoController;

// Definir ruta
Route::get('/ruta', [ProductoController::class, 'metodo'])->name('nombre.ruta');

// Usar en vistas
<a href="{{ route('nombre.ruta') }}">Enlace</a>
<a href="{{ route('nombre.ruta', $id) }}">Con parámetro</a>
```

---

## ✅ VERIFICACIÓN

Antes de consultar las soluciones, comprueba que:

- [ ] La base de datos `tienda` existe con 10 productos
- [ ] Tinker muestra productos correctamente
- [ ] El controlador se creó con `php artisan make:controller`
- [ ] Todas las rutas están definidas con nombres
- [ ] La carpeta `productos/` existe en `resources/views/`
- [ ] La vista de listado muestra todos los productos
- [ ] La vista de detalle muestra información completa
- [ ] El filtro de destacados funciona
- [ ] El filtro de económicos funciona
- [ ] El filtro por categoría funciona
- [ ] Los enlaces entre vistas funcionan
- [ ] Acceder a ID inexistente muestra error 404
- [ ] El diseño es atractivo y funcional

---

## 🏆 RETOS ADICIONALES (Opcionales)

Si terminaste rápido, intenta añadir:

1. **Búsqueda por marca:**
   - Ruta: `/productos/marca/{marca}`
   - Método en controlador
   - Vista correspondiente

2. **Productos con stock bajo:**
   - Filtrar productos con stock < 20
   - Ordenar por stock ascendente

3. **Estadísticas:**
   - Total de productos
   - Precio promedio
   - Stock total
   - Producto más caro

4. **Top 3 más caros:**
   - Ordenar por precio descendente
   - Limitar a 3 resultados
   - Vista especial

---

## 📝 NOTA IMPORTANTE

**Las soluciones completas de este ejercicio se encuentran en un documento separado:**

📄 **Archivo:** `SolucionPractica.md`

**Recomendación:** Intenta resolver el ejercicio por tu cuenta antes de consultar las soluciones. Usa el checklist de verificación para comprobar tu progreso.

**Si te atascas:**
1. Revisa las pistas y recordatorios
2. Consulta la teoría de esta fase
3. Revisa ejemplos del proyecto Marvel Hub
4. Solo entonces, consulta la solución específica que necesites

**¡Buena suerte con el ejercicio!** 🚀
