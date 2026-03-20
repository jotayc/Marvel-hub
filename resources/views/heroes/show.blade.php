<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Héroe</title>
</head>
<body>
    @if ($hero)
        <h1>{{ $hero['nombre'] }}</h1>
        <p>Poder: {{ $hero['poder'] }}</p>
    @else
        <p>Héroe no encontrado</p>
    @endif
</body>
</html>
