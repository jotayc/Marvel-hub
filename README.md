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

Ahora que dominas Query Builder, es momento de practicar creando un catálogo de productos que lee datos de la base de datos.

### Objetivo

Crear una tienda de productos que:
1. Muestre todos los productos desde la base de datos
2. Permita ver detalle de cada producto
3. Filtre productos por categoría
4. Muestre productos ordenados por precio
5. Use Query Builder para todas las consultas

### Paso 1: Crear la Base de Datos

Abre **phpMyAdmin** (http://localhost/phpMyAdmin) y ejecuta este SQL:

```sql
-- Crear base de datos
CREATE DATABASE tienda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE tienda;

-- Crear tabla productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria VARCHAR(50) NOT NULL,
    marca VARCHAR(50),
    destacado TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar productos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, stock, categoria, marca, destacado) VALUES
('Laptop Dell XPS 13', 'Portátil ultraligero de 13 pulgadas con procesador Intel i7', 1299.99, 15, 'Informática', 'Dell', 1),
('iPhone 15 Pro', 'Smartphone de última generación con cámara de 48MP', 1199.00, 25, 'Móviles', 'Apple', 1),
('Auriculares Sony WH-1000XM5', 'Auriculares inalámbricos con cancelación de ruido', 349.99, 40, 'Audio', 'Sony', 0),
('Teclado Mecánico Logitech', 'Teclado gaming con switches mecánicos RGB', 129.99, 30, 'Periféricos', 'Logitech', 0),
('Monitor Samsung 27"', 'Monitor 4K de 27 pulgadas con HDR', 449.00, 20, 'Informática', 'Samsung', 1),
('Mouse Logitech MX Master 3', 'Ratón ergonómico inalámbrico para productividad', 99.99, 50, 'Periféricos', 'Logitech', 0),
('Tablet iPad Air', 'Tablet de 10.9 pulgadas con chip M1', 699.00, 18, 'Tablets', 'Apple', 1),
('Webcam Logitech C920', 'Cámara web Full HD 1080p', 79.99, 35, 'Periféricos', 'Logitech', 0),
('Disco SSD Samsung 1TB', 'Unidad de estado sólido NVMe de alta velocidad', 89.99, 60, 'Almacenamiento', 'Samsung', 0),
('Altavoz Bluetooth JBL', 'Altavoz portátil resistente al agua', 129.00, 45, 'Audio', 'JBL', 0);
```

**Verifica los datos:**

En phpMyAdmin, selecciona la base `tienda` → tabla `productos` → pestaña "Examinar".

Deberías ver 10 productos.

### Paso 2: Crear Proyecto de Práctica (Opcional)

Puedes crear un proyecto separado o usar el mismo `marvel-hub`. Si quieres practicar aisladamente:

```bash
cd C:\MAMP\htdocs
composer create-project laravel/laravel tienda-productos
cd tienda-productos
```

Si usas `marvel-hub`, simplemente añade las rutas y controlador nuevos.

### Paso 3: Configurar Conexión a la BD

Edita `.env` y añade configuración para la base `tienda`:

**Opción A: Proyecto separado**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tienda
DB_USERNAME=root
DB_PASSWORD=root
DB_COLLATION=utf8mb4_unicode_ci
DB_CHARSET=utf8mb4
```

**Opción B: Mismo proyecto marvel-hub**

Si usas el mismo proyecto, puedes trabajar con ambas BD o cambiar temporalmente a `tienda` en `.env`.

**Limpiar caché:**

```bash
php artisan config:clear
```

### Paso 4: Probar Conexión con Tinker

```bash
php artisan tinker
```

Dentro de Tinker:

```php
// Probar conexión
DB::connection()->getPdo();

// Contar productos
DB::table('productos')->count();
// Debería mostrar: 10

// Ver primer producto
DB::table('productos')->first();

// Productos destacados
DB::table('productos')->where('destacado', 1)->get();

// Salir
exit
```

### Paso 5: Crear el Controlador

```bash
php artisan make:controller ProductoController
```

Edita `app/Http/Controllers/ProductoController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    // Listar todos los productos
    public function index()
    {
        $productos = DB::table('productos')
            ->orderBy('nombre', 'asc')
            ->get();
        
        return view('productos.index', ['productos' => $productos]);
    }

    // Mostrar detalle de un producto
    public function show($id)
    {
        $producto = DB::table('productos')->find($id);
        
        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }
        
        return view('productos.show', ['producto' => $producto]);
    }

    // Productos destacados
    public function destacados()
    {
        $productos = DB::table('productos')
            ->where('destacado', 1)
            ->orderBy('precio', 'desc')
            ->get();
        
        return view('productos.destacados', ['productos' => $productos]);
    }

    // Productos por categoría
    public function porCategoria($categoria)
    {
        $productos = DB::table('productos')
            ->where('categoria', $categoria)
            ->orderBy('precio', 'asc')
            ->get();
        
        $totalProductos = count($productos);
        
        return view('productos.categoria', [
            'productos' => $productos,
            'categoria' => $categoria,
            'total' => $totalProductos
        ]);
    }

    // Productos económicos (menos de 100€)
    public function economicos()
    {
        $productos = DB::table('productos')
            ->where('precio', '<', 100)
            ->orderBy('precio', 'asc')
            ->get();
        
        return view('productos.economicos', ['productos' => $productos]);
    }
}
```

### Paso 6: Crear las Rutas

Edita `routes/web.php`:

```php
use App\Http\Controllers\ProductoController;

