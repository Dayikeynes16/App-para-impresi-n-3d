<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Pedido Ha Sido Enviado</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="email-content">
            <div class="header">
                <h1>¡Tu pedido ha sido enviado!</h1>
            </div>
            <div class="body-content">
                <p>Hola {{ $user->name ?? 'Cliente' }},</p>
                <p>Nos complace informarte que tu pedido con ID <strong>{{ $carrito->id ?? '12345' }}</strong> ha sido enviado por paquetería y está en camino hacia tu dirección.</p>
                <p><strong>Detalles del Pedido:</strong></p>
                <ul>
                    <li>ID del Pedido: <strong>{{ $carrito->id ?? '12345' }}</strong></li>
                    <li>Estado: <strong>{{ $carrito->status ?? 'Enviado' }}</strong></li>
                    <li>Total: <strong>{{ $carrito->total ?? '$0.00' }}</strong></li>
                   
                </ul>
                <p>Si tienes alguna pregunta o necesitas más información, no dudes en contactarnos.</p>
                <p>¡Gracias por confiar en nosotros!</p>
            </div>
            <div class="footer">
                <p>Aplicaciones Creativas | Teléfono: (993) 352 5394 | Email: ventas@apscreativas.com</p>
                <p>&copy; 2024 Aplicaciones Creativas. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
