# Fase 7: CRUD Completo — Editar y Eliminar

---

## De dónde partimos

Tras la Fase 6 el proyecto Marvel Hub ya permite **crear** héroes desde el navegador. Junto con la lectura implementada en fases anteriores, el ciclo CRUD está a medias:

- ✅ **C**reate — Crear (Fase 6)
- ✅ **R**ead — Leer (Fases 3 y 4)
- ⬜ **U**pdate — Actualizar
- ⬜ **D**elete — Eliminar

En esta fase se completan las dos operaciones que faltan. El flujo de cada una es el siguiente:

```
Editar
   |--- GET /heroes/{id}/edit --|  Muestra el formulario con los datos actuales
   |<-- Vista edit.blade --------|
   |--- PUT /heroes/{id} --------|  Envía los datos modificados
   |                             |  Valida y actualiza el héroe
   |<-- Redirección a show ------|

Eliminar
   |--- DELETE /heroes/{id} -----|  Envía la petición de eliminar
   |                             |  Elimina el héroe
   |<-- Redirección a index -----|
```

---

## Preparación: Crear una rama nueva

```bash
git checkout 6.Formularios
git checkout -b 7.CRUD
```

---

## El problema con los métodos HTTP en formularios HTML

Los formularios HTML solo soportan dos métodos: `GET` y `POST`. Sin embargo, por convención las operaciones de actualización deben usar `PUT` o `PATCH`, y las de eliminación deben usar `DELETE`. Laravel necesita esos métodos para distinguir entre crear, actualizar y eliminar cuando las rutas comparten la misma URL.

La solución es el **method spoofing**: incluir un campo oculto `_method` en el formulario con el método real que Laravel debe usar. Blade lo simplifica con la directiva `@method`:

```html
<form action="/heroes/1" method="POST">
    @csrf
    @method('PUT')
    {{-- campos --}}
</form>
```

`@method('PUT')` genera el campo oculto:

```html
<input type="hidden" name="_method" value="PUT">
```

Laravel lee ese campo antes de enrutar la petición. Aunque el navegador envía un `POST`, Laravel lo trata como `PUT` y lo dirige a la ruta correspondiente. El mismo mecanismo funciona con `@method('DELETE')`.

---

## Parte 1: Editar un héroe

### Paso 1.1: Añadir las rutas de edición

La edición necesita dos rutas, igual que la creación: una GET para mostrar el formulario y una PUT para recibir los datos modificados.

```php
// GET: muestra el formulario con los datos actuales del héroe
Route::get('/heroes/{id}/edit', [HeroController::class, 'edit'])->name('heroes.edit');

// PUT: recibe los datos modificados y actualiza el héroe
Route::put('/heroes/{id}', [HeroController::class, 'update'])->name('heroes.update');
```

El archivo `web.php` completo hasta esta fase:

```php
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes.index');
Route::get('/heroes/create', [HeroController::class, 'create'])->name('heroes.create');
Route::get('/heroes/active', [HeroController::class, 'active'])->name('heroes.active');
Route::get('/heroes/powerful', [HeroController::class, 'powerful'])->name('heroes.powerful');
Route::get('/heroes/{id}/edit', [HeroController::class, 'edit'])->name('heroes.edit');
Route::get('/heroes/{id}', [HeroController::class, 'show'])->name('heroes.show');
Route::post('/heroes', [HeroController::class, 'store'])->name('heroes.store');
Route::put('/heroes/{id}', [HeroController::class, 'update'])->name('heroes.update');
```

La ruta `GET /heroes/{id}/edit` no entra en conflicto con `GET /heroes/{id}` porque el segmento `/edit` al final la hace distinta. Sí debe estar antes de `GET /heroes/{id}` para que Laravel no intente interpretar `edit` como un ID.

### Paso 1.2: El método edit()

`edit()` recibe el ID, busca el héroe y devuelve el formulario de edición con el héroe cargado:

```php
public function edit($id)
{
    $hero = Hero::findOrFail($id);
    return view('heroes.edit', compact('hero'));
}
```

La diferencia con `create()` es que aquí se pasa `$hero` a la vista para que los campos aparezcan con sus valores actuales ya rellenos.

