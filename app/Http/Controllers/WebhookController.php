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

}
