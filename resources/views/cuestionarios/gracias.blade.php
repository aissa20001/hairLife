<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu participación! - Mi Pelo</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #e9ecef;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 95vh;
            text-align: center;
        }

        .container {
            padding: 35px 45px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 550px;
        }

        .icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        h1 {
            color: #343a40;
            font-size: 2em;
            margin-bottom: 15px;
        }

        p {
            color: #495057;
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .btn-primary {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 28px;
            background-color: #7952b3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 1em;
            transition: background-color 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #5a3d8a;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">&#10004;</div>
        <h1>¡Cuestionario Enviado!</h1>

        @if (session('success'))
        <p>{{ session('success') }}</p>
        @else
        <p>Gracias por completar nuestro cuestionario. Tus respuestas nos ayudarán a ofrecerte las mejores recomendaciones.</p>
        @endif

        @isset($nick)
        <p>Puedes volver a tu panel para explorar otras opciones.</p>
        <a href="{{ route('user.dashboard', ['nick' => $nick]) }}" class="btn-primary">Volver al Panel de {{ htmlspecialchars($nick) }}</a>
        @else
        <a href="{{ route('home') }}" class="btn-primary">Volver a la página principal</a>
        @endisset
    </div>
</body>

</html>