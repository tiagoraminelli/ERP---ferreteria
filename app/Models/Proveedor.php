<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'razon_social',
        'cuit',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'provincia',
        'codigo_postal',
        'contacto',
        'notas',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
