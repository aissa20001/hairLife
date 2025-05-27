<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{
    use HasFactory;

    protected $table = 'recomendaciones';
    //Indica que esta tabla no tiene las columnas automáticas created_at y updated_at.
    public $timestamps = false;

    //Esto define qué campos pueden ser asignados en masa
    protected $fillable = [
        'envio_id',
        'id_producto',
        'justificacion_titulo',
        'justificacion_detalle',
    ];

    //Define una relación de tipo belongsTo (muchas recomendaciones pueden pertenecer a un solo envío de cuestionario).
    public function envio()
    {
        return $this->belongsTo(CuestionarioEnvio::class, 'envio_id', 'id');
    }
    //Otra relación de tipo belongsTo.
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'idproducto');
    }
}
