<?php
// Archivo: app/Models/PreguntaOpcion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreguntaOpcion extends Model
{
    use HasFactory;

    protected $table = 'pregunta_opciones';
    public $timestamps = false;

    protected $fillable = [
        'pregunta_id',
        'valor_opcion',
        'texto_opcion',
        'orden',
    ];

    // Relación: Una opción pertenece a una pregunta
    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id', 'id');
    }
}
