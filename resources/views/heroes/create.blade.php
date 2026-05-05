{{-- resources/views/heroes/create.blade.php --}}
@extends('layouts.app')

@section('titulo', 'Nuevo Héroe — Marvel Hub')

@section('contenido')
    <a href="{{ route('heroes.index') }}" class="back-link">&larr; Volver al listado</a>

    <div class="form-card">
        <h1>Nuevo Héroe</h1>

        <form action="{{ route('heroes.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}">
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="real_name">Nombre real</label>
                <input type="text" id="real_name" name="real_name" value="{{ old('real_name') }}">
                @error('real_name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="power">Poder</label>
                <input type="text" id="power" name="power" value="{{ old('power') }}">
                @error('power')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="power_level">Nivel de poder</label>
                <input type="number" id="power_level" name="power_level" min="1" max="10000" value="{{ old('power_level') }}">
                @error('power_level')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="team">Equipo</label>
                <input type="text" id="team" name="team" value="{{ old('team') }}">
                @error('team')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="bio">Biografía</label>
                <textarea id="bio" name="bio">{{ old('bio') }}</textarea>
                @error('bio')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-checkbox">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                <label for="is_active">Activo</label>
            </div>

            <button type="submit" class="btn-submit">Guardar héroe</button>
        </form>
    </div>
@endsection
