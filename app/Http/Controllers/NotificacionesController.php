<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    public function index (Request $request) {
        $pedidos = Carrito::where('status','pago confirmado')->get();
        $pedidoCount = $pedidos->count();
        if($pedidoCount > 0){
            $pedidoDetails = $pedidos->map(function($pedido) {
                
                return [
                    'id' => $pedido->id,
                    'total' => $pedido->total,
                    'fecha' => $pedido->created_at,
                ];
            });
    
            return response()->json(['count' => $pedidoCount, 'details' => $pedidoDetails]);
        }
        return response()->json(['count' => 0]);
    }
}
