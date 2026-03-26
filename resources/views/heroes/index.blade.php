<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Héroes Marvel - Marvel Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            color: #d32f2f;
            text-align: center;
        }
        .heroes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .hero-card {
            border: 2px solid #333;
            padding: 20px;
            border-radius: 8px;
            background: #f5f5f5;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: transform 0.2s;
        }
        .hero-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .hero-name {
            font-size: 1.4em;
            font-weight: bold;
            color: #d32f2f;
            margin-bottom: 10px;
        }
        .hero-real-name {
            color: #666;
            margin-bottom: 10px;
        }
        .hero-power {
            font-style: italic;
            color: #1976d2;
        }
    </style>
</head>
<body>
    <h1>Héroes de Marvel</h1>

    <div class="heroes-grid">
        @foreach($heroes as $hero)
            <a href="/heroes/{{ $hero['id'] }}" class="hero-card">
                <div class="hero-name">{{ $hero['name'] }}</div>
                <div class="hero-real-name">{{ $hero['real_name'] }}</div>
                <div class="hero-power">{{ $hero['power'] }}</div>
            </a>
        @endforeach
    </div>
</body>
</html>
