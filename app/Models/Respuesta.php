<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory; // Esto me ayuda a crear respuestas de prueba fácilmente.

    // El nombre de la tabla en la base de datos para este modelo.
    protected $table = 'respuestas';

    // No necesito las columnas 'created_at' y 'updated_at' en esta tabla.
    public $timestamps = false;

    // Los campos que puedo rellenar cuando creo o actualizo una respuesta.
    protected $fillable = [
        'envio_id',
        'id_preguntas',
        'valor_pregunta',
    ];

    /**
     * Una respuesta siempre pertenece a un envío de cuestionario.
     */
    public function envio()
    {
        // Conecto esta respuesta a su CuestionarioEnvio.
        // 'envio_id' es la columna en esta tabla que guarda el ID del envío.
        // 'id' es la clave primaria en la tabla de CuestionarioEnvio.
        return $this->belongsTo(CuestionarioEnvio::class, 'envio_id', 'id');
    }

    /**
     * Una respuesta siempre se refiere a una pregunta específica.
     */
    public function pregunta()
    {
        // Conecto esta respuesta a su Pregunta.
        // 'id_preguntas' es la columna en esta tabla que guarda el ID de la pregunta.
        // 'id' es la clave primaria en la tabla de Pregunta.
        return $this->belongsTo(Pregunta::class, 'id_preguntas', 'id');
    }
}
