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
    </style>
</head>
<body>
    <div class="card ">
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
        <div class="centrado">
            <p>Información de Contacto</p>
    
            <p> <strong>
                Teléfono: (993) 352 5394 <br>
                Email: ventas@apscreativas.com
            </strong>
            </p>

        </div >


    </div>
</body>
</html>


