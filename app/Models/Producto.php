<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';
    protected $primaryKey = 'idproducto';
    public $timestamps = false; // Asumiendo que no tienes created_at, updated_at en esta tabla

    protected $fillable = [
        'nombre',
        'marca',
        'categoria',
        'descripcion',
        'url',
        'foto',
    ];

    // Relación: Un producto puede estar en muchas recomendaciones
    public function recomendaciones()
    {
        return $this->hasMany(Recomendacion::class, 'id_producto', 'idproducto');
    }
}
