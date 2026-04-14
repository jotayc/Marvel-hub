# Soluciones - Ejercicio Práctico Fase 3: Tienda de Productos

> **Curso:** Laravel 11 - Marvel Hub  
> **Fase:** 3 - Acceso a Base de Datos (Query Builder)  
> **Ejercicio:** Tienda de Productos

---

## ⚠️ IMPORTANTE

**Este archivo contiene las soluciones completas del ejercicio práctico.**

Antes de consultar estas soluciones:

1. ✅ Lee el enunciado del problema en la Fase 3
2. ✅ Revisa los requisitos técnicos
3. ✅ Intenta resolver las tareas por tu cuenta
4. ✅ Consulta las pistas y recordatorios
5. ✅ Verifica con el checklist

**Solo consulta las soluciones cuando:**
- Te hayas atascado en un punto específico
- Hayas completado el ejercicio y quieras comparar
- Necesites verificar un detalle técnico concreto

---

## 📚 SOLUCIONES COMPLETAS

A continuación encontrarás las soluciones completas del ejercicio. **Intenta resolver primero por tu cuenta** antes de consultar estas soluciones.

### Solución 1: Script SQL

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

### Solución 2: Configuración .env

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

**Comando para limpiar caché:**
```bash
php artisan config:clear
```

### Solución 3: Pruebas con Tinker

```bash
php artisan tinker
```

```php
// Contar productos
DB::table('productos')->count();
// Resultado esperado: 10

// Primer producto
DB::table('productos')->first();

// Productos destacados
DB::table('productos')->where('destacado', 1)->get();

// Productos económicos
DB::table('productos')->where('precio', '<', 100)->get();

// Productos de Audio
DB::table('productos')->where('categoria', 'Audio')->get();

// Salir
exit
```

### Solución 4: ProductoController

**Crear el controlador:**
```bash
php artisan make:controller ProductoController
```

**Código completo de `app/Http/Controllers/ProductoController.php`:**

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

### Solución 5: Rutas

**Archivo `routes/web.php`:**

```php
use App\Http\Controllers\ProductoController;

// Rutas de productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/destacados', [ProductoController::class, 'destacados'])->name('productos.destacados');
Route::get('/productos/economicos', [ProductoController::class, 'economicos'])->name('productos.economicos');
Route::get('/productos/categoria/{categoria}', [ProductoController::class, 'porCategoria'])->name('productos.categoria');
Route::get('/productos/{id}', [ProductoController::class, 'show'])->name('productos.show');
```

**⚠️ Importante:** Las rutas específicas (`destacados`, `economicos`) DEBEN ir ANTES de `{id}`.

### Solución 6: Vista index.blade.php

**Archivo `resources/views/productos/index.blade.php`:**

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

### Solución 7: Vista show.blade.php

**Archivo `resources/views/productos/show.blade.php`:**

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

### Solución 8: Vistas de Filtros

**Archivo `resources/views/productos/destacados.blade.php`:**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos Destacados</title>
    <style>
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

**Archivo `resources/views/productos/economicos.blade.php`:**

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

**Archivo `resources/views/productos/categoria.blade.php`:**

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

---

## 🎓 REFLEXIÓN FINAL

Este ejercicio te ha permitido practicar:

✅ **Query Builder completo** - `get()`, `find()`, `where()`, `orderBy()`
✅ **Controladores organizados** - Múltiples métodos con lógica clara
✅ **Rutas parametrizadas** - Filtros y búsquedas
✅ **Vistas dinámicas** - Objetos, condicionales, bucles
✅ **Arquitectura MVC** - Separación de responsabilidades
✅ **Manejo de errores** - Validación con `abort(404)`
✅ **Diseño web** - CSS funcional y atractivo

**Diferencias clave con la Fase 2:**
- Fase 2: Arrays estáticos en controlador
- Fase 3: Datos reales desde base de datos con Query Builder

**En la próxima fase aprenderás Eloquent ORM**, que simplifica aún más el trabajo con bases de datos usando modelos.

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
