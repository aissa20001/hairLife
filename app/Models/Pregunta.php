<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- IMPORTAR HasMany

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'preguntas';
    public $timestamps = false;

    protected $fillable = [
        'enunciado',
        'categoria',
        'tipo_input', // <-- AÑADIR tipo_input
    ];

    // Relación: Una pregunta puede estar en muchos cuestionarios a través de la tabla pivote
    public function cuestionarios()
    {
        return $this->belongsToMany(Cuestionario::class, 'cuestionario_preguntas', 'id_preguntas', 'id_cuestionario')
            ->withPivot('numero');
    }

    // Relación: Una pregunta puede tener muchas respuestas (a través de diferentes envíos)
    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'id_preguntas', 'id');
    }

    // AÑADIR: Relación: Una pregunta tiene muchas opciones
    public function opciones(): HasMany
    {
        // Asume que tienes un modelo llamado PreguntaOpcion
        // Asegúrate de crear el archivo app/Models/PreguntaOpcion.php
        return $this->hasMany(PreguntaOpcion::class, 'pregunta_id', 'id')->orderBy('orden', 'asc');
    }
}
