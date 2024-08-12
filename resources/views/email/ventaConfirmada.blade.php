<!DOCTYPE html>
<html>
<head>
    <title>Compra Exitosa</title>
    <style>
        html, body {
            background-color: #edf2f7;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            color: #718096;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .centrado {
            text-align: center;
        }

        .card {
            margin: 10%;
            padding: 10px 30px 30px 10px;
            background-color: #ffffff;
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
    <div class="card">
        <div class="centrado">
            <h1>Hola , {{ $user->name ?? 'miguel' }}</h1>
            <p>¡Gracias por tu compra! Tu pedido ha sido confirmado y está siendo procesado.</p>
            <p>Detalles de la compra:</p>
            <p>
                <p><strong>ID del Pedido: {{ $carrito->id ?? '23' }}</strong> </p>
                <p><strong>Estado: {{ $carrito->status ?? 'pagado' }}</strong> </p>
            </p>
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            <p>¡Gracias por comprar con nosotros!</p>
        </div>

        @if($carrito->recoleccion)
            <div class="map-container">
                
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3794.7202551275022!2d-92.93670352522263!3d17.991747485114725!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85edd8264090f32b%3A0xead705cba3e01996!2sAplicaciones%20Creativas!5e0!3m2!1ses-419!2smx!4v1723489175824!5m2!1ses-419!2smx" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        @endif

        <div class="centrado">
            <p>Información de Contacto</p>
            <p><strong>
                Teléfono: (993) 352 5394 <br>
                Email: ventas@apscreativas.com
            </strong></p>
        </div>
    </div>
</body>
</html>
