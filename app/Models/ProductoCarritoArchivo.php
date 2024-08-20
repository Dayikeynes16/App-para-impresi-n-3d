<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoCarritoArchivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'path',
        'minutos',
        'precio',
        'total',
        'cantidad',
        'producto_carrito_id'
    ];

    public function productoCarrito(){
        return $this->belongsToMany(Producto_Carrito::class, 'producto_carrito_id');
    }
}