### Paso 1.3: El método update()

`update()` recibe el ID y los datos del formulario, valida, actualiza y redirige:

```php
public function update(Request $request, $id)
{
    $hero = Hero::findOrFail($id);

    $request->validate([
        'name'        => 'required|string|max:100',
        'real_name'   => 'nullable|string|max:100',
        'power'       => 'required|string|max:150',
        'power_level' => 'required|integer|min:1|max:10000',
        'team'        => 'required|string|max:100',
        'bio'         => 'nullable|string',
        'is_active'   => 'boolean',
    ]);

    $hero->update([
        'name'        => $request->name,
        'real_name'   => $request->real_name,
        'power'       => $request->power,
        'power_level' => $request->power_level,
        'team'        => $request->team,
        'bio'         => $request->bio,
        'is_active'   => $request->boolean('is_active'),
    ]);

    return redirect()->route('heroes.show', $hero->id)
        ->with('success', 'Héroe actualizado correctamente.');
}
```

Las reglas de validación son idénticas a `store()`. Crear y actualizar comparten los mismos requisitos sobre los datos porque los datos en sí no cambian dependiendo de si son nuevos o existentes.

Tras actualizar, la redirección va a la vista de detalle del héroe en lugar del listado, para que el usuario pueda comprobar inmediatamente el resultado de sus cambios.

### Paso 1.4: La vista edit.blade.php

El formulario de edición es prácticamente idéntico al de creación. Las diferencias son tres: el `action` apunta a la ruta `update` con el ID del héroe, se añade `@method('PUT')`, y los campos tienen los valores actuales precargados.

El mecanismo para precargar valores es el segundo parámetro de `old()`:

```php
old('name', $hero->name)
```

`old()` con dos parámetros aplica esta lógica:

- Si el formulario se ha enviado y la validación ha fallado → usa el valor que el usuario había escrito (guardado en la sesión)
- Si el formulario se abre por primera vez → usa `$hero->name` (el valor actual en la base de datos)

Esto garantiza que al abrir el formulario los campos muestran los datos del héroe, y si la validación falla, los campos muestran lo que el usuario había modificado, no el valor original.

```html
{{-- resources/views/heroes/edit.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Editar ' . $hero->name . ' — Marvel Hub')

@section('contenido')
    <a href="{{ route('heroes.show', $hero->id) }}" class="back-link">&larr; Volver al detalle</a>

    <div class="form-card">
        <h1>Editar héroe</h1>

        <form action="{{ route('heroes.update', $hero->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name', $hero->name) }}">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="real_name">Nombre real</label>
                <input type="text" id="real_name" name="real_name" value="{{ old('real_name', $hero->real_name) }}">
                @error('real_name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="power">Poder</label>
                <input type="text" id="power" name="power" value="{{ old('power', $hero->power) }}">
                @error('power')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="power_level">Nivel de poder</label>
                <input type="number" id="power_level" name="power_level" min="1" max="10000"
                    value="{{ old('power_level', $hero->power_level) }}">
                @error('power_level')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="team">Equipo</label>
                <input type="text" id="team" name="team" value="{{ old('team', $hero->team) }}">
                @error('team')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="bio">Biografía</label>
                <textarea id="bio" name="bio">{{ old('bio', $hero->bio) }}</textarea>
                @error('bio')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-checkbox">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', $hero->is_active) ? 'checked' : '' }}>
                <label for="is_active">Activo</label>
            </div>

            <button type="submit" class="btn-submit">Guardar cambios</button>
        </form>
    </div>
@endsection
```

### Paso 1.5: Añadir el enlace a editar en la vista de detalle

Actualiza `show.blade.php` para incluir el enlace al formulario de edición. Aprovecha para añadir el contenedor `.detail-actions` que también usará el botón de eliminar:

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

        <div class="detail-actions">
            <a href="{{ route('heroes.edit', $hero->id) }}" class="btn-edit">Editar héroe</a>
        </div>
    </div>
