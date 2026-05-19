# FASE 0: Instalación y Configuración

---

## ¿Qué es Laravel?

Laravel es un **framework de PHP** diseñado para facilitar el desarrollo de aplicaciones web. Un framework es un conjunto de herramientas y estructuras predefinidas que permite construir aplicaciones de forma más rápida, organizada y segura.

### ¿Por qué usar Laravel en lugar de PHP puro?

**PHP puro:**
```php
// Archivo: heroes.php
<?php
$conexion = mysqli_connect("localhost", "root", "root", "marvel_hub");
$resultado = mysqli_query($conexion, "SELECT * FROM heroes");

while($hero = mysqli_fetch_assoc($resultado)) {
    echo "<h2>" . $hero['name'] . "</h2>";
    echo "<p>" . $hero['power'] . "</p>";
}
mysqli_close($conexion);
?>
```

**Con Laravel:**
```php
// Archivo: HeroController.php
public function index()
{
    $heroes = Hero::all();
    return view('heroes.index', ['heroes' => $heroes]);
}
```

**Ventajas de Laravel:**
- ✅ Código más limpio y organizado
- ✅ Seguridad integrada (protección contra SQL injection, CSRF, XSS)
- ✅ Sistema de rutas profesional
- ✅ ORM (Eloquent) para trabajar con bases de datos
- ✅ Sistema de plantillas (Blade)
- ✅ Validación de datos incorporada
- ✅ Gran comunidad y documentación

---

## Arquitectura MVC

Laravel utiliza el patrón **MVC (Model-View-Controller)**, que separa la aplicación en tres capas:

**Flujo completo del patrón MVC:**

1. **Usuario → Rutas:** El navegador solicita `GET /heroes`

2. **Rutas → Controller:** La ruta dirige la petición al método `index()` de `HeroController`

3. **Controller → Model:** El controlador solicita datos al modelo: `Hero::all()`

4. **Model → Base de Datos:** El modelo ejecuta: `SELECT * FROM heroes`

5. **Base de Datos → Model:** La BD retorna los registros encontrados

6. **Model → Controller:** El modelo devuelve los datos al controlador

7. **Controller → View:** El controlador pasa los datos a la vista: `view('heroes.index', ['heroes' => $datos])`

8. **View → Usuario:** La vista genera el HTML final y el controlador lo envía como respuesta HTTP al navegador

### Explicación de cada capa:

**Model (Modelo):**
- Representa una tabla de la base de datos
- Maneja toda la lógica de datos
- Ejemplo: `Hero.php` → tabla `heroes`

**View (Vista):**
- Archivos que contienen HTML, CSS y código Blade
- Solo se encargan de mostrar información
- Ejemplo: `heroes/index.blade.php`

**Controller (Controlador):**
- Intermediario entre Model y View
- Contiene la lógica de negocio
- Ejemplo: `HeroController.php`

---

## Requisitos Previos

Antes de instalar Laravel, necesitas tener instalado:

### 1. PHP >= 8.2

**Para este curso usaremos PHP 8.2**, que es la versión incluida en XAMPP y compatible con Laravel 11.

Verificar versión de PHP:
```bash
php -v
```

Deberías ver algo como:
```
PHP 8.2.x (cli) ...
```

**Nota:** Laravel 11 requiere PHP 8.2 o superior. XAMPP incluye PHP 8.2 por lo que no necesitas instalar nada adicional.

### 2. Composer

Composer es el gestor de dependencias de PHP (similar a npm en JavaScript).

**Verificar instalación:**
```bash
composer --version
```

**Si no lo tienes, descárgalo de:** https://getcomposer.org/

### 3. Servidor web + MySQL

En este curso usaremos **XAMPP**, que incluye:
- Apache (servidor web)
- MariaDB (base de datos compatible con MySQL)
- PHP

**Configuración en XAMPP:**
- Apache corriendo en puerto 80
- MySQL/MariaDB corriendo en puerto 3306

---

## Instalación de Laravel

### ¿Qué es Composer?

Antes de instalar Laravel, es fundamental entender qué es **Composer** y por qué lo necesitamos.

**Composer** es un **gestor de dependencias para PHP**. Permite descargar, instalar y gestionar automáticamente las librerías de terceros que tu proyecto necesita.

