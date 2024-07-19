<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;
    protected $fillable = ['id','status', 'usuario_id', 'total','carrito_id'];

    protected $table = 'ordenes';
    
    public function files (){
        return $this->morphMany(Files::class, 'morphable');
    }
    public function carrito(){
        return $this->belongsTo(Carrito::class,'carrito_id');
    }

}
