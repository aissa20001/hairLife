<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pregunta extends Model
{
    use HasFactory;

    // Defino la tabla de la base de datos a la que este modelo se mapea.
    // Aunque Laravel lo inferiría como 'preguntas' por el nombre del modelo,
    // me gusta ser explícito para que quede claro.
    protected $table = 'preguntas';

    // Para este modelo, no necesito las marcas de tiempo 'created_at' y 'updated_at'
    // en la tabla de la base de datos, así que las desactivo.
    public $timestamps = false;

    // Estos son los atributos que puedo asignar masivamente (mass assignable).
    // Es una buena práctica de seguridad para evitar asignaciones inesperadas.
    protected $fillable = [
        'enunciado',
        'categoria',
        'tipo_input', // ¡Importante! Añadí este campo para saber qué tipo de input es (texto, radio, etc.).
    ];

    /**
     * Defino la relación de muchos a muchos con el modelo Cuestionario.
     * Una pregunta puede estar en muchos cuestionarios, y un cuestionario puede tener muchas preguntas.
     * Esto se gestiona a través de una tabla pivote.
     */
    public function cuestionarios()
    {
        // Esto es una relación de muchos a muchos.
        // 'Cuestionario::class' es el modelo con el que me relaciono.
        // 'cuestionario_preguntas' es el nombre de mi tabla pivote (intermedia).
        // 'id_preguntas' es la clave foránea de este modelo (Pregunta) en la tabla pivote.
        // 'id_cuestionario' es la clave foránea del modelo Cuestionario en la tabla pivote.
        // Y con 'withPivot('numero')' le digo a Laravel que también quiero acceder
        // a la columna 'numero' de la tabla pivote, que indica el orden de la pregunta en el cuestionario.
        return $this->belongsToMany(Cuestionario::class, 'cuestionario_preguntas', 'id_preguntas', 'id_cuestionario')
            ->withPivot('numero');
    }

    /**
     * Defino la relación de uno a muchos con el modelo Respuesta.
     * Una pregunta puede tener muchas respuestas (a lo largo de diferentes envíos de cuestionarios).
     */
    public function respuestas()
    {
        // Una Pregunta tiene muchas Respuestas.
        // 'id_preguntas' es la clave foránea en la tabla 'respuestas' que apunta
        // a la clave primaria 'id' de este modelo (Pregunta).
        return $this->hasMany(Respuesta::class, 'id_preguntas', 'id');
    }

    /**
     * ¡Nueva relación! Una pregunta tiene muchas opciones (para preguntas de selección múltiple, por ejemplo).
     * Esto me permite asociar las opciones directamente a la pregunta.
     */
    public function opciones(): HasMany // Especifico el tipo de retorno para mayor claridad.
    {
        // Asumo que ya tengo un modelo llamado PreguntaOpcion.
        // Es crucial que este modelo exista y que la tabla 'pregunta_opciones'
        // tenga una columna 'pregunta_id' que apunte a la 'id' de esta pregunta.
        // Además, las ordeno por la columna 'orden' para que aparezcan en el orden correcto.
        return $this->hasMany(PreguntaOpcion::class, 'pregunta_id', 'id')->orderBy('orden', 'asc');
    }
}