**Analogía:** Imagina que estás construyando una casa. En lugar de fabricar tú mismo cada ladrillo, cada tubo y cada ventana, Composer es como un proveedor especializado que te trae todos esos materiales ya hechos, verifica que sean compatibles entre sí, y los coloca organizadamente en tu proyecto.

**¿Qué son las dependencias?**

Las dependencias son paquetes de código creados por otros desarrolladores que tu proyecto utiliza. Por ejemplo:
- Laravel necesita un paquete para manejar rutas
- Laravel necesita un paquete para conectarse a bases de datos
- Laravel necesita un paquete para enviar emails
- Y docenas más...

Composer se encarga de:
1. Descargar todos estos paquetes
2. Verificar que las versiones sean compatibles
3. Mantenerlos actualizados
4. Gestionarlos en el archivo `composer.json`

**Ejemplo visual:**

```
Tu Proyecto (marvel-hub)
│
├── Laravel Framework ────┐
│                         │
│   Composer descarga:    │
│   ├── Paquete de rutas  │
│   ├── Paquete de BD     │
│   ├── Paquete de vistas │
│   └── +50 paquetes más  │
│                         │
└─────────────────────────┘
```

Sin Composer tendrías que:
1. Buscar cada paquete manualmente
2. Descargarlo uno por uno
3. Verificar versiones compatibles (pesadilla)
4. Configurar cada uno manualmente

Con Composer:
```bash
composer create-project laravel/laravel marvel-hub
```
Y listo. Todo se descarga y configura automáticamente.

### Configurar PATH de Windows (Importante)

Para que el sistema use el PHP de XAMPP, necesitas configurar las **Variables de Entorno**.

**¿Por qué es necesario?**

Si tienes otras instalaciones de PHP, Windows puede usar la incorrecta. Debemos asegurarnos que use la de XAMPP.

**Pasos:**

1. Presiona **Windows + R**, escribe `sysdm.cpl` y presiona Enter
2. Ve a la pestaña **"Opciones avanzadas"**
3. Clic en **"Variables de entorno"**
4. En **"Variables del sistema"** (sección inferior), busca `Path`
5. Selecciona `Path` y clic en **"Editar"**
6. Verifica si hay rutas de otras instalaciones PHP (Laragon, MAMP, etc.)
7. Si existen, **elimínalas** (selecciona y clic en "Eliminar")
8. Clic en **"Nuevo"** y agrega:
   ```
   C:\xampp\php
   ```
9. Usa el botón **"Subir"** para mover esta ruta **arriba de todo**
10. Clic en **"Aceptar"** en todas las ventanas
11. **Reinicia Windows** para que los cambios surtan efecto

**Verificar configuración:**

Después de reiniciar, abre una **nueva terminal** y ejecuta:

```bash
php -v
```

Debe mostrar:
```
PHP 8.2.x (cli) ... (ZTS Visual C++ 2022 x64)
```

Y la ruta debe ser de XAMPP:
```bash
where php
```

Debe mostrar:
```
C:\xampp\php\php.exe
```

Si aparece otra ruta (Laragon, MAMP), repite los pasos de configuración del PATH.

### Paso 1: Crear el proyecto

Abre la terminal y navega a la carpeta donde quieres crear el proyecto:

```bash
cd C:\xampp\htdocs
```

Crea el proyecto Laravel con Composer:

```bash
composer create-project laravel/laravel:^11.0 marvel-hub
```

**Nota:** El `:^11.0` especifica que queremos Laravel 11 (versión estable LTS). Si omites esta parte, Composer instalará la versión más reciente, que puede ser inestable.

Este comando:
1. Descarga Laravel 11 y todas sus dependencias (unos 50-60 paquetes)
2. Crea la carpeta `marvel-hub` con toda la estructura
3. Configura el proyecto automáticamente
4. Genera la clave de cifrado (`APP_KEY`)


### Paso 2: Acceder al proyecto

```bash
cd marvel-hub
```

---

## Estructura de Carpetas

Laravel tiene una estructura predefinida. Estas son las carpetas más importantes:

