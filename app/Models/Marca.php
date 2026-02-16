<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';

    public $timestamps = false; // ⚠️ porque solo tenés created_at

    protected $fillable = [
        'nombre',
        'pais_origen',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->hasMany(\App\Models\Producto::class);
    }
}
