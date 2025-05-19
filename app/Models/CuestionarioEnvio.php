<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuestionarioEnvio extends Model
{
    use HasFactory;

    protected $table = 'cuestionario_envios';
    // Laravel asume 'id' como PK.
    // 'fecha_creacion' se maneja por defecto por `DEFAULT CURRENT_TIMESTAMP` en la BD
    // o Laravel puede manejarlo si `const CREATED_AT = 'fecha_creacion';` y `const UPDATED_AT = null;`
    // o `public $timestamps = true;` si tienes created_at y updated_at
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null; // No hay columna updated_at

    protected $fillable = [
        'usuario_codigo',
        'cuestionario_id',
        'nick_utilizado',
        'fecha_creacion', // Laravel lo manejará si CREATED_AT está definido
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_codigo', 'Codigo');
    }

    public function cuestionario()
    {
        return $this->belongsTo(Cuestionario::class, 'cuestionario_id', 'id');
    }

    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'envio_id', 'id');
    }

    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'envio_id', 'id');
    }
}