```
marvel-hub/
├── app/
│   ├── Http/
│   │   └── Controllers/     ← Controladores (lógica)
│   └── Models/              ← Modelos (representan tablas BD)
│
├── config/                  ← Archivos de configuración
│
├── database/
│   ├── migrations/          ← Definiciones de tablas (no lo usaremos)
│   └── seeders/             ← Datos de prueba (no lo usaremos)
│
├── public/                  ← Archivos públicos (CSS, JS, imágenes)
│   └── index.php            ← Punto de entrada de la aplicación
│
├── resources/
│   └── views/               ← Vistas (archivos Blade)
│
├── routes/
│   └── web.php              ← Definición de rutas web
│
├── storage/                 ← Archivos generados (logs, caché)
│
├── .env                     ← Variables de entorno (configuración)
├── artisan                  ← Herramienta CLI de Laravel
└── composer.json            ← Dependencias del proyecto
```

### Carpetas que usaremos frecuentemente:

| Carpeta | Uso |
|---------|-----|
| `app/Http/Controllers/` | Crear controladores |
| `app/Models/` | Crear modelos |
| `resources/views/` | Crear vistas (HTML + Blade) |
| `routes/web.php` | Definir rutas de la aplicación |
| `public/css/` | Archivos CSS (si los necesitamos) |

---

## Configuración Inicial

### Archivo .env

El archivo `.env` contiene la configuración específica de tu entorno (desarrollo, producción, etc.).

**Ubicación:** `marvel-hub/.env`

Abre el archivo con un editor de texto y localiza estas variables:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...generada automáticamente...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marvel_hub
DB_USERNAME=root
DB_PASSWORD=root
```

### Variables importantes:

**APP_NAME:**
- Nombre de tu aplicación
- Cámbialo a: `APP_NAME="Marvel Hub"`

**APP_KEY:**
- Clave de cifrado única
- **Ya viene generada automáticamente**
- Si por alguna razón está vacía, ejecuta: `php artisan key:generate`

**APP_DEBUG:**
- En desarrollo: `true` (muestra errores detallados)
- En producción: `false` (oculta errores al usuario)

**APP_URL:**
- URL base de tu aplicación
- Si usas el servidor integrado de Laravel: `http://localhost:8000`
- Si usas XAMPP con virtual host: `http://marvel-hub.test`
- Si usas XAMPP sin virtual host: `http://localhost/marvel-hub/public`

**Variables de Base de Datos:**
- `DB_DATABASE`: Nombre de la base de datos que crearemos
- `DB_USERNAME`: Usuario de MySQL (por defecto `root` en XAMPP)
- `DB_PASSWORD`: Contraseña de MySQL (por defecto vacía en XAMPP)

### Configuración actualizada:

```env
APP_NAME="Marvel Hub"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marvel_hub
DB_USERNAME=root
DB_PASSWORD=
```

**Guarda el archivo.**

### Configuración adicional para XAMPP (Importante)

Si usas XAMPP con MariaDB, necesitas configurar la collation correcta para evitar errores.

**Problema común:**
```
SQLSTATE[HY000]: General error: 1273 Unknown collation: 'utf8mb4_0900_ai_ci'
```

**Causa:** Laravel 11 usa por defecto `utf8mb4_0900_ai_ci` (MySQL 8.0+), pero XAMPP incluye MariaDB que no soporta esa collation.

**Solución:** Añade estas líneas al final de tu archivo `.env`:

```env
DB_COLLATION=utf8mb4_unicode_ci
DB_CHARSET=utf8mb4
```

**Tu .env completo debería verse así:**

```env
APP_NAME="Marvel Hub"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marvel_hub
DB_USERNAME=root
DB_PASSWORD=
DB_COLLATION=utf8mb4_unicode_ci
```

**Luego limpia la caché de configuración:**

```bash
php artisan config:clear
```

**Nota:** Si recibes este error más adelante al usar Tinker, esta es la solución.

---

## Base de Datos

### Paso 1: Crear la base de datos

Accede a **phpMyAdmin**:
- URL: http://localhost/phpmyadmin
- Usuario: `root`
- Contraseña: `root`

1. Haz clic en **"Nueva"** en el panel izquierdo
2. Nombre de la base de datos: `marvel_hub`
3. Cotejamiento: `utf8mb4_unicode_ci`
4. Clic en **"Crear"**

