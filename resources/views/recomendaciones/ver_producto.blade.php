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
        }

        .home-button:hover,
        .home-button:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
            border-color: var(--purple-dark);
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
            font-size: 2.8rem;
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
            max-height: 400px;
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
        <a href="{{ session('usuario_nombre') ? route('user.dashboard', ['nick' => session('usuario_nombre')]) : route('crear.nick') }}" class="btn home-button" title="Volver al Panel">
            <i class="bi bi-house-fill"></i>
        </a>
    </div>

    <div class="product-view-container">
        <div class="product-header">
            <h1 class="product-name">{{ htmlspecialchars($producto->nombre) }}</h1>
            <p class="product-brand-category">
                <strong>Marca:</strong> {{ htmlspecialchars($producto->marca) }} |
                <strong>Categoría:</strong> {{ htmlspecialchars(ucfirst($producto->categoria)) }}
            </p>
        </div>

        @if($producto->foto)
        <div class="product-image-container">
            <img src="{{ filter_var($producto->foto, FILTER_VALIDATE_URL) ? $producto->foto : asset('storage/' . $producto->foto) }}"
                alt="Foto de {{ htmlspecialchars($producto->nombre) }}" class="product-image img-fluid">
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

        {{-- SECCIÓN DE JUSTIFICACIÓN ELIMINADA --}}
        {{-- @if($recomendacion->justificacion_titulo || $recomendacion->justificacion_detalle)
        <div class="justification">
            <h3>{{ htmlspecialchars($recomendacion->justificacion_titulo ?: 'Nuestra Sugerencia Para Ti') }}</h3>
        @if($recomendacion->justificacion_detalle)
        <p>{!! nl2br(e($recomendacion->justificacion_detalle)) !!}</p>
        @endif
    </div>
    @endif --}}

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