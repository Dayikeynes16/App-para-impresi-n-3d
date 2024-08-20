<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto_Carrito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'producto_carritos';

    protected $fillable=[
        'cantidad',
        'total',
        'carrito_id',
        'producto_id',
    ];

    public function carrito(){
        return $this->belongsTo(Carrito::class,'carrito_id');
    }

    public function productoCarritoArchivos(){
        return $this->hasMany(ProductoCarritoArchivo::class, 'producto_carrito_id', 'id');
    }

    public function producto(){
        return $this->belongsTo(Product::class,'producto_id');
    }
}
