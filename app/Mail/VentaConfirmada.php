<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VentaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $carrito;

    public function __construct($user, $carrito)
    {
        $this->user = $user;
        $this->carrito = $carrito;
    }

    public function build()
    {
        return $this->subject('Confirmación de Venta')
                    ->view('emails.ventaConfirmada');
    }
}

