<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresupuestoDetalle extends Model
{
    protected $table = 'presupuesto_detalles';

    public $timestamps = false;

    protected $fillable = [
        'presupuesto_id',
        'producto_id',
        'cantidad',
        'precio',
        'descuento_porcentaje',
        'descuento_monto',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class, 'presupuesto_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
