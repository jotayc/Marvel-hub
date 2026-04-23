# Ejercicio Tinker — Fase 4: Eloquent ORM

Abre Tinker desde la raíz del proyecto:

```bash
php artisan tinker
```

Importa el modelo antes de empezar:

```php
use App\Models\Hero;
```

---

## Bloque 1: Consultas básicas

**1.1.** Obtén todos los héroes de la base de datos.

**1.2.** Obtén solo el primer héroe de la tabla.

**1.3.** Busca el héroe con ID 3.

**1.4.** Guarda el héroe con ID 2 en una variable e imprime su nombre y su poder por separado.

**1.5.** Cuenta cuántos héroes hay en total.

---

## Bloque 2: Filtros y ordenación

**2.1.** Obtén todos los héroes que pertenecen al equipo `Vengadores`.

**2.2.** Obtén todos los héroes con `power_level` mayor que 8000.

**2.3.** Obtén todos los héroes activos (is_active = true), ordenados por nombre de forma ascendente.

**2.4.** Obtén el héroe con mayor `power_level`.

**2.5.** Obtén los 3 héroes con mayor `power_level`.

**2.6.** Cuenta cuántos héroes hay en el equipo `Vengadores`.

---

## Bloque 3: Creación y modificación

**3.1.** Crea un nuevo héroe con estos datos:
- Nombre: `Capitana Marvel`
- Nombre real: `Carol Danvers`
- Poder: `Energía cósmica`
- Nivel de poder: `9500`
- Equipo: `Vengadores`
- Bio: `La heroína más poderosa del universo`
- Activo: `true`

**3.2.** Busca el héroe que acabas de crear y comprueba que está en la base de datos.

**3.3.** Actualiza el `power_level` de `Capitana Marvel` a `9800`.

**3.4.** Comprueba el valor actualizado recuperando el héroe de nuevo de la base de datos.

---

## Bloque 4: Eliminación y verificación

**4.1.** Elimina el héroe `Capitana Marvel` de la base de datos.

**4.2.** Intenta buscar ese héroe por su ID con `find()`. ¿Qué devuelve?

**4.3.** Intenta buscar ese mismo héroe con `findOrFail()`. ¿Qué ocurre? ¿Por qué es diferente al caso anterior?
