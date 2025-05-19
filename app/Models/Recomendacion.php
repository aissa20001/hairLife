<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recomendacion extends Model
{
    use HasFactory;

    protected $table = 'recomendaciones';
    public $timestamps = false;

    protected $fillable = [
        'envio_id',
        'id_producto',
        'justificacion_titulo',
        'justificacion_detalle',
    ];

    public function envio()
    {
        return $this->belongsTo(CuestionarioEnvio::class, 'envio_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'idproducto');
    }
}
