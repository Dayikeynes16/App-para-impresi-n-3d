<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Expr\New_;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable=[
        'name',
        'description',
        'price', 
        'is_custom'
    ];

    protected $casts = [
        'price' => 'float',
    ];
    

    public function Imagenes(){
        return $this->hasMany(Images::class,'producto_id');
    }

    public function productoCarritos(){
        return $this->hasMany(Producto_Carrito::class,'producto_id');
    }

    public function files (){
        return $this->morphMany(Files::class, 'morphable');
    }

  
}
