<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $hero->name }} - Marvel Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .hero-detail {
            border: 2px solid #333;
            padding: 30px;
            border-radius: 8px;
            background: #f5f5f5;
        }
        h1 {
            color: #d32f2f;
            margin-top: 0;
        }
        .info-row {
            margin: 15px 0;
            padding: 10px;
            background: white;
            border-radius: 4px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #1976d2;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="hero-detail">
        <h1>{{ $hero->name }}</h1>

        <div class="info-row">
            <span class="label">Nombre real:</span>
            {{ $hero->real_name }}
        </div>

        <div class="info-row">
            <span class="label">Poder:</span>
            {{ $hero->power }}
        </div>

        <div class="info-row">
            <span class="label">Nivel de poder:</span>
            {{ $hero->power_level }}
        </div>

        <div class="info-row">
            <span class="label">Equipo:</span>
            {{ $hero->team }}
        </div>

        <a href="{{ route('heroes.index') }}" class="back-link">← Volver al listado</a>

    </div>
</body>
</html>
