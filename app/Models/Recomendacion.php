<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{
    use HasFactory; // Me ayuda a crear recomendaciones de prueba fácilmente.

    // El nombre de la tabla en la base de datos para este modelo.
    protected $table = 'recomendaciones';

    // No necesito las columnas 'created_at' y 'updated_at' en esta tabla.
    public $timestamps = false;

    // Los campos que puedo rellenar cuando creo o actualizo una recomendación.
    protected $fillable = [
        'envio_id',
        'id_producto',
        'justificacion_titulo',
        'justificacion_detalle',
    ];

    /**
     * Una recomendación siempre pertenece a un envío de cuestionario.
     */
    public function envio()
    {
        // Conecto esta recomendación a su CuestionarioEnvio.
        // 'envio_id' es la columna en esta tabla que guarda el ID del envío.
        // 'id' es la clave primaria en la tabla de CuestionarioEnvio.
        return $this->belongsTo(CuestionarioEnvio::class, 'envio_id', 'id');
    }

    /**
     * Una recomendación siempre se refiere a un producto específico.
     */
    public function producto()
    {
        // Conecto esta recomendación a su Producto.
        // 'id_producto' es la columna en esta tabla que guarda el ID del producto.
        // 'idproducto' es la clave primaria en la tabla de Producto.
        return $this->belongsTo(Producto::class, 'id_producto', 'idproducto');
    }
}
