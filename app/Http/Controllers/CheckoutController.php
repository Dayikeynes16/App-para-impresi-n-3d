<?php

// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Mail\VentaConfirmada;
use App\Models\Carrito;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Expr\Cast\Double;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function checkout()
    {
        return view('checkout');
    }

    public function createSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => 'Servicios de impresión apps creativas',
                    ],
                    'unit_amount' => (float) $request->input('total') * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/success'),  
            'cancel_url' => url('/CarritoFinal'),
        ]);

        $carrito = Carrito::where('usuario_id', $request->user()->id)->where('status', 'activo')->first();
        $carrito->total = (float) $request->input('total');
        $carrito->save();

        return response()->json(['id' => $session->id]);
    }

    
}
