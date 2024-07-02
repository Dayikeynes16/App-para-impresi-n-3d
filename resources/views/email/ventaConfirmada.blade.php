<!DOCTYPE html>
<html>
<head>
    <title>Compra Exitosa</title>
</head>
<body>
    <h1>Hola {{ $user->name }},</h1>
    <p>¡Gracias por tu compra! Tu pedido ha sido confirmado y está siendo procesado.</p>
    <p>Detalles de la compra:</p>
    <ul>
        <li><strong>ID del Pedido:</strong> {{ $carrito->id }}</li>
        <li><strong>Estado:</strong> {{ $carrito->status }}</li>
    </ul>
    <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
    <p>¡Gracias por comprar con nosotros!</p>
</body>
</html>
