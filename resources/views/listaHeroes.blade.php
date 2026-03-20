<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Héroes Marvel</title>
</head>
<body>
    <h1>Héroes de Marvel</h1>
    <h2> VERSION 1</h2>
    <ul>
         <!-- Para recorrer el array de héroes usamos un foreach y seguimos usando las llaves dobles -->
        @foreach($heroes as $hero)
            <li>{{ $hero }}</li>
        @endforeach  <!-- Tenemos que tener en cuenta el cierre del foreach -->
    </ul>
</body>
</html>
