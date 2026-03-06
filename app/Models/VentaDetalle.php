<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $table = 'venta_detalles';

    // desactivamos timestamps porque no los necesitamos para esta tabla
    public $timestamps = false;

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio',
        'precio_costo',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal',
        'subtotal_sin_descuento',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio' => 'decimal:2',
        'precio_costo' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'subtotal_sin_descuento' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Un detalle pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Un detalle pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
