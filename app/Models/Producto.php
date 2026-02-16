<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_barra',
        'modelo',
        'categoria_id',
        'marca_id',
        'unidad_medida_id',
        'proveedor_id',
        'precio',
        'precio_costo',
        'margen_ganancia',
        'stock',
        'unidad_medida',
        'stock_minimo',
        'ubicacion_deposito',
        'imagen',
        'activo'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }


}