### Paso 2: Crear la tabla heroes

Selecciona la base de datos `marvel_hub` y ve a la pestaña **SQL**.

Copia y ejecuta este script:

```sql
CREATE TABLE heroes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    real_name VARCHAR(100),
    power VARCHAR(255),
    power_level INT DEFAULT 0,
    team VARCHAR(100),
    bio TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Estructura de la tabla:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | Identificador único (autoincremental) |
| `name` | VARCHAR(100) | Nombre del héroe (obligatorio) |
| `real_name` | VARCHAR(100) | Identidad real (opcional) |
| `power` | VARCHAR(255) | Descripción del poder |
| `power_level` | INT | Nivel de poder (0-10000) |
| `team` | VARCHAR(100) | Equipo al que pertenece |
| `bio` | TEXT | Biografía del héroe |
| `is_active` | TINYINT(1) | Si está activo (1) o no (0) |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Fecha de última actualización |

### Paso 3: Insertar datos de ejemplo

Ejecuta este script en la pestaña SQL:

```sql
INSERT INTO heroes (name, real_name, power, power_level, team, bio, is_active) VALUES
('Iron Man', 'Tony Stark', 'Tecnología avanzada', 8500, 'Vengadores', 'Genio, multimillonario, playboy, filántropo', 1),
('Thor', 'Thor Odinson', 'Dios del Trueno', 9000, 'Vengadores', 'Príncipe de Asgard y portador de Mjolnir', 1),
('Spider-Man', 'Peter Parker', 'Sentido arácnido', 7000, 'Vengadores', 'Amigable vecino de Nueva York', 1),
('Doctor Strange', 'Stephen Strange', 'Hechicería', 9000, 'Vengadores', 'Hechicero Supremo y guardián de la Gema del Tiempo', 1),
('Black Widow', 'Natasha Romanoff', 'Espía experta', 6500, 'Vengadores', 'Agente de alto nivel de S.H.I.E.L.D.', 1);
```

**Verifica los datos:**

Ejecuta en SQL:
```sql
SELECT * FROM heroes;
```

Deberías ver los 5 héroes insertados.

---

## Verificar Conexión a la Base de Datos

### ¿Qué es Artisan?

Laravel incluye una herramienta de línea de comandos llamada **Artisan** que facilita muchas tareas comunes de desarrollo.

**Artisan** es una CLI (Command Line Interface - Interfaz de Línea de Comandos) que viene con Laravel y permite ejecutar comandos para:
- Generar archivos (controladores, modelos, etc.)
- Gestionar la base de datos
- Limpiar cachés
- Iniciar el servidor de desarrollo
- Y mucho más...

**Analogía:** Es como tener un asistente que te ayuda con tareas repetitivas. En lugar de crear archivos manualmente, Artisan los genera con la estructura correcta.

**Estructura de un comando Artisan:**

```bash
php artisan <nombre-comando> <argumentos> <opciones>
```

Por ejemplo:
```bash
php artisan serve          # Inicia servidor de desarrollo
php artisan list           # Muestra todos los comandos disponibles
php artisan make:controller HeroController  # Genera un controlador
```

**¿Por qué se escribe `php artisan`?**
- `php`: Ejecuta el intérprete de PHP
- `artisan`: Es un archivo PHP en la raíz del proyecto que contiene toda la lógica de comandos

### ¿Qué es Tinker?

**Tinker** es un comando de Artisan que abre una consola interactiva (REPL) donde puedes ejecutar código PHP con todo Laravel cargado.

REPL significa: **R**ead-**E**val-**P**rint-**L**oop (Leer-Evaluar-Imprimir-Repetir)

**¿Para qué sirve Tinker?**
- Probar código PHP rápidamente sin crear archivos
- Interactuar con la base de datos
- Probar modelos y consultas
- Verificar configuraciones

**Analogía:** Es como tener un "laboratorio de experimentos" donde puedes probar cosas en tiempo real sin afectar tu código.

### Probar la conexión con Tinker:

Ejecuta este comando desde la carpeta del proyecto:

```bash
php artisan tinker
```

Verás algo como:
```
Psy Shell v0.x.x (PHP 8.2.x — cli)
>>>
```

El símbolo `>>>` indica que Tinker está esperando que escribas código PHP.

Dentro de Tinker, ejecuta:

```php
DB::connection()->getPdo();
```

**¿Qué hace este comando?**
- `DB::connection()`: Obtiene la conexión a la base de datos configurada en `.env`
- `->getPdo()`: Accede al objeto PDO (PHP Data Objects) que gestiona la conexión

Si la conexión es exitosa, verás algo como:
```
=> PDO {#4567
     inTransaction: false,
     attributes: {
       ...
     }
   }
