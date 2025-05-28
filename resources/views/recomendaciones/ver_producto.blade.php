<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Producto Recomendado: {{ htmlspecialchars($producto->nombre) }} - HairLife</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Paleta de Morados con Nombres de Colores */
            --purple-primary: RebeccaPurple;
            --purple-secondary: MediumPurple;
            --purple-dark: Indigo;
            --purple-medium: MediumSlateBlue;
            --purple-light: Thistle;
            --purple-very-light: Lavender;
            --purple-background: GhostWhite;

            /* Otros colores base */
            --text-on-purple: white;
            --card-bg: white;

            /* Variables que dependen de las anteriores */
            --border-color: var(--purple-very-light);

            /* Sombra elegante - RGB de RebeccaPurple (102, 51, 153) */
            --card-shadow-elegant: rgba(102, 51, 153, 0.15);

            /* Variables de foco (usan variables de color morado) */
            --input-focus-color: var(--purple-secondary);
            --input-focus-box-shadow: rgba(147, 112, 219, 0.25);
            /* RGB de MediumPurple */
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: #495057;
            padding-top: 80px;
            line-height: 1.7;
            position: relative;
            min-height: 100vh;
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

        .home-button-container {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1030;
        }

        .home-button {
            background-color: var(--purple-primary);
            color: var(--text-on-purple);
            border: 1px solid var(--purple-dark);
            padding: 0.5rem 0.9rem;
            font-size: 1.2rem;
            border-radius: 0.5rem;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2),
                0 0 0 2px rgba(var(--text-on-purple), 0.2);
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        }

        .home-button:hover,
        .home-button:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
            border-color: var(--purple-dark);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3),
                0 0 0 2px rgba(var(--text-on-purple), 0.3);
            transform: translateY(-2px);

        }

        .product-view-container {
            background-color: var(--card-bg);
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 35px var(--card-shadow-elegant);
            max-width: 850px;
            width: 100%;
            padding: 40px;
            margin: 20px auto 40px auto;
        }

        .product-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .product-header .product-name {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 3.5rem;
            margin-bottom: 10px;
        }

        .product-brand-category {
            color: var(--purple-medium);
            font-size: 1.1em;
            margin-bottom: 0;
        }

        .product-brand-category strong {
            color: var(--purple-secondary);
        }


        .product-image-container {
            text-align: center;
            margin-bottom: 35px;
        }

        .product-image {
            max-width: 70%;
            height: auto;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            max-height: 300px;
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: var(--purple-primary);
            font-size: 1.8rem;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--purple-very-light);
        }

        .product-details p {
            line-height: 1.8;
            color: #5a6268;
            font-size: 1.05rem;
            margin-bottom: 15px;
        }

        .purchase-link-container {
            text-align: center;
            margin-top: 35px;
            margin-bottom: 25px;
        }

        .btn-purchase {
            background-color: var(--purple-primary);
            color: var(--text-on-purple);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: background-color 0.2s ease-in-out, transform 0.2s ease;
            border-radius: 8px;
        }

        .btn-purchase:hover,
        .btn-purchase:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
            transform: translateY(-2px);
        }

        /* Se eliminaron los estilos .justification, .justification h3, .justification p */

        .navigation-links-footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .navigation-links-footer a {
            color: var(--purple-medium);
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 6px;
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        .navigation-links-footer a:hover {
            color: var(--purple-dark);
            background-color: var(--purple-very-light);
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="home-button-container">
        <?php
        $homeLink = route('crear.nick'); // Fallback por defecto si no hay sesión o datos necesarios
        $userNick = session('usuario_nombre');

        $queryParams = [];
        if (isset($envio_id_para_link_cuestionario) && $envio_id_para_link_cuestionario) {
            $queryParams['envio_previo'] = $envio_id_para_link_cuestionario;
        }

        // Se asume que $id_cuestionario_actual y $id_pregunta_filtro son pasados desde RecomendacionController
        // Si no existen (porque no se implementó la opción dinámica o falló), se usarán los fallbacks.
        if ($userNick && isset($id_cuestionario_actual) && $id_cuestionario_actual && isset($id_pregunta_filtro) && $id_pregunta_filtro) {
            // Construye el enlace a la pregunta específica del cuestionario
            //// Construye el enlace con los query params y el ancla con el array_merge
            $homeLink = route('cuestionarios.mostrarParaNick', array_merge(
                [
                    'nick' => $userNick,
                    'id_cuestionario' => $id_cuestionario_actual
                ],
                $queryParams

            )) . '#pregunta-' . $id_pregunta_filtro; // Añade el ancla para la pregunta
        } elseif ($userNick) {
            // Si faltan datos del cuestionario/pregunta pero hay sesión, va al dashboard del usuario
            $homeLink = route('user.dashboard', ['nick' => $userNick]);
        }
        // Si $userNick no está definido, $homeLink sigue siendo route('crear.nick') como se definió inicialmente
        ?>

        <a href="{{ $homeLink }}" class="btn home-button" title="Volver al cuestionario para elegir otra categoría">
            <i class="bi bi-house-fill"></i>
        </a>
    </div>

    <div class="product-view-container">
        <div class="product-header">
            <h1 class="product-name">{{ $producto->nombre }}</h1>
            <p class="product-brand-category">
                <strong>Marca:</strong> {{ htmlspecialchars($producto->marca) }} |
                <strong>Categoría:</strong> {{ htmlspecialchars(ucfirst($producto->categoria)) }}
            </p>
        </div>

        @if($producto->foto)
        <div class="product-image-container">
            <img src="{{ asset('storage/' . $producto->foto) }}"
                alt="Foto de {{ $producto->nombre }}"
                class="product-image img-fluid">
        </div>
        @else
        <p class="text-center fst-italic text-muted">(Imagen no disponible)</p>
        @endif

        <div class="product-details">
            <h2 class="section-title">Descripción y Modo de Uso</h2>
            <p>{!! nl2br(e($producto->descripcion)) !!}</p>
        </div>

        @if($producto->url)
        <div class="purchase-link-container">
            <a href="{{ $producto->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-lg btn-purchase">
                <i class="bi bi-cart3 me-2"></i>Ver o Comprar Producto
            </a>
        </div>
        @endif


        <div class="navigation-links-footer">
            @if (session('usuario_nombre'))
            <a href="{{ route('user.dashboard', ['nick' => session('usuario_nombre')]) }}">Volver a Mi Panel</a>
            @else
            <a href="{{ route('crear.nick') }}">Ir al Inicio</a>
            @endif
            <a href="{{ route('login') }}">Comenzar de Nuevo</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>