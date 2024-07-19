<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;
    protected $fillable= ['usuario_id','status','direccion_id','total','recoleccion'];

    public function direccion(){
        return $this->HasOne(Direccion::class,'direccion_id');
    }

    public function productos(){
        return $this->hasMany(Producto_Carrito::class,'carrito_id');
    }

    public function orden(){
        return $this->hasMany(Orden::class,'carrito_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class,'usuario_id');
    }
}
