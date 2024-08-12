<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Pedido Está Listo para Recolectar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
        .email-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #4CAF50;
        }
        .body-content {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        .body-content p {
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #aaa;
        }
        .map-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
        }

        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-content">
            <div class="header">
                <h1>¡Tu pedido está listo para ser recolectado!</h1>
            </div>
            <div class="body-content">
                <p>Hola {{ $user->name ?? 'Cliente' }},</p>
                <p>Nos complace informarte que tu pedido con ID <strong>{{ $carrito->id ?? '12345' }}</strong> ha sido impreso y está listo para ser recolectado en nuestras instalaciones.</p>
                <p><strong>Detalles del Pedido:</strong></p>
                <ul>
                    <li>ID del Pedido: <strong>{{ $carrito->id ?? '12345' }}</strong></li>
                    <li>Estado: <strong>{{ $carrito->status ?? 'Impreso y listo para recolección' }}</strong></li>
                    <li>Total: <strong>{{ $carrito->total ?? '$0.00' }}</strong></li>
                </ul>
                <p>Puedes pasar a recoger tu pedido en la siguiente dirección:</p>
                <p><strong>Aplicaciones Creativas</strong></p>
                <p>Dirección: C. 3 101, José Narciso Rovirosa, 86050 Villahermosa, Tab.</p>
                <p>Horario de atención: Lunes a Viernes, 9:00 AM - 6:00 PM</p>
                <p>Si tienes alguna pregunta o necesitas más información, no dudes en contactarnos.</p>
                <p>¡Gracias por confiar en nosotros!</p>
                @if($carrito->recoleccion)
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3794.7202551275022!2d-92.93670352522263!3d17.991747485114725!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85edd8264090f32b%3A0xead705cba3e01996!2sAplicaciones%20Creativas!5e0!3m2!1ses-419!2smx!4v1723489175824!5m2!1ses-419!2smx" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                @endif
            </div>
            <div class="footer">
                <p>Aplicaciones Creativas | Teléfono: (993) 352 5394 | Email: ventas@apscreativas.com</p>
                <p>&copy; 2024 Aplicaciones Creativas. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
