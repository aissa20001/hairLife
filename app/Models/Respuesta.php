<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;

    protected $table = 'respuestas';
    public $timestamps = false;

    protected $fillable = [
        'envio_id',
        'id_preguntas',
        'valor_pregunta',
    ];

    public function envio()
    {
        return $this->belongsTo(CuestionarioEnvio::class, 'envio_id', 'id');
    }

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'id_preguntas', 'id');
    }
}
