# Fase 0: Introducción al Ecosistema Laravel

> **Tiempo estimado:** 2-3 sesiones (3-4.5 horas)  
> **Objetivo:** Comprender el ecosistema PHP moderno y configurar el entorno de desarrollo

---

## 🎯 Objetivos de Aprendizaje

Al finalizar esta fase serás capaz de:

1. ✅ Entender qué es Laravel y por qué es el framework PHP más popular
2. ✅ Comprender el ecosistema PHP moderno (Composer, PSR, dependencias)
3. ✅ Instalar y configurar Laravel en Windows correctamente
4. ✅ Comprender la estructura de directorios de un proyecto Laravel
5. ✅ Usar Artisan (CLI de Laravel) para tareas comunes
6. ✅ Crear el proyecto base "Marvel Universe Hub"

---

## 📚 Parte 1: ¿Qué es Laravel y Por Qué Aprenderlo?

### 1.1 Laravel en el Ecosistema Web

**Laravel** es un framework MVC de PHP creado por Taylor Otwell en 2011. Es el framework PHP más popular del mundo según encuestas de Stack Overflow, GitHub stars y descargas de Packagist.

#### Comparación conceptual con Java/Android

| Concepto | Java/Android | Laravel/PHP |
|----------|--------------|-------------|
| **Framework web** | Spring Boot, Spark | **Laravel** |
| **Gestor de dependencias** | Maven, Gradle | **Composer** |
| **ORM** | Hibernate, Room | **Eloquent** |
| **Repositorio central** | Maven Central | **Packagist** |
| **CLI de proyecto** | `gradle`, `mvn` | **`artisan`** |
| **Servidor embebido** | Tomcat, Jetty | **Artisan serve** |

### 1.2 ¿Por Qué Laravel?

**Razones profesionales:**
- 🏆 Framework PHP más demandado en ofertas de empleo (2024)
- 📦 Ecosistema maduro con miles de paquetes
- 🚀 Curva de aprendizaje progresiva (vs Symfony)
- 📖 Documentación excelente y comunidad activa
- 🎯 Equilibrio entre "magia" y control

**Razones didácticas (para FP):**
- ✨ Sintaxis elegante y expresiva (fácil de leer)
- 🧰 Herramientas CLI que aceleran el desarrollo
- 🎨 Blade: motor de plantillas muy intuitivo
- 🗄️ Eloquent: ORM que "se entiende solo"
- 📚 Conceptos extrapolables a otros frameworks (MVC, ORM, routing)

### 1.3 Laravel 11: La Versión a Usar en Este Plan

Laravel 11 es la versión **LTS (Long Term Support)** lanzada en marzo 2024, con soporte hasta 2026 (bugs) y 2027 (seguridad).

**Novedades destacadas:**
- Estructura de directorios más simple
- Configuración reducida (menos archivos)
- Mejor rendimiento
- PHP 8.2+ obligatorio (compatible con PHP 8.3)

> ⚠️ **IMPORTANTE:** Laravel 12 salió en enero 2025 y es muy reciente. Para aprender, usaremos **Laravel 11** que es más estable, tiene mejor documentación y es lo que encontrarás en empresas actualmente.

> 💡 **Sobre tu PHP 8.3:** Perfecto, PHP 8.3 es totalmente compatible con Laravel 11 (requiere mínimo PHP 8.2).

---

## 📚 Parte 2: Ecosistema PHP Moderno

### 2.1 Composer: El Gestor de Dependencias

**Composer** es para PHP lo que Maven/Gradle es para Java.

**Conceptos clave:**

```json
{
    "require": {
        "laravel/framework": "^11.0"
    }
}
```

**Comparación con Gradle (Android):**

```groovy
// build.gradle (Android)
dependencies {
    implementation 'androidx.appcompat:appcompat:1.6.1'
}
```

```json
// composer.json (Laravel)
{
    "require": {
        "laravel/framework": "^11.0"
    }
}
```

**Comandos básicos de Composer:**

```bash
composer install        # Equivale a: gradle build
composer update         # Actualiza dependencias
composer require vendor/package  # Añade nueva dependencia
composer dump-autoload  # Regenera autoload (como gradle clean)
```

### 2.2 PSR: Estándares PHP

**PSR (PHP Standard Recommendations)** son estándares de la comunidad PHP.

