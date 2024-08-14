<!DOCTYPE html>
<html>
<head>
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            max-width: 500px;
            width: 100%;
            margin: auto;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .card h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #343a40;
        }
        .card p {
            font-size: 16px;
            margin-bottom: 20px;
            color: #6c757d;
        }
        .card a {
            font-size: 16px;
            color: #007bff;
            text-decoration: none;
        }
        .card a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Recuperar Contraseña</h1>
        <p>Haga clic en el siguiente enlace para restablecer su contraseña:</p>
        <a type="button" href="{{ url('reiniciar-contrasenia/' . $token . '/' . $email) }}">Restablecer Contraseña</a>
    </div>
</body>
</html>
