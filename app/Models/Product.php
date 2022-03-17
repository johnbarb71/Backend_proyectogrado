<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        /* 'codigo', */
        'codigo1',
        'codigo2',
        'linea',
        'nombre',
        'paqxcaja',
        'unixcaja',
        'paqxdisp',
        'fecha',
        'estado',
        'gondola',
        'bodega',
        'resultado',
        'cantidad'
    ];

}
