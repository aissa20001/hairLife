<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu participación! - HairLife</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --purple-primary: RebeccaPurple;
            --purple-secondary: MediumPurple;
            --purple-dark: Indigo;
            --purple-medium: MediumSlateBlue;
            --purple-light: Thistle;
            --purple-very-light: Lavender;
            --purple-background: GhostWhite;
            --text-on-purple: white;
            --card-bg: white;
            --border-color: var(--purple-very-light);
            --card-shadow-elegant: rgba(102, 51, 153, 0.18);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: darkslategrey;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            text-align: center;
            box-sizing: border-box;
            position: relative;
            z-index: 0;
            margin: 0;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: url('/storage/imagenes/fondo.jpg');
            background-repeat: repeat;
            background-size: 250px;
            opacity: 0.2;
            z-index: -1;
            pointer-events: none;
        }

        .main-gracias-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .gracias-container {
            background-color: var(--card-bg);
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 35px var(--card-shadow-elegant);
            max-width: 650px;
            width: 100%;
            padding: 50px 40px;
        }

        .gracias-icon {
            font-size: 5.5rem;
            color: var(--purple-primary);
            margin-bottom: 25px;
            display: block;
        }

        .gracias-container h1 {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .gracias-container p {
            font-size: 1.15rem;
            line-height: 1.7;
            margin-bottom: 30px;
            color: #5a6268;
        }

        .gracias-container p.session-success {
            color: var(--purple-medium);
            font-weight: 500;
        }

        .loader-container {
            display: none;
            padding: 20px 0;
            text-align: center;
        }

        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid var(--purple-primary);
            border-radius: 50%;
            width: 70px;
            height: 70px;
            animation: spin 1.5s linear infinite;
            margin: 25px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            font-size: 1.3em;
            color: var(--purple-secondary);
            font-weight: 500;
        }

        .btn-purple-home {
            background-color: var(--purple-primary);
            color: var(--text-on-purple);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out;
        }

        .btn-purple-home:hover,
        .btn-purple-home:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
        }

        .btn-outline-purple {
            /* Estilo para el nuevo botón */
            color: var(--purple-secondary);
            border-color: var(--purple-secondary);
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
        }

        .btn-outline-purple:hover {
            color: var(--text-on-purple);
            background-color: var(--purple-secondary);
            border-color: var(--purple-secondary);
        }

        .buttons-container-gracias {
            /* Contenedor para los botones */
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            /* Para que se ajusten en pantallas pequeñas */
        }

        .site-footer {
            background-color: var(--purple-dark);
            color: rgba(255, 255, 255, 0.8);
            padding: 5px 0;
            text-align: center;
            font-size: 0.9em;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <div class="main-gracias-content">
        <div class="gracias-container">
            <i class="bi bi-check-circle-fill gracias-icon"></i>
            <h1>¡Cuestionario Enviado!</h1>

            @if (session('success'))
            <p class="session-success">{{ session('success') }}</p>
            @else
            <p>Gracias por completar nuestro cuestionario y dedicar tu tiempo.</p>
            @endif

            @if (isset($recomendacionId) && $recomendacionId !== null)
            {{-- Caso: Producto ENCONTRADO -> Mostrar loader para redirección --}}
            <div class="loader-container" id="loaderContainer">
                <div class="loader"></div>
                <p class="loading-text">Estamos preparando tu recomendación personalizada...</p>
            </div>
            @else
            {{-- Caso: Producto NO ENCONTRADO -> Mostrar los dos botones de acción --}}
            {{-- El párrafo "Revisaremos tus respuestas con atención." ha sido eliminado. --}}
            <p>Apreciamos sinceramente tu tiempo y esfuerzo al completar el cuestionario. Lamentablemente, no hemos podido encontrar una recomendación de producto específica basada en tus respuestas en este momento.</p>
            <div class="buttons-container-gracias mt-4">
                @if (isset($id_cuestionario_actual) && $id_cuestionario_actual && isset($nick))
                <a href="{{ route('cuestionarios.mostrarParaNick', ['nick' => $nick, 'id_cuestionario' => $id_cuestionario_actual]) }}" class="btn btn-lg btn-outline-purple">
                    <i class="bi bi-arrow-left-circle"></i> Volver al Cuestionario
                </a>
                @endif

                @if (session('usuario_nombre'))
                <a href="{{ route('user.dashboard', ['nick' => session('usuario_nombre')]) }}" class="btn btn-lg btn-purple-home">
                    <i class="bi bi-person-circle"></i> Volver a Mi Panel
                </a>
                @else
                <a href="{{ route('crear.nick') }}" class="btn btn-lg btn-purple-home">
                    <i class="bi bi-house-door"></i> Volver al Inicio
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>

    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} HairLife. Todos los derechos reservados.</p>
    </footer>

    @if (isset($recomendacionId) && $recomendacionId !== null)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loaderContainer = document.getElementById('loaderContainer');
            if (loaderContainer) {
                loaderContainer.style.display = 'block';
            }
            setTimeout(function() {
                window.location.href = "{{ route('recomendacion.ver_producto', ['id' => $recomendacionId]) }}";
            }, Math.floor(Math.random() * (7000 - 3000 + 1)) + 3000);
        });
    </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>