@endsection
```

---

## Parte 2: Eliminar un héroe

### Paso 2.1: Añadir la ruta de eliminación

```php
Route::delete('/heroes/{id}', [HeroController::class, 'destroy'])->name('heroes.destroy');
```

El archivo `web.php` definitivo con todas las rutas del proyecto:

```php
Route::get('/heroes', [HeroController::class, 'index'])->name('heroes.index');
Route::get('/heroes/create', [HeroController::class, 'create'])->name('heroes.create');
Route::get('/heroes/active', [HeroController::class, 'active'])->name('heroes.active');
Route::get('/heroes/powerful', [HeroController::class, 'powerful'])->name('heroes.powerful');
Route::get('/heroes/{id}/edit', [HeroController::class, 'edit'])->name('heroes.edit');
Route::get('/heroes/{id}', [HeroController::class, 'show'])->name('heroes.show');
Route::post('/heroes', [HeroController::class, 'store'])->name('heroes.store');
Route::put('/heroes/{id}', [HeroController::class, 'update'])->name('heroes.update');
Route::delete('/heroes/{id}', [HeroController::class, 'destroy'])->name('heroes.destroy');
```

### Paso 2.2: El método destroy()

```php
public function destroy($id)
{
    $hero = Hero::findOrFail($id);
    $hero->delete();

    return redirect()->route('heroes.index')
        ->with('success', 'Héroe eliminado correctamente.');
}
```

Se busca el héroe con `findOrFail()` antes de eliminarlo. Esto garantiza que si alguien intenta eliminar un ID que no existe, Laravel devuelve un 404 en lugar de un error inesperado.

### Paso 2.3: El botón de eliminar en la vista de detalle

Eliminar requiere enviar una petición `DELETE`. Como los formularios HTML no soportan ese método, se usa el mismo mecanismo que en la edición: un formulario `POST` con `@method('DELETE')`.

Antes de actualizar la vista, añade los estilos necesarios en `public/css/heroes.css`:

```css
/* ========================================
   ACCIONES DE DETALLE
   ======================================== */
.detail-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    align-items: center;
}

.btn-edit {
    display: inline-block;
    padding: 10px 24px;
    background-color: #1a1a1a;
    color: #ffffff;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 700;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background-color: #333333;
    transform: translateY(-2px);
}

.btn-delete {
    display: inline-block;
    padding: 10px 24px;
    background-color: #e23636;
    color: #ffffff;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-delete:hover {
    background-color: #c42e2e;
    transform: translateY(-2px);
}
```

La vista `show.blade.php` actualizada con ambas acciones:

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

        <div class="detail-actions">
            <a href="{{ route('heroes.edit', $hero->id) }}" class="btn-edit">Editar héroe</a>

            <form action="{{ route('heroes.destroy', $hero->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete"
                    onclick="return confirm('¿Seguro que quieres eliminar a {{ $hero->name }}?')">
                    Eliminar héroe
                </button>
            </form>
        </div>
    </div>
@endsection
```

**Por qué se usa `confirm()`**

El atributo `onclick="return confirm(...)"` muestra un diálogo nativo del navegador antes de enviar el formulario. Si el usuario pulsa Cancelar, `confirm()` devuelve `false`, el evento del formulario se cancela y la petición DELETE no se envía. Es la forma más directa de proteger una acción destructiva sin necesitar JavaScript adicional.

---

