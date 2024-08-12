<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Carrito;

class PedidoRecoleccion extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $carrito;

    public function __construct(User $user, Carrito $carrito)
    {
        $this->user = $user;
        $this->carrito = $carrito;
    }

    public function build()
    {
        return $this->view('email.pedido-recoleccion')
            ->subject('Confirmación de Compra');
    }
}
