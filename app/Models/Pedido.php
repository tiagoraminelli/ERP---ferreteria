<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'cantidad',
        'categoria_id',
        'proveedor',
        'estado',
        'visible',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'visible' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
