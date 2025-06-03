<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuestionarioEnvio extends Model
{
    use HasFactory;

    // Defino la tabla a la que este modelo está asociado.
    // Por defecto, Laravel inferiría 'cuestionario_envios' del nombre del modelo,
    // pero siempre me gusta ser explícito para evitar confusiones.
    protected $table = 'cuestionario_envios';

    // Aquí configuro las marcas de tiempo. No tengo una columna 'updated_at'
    // en la base de datos para los envíos de cuestionarios, solo 'fecha_creacion'.
    // Así que le digo a Laravel que use 'fecha_creacion' para el campo de creación
    // y que no se preocupe por el de actualización.
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    // Estos son los campos que puedo asignar masivamente (mass assignable).
    // Siempre es buena práctica definir esto para la seguridad.
    protected $fillable = [
        'usuario_codigo',
        'cuestionario_id',
        'nick_utilizado',
        'fecha_creacion', // Aunque Laravel lo maneja con CREATED_AT, lo incluyo aquí.
    ];

    /**
     * Defino la relación con el modelo de Usuario.
     * Un envío de cuestionario pertenece a un usuario.
     */
    public function usuario()
    {
        // Indico que un CuestionarioEnvio pertenece a un Usuario.
        // 'usuario_codigo' es la clave foránea en la tabla 'cuestionario_envios'.
        // 'Codigo' es la clave primaria en la tabla 'usuarios' a la que hago referencia.
        return $this->belongsTo(Usuario::class, 'usuario_codigo', 'Codigo');
    }

    /**
     * Defino la relación con el modelo de Cuestionario.
     * Un envío de cuestionario pertenece a un cuestionario específico.
     */
    public function cuestionario()
    {
        // Un CuestionarioEnvio pertenece a un Cuestionario.
        // 'cuestionario_id' es la clave foránea aquí.
        // 'id' es la clave primaria en la tabla 'cuestionarios'.
        return $this->belongsTo(Cuestionario::class, 'cuestionario_id', 'id');
    }

    /**
     * Defino la relación con el modelo de Respuesta.
     * Un envío de cuestionario puede tener muchas respuestas.
     */
    public function respuestas()
    {
        // Un CuestionarioEnvio tiene muchas Respuestas.
        // 'envio_id' es la clave foránea en la tabla 'respuestas' que apunta
        // a la clave primaria 'id' de este modelo (CuestionarioEnvio).
        return $this->hasMany(Respuesta::class, 'envio_id', 'id');
    }

    /**
     * Defino la relación con el modelo de Recomendacion.
     * Un envío de cuestionario puede generar muchas recomendaciones.
     */
    public function recomendaciones()
    {
        // Un CuestionarioEnvio tiene muchas Recomendaciones.
        // 'envio_id' es la clave foránea en la tabla 'recomendaciones'
        // que referencia a la clave primaria 'id' de CuestionarioEnvio.
        return $this->hasMany(Recomendacion::class, 'envio_id', 'id');
    }
}
