<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HairLife</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Estilos personalizados */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f4e8f7;
            font-family: 'Montserrat', sans-serif;
        }

        .login-container {
            display: flex;
            flex-wrap: wrap;
            /* Importante para que el 'order' funcione bien en diferentes tamaños */
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(106, 13, 173, 0.2);
            overflow: hidden;
            max-width: 950px;
            width: 90%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .login-image-section {
            flex: 1 1 40%;
            background-image: url('https://placehold.co/600x800/8e44ad/ffffff?text=HairLife&font=dancing-script');
            /* ¡REEMPLAZA! */
            background-size: cover;
            background-position: center;
            min-height: 450px;
            order: 2;
            /* Imagen a la derecha en desktop */
        }

        .login-form-section {
            flex: 1 1 60%;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            order: 1;
            /* Formulario a la izquierda en desktop */
        }

        .welcome-title {
            color: #6a0dad;
            font-family: 'Dancing Script', cursive;
            font-weight: 700;
            font-size: 4rem;
            text-align: center;
            margin-bottom: 0px;
            line-height: 1.3;
        }

        .welcome-subtitle {
            color: #8e44ad;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.15rem;
            text-align: center;
            margin-bottom: 35px;
            margin-top: 8px;
            font-weight: 400;
        }

        .login-form-section h2 {
            color: #5b2c6f;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.9rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        .form-label {
            color: #4a074a;
            font-weight: 500;
        }

        .form-control {
            border-radius: 6px;
            padding: 0.6rem 0.75rem;
        }

        .form-control:focus {
            border-color: #8e44ad;
            box-shadow: 0 0 0 0.25rem rgba(142, 68, 173, 0.25);
        }

        .btn-primary {
            background-color: #6a0dad;
            border-color: #6a0dad;
            padding: 14px 20px;
            font-size: 1.2rem;
            font-weight: 500;
            border-radius: 6px;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #4c0b56;
            border-color: #4c0b56;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                /* Apila los elementos verticalmente */
                margin-top: 10px;
                margin-bottom: 10px;
                max-width: 450px;
            }

            /* En móvil, el orden HTML (o el especificado aquí) determinará la secuencia vertical */
            .login-image-section {
                flex-basis: 100%;
                min-height: 200px;
                order: 1;
                /* Imagen arriba en móvil */
            }

            .login-form-section {
                flex-basis: 100%;
                padding: 30px 25px;
                order: 2;
                /* Formulario abajo en móvil */
            }

            .welcome-title {
                font-size: 3rem;
            }

            .welcome-subtitle {
                font-size: 1rem;
            }

            .login-form-section h2 {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 480px) {
            .welcome-title {
                font-size: 2.5rem;
            }

            .login-form-section {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-image-section">
        </div>

        <div class="login-form-section">
            <h1 class="welcome-title">Bienvenid@s a HairLife</h1>
            <p class="welcome-subtitle">Asesoramiento capilar</p>

            <h2>Iniciar sesión</h2>

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (session('error_login'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error_login') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
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