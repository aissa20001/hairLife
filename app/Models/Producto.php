<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory; // Ayuda a crear productos de prueba fácilmente.

    // El nombre de la tabla en la base de datos para este modelo.
    protected $table = 'producto';

    // La clave primaria de esta tabla no es 'id', es 'idproducto'.
    protected $primaryKey = 'idproducto';

    // No necesito las columnas 'created_at' y 'updated_at' en esta tabla.
    public $timestamps = false;

    // Los campos que puedo rellenar cuando creo o actualizo un producto.
    protected $fillable = [
        'nombre',
        'marca',
        'categoria',
        'descripcion',
        'url',
        'foto',
    ];

    /**
     * Un producto puede aparecer en muchas recomendaciones.
     */
    public function recomendaciones()
    {
        // Un Producto tiene muchas Recomendaciones.
        // 'id_producto' es la columna en la tabla 'recomendaciones' que guarda el ID de este producto.
        // 'idproducto' es la clave primaria de este modelo (Producto).
        return $this->hasMany(Recomendacion::class, 'id_producto', 'idproducto');
    }
}
