@extends('layouts.app')

@section('titulo', $hero->name . ' — Marvel Hub')

@section('contenido')
    <a href="{{ route('heroes.index') }}" class="back-link">&larr; Volver al listado</a>

    <div class="hero-detail">
        <h1>{{ $hero->name }}</h1>

        <div class="info-row">
            <span class="label">Nombre real</span>
            <span class="value">{{ $hero->real_name }}</span>
        </div>

        <div class="info-row">
            <span class="label">Poder</span>
            <span class="value">{{ $hero->power }}</span>
        </div>

        <div class="info-row">
            <span class="label">Nivel de poder</span>
            <span class="value">{{ $hero->power_level }}</span>
        </div>

        <div class="info-row">
            <span class="label">Equipo</span>
            <span class="value">{{ $hero->team }}</span>
        </div>

        <div class="info-row">
            <span class="label">Biografía</span>
            <span class="value">{{ $hero->bio }}</span>
        </div>

        <div class="info-row">
            <span class="label">Estado</span>
            <span class="value">
                @if($hero->is_active)
                    Activo
                @else
                    Inactivo
                @endif
            </span>
        </div>
    </div>
@endsection
