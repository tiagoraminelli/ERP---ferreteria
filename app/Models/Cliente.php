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

}
