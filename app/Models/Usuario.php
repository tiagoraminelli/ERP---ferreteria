<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $primaryKey = 'id';

    public $timestamps = true; // Porque tenés created_at y updated_at

    protected $fillable = [
        'username',
        'password',
        'nombre',
        'email',
        'rol',
        'role_id',
        'activo',
        'is_active',
        'last_login',
        'failed_attempts',
        'last_failed_login'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'last_failed_login' => 'datetime',
        'failed_attempts' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
