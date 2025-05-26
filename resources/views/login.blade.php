<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HairLife</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos personalizados */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #e9ecef;
            /* Un fondo gris muy claro para contraste */
        }

        .login-container {
            display: flex;
            /* Usaremos flexbox para la disposición lado a lado en pantallas grandes */
            flex-wrap: wrap;
            /* Permitir que los elementos se envuelvan en pantallas pequeñas */
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            /* Para que los bordes redondeados afecten a la imagen */
            max-width: 900px;
            /* Ancho máximo del contenedor general */
            width: 90%;
        }

        .login-image-section {
            flex: 1 1 50%;
            /* Ocupa el 50% del espacio, permite encogerse/crecer */
            background-image: url('https://placehold.co/600x800/6a0dad/white?text=HairLife+Imagen');
            /* Placeholder - ¡REEMPLAZA ESTA URL! */
            background-size: cover;
            background-position: center;
            min-height: 300px;
            /* Altura mínima para la imagen en móviles */
        }

        .login-form-section {
            flex: 1 1 50%;
            /* Ocupa el 50% del espacio */
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome-message {
            color: #4c0b56;
            /* Un morado oscuro para el texto de bienvenida */
            font-weight: bold;
            font-size: 1.8rem;
            /* Tamaño de fuente más grande */
            text-align: center;
            margin-bottom: 15px;
        }

        .login-form-section h2 {
            color: #6a0dad;
            /* Morado principal para el título "Iniciar sesión" */
            text-align: center;
            margin-bottom: 30px;
            /* Más espacio debajo del título */
        }

        .btn-primary {
            background-color: #6a0dad;
            /* Morado principal para el botón */
            border-color: #6a0dad;
            /* Borde del mismo color */
            padding: 10px 20px;
            font-size: 1.1rem;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #4c0b56;
            /* Morado más oscuro para hover/focus */
            border-color: #4c0b56;
        }

        /* Para pantallas más pequeñas, apilar verticalmente */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-image-section,
            .login-form-section {
                flex-basis: 100%;
                /* Cada sección ocupa todo el ancho */
            }

            .login-form-section {
                padding: 30px;
                /* Menos padding en móviles */
            }

            .welcome-message {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-image-section">
        </div>

        <div class="login-form-section">
            <p class="welcome-message">¡Bienvenidos/as a HairLife!</p>
            <h2>Iniciar sesión</h2>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('error_login'))
            <div class="alert alert-danger">{{ session('error_login') }}</div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nombreUsuario" class="form-label">Nombre de usuario</label>
                    <input type="text" class="form-control" id="nombreUsuario" name="Nombre" placeholder="Tu nombre de usuario" required>
                </div>
                <div class="mb-3">
                    <label for="claveUsuario" class="form-label">Clave</label>
                    <input type="password" class="form-control" id="claveUsuario" name="Clave" placeholder="Tu clave" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>