```

Si hay error, verás un mensaje indicando el problema:
- "Access denied": Usuario o contraseña incorrectos en `.env`
- "Unknown database": La base de datos `marvel_hub` no existe
- "Connection refused": MySQL no está corriendo en XAMPP (inicia MySQL desde el Control Panel)

**Salir de Tinker:**
```php
exit
```

O presiona `Ctrl + C`

---

## Servidor de Desarrollo

Laravel incluye un servidor de desarrollo integrado que no requiere configurar Apache.

### Iniciar el servidor:

Desde la carpeta del proyecto (`C:\xampp\htdocs\marvel-hub`), ejecuta:

```bash
php artisan serve
```

Verás un mensaje como:
```
INFO  Server running on [http://127.0.0.1:8000].

Press Ctrl+C to stop the server
```

### Acceder a la aplicación:

Abre tu navegador y ve a:
```
http://localhost:8000
```

Deberías ver la página de bienvenida de Laravel.

### Detener el servidor:

Presiona **Ctrl + C** en la terminal.

---



---

## Más Comandos Artisan Útiles

Ahora que conoces Artisan y Tinker, aquí tienes otros comandos útiles que irás descubriendo:

```bash
# Ver todos los comandos disponibles
php artisan list

# Generar clave de aplicación (solo si APP_KEY está vacía)
php artisan key:generate

# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Ver todas las rutas definidas
php artisan route:list

# Abrir consola interactiva (Tinker)
php artisan tinker

# Iniciar servidor de desarrollo
php artisan serve
```

**Nota:** Iremos aprendiendo más comandos de Artisan a medida que avancemos en las fases.

---

## Resumen de la Fase 0

### ¿Qué hemos aprendido?

1. **¿Qué es Laravel?**
   - Framework de PHP para desarrollo web
   - Ventajas sobre PHP puro

2. **Arquitectura MVC**
   - Model: Datos (tablas de BD)
   - View: Presentación (HTML)
   - Controller: Lógica intermedia

3. **Instalación**
   - Requisitos (PHP, Composer, MySQL)
   - Crear proyecto con Composer
   - Estructura de carpetas

4. **Configuración**
   - Archivo `.env`
   - Variables importantes
   - APP_KEY, DB_*

5. **Base de Datos**
   - Crear BD en phpMyAdmin
   - Crear tabla `heroes`
   - Insertar datos de prueba

6. **Servidor**
   - Apache de XAMPP (`http://localhost/marvel-hub/public`)
   - `php artisan serve` (alternativa)
   - Virtual host (alternativa)

---

## Resolución de Problemas Comunes

### Error: "No application encryption key has been specified"

**Solución:**
```bash
php artisan key:generate
```

### Error: "SQLSTATE[HY000] [1049] Unknown database 'marvel_hub'"

**Solución:**
- Verifica que la BD existe en phpMyAdmin
- Verifica que `DB_DATABASE=marvel_hub` en `.env`

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Solución:**
- Asegúrate de que MySQL está corriendo en XAMPP (Panel de control → Start MySQL)
- Verifica el puerto en `.env` (normalmente 3306)

### Error: Página en blanco al acceder a http://localhost:8000

**Solución:**
- Revisa la terminal donde corre `php artisan serve`
- Busca mensajes de error
- Ejecuta `php artisan config:clear`

### El CSS o JS no cargan

**Solución:**
- Los archivos públicos van en `public/`
- Usa rutas absolutas: `/css/style.css`
- Verifica que el servidor esté corriendo

---

## Recursos Adicionales

- **Documentación oficial:** https://laravel.com/docs
- **Laracasts (tutoriales en video):** https://laracasts.com
- **Laravel News:** https://laravel-news.com
