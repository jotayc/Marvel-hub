@extends('layouts.app')

@section('titulo', 'Héroes Activos — Marvel Hub')

@section('contenido')
    <h1>Héroes Activos</h1>

    @forelse($heroes as $hero)
        <div class="hero-card">
            <h2>{{ $hero->name }}</h2>
            <p>{{ $hero->power }}</p>
            <a href="{{ route('heroes.show', $hero->id) }}">Ver detalle</a>
        </div>
    @empty
        <p>No hay héroes activos en este momento.</p>
    @endforelse
@endsection
