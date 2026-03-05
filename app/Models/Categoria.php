<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con productos

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    // relación con pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }


}
