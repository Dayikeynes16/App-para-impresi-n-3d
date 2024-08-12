<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Mail\VentaConfirmada;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Carrito;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;

class WebhookController extends Controller
{
    public function paymentSuccess(Request $request)
    {
        Log::alert('Payment webhook received');
        Log::alert($request);
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            if (isset($session->metadata->id_carrito)) {
                $carritoId = $session->metadata->id_carrito;
                $carrito = Carrito::find($carritoId);
                if ($carrito) {
                    $carrito->status = "Pago confirmado";
                    $carrito->save();
                    
                    
                } 
            } 
        } else {
            return response()->json(['message' => 'Received unknown event type ' . $event->type], 200);
        }

        return response()->json(['message' => 'Webhook handled successfully'], 200);
    }
}
