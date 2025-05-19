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

        /* Estilos para el loader */
        .loader-container {
            display: none;
            /* Oculto por defecto, se muestra si hay recomendacionId */
            padding: 20px;
            text-align: center;
        }

        .loader {
            border: 8px solid #f3f3f3;
            /* Light grey */
            border-top: 8px solid #7952b3;
            /* Tu color principal o el que prefieras */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1.5s linear infinite;
            margin: 20px auto;
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
            font-size: 1.2em;
            color: #5a3d8a;
            /* Un tono más oscuro de tu color principal */
        }

        .btn-home {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-home:hover {
            background-color: #5a6268;
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
        <p>Gracias por completar nuestro cuestionario.</p>
        @endif

        {{-- Contenedor del Loader y mensaje de redirección --}}
        {{-- Comprueba si la variable $recomendacionId existe y no es null --}}
        @if (isset($recomendacionId) && $recomendacionId !== null)
        <div class="loader-container" id="loaderContainer">
            <div class="loader"></div>
            <p class="loading-text">Estamos preparando tu recomendación personalizada...</p>
        </div>
        @else
        <p>Revisaremos tus respuestas. ¡Gracias por tu tiempo!</p>
        {{-- Enlace para volver si no hay recomendación automática --}}
        <a href="{{ route('crear.nick') }}" class="btn-home">Volver al inicio</a>
        @endif
    </div>

    {{-- Solo ejecuta el script de redirección si $recomendacionId está presente y tiene valor --}}
    @if (isset($recomendacionId) && $recomendacionId !== null)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar el loader
            var loaderContainer = document.getElementById('loaderContainer');
            if (loaderContainer) {
                loaderContainer.style.display = 'block';
            }

            // Redirigir después de 5 segundos
            setTimeout(function() {
                // Redirige a la ruta que mostrará la recomendación.
                // Esta ruta la definiremos en el siguiente paso (Fase 3).
                // El nombre de la ruta será 'recomendacion.ver' y le pasamos el $recomendacionId.
                window.location.href = "{{ route('recomendacion.ver_producto', ['id' => $recomendacionId]) }}";
            }, 5000); // 5000 milisegundos = 5 segundos
        });
    </script>
    @endif
</body>

</html>