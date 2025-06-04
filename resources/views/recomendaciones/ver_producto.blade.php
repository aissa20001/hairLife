<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- El título de la página es dinámico, mostrando el nombre del producto recomendado. -->
    <title>Tu Producto Recomendado: {{ htmlspecialchars($producto->nombre) }} - HairLife</title>

    <!-- Importamos Bootstrap CSS. Esto nos proporciona un framework de estilos
         que facilita la creación de diseños responsivos y nos da acceso a componentes
         de interfaz de usuario con un estilo consistente (botones, imágenes responsivas). -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Importamos Bootstrap Icons. Esta librería nos permite usar una amplia gama
         de iconos vectoriales simplemente añadiendo clases CSS a elementos HTML,
         como los iconos en los botones. -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Preconexión y carga de Google Fonts. Usamos 'Dancing Script' para títulos
         y 'Montserrat' para el texto general, lo que le da un toque estético a la página. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Definición de variables CSS globales para la paleta de colores y temas.
           Esto es una excelente práctica para mantener la consistencia en el diseño
           y facilitar cambios de color de forma centralizada en el futuro. */
        :root {
            --purple-primary: RebeccaPurple;
            --purple-secondary: MediumPurple;
            --purple-dark: Indigo;
            --purple-medium: MediumSlateBlue;
            --purple-light: Thistle;
            --purple-very-light: Lavender;
            --purple-background: GhostWhite;

            /* Otros colores base. */
            --text-on-purple: white;
            /* Se usará para el color del texto sobre fondos morados. */
            --text-on-purple-rgb: 255, 255, 255;
            /* Componentes RGB para usar en rgba() con --text-on-purple. */
            --card-bg: white;

            /* Variables que dependen de las anteriores. */
            --border-color: var(--purple-very-light);

            /* Sombra elegante para tarjetas. */
            --card-shadow-elegant: rgba(102, 51, 153, 0.15);

            /* Variables de foco para inputs (aunque no hay inputs en esta página, es buena práctica). */
            --input-focus-color: var(--purple-secondary);
            --input-focus-box-shadow: rgba(147, 112, 219, 0.25);
        }

        /* Estilos generales para el cuerpo (body) de la página.
           Usamos `display: flex` y `flex-direction: column` para que el contenido
           principal y el pie de página se organicen verticalmente y el pie de página
           siempre esté al final de la pantalla, incluso con poco contenido. */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: #495057;
            /* Color de texto general. */
            padding-top: 80px;
            /* Espacio para el botón de inicio fijo. */
            line-height: 1.7;
            /* Altura de línea para mejor legibilidad. */
            position: relative;
            min-height: 100vh;
            z-index: 0;
            display: flex;
            /* Habilitamos Flexbox para el body. */
            flex-direction: column;
            /* Apilamos los elementos verticalmente. */
        }

        /* Este pseudo-elemento crea un patrón de fondo sutil que se repite por toda la página.
           Se mantiene fijo y detrás de todo el contenido. */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: url('/storage/imagenes/fondo.jpg');
            /* Ruta de tu imagen de fondo. */
            background-repeat: repeat;
            background-size: 250px;
            opacity: 0.2;
            /* Opacidad baja para que sea sutil. */
            z-index: -1;
            /* Lo coloca detrás de todo el contenido del body. */
            pointer-events: none;
            /* Asegura que no interfiera con clics u otras interacciones. */
        }

        /* Contenedor del botón de inicio. Se posiciona de forma fija en la esquina superior izquierda. */
        .home-button-container {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1030;
            /* Asegura que esté por encima de otros elementos. */
        }

        /* Estilos para el botón de inicio.
           Combina un estilo base con el efecto de foco de Bootstrap. */
        .home-button {
            background-color: var(--purple-primary);
            color: var(--text-on-purple);
            border: 1px solid var(--purple-dark);
            padding: 0.5rem 1.5rem;
            font-size: 1.2rem;
            border-radius: 0.5rem;
            /* La sombra usa la variable RGB para `text-on-purple` (blanco). */
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2),
                0 0 0 2px rgba(var(--text-on-purple-rgb), 0.2);
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        }

        /* Efecto al pasar el ratón o enfocar el botón de inicio. */
        .home-button:hover,
        .home-button:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
            border-color: var(--purple-dark);
            /* La sombra usa la variable RGB para `text-on-purple` (blanco). */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3),
                0 0 0 2px rgba(var(--text-on-purple-rgb), 0.3);
            transform: translateY(-2px);
            /* Pequeño efecto de elevación. */
        }

        /* Contenedor principal de la vista del producto.
           Estilizado como una "tarjeta" con fondo blanco, bordes redondeados y sombra.
           `margin: auto` lo centra horizontalmente. */
        .product-view-container {
            background-color: var(--card-bg);
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 35px var(--card-shadow-elegant);
            max-width: 850px;
            width: 100%;
            padding: 40px;
            margin: 20px auto 40px auto;
            flex-grow: 1;
            /* Permite que este contenedor ocupe el espacio disponible, empujando el footer. */
        }

        /* Estilos para el encabezado del producto (nombre, marca, categoría). */
        .product-header {
            text-align: center;
            margin-bottom: 35px;
        }

        /* Estilos para el nombre del producto. */
        .product-header .product-name {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 3.5rem;
            margin-bottom: 10px;
        }

        /* Estilos para la marca y categoría del producto. */
        .product-brand-category {
            color: var(--purple-medium);
            font-size: 1.1em;
            margin-bottom: 0;
        }

        .product-brand-category strong {
            color: var(--purple-secondary);
        }

        /* Contenedor de la imagen del producto. */
        .product-image-container {
            text-align: center;
            margin-bottom: 35px;
        }

        /* Estilos para la imagen del producto.
           `img-fluid` es una clase de Bootstrap que hace que la imagen sea responsiva,
           ajustando su tamaño al ancho del contenedor. */
        .product-image {
            max-width: 70%;
            height: auto;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            max-height: 300px;
        }

        /* Estilos para los títulos de sección (ej: "Descripción y Modo de Uso"). */
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

        /* Estilos para los párrafos de la descripción del producto. */
        .product-details p {
            line-height: 1.8;
            color: #5a6268;
            font-size: 1.05rem;
            margin-bottom: 15px;
        }

        /* Contenedor del enlace de compra. */
        .purchase-link-container {
            text-align: center;
            margin-top: 35px;
            margin-bottom: 25px;
        }

        /* Estilos para el botón de compra.
           Combina clases de Bootstrap (`btn`, `btn-lg`) para la base y el tamaño,
           y nuestras clases personalizadas (`btn-purchase`) para el color y el hover. */
        .btn-purchase {
            background-color: var(--purple-primary);
            color: var(--text-on-purple);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 0.5rem;
            /* La sombra usa la variable RGB para `text-on-purple` (blanco). */
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2),
                0 0 0 2px rgba(var(--text-on-purple-rgb), 0.2);
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        }

        /* Efecto al pasar el ratón o enfocar el botón de compra. */
        .btn-purchase:hover,
        .btn-purchase:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
            border-color: var(--purple-dark);
            /* La sombra usa la variable RGB para `text-on-purple` (blanco). */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3),
                0 0 0 2px rgba(var(--text-on-purple-rgb), 0.3);
            transform: translateY(-2px);
        }

        /* Contenedor para los enlaces de navegación del pie. */
        .navigation-links-footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        /* Estilos para los enlaces de navegación del pie. */
        .navigation-links-footer a {
            color: var(--purple-medium);
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 6px;
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        /* Efecto al pasar el ratón sobre los enlaces de navegación del pie. */
        .navigation-links-footer a:hover {
            color: var(--purple-dark);
            background-color: var(--purple-very-light);
            text-decoration: none;
        }

        /* Estilos para el pie de página de la web. */
        .site-footer {
            background-color: var(--purple-dark);
            color: rgba(255, 255, 255, 0.8);
            padding: 5px 0;
            text-align: center;
            font-size: 0.9em;
            margin-top: auto;
            /* Empuja el footer al final de la página. */
        }
    </style>
</head>

<body>
    <!-- Contenedor del botón de inicio fijo en la esquina superior izquierda. -->
    <div class="home-button-container">
        <?php
        // Lógica PHP para determinar el enlace del botón de "Volver".
        // Por defecto, redirige a la página de creación de nick.
        $homeLink = route('crear.nick');
        $userNick = session('usuario_nombre'); // Obtiene el nick del usuario de la sesión.

        $queryParams = [];
        // Si existe un ID de envío previo, se añade como parámetro de consulta.
        if (isset($envio_id_para_link_cuestionario) && $envio_id_para_link_cuestionario) {
            $queryParams['envio_previo'] = $envio_id_para_link_cuestionario;
        }

        // Si el usuario está logueado y se tienen los IDs de cuestionario y pregunta,
        // el enlace apunta a esa pregunta específica en el cuestionario.
        if ($userNick && isset($id_cuestionario_actual) && $id_cuestionario_actual && isset($id_pregunta_filtro) && $id_pregunta_filtro) {
            $homeLink = route('cuestionarios.mostrarParaNick', array_merge(
                [
                    'nick' => $userNick,
                    'id_cuestionario' => $id_cuestionario_actual
                ],
                $queryParams
            )) . '#pregunta-' . $id_pregunta_filtro; // Añade un ancla para ir a una pregunta específica.
        } elseif ($userNick) {
            // Si el usuario está logueado pero faltan datos de cuestionario/pregunta,
            // el enlace apunta al panel de usuario.
            $homeLink = route('user.dashboard', ['nick' => $userNick]);
        }
        // Si $userNick no está definido, $homeLink sigue siendo route('crear.nick') como se definió inicialmente.
        ?>

        <!-- Botón de "Volver al cuestionario".
             Usa la clase `btn` de Bootstrap para el estilo base y un icono de Bootstrap Icons. -->
        <a href="{{ $homeLink }}" class="btn home-button" title="Volver al cuestionario para elegir otra categoría">
            <i class="bi bi-clipboard2-pulse me-2"></i>Volver al cuestionario
        </a>
    </div>

    <!-- Contenedor principal de la información del producto recomendado, estilizado como una tarjeta. -->
    <div class="product-view-container">
        <!-- Encabezado del producto, con nombre, marca y categoría. -->
        <div class="product-header">
            <h1 class="product-name">{{ $producto->nombre }}</h1>
            <p class="product-brand-category">
                <strong>Marca:</strong> {{ $producto->marca }} |
                <strong>Categoría:</strong> {{ ucfirst($producto->categoria) }}
            </p>
        </div>

        <!-- Bloque para mostrar la imagen del producto. -->
        @if($producto->foto)
        <div class="product-image-container">
            <!-- La imagen usa la clase `img-fluid` de Bootstrap para ser responsiva. -->
            <img src="{{ asset('storage/' . $producto->foto) }}"
                alt="Foto de {{ $producto->nombre }}"
                class="product-image img-fluid">
        </div>
        @else
        <p class="text-center fst-italic text-muted">(Imagen no disponible)</p>
        @endif

        <!-- Sección de detalles del producto (descripción y modo de uso). -->
        <div class="product-details">
            <h2 class="section-title">Descripción y Modo de Uso</h2>
            <!-- `nl2br(e($producto->descripcion))` es una función de Blade/Laravel
                 que convierte saltos de línea en etiquetas `<br>` y escapa HTML para seguridad. -->
            <p>{!! nl2br(e($producto->descripcion)) !!}</p>
        </div>

        <!-- Enlace para ver o comprar el producto, si la URL existe. -->
        @if($producto->url)
        <div class="purchase-link-container">
            <!-- Botón de compra. Usa clases de Bootstrap (`btn`, `btn-lg`)
                 y nuestras clases personalizadas (`btn-purchase`).
                 `target="_blank" rel="noopener noreferrer"` para abrir en una nueva pestaña de forma segura. -->
            <a href="{{ $producto->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-lg btn-purchase">
                <i class="bi bi-cart3 me-2"></i>Ver o Comprar Producto
            </a>
        </div>
        @endif

        <!-- Enlaces de navegación en la parte inferior de la tarjeta. -->
        <div class="navigation-links-footer">
            @if (session('usuario_nombre'))
            <a href="{{ route('user.dashboard', ['nick' => session('usuario_nombre')]) }}">Volver a Mi Panel</a>
            @else
            <a href="{{ route('crear.nick') }}">Ir al Inicio</a>
            @endif
            <a href="{{ route('login') }}">Cerrar Sesión</a>
        </div>

    </div>
    <!-- Pie de página de la web. -->
    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} HairLife. Todos los derechos reservados.</p>
    </footer>

</body>

</html>