<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    // Campos que permiten asignación masiva, es decir,
    // que se pueden llenar a través de un array
    //IMPORTANTE: NO incluyas id, created_at, updated_at → Se manejan automáticamente
    protected $fillable = [
        'name',
        'real_name',
        'power',
        'power_level',
        'team',
        'bio',
        'is_active'
    ];

    // Especifica los tipos de datos para cada campo,
    //lo que ayuda a Laravel a manejar correctamente
    //las conversiones de datos y a evitar errores al trabajar con los atributos del modelo

    protected $casts = [
        'power_level' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
