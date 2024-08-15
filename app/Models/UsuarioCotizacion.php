<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioCotizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'usuarios_cotizaciones';

    protected $fillable = [
        'nombre',
        'precio',
        'minutos',
        'cantidad',
        'path',
        'usuario_id',
        'total',
    ];

    protected $casts = [
        'precio' => 'double',
        'total' => 'double',
    ];

    public function usuario() 
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
}
