{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Marvel Hub')</title>
    <link rel="stylesheet" href="{{ asset('css/heroes.css') }}">
</head>
<body>

    <nav>
        <a href="{{ route('heroes.index') }}">Héroes</a>
        <a href="{{ route('heroes.active') }}">Activos</a>
        <a href="{{ route('heroes.powerful') }}">Más poderosos</a>
    </nav>

    <main>
        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @yield('contenido')
    </main>

    <footer>
        <p>JC Alfaro - Marvel Hub &copy; 2026</p>
    </footer>

</body>
</html>
