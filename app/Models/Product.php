<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\New_;

class Product extends Model
{
    use HasFactory;
    protected $fillable=['name', 'description',  'price', 'codigo_postal', 'estado'];

    

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
