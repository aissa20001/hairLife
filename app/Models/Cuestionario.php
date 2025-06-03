<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuestionario extends Model
{
    use HasFactory; // Para las factorías, útil para tests y seeders.

    // Le digo a Laravel cómo se llama mi tabla, porque no sigue la convención de pluralizar 'Cuestionario'.
    protected $table = 'cuestionario';

    // Configuración de timestamps:
    // Laravel espera 'created_at' y 'updated_at'. Aquí le digo que mi 'created_at' es 'fecha_creacion'.
    const CREATED_AT = 'fecha_creacion';
    // Y que no me gestione un 'updated_at', no lo necesito para este modelo.
    const UPDATED_AT = null;

    // Campos que permito que se asignen masivamente (ej. con Cuestionario::create([...])).
    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_creacion', // Si Laravel no lo maneja (como updated_at), y quiero guardarlo, me encargo yo.
        // Pero como CREATED_AT está definido a 'fecha_creacion', Laravel lo llenará al crear.
        'n_preguntas',
        'estado',
    ];

    // --- RELACIONES ---
    // Aquí defino cómo este Cuestionario se conecta con otros Modelos (otras tablas).

    /**
     * Función para las PREGUNTAS de ESTE Cuestionario.
     * Un Cuestionario puede tener MUCHAS Preguntas, y una Pregunta puede estar en MUCHOS Cuestionarios.
     * Esto es una relación MUCHOS A MUCHOS (belongsToMany).
     * Necesita una tabla intermedia (pivote) para funcionar.
     */
    public function preguntas()
    {
        // Con `belongsToMany`, estoy diciendo:
        // "Este Cuestionario SE RELACIONA CON MUCHAS Preguntas".
        // Para que esto funcione, Laravel usa una tabla intermedia.
        return $this->belongsToMany(
            Pregunta::class,            // 1. ¿Con qué otro Modelo se relaciona? Con 'Pregunta'.
            'cuestionario_preguntas',   // 2. ¿Cómo se llama la tabla intermedia (pivote)? 'cuestionario_preguntas'.
            'id_cuestionario',          // 3. En la tabla pivote, ¿cuál es la columna que guarda el ID de ESTE Cuestionario? 'id_cuestionario'.
            'id_preguntas'              // 4. En la tabla pivote, ¿cuál es la columna que guarda el ID de la Pregunta? 'id_preguntas'.
        )
            // Además, en esa tabla pivote (`cuestionario_preguntas`), tengo una columna extra llamada 'numero'.
            // Con `withPivot('numero')` le digo a Laravel: "Cuando me traigas las preguntas,
            // quiero poder acceder también al valor de la columna 'numero' de la tabla pivote".
            // Esto es súper útil para saber el orden de la pregunta DENTRO de este cuestionario específico.
            ->withPivot('numero')
            // Y ya que estamos, que me las traiga ordenadas por ese número del pivote.
            // Así, cuando haga `$unCuestionario->preguntas`, ya vienen en orden.
            ->orderBy('pivot_numero', 'asc');
    }

    /**
     * Función para los ENVÍOS de ESTE Cuestionario.
     * Un Cuestionario puede tener MUCHOS Envíos (CuestionarioEnvio).
     * Pero un Envío pertenece a UN SOLO Cuestionario.
     * Esto es una relación UNO A MUCHOS (hasMany) desde el punto de vista del Cuestionario (el "Uno").
     */
    public function envios()
    {
        // Con `hasMany`, estoy diciendo:
        // "Este Cuestionario PUEDE TENER MUCHOS Envíos (CuestionarioEnvio)".
        // Laravel buscará en la tabla de 'CuestionarioEnvio' todos los que tengan el ID de este Cuestionario.
        return $this->hasMany(
            CuestionarioEnvio::class,   // 1. ¿Qué Modelo es el que "tiene muchos"? Es 'CuestionarioEnvio'.
            'cuestionario_id',          // 2. En la tabla de 'CuestionarioEnvio', ¿cómo se llama la columna
            //    que guarda el ID de ESTE Cuestionario (la foreign key)? 'cuestionario_id'.
            'id'                        // 3. ¿Cómo se llama la columna ID en ESTA tabla ('cuestionario')? 'id'.
            //    (Esta es la local key, Laravel la suele adivinar si es 'id').
        );
        // Así, si tengo `$unCuestionario`, puedo hacer `$unCuestionario->envios` y me dará una colección
        // de todos los objetos CuestionarioEnvio que estén asociados a él.
    }
}