## El controlador completo

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

        return redirect()->route('heroes.index')
            ->with('success', 'Héroe creado correctamente.');
    }

    public function show($id)
    {
        $hero = Hero::findOrFail($id);
        return view('heroes.show', compact('hero'));
    }

    public function edit($id)
    {
        $hero = Hero::findOrFail($id);
        return view('heroes.edit', compact('hero'));
    }

    public function update(Request $request, $id)
    {
        $hero = Hero::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:100',
            'real_name'   => 'nullable|string|max:100',
            'power'       => 'required|string|max:150',
            'power_level' => 'required|integer|min:1|max:10000',
            'team'        => 'required|string|max:100',
            'bio'         => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $hero->update([
            'name'        => $request->name,
            'real_name'   => $request->real_name,
            'power'       => $request->power,
            'power_level' => $request->power_level,
            'team'        => $request->team,
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

## Resumen del ciclo CRUD completo

| Operación | Método HTTP | Ruta | Método controlador | Vista |
|-----------|-------------|------|--------------------|-------|
| Listar | GET | `/heroes` | `index()` | `heroes/index` |
| Ver detalle | GET | `/heroes/{id}` | `show()` | `heroes/show` |
| Formulario crear | GET | `/heroes/create` | `create()` | `heroes/create` |
| Guardar nuevo | POST | `/heroes` | `store()` | — (redirige) |
| Formulario editar | GET | `/heroes/{id}/edit` | `edit()` | `heroes/edit` |
| Guardar cambios | PUT | `/heroes/{id}` | `update()` | — (redirige) |
| Eliminar | DELETE | `/heroes/{id}` | `destroy()` | — (redirige) |

---

## Ejercicio Práctico: Plataforma de Música

Completa el CRUD del ejercicio de música añadiendo edición y eliminación de álbumes.

### Requisitos

1. Formulario de edición accesible desde la ficha de detalle de cada álbum
2. Los campos del formulario de edición muestran los datos actuales del álbum
3. Si la validación falla, los campos conservan los valores que el usuario había modificado
4. Tras actualizar, redirige a la ficha de detalle con mensaje de confirmación
5. Botón de eliminar en la ficha de detalle con confirmación antes de ejecutar
6. Tras eliminar, redirige al listado con mensaje de confirmación

### Tareas a realizar

**Tarea 1:** Añade las rutas `GET /albumes/{id}/edit`, `PUT /albumes/{id}` y `DELETE /albumes/{id}` a `web.php` respetando el orden correcto.

**Tarea 2:** Añade los métodos `edit()`, `update()` y `destroy()` a `AlbumController`. Las validaciones de `update()` deben ser las mismas que las de `store()`.

**Tarea 3:** Crea la vista `resources/views/albumes/edit.blade.php` con los campos precargados usando `old('campo', $album->campo)` y `@method('PUT')`.

**Tarea 4:** Actualiza la vista `show.blade.php` de álbumes con el enlace a editar y el formulario de eliminar con `@method('DELETE')` y confirmación.

**Tarea 5:** Añade a `heroes.css` los estilos `.detail-actions`, `.btn-edit` y `.btn-delete`.

---

## Pistas y recordatorios

### Sobre old() con valor por defecto en la edición

```html
<input type="text" name="titulo" value="{{ old('titulo', $album->titulo) }}">
```

### Sobre @method en el formulario de edición

```html
<form action="{{ route('albumes.update', $album->id) }}" method="POST">
    @csrf
    @method('PUT')
    {{-- campos --}}
    <button type="submit" class="btn-submit">Guardar cambios</button>
</form>
```

### Sobre el formulario de eliminar

```html
<form action="{{ route('albumes.destroy', $album->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn-delete"
        onclick="return confirm('¿Seguro que quieres eliminar este álbum?')">
        Eliminar álbum
    </button>
</form>
```

### Sobre la redirección tras actualizar

```php
return redirect()->route('albumes.show', $album->id)
    ->with('success', 'Álbum actualizado correctamente.');
```

---

## Verificación

- [ ] Ruta `GET /albumes/{id}/edit` definida antes de `GET /albumes/{id}`
- [ ] Rutas `PUT /albumes/{id}` y `DELETE /albumes/{id}` definidas
- [ ] Método `edit()` busca el álbum con `findOrFail()` y devuelve la vista
- [ ] Método `update()` valida antes de actualizar
- [ ] Método `destroy()` busca el álbum con `findOrFail()` antes de eliminar
- [ ] El formulario de edición incluye `@method('PUT')`
- [ ] Los campos del formulario de edición usan `old('campo', $album->campo)`
- [ ] Si la validación falla en edición, los campos conservan los valores modificados
- [ ] El formulario de eliminar incluye `@method('DELETE')` y `confirm()`
- [ ] Tras actualizar redirige a la ficha de detalle con mensaje de éxito
- [ ] Tras eliminar redirige al listado con mensaje de éxito
- [ ] El CRUD completo funciona: crear, leer, actualizar y eliminar
