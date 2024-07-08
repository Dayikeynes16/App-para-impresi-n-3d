<?php

// app/Http/Controllers/CheckoutController.php

namespace App\Http\Controllers;

use App\Mail\VentaConfirmada;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => 'Servicios de apps creativas',
                    ],
                    'unit_amount' => $request->total * 4,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/success'),  
            'cancel_url' => url('/CarritoFinal'),
        ]);

        return response()->json(['id' => $session->id]);
    }

    
}
