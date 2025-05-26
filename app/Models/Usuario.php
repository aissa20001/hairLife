<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model // O solo `Model` si no es para autenticación de Laravel directamente
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios'; // Especifica el nombre de la tabla si no sigue la convención de Laravel (plural minúscula del nombre del modelo)
    protected $primaryKey = 'Codigo'; // Especifica la clave primaria si no es 'id'
    public $timestamps = false; // Si no tienes las columnas created_at y updated_at

    protected $fillable = [
        'Nombre',
        'Clave', // ¡Recuerda hashear las contraseñas!
        'Rol',
        'nick',
    ];

    protected $hidden = [
        'Clave', // Para no exponer la clave en serializaciones
    ];

    // Relación: Un usuario puede tener muchos envíos de cuestionarios
    public function cuestionarioEnvios()
    {
        return $this->hasMany(CuestionarioEnvio::class, 'usuario_codigo', 'Codigo');
    }
}
