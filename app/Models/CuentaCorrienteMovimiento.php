<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaCorrienteMovimiento extends Model
{
    protected $table = 'cuenta_corriente_movimientos';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'descripcion',
        'monto'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