Los más importantes:
- **PSR-4:** Autoloading de clases (similar a package en Java)
- **PSR-12:** Estilo de código (como Google Java Style Guide)
- **PSR-7:** HTTP messages

**Ejemplo comparativo:**

```java
// Java: package define la estructura
package com.marvel.heroes;

public class Hero {
    // ...
}
```

```php
<?php
// PHP PSR-4: namespace define la estructura
namespace App\Models;

class Hero {
    // ...
}
```

### 2.3 Packagist: El Repositorio Central

**Packagist** (https://packagist.org) es el Maven Central de PHP.

Ejemplos de paquetes populares:
- `laravel/framework` - El propio Laravel
- `guzzlehttp/guzzle` - Cliente HTTP (como OkHttp/Retrofit)
- `intervention/image` - Manipulación de imágenes
- `barryvdh/laravel-debugbar` - Debug toolbar

---

## 💻 Parte 3: Instalación y Configuración en Windows

### 3.1 Requisitos Previos

**Software necesario:**
1. ✅ **PHP 8.2+** (PHP 8.3 recomendado) con extensiones (openssl, pdo, mbstring, tokenizer, xml, ctype, json)
2. ✅ **Composer** (gestor de dependencias)
3. ✅ **MySQL** (base de datos) - Incluido en MAMP Pro, XAMPP o Laragon
4. ✅ **VS Code** + extensiones PHP (recomendado)
5. ⚠️ **Git** (control de versiones)

> 💡 **Nota:** En este plan usaremos **MySQL** en lugar de SQLite porque es más realista para producción y es lo que encontrarás en empresas. MAMP Pro, XAMPP y Laragon incluyen MySQL.

### 3.2 Instalación Paso a Paso

#### Opción A: Laragon (Recomendada para Windows) ✅

**Laragon** es un entorno AMP (Apache, MySQL, PHP) portable para Windows. Es la forma MÁS SIMPLE de tener todo configurado.

**Ventajas de Laragon:**
- ✨ Instalación todo-en-uno (Apache, MySQL, PHP, Composer)
- 🚀 Configuración automática del PATH
- 🔄 Cambiar versiones de PHP con un clic
- 🌐 Virtual hosts automáticos (marvel-hub.test)
- 📦 Terminal integrada con cmder
- 🎯 Diseñado específicamente para Laravel

1. **Descargar Laragon Full:**
   - Ir a https://laragon.org/download/
   - Descargar "Laragon Full" (incluye Apache, MySQL, PHP, Composer)

2. **Instalar:**
   - Ejecutar el instalador
   - Ruta recomendada: `C:\laragon`
   - Marcar: "Añadir Laragon al PATH"

3. **Verificar instalación:**
   ```bash
   php -v        # Debe mostrar PHP 8.2+ o 8.3
   composer -V   # Debe mostrar Composer 2.x
   ```

4. **Iniciar servicios:**
   - Clic derecho en icono de Laragon → Start All
   - O desde la ventana principal → "Start All"

#### Opción B: XAMPP + Composer

Si prefieres XAMPP (más conocido):

1. Instalar XAMPP (https://www.apachefriends.org/)
2. Instalar Composer por separado (https://getcomposer.org/)
3. Añadir PHP al PATH de Windows
4. Credenciales MySQL: `root` con contraseña vacía (como Laragon)

> 💡 **Recomendación didáctica:** Usa **Laragon** (Opción A) para aprender. Es menos problemático que configurar XAMPP manualmente y tus alumnos agradecerán no lidiar con configuraciones de PATH.

### 3.3 Verificación del Entorno

```bash
# Verifica versiones
php -v
# PHP 8.2.x o 8.3.x (ambas versiones son compatibles con Laravel 11)

composer -V
# Composer version 2.x.x

# Verifica extensiones PHP necesarias
php -m | grep pdo
php -m | grep mbstring
php -m | grep openssl
```

> ✅ **PHP 8.3:** Si tienes PHP 8.3, perfecto. Laravel 11 es compatible con PHP 8.2, 8.3 y superiores. PHP 8.3 es incluso mejor para rendimiento.

> 💡 **Tip de Laragon:** Para abrir la terminal integrada, clic derecho en el icono de Laragon → Terminal. Esto abre cmder con todas las rutas ya configuradas.

### 3.4 Servidor de Desarrollo: artisan serve vs Apache

**Laragon incluye Apache**, pero para aprender Laravel es mejor usar el servidor de desarrollo integrado:

**Durante este curso usaremos: `php artisan serve`**
- ✅ URL simple: `http://127.0.0.1:8000`
- ✅ No requiere configurar Virtual Hosts
- ✅ Fácil de reiniciar si cambias `.env`
- ✅ Es la forma estándar en Laravel

**Apache de Laragon lo usarás después para:**
- Múltiples proyectos simultáneos
- URLs bonitas (marvel-hub.test)
- Configuraciones avanzadas

> 💡 **No necesitas iniciar Apache** de Laragon para este curso, solo asegúrate de que **MySQL esté corriendo**.

---

## 🚀 Parte 4: Creando el Primer Proyecto Laravel

### 4.1 Crear Proyecto con Composer

**Comando para instalar Laravel 11 (NO Laravel 12):**
```bash
# Navega a tu carpeta de proyectos
cd C:\laragon\www

# Crea el proyecto Laravel 11 específicamente
composer create-project laravel/laravel marvel-hub "^11.0"

# Entra en el proyecto
cd marvel-hub
```

> ⚠️ **IMPORTANTE:** El sufijo `"^11.0"` indica a Composer que instale Laravel 11.x (no 12). Sin este parámetro, instalaría Laravel 12 que es demasiado reciente para este curso.

**Verificar la versión instalada:**
```bash
php artisan --version
# Debe mostrar: Laravel Framework 11.x.x
```

> 🔍 **Comparación con Android:** Es similar a crear un proyecto nuevo en Android Studio usando Gradle, pero desde CLI. El `"^11.0"` es como especificar `compileSdkVersion` o versiones de dependencias en `build.gradle`.

**Lo que acaba de pasar:**
1. Composer descargó Laravel 11 y todas sus dependencias (puede tardar 2-3 minutos)
2. Se creó la estructura completa de directorios
3. Se generó una clave de aplicación (APP_KEY en .env)
4. Se instalaron todos los paquetes necesarios

### 4.2 Arrancar el Servidor de Desarrollo

```bash
# Dentro de la carpeta marvel-hub
php artisan serve
```

**Salida esperada:**
```
INFO  Server running on [http://127.0.0.1:8000].

Press Ctrl+C to stop the server
```

**Abre el navegador:** http://127.0.0.1:8000

Deberías ver la página de bienvenida de Laravel 🎉

> 💡 **Nota:** `php artisan serve` es como ejecutar un servidor Tomcat embebido en Java, pero mucho más simple.

---

## 📁 Parte 5: Estructura de Directorios de Laravel 11

```
marvel-hub/
│
├── app/                    # Lógica de la aplicación (Models, Controllers, etc.)
│   ├── Http/
│   │   └── Controllers/    # Controladores (equivalente a Activities en Android)
│   ├── Models/             # Modelos Eloquent (equivalente a Entities de Room)
│   └── Providers/          # Service Providers (configuración avanzada)
│
├── bootstrap/              # Archivos de inicialización
│   └── app.php             # Bootstrap de la app
│
├── config/                 # Archivos de configuración
│   ├── app.php             # Configuración general
│   └── database.php        # Configuración de BD
│
├── database/               # Migraciones, factories, seeders
│   ├── migrations/         # Esquema de BD en PHP (como SQL scripts versionados)
│   └── seeders/            # Datos de prueba
│
├── public/                 # Punto de entrada web (carpeta pública)
│   └── index.php           # Front Controller (entrada única)
│
├── resources/              # Assets y vistas
│   ├── css/                # CSS (procesado por Vite)
│   ├── js/                 # JavaScript
│   └── views/              # Plantillas Blade (equivalente a XML layouts)
│
├── routes/                 # Definición de rutas
│   └── web.php             # Rutas web (las usaremos mucho)
│
├── storage/                # Archivos generados (logs, cache, uploads)
│   ├── app/
│   ├── framework/
│   └── logs/
│
├── tests/                  # Tests automatizados (PHPUnit)
│
├── vendor/                 # Dependencias (como node_modules o build en Android)
│
├── .env                    # Variables de entorno (DB, keys, etc.)
├── artisan                 # CLI de Laravel (como gradlew)
└── composer.json           # Dependencias (como build.gradle)
```

### Comparación con Android Studio

| Laravel | Android Studio |
|---------|----------------|
| `app/Models/` | `app/src/main/java/.../models/` |
| `app/Http/Controllers/` | `app/src/main/java/.../` (Activities/Fragments) |
| `resources/views/` | `app/src/main/res/layout/` |
| `routes/web.php` | AndroidManifest.xml + Intent Filters |
| `public/` | `app/src/main/res/` |
| `storage/` | Caché interna de Android |
| `.env` | `BuildConfig` o `strings.xml` |
| `vendor/` | `build/` + dependencias externas |

### Carpetas Clave para Empezar

🎯 **Enfócate en estas 4 carpetas al principio:**

1. **`routes/web.php`** → Aquí defines las URLs de tu app
2. **`app/Http/Controllers/`** → Lógica de negocio
3. **`app/Models/`** → Modelos de datos (BD)
4. **`resources/views/`** → Templates HTML (Blade)

---

## 🔧 Parte 6: Artisan - La CLI de Laravel

**Artisan** es el CLI (Command Line Interface) de Laravel. Es tu mejor amigo para desarrollo.

### 6.1 Comandos Artisan Esenciales

```bash
# Ver lista completa de comandos
php artisan list

# Información de la aplicación
php artisan about

# Crear un controlador
php artisan make:controller HeroController

# Crear un modelo
php artisan make:model Hero

# Crear una migración
php artisan make:migration create_heroes_table

# Ejecutar migraciones (crear tablas)
php artisan migrate

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rutas registradas
php artisan route:list
```

### 6.2 Comparación con Gradle/Android

```bash
# Android (Gradle)
./gradlew build
./gradlew clean
./gradlew test

# Laravel (Artisan)
php artisan serve        # Servidor de desarrollo
php artisan migrate      # Actualizar BD
php artisan test         # Ejecutar tests
```

### 6.3 Ayuda de Comandos

```bash
# Ayuda de un comando específico
php artisan help make:controller

# Mostrará todas las opciones y flags disponibles
```

---

## 🦸 Proyecto Marvel: Setup Inicial

### Paso 1: Crear el Proyecto

```bash
cd C:\laragon\www

# Instalar Laravel 11 específicamente
composer create-project laravel/laravel marvel-hub "^11.0"

cd marvel-hub
```

**Verificar que tenemos Laravel 11:**
```bash
php artisan --version
# Salida esperada: Laravel Framework 11.x.x
```

> ⚠️ **Si instalaste Laravel 12 por error:** Elimina la carpeta `marvel-hub` y vuelve a crear el proyecto con el comando correcto incluyendo `"^11.0"`.

### Paso 2: Configurar Base de Datos (MySQL)

**Crear la base de datos en MySQL:**

1. **Abre phpMyAdmin** (Laragon lo incluye):
   - Clic derecho en el icono de Laragon en la bandeja del sistema
   - Menu → MySQL → phpMyAdmin
   - Se abrirá en el navegador: http://localhost/phpmyadmin

2. **Crear base de datos:**
   - Clic en "Nueva" o "New" en el panel izquierdo
   - Nombre: `marvel_hub`
   - Cotejamiento: `utf8mb4_unicode_ci`
   - Clic en "Crear"

**Configurar `.env` para MySQL:**

```bash
# Abre .env con VS Code
code .env
```

**Editar estas líneas:**

```env
# Configuración de MySQL para Laragon
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306               # Puerto por defecto de MySQL
DB_DATABASE=marvel_hub     # Nombre de la BD que creaste
DB_USERNAME=root           # Usuario por defecto de Laragon
DB_PASSWORD=               # Contraseña VACÍA en Laragon (dejar sin valor)
```

> 💡 **Credenciales de Laragon:** Por defecto es `root` sin contraseña (campo vacío). Esto es diferente a MAMP Pro (root/root) o algunos XAMPP (root/root).

> 🔍 **Comparación con Android:** Esto es equivalente a configurar Room Database en Android, donde defines el nombre y versión de la BD. Aquí defines la conexión en `.env` y Laravel se encarga del resto.

### Paso 3: Verificar Configuración

**Probar conexión a MySQL:**

```bash
# Dentro de la carpeta marvel-hub
php artisan db:show

# Si la conexión es correcta, verás información de la BD:
# MySQL ......................................... 8.x.x
# Database ...................................... marvel_hub
# Host .......................................... 127.0.0.1
# Port .......................................... 3306
```

> ⚠️ **Si da error:** Revisa que MySQL esté corriendo en MAMP y que las credenciales en `.env` sean correctas. Ejecuta `php artisan config:clear` después de cambiar `.env`.

**Ejecutar migraciones:**

```bash
# Probar conexión a BD
php artisan migrate

# Deberías ver:
# Migration table created successfully.
# Migrating: 0001_01_01_000000_create_users_table
# Migrated:  0001_01_01_000000_create_users_table
# Migrating: 0001_01_01_000001_create_cache_table
# Migrated:  0001_01_01_000001_create_cache_table
# Migrating: 0001_01_01_000002_create_jobs_table  
# Migrated:  0001_01_01_000002_create_jobs_table
```

**Verificar en phpMyAdmin:**
- Actualiza phpMyAdmin
- Verás las tablas creadas: `users`, `cache`, `jobs`, `migrations`, etc.

> 🎉 **¡Éxito!** Laravel creó automáticamente las tablas en MySQL. Esto es el sistema de migraciones que aprenderás en la Fase 5.

### Paso 4: Arrancar Servidor

```bash
php artisan serve
```

**Abrir:** http://127.0.0.1:8000

**¡Ya tienes tu primer proyecto Laravel funcionando! 🎉**

---

## ⚠️ Errores Comunes y Advertencias Didácticas

### Error 0: Se instaló Laravel 12 en lugar de Laravel 11

**Problema:** Ejecutaste `composer create-project laravel/laravel marvel-hub` sin especificar versión.

**Por qué pasa:** Composer instala la última versión disponible (Laravel 12 desde enero 2025).

**Solución:**
```bash
# 1. Elimina el proyecto creado
rm -rf marvel-hub  # En Linux/Mac
# O simplemente borra la carpeta en Windows

# 2. Crea el proyecto con versión específica
composer create-project laravel/laravel marvel-hub "^11.0"

# 3. Verifica la versión
cd marvel-hub
php artisan --version
# Debe mostrar: Laravel Framework 11.x.x
```

**¿Por qué usar Laravel 11 y no 12?**
- Laravel 11 es LTS (soporte largo plazo)
- Más documentación y recursos disponibles
- Es lo que se usa actualmente en empresas
- Laravel 12 es muy reciente (enero 2025), puede tener bugs

### Error 1: "composer: command not found"

**Problema:** Composer no está en el PATH de Windows.

**Solución:**
1. Si usas Laragon: Reinicia el terminal después de instalar
2. Si usas XAMPP: Añade Composer al PATH manualmente

### Error 2: "required extension missing"

**Problema:** Faltan extensiones PHP.

**Solución en Laragon:**
1. Botón derecho en Laragon → Tools → Quick add → Extensions
2. Marca: `openssl`, `pdo_mysql`, `mbstring`, `zip`
3. Reinicia Apache

### Error 3: "Application key not generated"

**Problema:** Falta APP_KEY en `.env`.

**Solución:**
```bash
php artisan key:generate
```

### Error 4: "Permission denied" en storage/

**Problema:** Windows no permite escribir en `storage/` o `bootstrap/cache/`.

**Solución:**
- Botón derecho en carpeta → Propiedades → Desmarcar "Solo lectura"
- O ejecutar terminal como Administrador

### Error 5: Puerto 8000 ocupado

**Problema:** Otro proceso usa el puerto 8000.

**Solución:**
```bash
# Usa otro puerto
php artisan serve --port=8080
```

### Error 6: "SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'"

**Problema:** Credenciales de MySQL incorrectas en `.env`.

**Solución para Laragon:**
1. Verifica las credenciales correctas:
   - Usuario: `root`
   - Contraseña: **VACÍA** (dejar el campo sin valor)
   
2. Edita `.env`:
   ```env
   DB_USERNAME=root
   DB_PASSWORD=        # ← Dejar VACÍO (sin nada después del =)
   ```

3. **Limpia la caché de configuración:**
   ```bash
   php artisan config:clear
   ```

> 💡 **Nota:** Laragon usa contraseña vacía por defecto, diferente a MAMP Pro (root/root) o algunos XAMPP.

### Error 7: "SQLSTATE[HY000] [2002] Connection refused"

**Problema:** MySQL no está ejecutándose.

**Solución con Laragon:**
1. Clic derecho en el icono de Laragon (bandeja del sistema)
2. Verifica que MySQL esté iniciado:
   - Si dice "Stop MySQL" → está corriendo ✅
   - Si dice "Start MySQL" → haz clic para iniciarlo
3. Alternativamente: Botón "Start All" en la ventana principal de Laragon
4. Verifica que el puerto sea 3306 en `.env`

### Error 8: "Base de datos 'marvel_hub' no existe"

**Problema:** No creaste la base de datos en MySQL.

**Solución:**
1. Abre phpMyAdmin
2. Crea la base de datos `marvel_hub`
3. Verifica que `DB_DATABASE=marvel_hub` en `.env`
4. Ejecuta `php artisan config:clear`

---

## 📝 Conceptos Clave para Recordar

### 1. Composer vs npm
- **Composer** = dependencias PHP (backend)
- **npm** = dependencias JavaScript (frontend)
- Laravel usa AMBOS (Composer para PHP, npm para assets)

### 2. .env: Tu Archivo de Configuración
- Nunca lo subas a Git (está en `.gitignore`)
- Contiene credenciales sensibles (DB, API keys)
- Cada entorno (dev, producción) tiene su propio `.env`
- **Importante:** Ejecuta `php artisan config:clear` después de cambiar `.env`

### 3. Artisan: Tu Mejor Amigo
- Generación de código (controllers, models, migrations)
- Mantenimiento (caché, migraciones)
- Información (rutas, comandos)

### 4. Vendor: No Tocar
- Contiene dependencias descargadas
- Se regenera con `composer install`
- NUNCA edites archivos en `vendor/`

---

## 🎯 Ejercicio Práctico Final

### Ejercicio 1: Exploración de Archivos

1. **Abre `routes/web.php`:**
   - Observa la ruta `/` (página de bienvenida)
   - Identifica la función anónima (closure)

2. **Abre `resources/views/welcome.blade.php`:**
   - Es la vista de bienvenida
   - Observa la sintaxis HTML mezclada con PHP

3. **Ejecuta comandos Artisan:**
   ```bash
   php artisan route:list    # Ver rutas
   php artisan about         # Info del proyecto
   ```

### Ejercicio 2: Primera Modificación

**Modifica la página de inicio:**

```php
// routes/web.php
Route::get('/', function () {
    return view('welcome', [
        'appName' => 'Marvel Universe Hub'
    ]);
});
```

```blade
<!-- resources/views/welcome.blade.php -->
<!-- Busca el <title> y cámbialo: -->
<title>{{ $appName }}</title>
```

**Refresca el navegador** → Deberías ver "Marvel Universe Hub" en el título de la pestaña.

---

## 📚 Recursos Adicionales para Esta Fase

- **Documentación oficial de instalación:** https://laravel.com/docs/11.x/installation
- **Laracasts "Laravel from Scratch" (primeros videos gratis):** https://laracasts.com/series/laravel-11-for-beginners
- **PHP: The Right Way:** https://phptherightway.com/ (guía de PHP moderno)

---

## ✅ Checklist de Finalización de Fase 0

Marca las tareas completadas:

- [ ] ✅ Instalado PHP 8.2+ o 8.3 y Composer
- [ ] ✅ Creado proyecto `marvel-hub` con **Laravel 11** (verificado con `php artisan --version`)
- [ ] ✅ Creado base de datos `marvel_hub` en MySQL
- [ ] ✅ Configurado `.env` con credenciales de MySQL correctas
- [ ] ✅ Ejecutado `php artisan migrate` exitosamente
- [ ] ✅ Servidor funcionando en http://127.0.0.1:8000
- [ ] ✅ Comprendido la estructura de directorios básica
- [ ] ✅ Probado al menos 5 comandos de Artisan
- [ ] ✅ Hecho el ejercicio de modificación de welcome

---

## 🚀 Próxima Fase

**Fase 1: Fundamentos de Laravel y Primera Ruta**

En la siguiente fase aprenderás:
- El patrón MVC en Laravel (comparado con Android)
- Ciclo de vida de una petición HTTP
- Crear tu primera ruta personalizada
- Pasar datos a las vistas
- Variables de entorno

**Archivo:** `Fase_01_Fundamentos_Laravel.md`

---

> 💬 **Reflexión didáctica:** Esta fase es CRÍTICA. Los alumnos que no tienen bien configurado el entorno se frustran rápido. Dedica tiempo en clase a que todos tengan Laravel funcionando antes de avanzar.
