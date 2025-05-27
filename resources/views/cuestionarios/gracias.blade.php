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
            /* Paleta de Morados con Nombres de Colores */
            --purple-primary: RebeccaPurple;
            /* Original: #6a0dad (RebeccaPurple es #663399) */
            --purple-secondary: MediumPurple;
            /* Original: #8e44ad (MediumPurple es #9370DB) */
            --purple-dark: Indigo;
            /* Original: #4c0b56 (Indigo es #4B0082) */
            --purple-medium: MediumSlateBlue;
            /* Original: #7952b3 (MediumSlateBlue es #7B68EE) */
            --purple-light: Thistle;
            /* Original: #c3a2d9 (Thistle es #D8BFD8) */
            --purple-very-light: Lavender;
            /* Original: #e0cce8 (Lavender es #E6E6FA) */
            --purple-background: GhostWhite;
            /* Original: #f8f5f9 (GhostWhite es #F8F8FF) */

            /* Otros colores base */
            --text-on-purple: white;
            --card-bg: white;

            /* Variables que dependen de las anteriores */
            --border-color: var(--purple-very-light);

            /* Sombra elegante - RGB de RebeccaPurple (102, 51, 153) */
            --card-shadow-elegant: rgba(102, 51, 153, 0.18);

            /* Variables de foco (usan variables de color morado) */
            --input-focus-color: var(--purple-secondary);
            --input-focus-box-shadow: rgba(147, 112, 219, 0.25);
            /* RGB de MediumPurple (147, 112, 219) */
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: darkslategrey;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            text-align: center;
            box-sizing: border-box;
            position: relative;
            z-index: 0;
        }

        body::before {
            content: "";
            position: fixed;
            /* Cubre toda la ventana y se queda fijo */
            top: 0;
            left: 0;
            width: 100vw;
            /* Ancho completo de la ventana */
            height: 100vh;
            /* Alto completo de la ventana */

            background-image: url('/storage/imagenes/fondo.jpg');
            /* ¡Asegúrate que esta ruta sea correcta! */
            background-repeat: repeat;
            background-size: 250px;
            /* Ajusta el tamaño del patrón como desees */

            opacity: 0.2;
            /* Opacidad solicitada. Ajusta si 0.6 era lo que querías (más visible) */

            z-index: -1;
            /* Se coloca detrás de todo el contenido del body */
            pointer-events: none;
            /* Para asegurar que no interfiera con clics u otras interacciones */
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
    </style>
</head>

<body>
    <div class="gracias-container">
        <i class="bi bi-check-circle-fill gracias-icon"></i>
        <h1>¡Cuestionario Enviado!</h1>

        @if (session('success'))
        <p class="session-success">{{ session('success') }}</p>
        @else
        <p>Gracias por completar nuestro cuestionario y dedicar tu tiempo.</p>
        @endif

        {{-- Contenedor del Loader y mensaje de redirección --}}
        @if (isset($recomendacionId) && $recomendacionId !== null)
        <div class="loader-container" id="loaderContainer">
            <div class="loader"></div>
            <p class="loading-text">Estamos preparando tu recomendación personalizada...</p>
        </div>
        @else
        <p>Revisaremos tus respuestas con atención.</p>
        <a href="{{ route('crear.nick') }}" class="btn btn-lg btn-purple-home mt-3">Volver al inicio</a>
        @endif
    </div>

    @if (isset($recomendacionId) && $recomendacionId !== null)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loaderContainer = document.getElementById('loaderContainer');
            if (loaderContainer) {
                loaderContainer.style.display = 'block';
            }

            setTimeout(function() {
                window.location.href = "{{ route('recomendacion.ver_producto', ['id' => $recomendacionId]) }}";
            }, 5000); // 5 segundos
        });
    </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>