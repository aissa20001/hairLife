<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; // Para poder enviar notificaciones.
use Illuminate\Support\Facades\Hash; // Para encriptar contraseñas.

class Usuario extends Model // Este es el modelo para los usuarios de tu aplicación.
{
    use HasFactory, Notifiable; // 'HasFactory' para crear usuarios de prueba, 'Notifiable' para notificaciones.

    // El nombre de la tabla en la base de datos para este modelo.
    protected $table = 'usuarios';

    // La clave primaria de esta tabla no es 'id', es 'Codigo'.
    protected $primaryKey = 'Codigo';

    // No necesito las columnas 'created_at' y 'updated_at' en esta tabla.
    public $timestamps = false;

    // Los campos que puedo rellenar cuando creo o actualizo un usuario.
    protected $fillable = [
        'Nombre',
        'Clave',
        'Rol',
        'nick',
    ];

    // Campos que no quiero que se muestren cuando el modelo se convierte a array/JSON (por seguridad).
    protected $hidden = [
        'Clave', // La contraseña nunca debe ser visible directamente.
    ];

    /**
     * Este es un "mutador". Se activa automáticamente cuando intento guardar
     * o actualizar la 'Clave' de un usuario.
     */
    public function setClaveAttribute($value)
    {
        // Si se proporciona un valor para la clave...
        if ($value) {
            // ...lo encripto (hasheo) antes de guardarlo en la base de datos.
            // Esto es CRUCIAL para la seguridad de las contraseñas.
            $this->attributes['Clave'] = Hash::make($value);
        }
    }

    /**
     * Un usuario puede tener muchos envíos de cuestionarios.
     */
    public function cuestionarioEnvios()
    {
        // Un Usuario tiene muchos CuestionarioEnvio.
        // 'usuario_codigo' es la columna en la tabla 'cuestionario_envios' que guarda el ID de este usuario.
        // 'Codigo' es la clave primaria de este modelo (Usuario).
        return $this->hasMany(CuestionarioEnvio::class, 'usuario_codigo', 'Codigo');
    }
}
