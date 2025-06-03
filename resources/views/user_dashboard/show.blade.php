<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- El título de la página es dinámico, mostrando el nick del usuario. -->
    <title>Panel de {{ $nick }} - HairLife</title>

    <!-- Importamos Bootstrap CSS. Esto nos proporciona un framework de estilos
         que facilita la creación de diseños responsivos y nos da acceso a componentes
         de interfaz de usuario con un estilo consistente (como el sistema de cuadrícula). -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Importamos Bootstrap Icons. Esta librería nos permite usar una amplia gama
         de iconos vectoriales simplemente añadiendo clases CSS a elementos HTML,
         como los iconos dentro de las tarjetas. -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Cargamos fuentes personalizadas de Google Fonts para mejorar la estética de la página.
         'Dancing Script' se usa para títulos y 'Montserrat' para el texto general. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Definimos variables CSS para nuestra paleta de colores. Esto es una excelente práctica
           para mantener la consistencia en el diseño y hacer cambios de color de forma centralizada
           en el futuro, sin tener que buscar y reemplazar valores hexadecimales en todo el CSS. */
        :root {
            --purple-primary: RebeccaPurple;
            /* Color principal morado */
            --purple-secondary: MediumPurple;
            /* Un morado más claro */
            --purple-dark: Indigo;
            /* Un morado oscuro */
            --purple-medium: MediumSlateBlue;
            /* Un morado intermedio */
            --purple-light: Thistle;
            /* Un morado claro */
            --purple-very-light: Lavender;
            /* Un morado muy claro */
            --purple-background: GhostWhite;
            /* Color de fondo muy claro */
            --text-on-purple: white;
            /* Color de texto para fondos morados */
            --card-bg: white;
            /* Color de fondo para tarjetas */
            --card-shadow: rgba(106, 13, 173, 0.1);
            /* Sombra estándar de tarjeta */
            --card-hover-shadow: rgba(106, 13, 173, 0.2);
            /* Sombra de tarjeta al pasar el ratón */
            --card-border-color: #efdbf5;
            /* Borde de tarjeta lila muy pálido */
            --icon-color: var(--purple-secondary);
            /* Color para los iconos */

            /* Variables RGB para usar en las sombras (corregido para que funcionen). */
            --purple-primary-rgb: 106, 13, 173;
            --purple-secondary-rgb: 142, 68, 173;

            /* Variables para el gradiente de la cara frontal de la tarjeta. */
            --gradient-card-front-start-deep-purple: #d9aadf;
            --gradient-card-front-end-light-purple: var(--card-border-color);
        }

        /* Estilos generales para el cuerpo de la página.
           Usamos Flexbox para asegurar que el pie de página siempre esté al final
           de la pantalla, incluso si el contenido es corto. */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 0;
            /* Las sombras del body ahora usarán las variables RGB definidas. */
            box-shadow: 0 15px 35px rgba(var(--purple-primary-rgb), 0.07),
                0 5px 15px rgba(var(--purple-primary-rgb), 0.08);
        }

        /* Este pseudo-elemento crea un patrón de fondo sutil que se repite por toda la página.
           Se mantiene fijo y detrás del contenido principal. */
        body::before {
            content: "";
            position: fixed;
            /* Fijo para que cubra toda la ventana detrás de todo. */
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

        /* Estilos para el encabezado del panel. Incluye una imagen de fondo y texto grande. */
        .header-panel {
            background-image: url('/storage/imagenes/foto_banner.jpeg');
            /* Imagen de banner. */
            background-size: cover;
            /* Cubre toda el área del encabezado. */
            background-position: center center;
            background-repeat: no-repeat;
            color: var(--text-on-purple);
            padding: 60px 20px;
            text-align: center;
            position: relative;
        }

        /* Estilos para el título y subtítulo dentro del encabezado. */
        .header-panel h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #d9aadf;
            /* Color lila/lavanda. */
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);
        }

        .header-panel p {
            font-size: 1.6rem;
            color: #d9aadf;
            opacity: 0.95;
            margin-bottom: 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }

        /* Sección que contiene las tarjetas del panel. */
        .buttons-panel-section {
            padding-top: 50px;
            padding-bottom: 50px;
            background-color: transparent;
            /* Fondo transparente. */
            border-bottom: 1px solid #eee;
            border-top: 1px solid #eee;
            position: relative;
        }

        /* Contenedor para las tarjetas. Utiliza `max-width` y `margin: auto` para centrar. */
        .cards-container {
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilos base para las caras de las tarjetas (`.card-front` y `.card-back`).
           Estas clases se aplican a elementos que también tienen la clase `panel-card`. */
        .panel-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border-color);
            border-radius: 15px;
            box-shadow: 0 6px 12px var(--card-shadow);
            height: 100%;
            /* Asegura que la cara ocupe toda la altura del flipper. */
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Estilos para los iconos dentro de las tarjetas. */
        .panel-card .card-icon {
            font-size: 3rem;
            color: var(--icon-color);
            margin-bottom: 15px;
            /* Transición para el efecto de "rebote" y "glow" al pasar el ratón. */
            transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0s,
                text-shadow 0.35s ease-out 0s;
        }

        /* Efecto al pasar el ratón sobre el contenedor del flipper: el icono se escala, eleva y rota ligeramente. */
        .panel-card-flipper:hover .card-front .card-icon {
            transform: scale(1.2) translateY(-4px) rotate(-5deg);
            text-shadow: 0 0 12px rgba(var(--purple-primary-rgb), 0.6);
            /* Añade un resplandor. */
        }

        /* Estilo específico para la cara frontal de la tarjeta, con un gradiente. */
        .panel-card.card-front {
            background: linear-gradient(170deg,
                    var(--gradient-card-front-start-deep-purple) 0%,
                    var(--gradient-card-front-end-light-purple) 45%);
        }

        /* Estilos para el cuerpo de la tarjeta, usando Flexbox para centrar el contenido. */
        .panel-card .card-body {
            padding: 30px 25px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Estilos para el título y descripción de los botones/tarjetas. */
        .panel-card .button-title {
            font-size: 1.7em;
            font-weight: 700;
            color: var(--purple-primary);
            margin-bottom: 10px;
        }

        .panel-card .button-description {
            font-size: 0.95em;
            color: #555;
            line-height: 1.5;
        }

        /* Estilos para el efecto de giro (FLIP) de las tarjetas.
           `perspective` es crucial para el efecto 3D.
           `height` fija la altura de las tarjetas que giran. */
        .panel-card-flipper {
            perspective: 1200px;
            position: relative;
            width: 100%;
            height: 260px;
            /* Ajusta esta altura si tu contenido varía. */
            cursor: pointer;
            /* Por defecto, las tarjetas están un poco atenuadas, excepto la del cuestionario. */
            opacity: var(--card-dimmed-opacity);
            /* Asegúrate de definir --card-dimmed-opacity en :root, por ejemplo, 0.7. */
            transition: opacity 0.35s ease-in-out;
        }

        /* La tarjeta del cuestionario (que tiene la clase `card-back-action` en su cara trasera)
           no estará atenuada por defecto. */
        .panel-card-flipper:has(a > .card-back-action) {
            opacity: 1;
        }

        /* Al pasar el ratón sobre CUALQUIER tarjeta, recupera su opacidad total. */
        .panel-card-flipper:hover {
            opacity: 1;
        }

        /* Estilos para el enlace que envuelve el flipper de la tarjeta "Cuestionario". */
        .panel-card-link-flipper {
            display: block;
            width: 100%;
            height: 100%;
            text-decoration: none;
            color: inherit;
        }

        /* Estilos comunes para las caras frontal y trasera de las tarjetas giratorias.
           `backface-visibility: hidden` es esencial para que la cara opuesta no sea visible
           durante la animación de giro. */
        .panel-card-flipper .card-front,
        .panel-card-flipper .card-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.4s;
            /* Las clases `panel-card` ya aplican los estilos base de tarjeta. */
        }

        /* La cara frontal de la tarjeta. */
        .panel-card-flipper .card-front {
            z-index: 2;
            transform: rotateY(0deg);
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.4s,
                box-shadow 0.25s ease-out,
                border-color 0.25s ease-out;
        }

        /* La cara trasera de la tarjeta. Inicialmente está girada 180 grados para que no sea visible. */
        .panel-card-flipper .card-back {
            transform: rotateY(180deg);
            background-color: var(--purple-secondary);
            /* Fondo para la cara trasera "PRÓXIMAMENTE". */
            color: var(--text-on-purple);
            /* Las clases de Bootstrap `d-flex`, `align-items-center`, `justify-content-center`
               se usan en el HTML para centrar el contenido de esta cara. */
        }

        /* Al pasar el ratón sobre el flipper, la cara frontal gira para ocultarse. */
        .panel-card-flipper:hover .card-front {
            transform: rotateY(-180deg);
        }

        /* Al pasar el ratón sobre el flipper, la cara trasera gira para mostrarse. */
        .panel-card-flipper:hover .card-back {
            transform: rotateY(0deg);
            box-shadow: 0 10px 20px var(--card-hover-shadow);
            /* Sombra al mostrarse. */
        }

        /* Estilo específico para la cara trasera de la tarjeta "Cuestionario" (la de acción). */
        .panel-card-flipper:has(a > .card-back-action) .card-back-action {
            background: linear-gradient(135deg, var(--purple-primary) 0%, var(--purple-secondary) 100%);
        }

        /* Efecto de hover mejorado para la cara trasera "¡COMENZAR YA!". */
        .panel-card-flipper:has(a > .card-back-action):hover .card-back-action {
            box-shadow: 0 10px 25px var(--card-hover-shadow),
                0 0 20px rgba(var(--purple-primary-rgb), 0.8);
            /* Resplandor morado. */
            filter: brightness(1.15);
        }

        /* Estilos para el texto de las caras traseras. */
        .soon-text-flip,
        .action-text-flip {
            font-family: 'Dancing Script', cursive;
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            padding: 15px;
            text-align: center;
        }

        /* Animación de pulso para el texto "¡COMENZAR YA!" al pasar el ratón. */
        @keyframes pulse-glow-text {
            0% {
                text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3), 0 0 5px rgba(255, 255, 255, 0.4);
            }

            50% {
                text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5), 0 0 15px rgba(255, 255, 255, 0.8);
            }

            100% {
                text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3), 0 0 5px rgba(255, 255, 255, 0.4);
            }
        }

        .panel-card-flipper:has(a > .card-back-action):hover .action-text-flip {
            animation: pulse-glow-text 2s infinite ease-in-out;
        }

        /* Estilos para la tarjeta de "Cuestionario" cuando no está disponible (deshabilitada). */
        .button-disabled {
            background-color: var(--card-bg);
            /* Fondo de la tarjeta deshabilitada. */
            opacity: 0.6;
            /* Menor opacidad para indicar que está deshabilitada. */
            pointer-events: none;
            /* Deshabilita clics en la tarjeta. */
            border-color: var(--card-border-color);
            /* Borde de la tarjeta deshabilitada. */
            height: 260px;
            /* Mantiene la misma altura que las tarjetas con flip. */
            display: flex;
            flex-direction: column;
        }

        .button-disabled .card-body {
            padding: 30px 25px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .button-disabled .button-title {
            color: #6c757d;
            /* Color de texto gris para el título deshabilitado. */
        }

        .button-disabled .card-icon {
            opacity: 0.5;
            /* Icono más tenue. */
        }

        /* Estilos para el pie de página. */
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

    <header class="header-panel">
        <!-- CORRECCIÓN: Se ha eliminado la duplicación de la etiqueta <h1>. -->
        <h1>¡Hola, {{empty($displayNick) ? $nick : $displayNick }}!</h1>
        <p>Bienvenid@ a tu espacio en HairLife</p>
    </header>

    <section class="buttons-panel-section">
        <!-- Contenedor de Bootstrap: Proporciona un ancho máximo y centrado para el contenido. -->
        <div class="container cards-container">
            <!-- Fila de Bootstrap: Organiza las tarjetas en una fila.
                 `justify-content-center` centra las columnas horizontalmente.
                 `g-4 g-lg-5` añade espaciado (gap) entre las columnas en diferentes tamaños de pantalla. -->
            <div class="row justify-content-center g-4 g-lg-5">

                {{-- Tarjeta "Mi Pelo" --}}
                <!-- Columna de Bootstrap: Define el ancho de la tarjeta en diferentes tamaños de pantalla.
                     `col-lg-4`: 4 columnas en pantallas grandes.
                     `col-md-6`: 6 columnas en pantallas medianas.
                     `col-sm-10`: 10 columnas en pantallas pequeñas.
                     `d-flex`: Hace que la columna sea un contenedor flex, útil para que las tarjetas
                                 tengan la misma altura si su contenido varía. -->
                <div class="col-lg-4 col-md-6 col-sm-10 d-flex">
                    <!-- Contenedor para el efecto de giro de la tarjeta. `w-100` asegura que ocupe todo el ancho de su columna. -->
                    <div class="panel-card-flipper w-100">
                        <!-- Cara frontal de la tarjeta. Las clases `card` y `panel-card` le dan el estilo base. -->
                        <div class="card panel-card card-front">
                            <!-- Cuerpo de la tarjeta. -->
                            <div class="card-body">
                                <!-- Icono de Bootstrap Icons. -->
                                <i class="bi bi-person-hearts card-icon"></i>
                                <span class="button-title">Mi Pelo</span>
                                <span class="button-description">Gestiona tu perfil capilar.</span>
                            </div>
                        </div>
                        <!-- Cara trasera de la tarjeta.
                             `d-flex align-items-center justify-content-center` de Bootstrap
                             se usan para centrar el texto "PRÓXIMAMENTE". -->
                        <div class="card panel-card card-back d-flex align-items-center justify-content-center">
                            <span class="soon-text-flip">PRÓXIMAMENTE</span>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta "Cuestionario" --}}
                <div class="col-lg-4 col-md-6 col-sm-10 d-flex">
                    <div class="panel-card-flipper w-100">
                        <!-- Lógica de Blade: Si el cuestionario oficial está disponible, mostramos la tarjeta interactiva. -->
                        @if ($cuestionarioOficial)
                        <!-- Enlace que envuelve toda la tarjeta para el efecto de giro y navegación. -->
                        <a href="{{ route('cuestionarios.mostrarParaNick', ['nick' => $nick, 'id_cuestionario' => $cuestionarioOficial->id]) }}" class="panel-card-link-flipper">
                            <div class="card panel-card card-front">
                                <div class="card-body">
                                    <i class="bi bi-clipboard2-pulse card-icon"></i>
                                    <span class="button-title">Cuestionario</span>
                                    <span class="button-description">Obtén recomendaciones personalizadas.</span>
                                </div>
                            </div>
                            <!-- Cara trasera de la tarjeta de acción. Tiene un gradiente especial. -->
                            <div class="card panel-card card-back card-back-action d-flex align-items-center justify-content-center">
                                <span class="action-text-flip">¡COMENZAR YA!</span>
                            </div>
                        </a>
                        @else
                        <!-- Fallback: Si el cuestionario no está disponible, mostramos una tarjeta deshabilitada
                             sin el efecto de giro. -->
                        <div class="card panel-card button-disabled">
                            <div class="card-body">
                                <i class="bi bi-clipboard2-x card-icon"></i>
                                <span class="button-title">Cuestionario</span>
                                <span class="button-description">No disponible actualmente.</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Tarjeta "Peinados y Cortes" --}}
                <div class="col-lg-4 col-md-6 col-sm-10 d-flex">
                    <div class="panel-card-flipper w-100">
                        <div class="card panel-card card-front">
                            <div class="card-body">
                                <i class="bi bi-scissors card-icon"></i>
                                <span class="button-title">Peinados y Cortes</span>
                                <span class="button-description">Inspírate para tu nuevo look.</span>
                            </div>
                        </div>
                        <div class="card panel-card card-back d-flex align-items-center justify-content-center">
                            <span class="soon-text-flip">PRÓXIMAMENTE</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Pie de página de la web. -->
    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} HairLife. Todos los derechos reservados.</p>
    </footer>

</body>

</html>