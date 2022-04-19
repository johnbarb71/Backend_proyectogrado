<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'id_products',
        'id_sucursal',
        'gondola',
        'bodega',
        'resultado',
        'cantidad'
    ];
}
