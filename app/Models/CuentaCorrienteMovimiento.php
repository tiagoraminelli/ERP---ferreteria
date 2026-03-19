<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaCorrienteMovimiento extends Model
{
    protected $table = 'cuenta_corriente_movimientos';

    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'tipo',
        'monto',
        'saldo_historico',
        'referencia_id',
        'descripcion',
        'usuario_id',
        'fecha',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_historico' => 'decimal:2',
        'fecha' => 'datetime',
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

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS VISUALES (solo para Blade)
    |--------------------------------------------------------------------------
    */

    public function esDebe()
    {
        return in_array($this->tipo, ['venta', 'ajuste_debito']);
    }

    public function esHaber()
    {
        return in_array($this->tipo, ['pago', 'ajuste_credito']);
    }
}
