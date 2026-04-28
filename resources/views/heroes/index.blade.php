{{-- Heredamos el layout principal de la aplicación
     que se encuentra en resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- Definimos el título de la página con la sintaxis corta de section --}}
@section('titulo', 'Héroes — Marvel Hub')

{{-- Contenido principal de la página --}}
@section('contenido')
    <h1>Héroes</h1>

    @foreach($heroes as $hero)
        <div class="hero-card">
            <h2>{{ $hero->name }}</h2>
            <p>{{ $hero->power }}</p>
            <p>Nivel: {{ $hero->power_level }}</p>
            <a href="{{ route('heroes.show', $hero->id) }}">Ver detalle</a>
        </div>
    @endforeach
@endsection {{-- Cierra la sección de contenido --}}
