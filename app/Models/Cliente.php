<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'documento',
        'tipo_documento',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'provincia',
        'codigo_postal',
        'saldo_cuenta_corriente',
        'limite_credito',
        'notas',
        'activo',
    ];

    protected $casts = [
        'saldo_cuenta_corriente' => 'decimal:2',
        'limite_credito' => 'decimal:2',
        'activo' => 'boolean',
    ];


    // Cliente -> muchas ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Cliente -> movimientos cuenta corriente
    public function cuentaCorriente()
    {
        return $this->hasMany(CuentaCorrienteMovimiento::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS (muy útiles)
    |--------------------------------------------------------------------------
    */

    // saldo calculado desde movimientos
    public function saldoCalculado()
    {
        return $this->cuentaCorriente()->sum('monto');
    }

    // Relación con presupuestos
public function presupuestos()
{
    return $this->hasMany(Presupuesto::class, 'cliente_id');
}
}
