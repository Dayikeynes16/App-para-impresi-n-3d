<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Mail\VentaConfirmada;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Carrito;

class WebhookController extends Controller
{
    public function handleStripeWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            // Buscar el usuario
            $user = User::find($session->client_reference_id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Buscar el carrito del usuario
            $carrito = Carrito::where('usuario_id', $user->id)->where('status', 'activo')->first();
            if (!$carrito) {
                return response()->json(['error' => 'Carrito not found'], 404);
            }

            // Actualizar el estado del carrito a "pagado"
            $carrito->status = 'pagada';
            $carrito->save();

            // Enviar correo de confirmación
            Mail::to($user->email)->send(new VentaConfirmada($user, $carrito));
        }

        return response()->json(['status' => 'success'], 200);
    }
}