// Rutas de productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/destacados', [ProductoController::class, 'destacados'])->name('productos.destacados');
Route::get('/productos/economicos', [ProductoController::class, 'economicos'])->name('productos.economicos');
Route::get('/productos/categoria/{categoria}', [ProductoController::class, 'porCategoria'])->name('productos.categoria');
Route::get('/productos/{id}', [ProductoController::class, 'show'])->name('productos.show');
```

**⚠️ Importante:** La ruta `productos/destacados` DEBE ir ANTES de `productos/{id}` para evitar conflictos.

### Paso 7: Crear Carpeta de Vistas

```bash
mkdir C:\MAMP\htdocs\marvel-hub\resources\views\productos
```

O si usas proyecto separado:

```bash
mkdir C:\MAMP\htdocs\tienda-productos\resources\views\productos
```

### Paso 8: Vista de Listado (index)

Crea `resources/views/productos/index.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Productos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }
        
        h1 {
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .filters {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            background: white;
            color: #667eea;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .filter-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.2);
        }
        
        .badge-destacado {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff6b6b;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .product-name {
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .product-brand {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .product-description {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #f0f0f0;
        }
        
        .product-price {
            font-size: 1.8em;
            font-weight: bold;
            color: #4caf50;
        }
        
        .product-stock {
            font-size: 0.9em;
            color: #666;
        }
        
        .stock-ok {
            color: #4caf50;
        }
        
        .stock-bajo {
            color: #ff9800;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Tienda de Productos</h1>
            <p>{{ count($productos) }} productos disponibles</p>
        </div>
        
        <div class="filters">
            <a href="{{ route('productos.index') }}" class="filter-btn">Todos</a>
            <a href="{{ route('productos.destacados') }}" class="filter-btn">⭐ Destacados</a>
            <a href="{{ route('productos.economicos') }}" class="filter-btn">💰 Económicos</a>
            <a href="{{ route('productos.categoria', 'Informática') }}" class="filter-btn">Informática</a>
            <a href="{{ route('productos.categoria', 'Audio') }}" class="filter-btn">Audio</a>
        </div>
        
        <div class="products-grid">
            @foreach($productos as $producto)
                <a href="{{ route('productos.show', $producto->id) }}" class="product-card">
                    @if($producto->destacado)
                        <span class="badge-destacado">⭐ Destacado</span>
                    @endif
                    
                    <div class="product-brand">{{ $producto->marca }}</div>
                    <div class="product-name">{{ $producto->nombre }}</div>
                    <div class="product-description">{{ $producto->descripcion }}</div>
                    
                    <div class="product-footer">
                        <div class="product-price">{{ number_format($producto->precio, 2) }}€</div>
                        <div class="product-stock {{ $producto->stock > 10 ? 'stock-ok' : 'stock-bajo' }}">
                            Stock: {{ $producto->stock }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</body>
</html>
```

### Paso 9: Vista de Detalle (show)

Crea `resources/views/productos/show.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->nombre }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .product-detail {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .badge {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .brand {
            color: #667eea;
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        
        .description {
            color: #666;
            font-size: 1.1em;
            line-height: 1.8;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .info-label {
            font-weight: bold;
            color: #667eea;
            font-size: 0.9em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .info-value {
            color: #333;
            font-size: 1.3em;
        }
        
        .price-section {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .price-label {
            font-size: 1em;
            margin-bottom: 10px;
        }
        
        .price-value {
            font-size: 3.5em;
            font-weight: bold;
        }
        
        .back-link {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 35px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            background: #764ba2;
            transform: translateX(-5px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="product-detail">
            @if($producto->destacado)
                <span class="badge">⭐ Producto Destacado</span>
            @endif
            
            <div class="brand">{{ $producto->marca }}</div>
            <h1>{{ $producto->nombre }}</h1>
            
            <div class="description">
                {{ $producto->descripcion }}
            </div>
            
            <div class="price-section">
                <div class="price-label">Precio</div>
                <div class="price-value">{{ number_format($producto->precio, 2) }}€</div>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Categoría</div>
                    <div class="info-value">{{ $producto->categoria }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Stock Disponible</div>
                    <div class="info-value">{{ $producto->stock }} unidades</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Código del Producto</div>
                    <div class="info-value">#{{ str_pad($producto->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Fecha de Registro</div>
                    <div class="info-value">{{ date('d/m/Y', strtotime($producto->created_at)) }}</div>
                </div>
            </div>
            
            <a href="{{ route('productos.index') }}" class="back-link">
                ← Volver al catálogo
            </a>
        </div>
    </div>
</body>
</html>
```

### Paso 10: Vista de Destacados

Crea `resources/views/productos/destacados.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos Destacados</title>
    <style>
        /* Reutiliza los estilos de index.blade.php */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        h1 { font-size: 3em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        .product-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border: 3px solid #ff6b6b;
        }
        .product-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 15px 30px rgba(0,0,0,0.3); }
        .product-name { font-size: 1.5em; font-weight: bold; color: #333; margin-bottom: 10px; }
        .product-brand { color: #ff6b6b; font-weight: bold; margin-bottom: 10px; font-size: 1.1em; }
        .product-description { color: #666; margin-bottom: 20px; line-height: 1.6; }
        .product-price { font-size: 2.2em; font-weight: bold; color: #4caf50; text-align: center; margin-top: 20px; }
        .back-link {
            display: inline-block;
            background: white;
            color: #ff6b6b;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin-bottom: 30px;
            transition: all 0.3s;
        }
        .back-link:hover { background: #ff6b6b; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('productos.index') }}" class="back-link">← Volver al catálogo</a>
        
        <div class="header">
            <h1>⭐ Productos Destacados</h1>
            <p>{{ count($productos) }} productos seleccionados</p>
        </div>
        
        <div class="products-grid">
            @forelse($productos as $producto)
                <a href="{{ route('productos.show', $producto->id) }}" class="product-card">
                    <div class="product-brand">{{ $producto->marca }}</div>
                    <div class="product-name">{{ $producto->nombre }}</div>
                    <div class="product-description">{{ $producto->descripcion }}</div>
                    <div class="product-price">{{ number_format($producto->precio, 2) }}€</div>
                </a>
            @empty
                <p style="color: white; text-align: center; grid-column: 1/-1;">No hay productos destacados</p>
            @endforelse
        </div>
    </div>
</body>
</html>
```

### Paso 11: Vista de Productos por Categoría

Crea `resources/views/productos/categoria.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $categoria }} - Productos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        h1 { font-size: 3em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .product-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 12px 20px rgba(0,0,0,0.2); }
        .product-name { font-size: 1.3em; font-weight: bold; color: #333; margin-bottom: 10px; }
        .product-price { font-size: 1.8em; font-weight: bold; color: #4caf50; margin-top: 15px; }
        .back-link {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .back-link:hover { background: #764ba2; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('productos.index') }}" class="back-link">← Volver al catálogo</a>
        
        <div class="header">
            <h1>📁 {{ $categoria }}</h1>
            <p>{{ $total }} productos encontrados</p>
        </div>
        
        <div class="products-grid">
            @forelse($productos as $producto)
                <a href="{{ route('productos.show', $producto->id) }}" class="product-card">
                    <div class="product-name">{{ $producto->nombre }}</div>
                    <div class="product-price">{{ number_format($producto->precio, 2) }}€</div>
                </a>
            @empty
                <p style="color: white; text-align: center; grid-column: 1/-1;">
                    No hay productos en esta categoría
                </p>
            @endforelse
        </div>
    </div>
</body>
</html>
```

### Paso 12: Vista de Productos Económicos

Crea `resources/views/productos/economicos.blade.php`:

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos Económicos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 40px; }
        h1 { font-size: 3em; margin-bottom: 10px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .product-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 2px solid #4caf50;
        }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 12px 20px rgba(0,0,0,0.2); }
        .product-name { font-size: 1.3em; font-weight: bold; color: #333; margin-bottom: 10px; }
        .product-price { font-size: 1.8em; font-weight: bold; color: #4caf50; margin-top: 15px; }
        .back-link {
            display: inline-block;
            background: white;
            color: #4caf50;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .back-link:hover { background: #4caf50; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('productos.index') }}" class="back-link">← Volver al catálogo</a>
        
        <div class="header">
            <h1>💰 Productos Económicos</h1>
            <p>{{ count($productos) }} productos por menos de 100€</p>
        </div>
        
        <div class="products-grid">
            @foreach($productos as $producto)
                <a href="{{ route('productos.show', $producto->id) }}" class="product-card">
                    <div class="product-name">{{ $producto->nombre }}</div>
                    <div class="product-price">{{ number_format($producto->precio, 2) }}€</div>
                </a>
            @endforeach
        </div>
    </div>
</body>
</html>
```

### Paso 13: Probar el Ejercicio

1. Inicia el servidor:
   ```bash
   php artisan serve
   ```

2. Accede a las diferentes rutas:
   - http://localhost:8000/productos (todos)
   - http://localhost:8000/productos/destacados (solo destacados)
   - http://localhost:8000/productos/economicos (menos de 100€)
   - http://localhost:8000/productos/categoria/Audio (filtrado)
   - http://localhost:8000/productos/1 (detalle)

3. Verifica las rutas:
   ```bash
   php artisan route:list --name=productos
   ```

### Verificación

Comprueba que todo funciona:

- ✅ Base de datos `tienda` creada con tabla `productos`
- ✅ 10 productos insertados correctamente
- ✅ Conexión a BD configurada en `.env`
- ✅ Tinker muestra productos correctamente
- ✅ Controlador usa `DB::table()` en todos los métodos
- ✅ Vista de listado muestra todos los productos
- ✅ Vista de detalle muestra información completa
- ✅ Filtro de destacados funciona
- ✅ Filtro de económicos funciona (< 100€)
- ✅ Filtro por categoría funciona
- ✅ Vistas usan objetos `$producto->campo` (no arrays)
- ✅ Enlaces usan `route()` helper
- ✅ Validación con `abort(404)` para IDs inexistentes

### Reto Extra (Opcional)

Añade estas funcionalidades avanzadas:

1. **Búsqueda por marca:**
   ```php
   public function porMarca($marca)
   {
       $productos = DB::table('productos')
           ->where('marca', $marca)
           ->get();
       return view('productos.marca', compact('productos', 'marca'));
   }
   ```

2. **Productos con stock bajo:**
   ```php
   public function stockBajo()
   {
       $productos = DB::table('productos')
           ->where('stock', '<', 20)
           ->orderBy('stock', 'asc')
           ->get();
       return view('productos.stock-bajo', compact('productos'));
   }
   ```

3. **Estadísticas:**
   ```php
   public function estadisticas()
   {
       $total = DB::table('productos')->count();
       $precioMedio = DB::table('productos')->avg('precio');
       $stockTotal = DB::table('productos')->sum('stock');
       $masCaro = DB::table('productos')->max('precio');
       
       return view('productos.stats', compact('total', 'precioMedio', 'stockTotal', 'masCaro'));
   }
   ```

4. **Productos más caros (Top 3):**
   ```php
   public function masCaros()
   {
       $productos = DB::table('productos')
           ->orderBy('precio', 'desc')
           ->limit(3)
           ->get();
       return view('productos.top', compact('productos'));
   }
   ```

### Conceptos Practicados

Con este ejercicio has trabajado:

- ✅ Crear base de datos y tablas con SQL
- ✅ Configurar conexión en `.env`
- ✅ Probar conexión con Tinker
- ✅ Query Builder: `DB::table('tabla')`
- ✅ Métodos de consulta: `get()`, `find()`, `first()`
- ✅ Filtros: `where('campo', 'valor')`
- ✅ Ordenación: `orderBy('campo', 'asc')`
- ✅ Agregación: `count()`, `avg()`, `sum()`, `max()`
- ✅ Comparaciones: `where('precio', '<', 100)`
- ✅ Objetos vs Arrays: `$producto->nombre` vs `$producto['nombre']`
- ✅ Importar facade: `use Illuminate\Support\Facades\DB;`
- ✅ Validación de existencia con `abort(404)`
- ✅ Directiva `@forelse` para listas vacías
- ✅ Múltiples vistas para diferentes filtros
- ✅ Rutas con parámetros: `categoria/{categoria}`

### Diferencias entre Fase 2 y Fase 3

**Fase 2 (Arrays hardcodeados):**
```php
private function getAllBooks()
{
    return [
        1 => ['titulo' => 'Libro 1', ...],
        2 => ['titulo' => 'Libro 2', ...],
    ];
}
```

**Fase 3 (Base de datos real):**
```php
public function index()
{
    $productos = DB::table('productos')->get();
    return view('productos.index', compact('productos'));
}
```

**Ventajas evidentes:**
- ✅ Datos persistentes (no se pierden al reiniciar)
- ✅ Fácil de modificar sin tocar código
- ✅ Consultas complejas (filtros, búsquedas, estadísticas)
- ✅ Escalable a millones de registros
- ✅ Múltiples usuarios pueden modificar datos

**¡Felicidades!** Has completado el ejercicio de Query Builder. Ahora comprendes cómo Laravel interactúa con bases de datos reales y cómo reemplazar datos estáticos por dinámicos.
