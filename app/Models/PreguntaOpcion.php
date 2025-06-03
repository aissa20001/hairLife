<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Necesito esto para la relación 'pertenece a'.

class PreguntaOpcion extends Model
{
    use HasFactory; // Me ayuda a crear opciones de prueba fácilmente.

    // La tabla de la base de datos para este modelo.
    protected $table = 'pregunta_opciones';

    // No necesito las columnas 'created_at' y 'updated_at' en esta tabla.
    public $timestamps = false;

    // Los campos que puedo rellenar cuando creo o actualizo una opción.
    protected $fillable = [
        'pregunta_id',    // El ID de la pregunta a la que pertenece esta opción.
        'valor_opcion',   // El valor que representa esta opción (ej: 'si', 'no').
        'texto_opcion',   // El texto que el usuario ve (ej: 'Sí, estoy de acuerdo').
        'orden',          // El orden en que se muestra esta opción.
    ];

    /**
     * Una opción siempre pertenece a una pregunta.
     */
    public function pregunta(): BelongsTo // Esta función define esa conexión.
    {
        // Conecto esta opción a su Pregunta.
        // 'pregunta_id' es la columna en esta tabla que guarda el ID de la pregunta.
        // 'id' es la columna ID en la tabla de Preguntas.
        return $this->belongsTo(Pregunta::class, 'pregunta_id', 'id');
    }
}
