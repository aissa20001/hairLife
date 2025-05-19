<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuestionario extends Model
{
    use HasFactory;

    protected $table = 'cuestionario';
    // Laravel asume 'id' como primaryKey, y que tienes created_at/updated_at
    // Si 'fecha_creacion' es tu único timestamp y no quieres los de Laravel:
    const CREATED_AT = 'fecha_creacion'; // Le dice a Laravel que use esta columna
    const UPDATED_AT = null; // Le dice a Laravel que no maneje updated_at

    // O si no quieres que Laravel maneje ningún timestamp automáticamente:
    // public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_creacion', // Si Laravel no lo maneja, debes llenarlo manualmente
        'n_preguntas',
        'estado',
    ];

    // Relación: Un cuestionario tiene muchas preguntas a través de la tabla pivote
    public function preguntas()
    {
        return $this->belongsToMany(Pregunta::class, 'cuestionario_preguntas', 'id_cuestionario', 'id_preguntas')
            ->withPivot('numero') // Para poder acceder al orden de la pregunta en el cuestionario
            ->orderBy('pivot_numero', 'asc'); // Ordenar por el campo 'numero' de la tabla pivote
    }

    // Relación: Un cuestionario puede tener muchos envíos
    public function envios()
    {
        return $this->hasMany(CuestionarioEnvio::class, 'cuestionario_id', 'id');
    }
}
