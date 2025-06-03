<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de {{ htmlspecialchars($nick) }} - HairLife</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --purple-primary: #6a0dad;
            --purple-secondary: #8e44ad;
            --purple-dark: #4c0b56;
            --purple-light-bg: #f8f5f9;
            --section-bg: #ffffff;
            --text-on-purple: #ffffff;
            --card-bg: #ffffff;
            --card-shadow: rgba(106, 13, 173, 0.1);
            --card-hover-shadow: rgba(106, 13, 173, 0.2);
            --card-border-color: #efdbf5;
            --icon-color: var(--purple-secondary);
            --purple-primary: #6a0dad;
            --purple-secondary: #8e44ad;
            --purple-primary: #6a0dad;
            --purple-secondary: #8e44ad;
            --purple-primary: #6a0dad;
            --purple-secondary: #8e44ad;
            --card-border-color: #efdbf5;
            /* Este es tu lila muy pálido existente */
            /* --card-bg: #ffffff; */
            /* Ya no lo usaremos directamente para el final del gradiente de la cara frontal */

            /* VARIABLES PARA EL GRADIENTE MORADO A MORADO MÁS CLARO */
            --gradient-card-front-start-deep-purple: #d9aadf;
            /* Un lila/lavanda notable (el mismo que antes para el inicio vibrante) */
            --gradient-card-front-end-light-purple: var(--card-border-color);
            /* Usamos tu --card-border-color (#efdbf5) como el morado más claro */
            --purple-primary: #6a0dad;
            --purple-primary-rgb: 106, 13, 173;
            /* Componentes RGB de --purple-primary */
            --purple-secondary: #8e44ad;
            --purple-secondary-rgb: 142, 68, 173;
            /* Componentes RGB de --purple-secondary */
            --icon-color: var(--purple-secondary);
            /* Ya lo tienes */
        }



        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-light-bg);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            /* Mantenlo para el body::before */
            z-index: 0;
        }

        body::before {
            /* Este es tu fondo con patrón global */
            content: "";
            position: fixed;
            /* Fijo para que cubra toda la ventana detrás de todo */
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: url('/storage/imagenes/fondo.jpg');
            /* */
            background-repeat: repeat;
            background-size: 250px;
            opacity: 0.2;
            z-index: -1;
            /* Detrás de todo el contenido del body */
            pointer-events: none;
        }

        .header-panel {

            background-image: url('/storage/imagenes/foto_banner.jpeg');
            /* Correcto si la URL directa funciona */
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            color: var(--text-on-purple);
            padding: 60px 20px;
            text-align: center;
            position: relative;
        }

        .header-panel h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #d9aadf;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);
        }

        .header-panel p {
            font-size: 1.6rem;
            color: #d9aadf;
            opacity: 0.95;
            margin-bottom: 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }

        .buttons-panel-section {
            padding-top: 50px;
            padding-bottom: 50px;
            background-color: var(--section-bg);
            background-color: transparent;
            border-bottom: 1px solid #eee;
            border-top: 1px solid #eee;
            position: relative;
        }

        .cards-container {
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilos base de .panel-card (se aplican a .card-front y .card-back a través de clases) */
        .panel-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border-color);
            border-radius: 15px;
            box-shadow: 0 6px 12px var(--card-shadow);
            height: 100%;
            /* Para que las caras ocupen toda la altura del flipper */
            display: flex;
            flex-direction: column;
            overflow: hidden;
            /* La transición original de hover la manejaremos en el flipper */
        }

        .panel-card .card-icon {
            font-size: 3rem;
            /* Existente */
            color: var(--icon-color);
            /* Existente */
            margin-bottom: 15px;
            /* Existente */
            /* Ajustamos la transición para incluir text-shadow y un efecto más suave */
            transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0s,
                text-shadow 0.35s ease-out 0s;
            /* El cubic-bezier da un efecto de "rebote" o más elástico a la transformación */
        }


        .panel-card-flipper:hover .card-front .card-icon {
            transform: scale(1.2) translateY(-4px) rotate(-5deg);
            /* Escala, eleva y rota ligeramente */
            text-shadow: 0 0 12px rgba(var(--purple-primary-rgb), 0.6);
            /* Un "glow" con el color primario */
        }

        .panel-card.card-front {

            background: linear-gradient(170deg,
                    /* Mantenemos el ángulo dinámico */
                    var(--gradient-card-front-start-deep-purple) 0%,
                    /* Comienza con el lila notable */
                    var(--gradient-card-front-end-light-purple) 45%
                    /* Se desvanece al lila muy pálido, extendiendo el color */
                );
        }

        .panel-card .card-body {
            padding: 30px 25px;
            text-align: center;
            /* Mantenido para el contenido de card-front */
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

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

        /* Estilos para el efecto de giro (FLIP) */
        .panel-card-flipper {
            perspective: 1200px;
            position: relative;
            width: 100%;
            height: 260px;
            /* Altura fija para las tarjetas con flip. AJUSTA ESTA ALTURA según tu contenido. */
            cursor: pointer;
            /*Opacidad por defecto para todas las tarjetas, las hace "secundarias" inicialmente */
            opacity: var(--card-dimmed-opacity);
            /* Transición para la opacidad */
            transition: opacity 0.35s ease-in-out;
        }

        /*  La tarjeta que contiene el enlace con la cara trasera de acción principal (Cuestionario) NO estará atenuada. */
        .panel-card-flipper:has(a > .card-back-action) {
            opacity: 1;
            /* El cuestionario activo es completamente opaco */
        }

        /* Cuando el ratón pasa sobre CUALQUIER tarjeta, recupera su opacidad total */
        .panel-card-flipper:hover {
            opacity: 1;
        }


        .panel-card-link-flipper {
            /* Para el enlace que envuelve el flipper del cuestionario */
            display: block;
            width: 100%;
            height: 100%;
            text-decoration: none;
            color: inherit;
        }


        .panel-card-flipper .card-front,
        .panel-card-flipper .card-back {
            background-color: var(--purple-secondary);
            /* Este prevalecerá para el fondo de .card-back */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.4s;
            /* .panel-card ya aplica los estilos base de tarjeta (fondo, borde, radius, shadow) */

        }

        .panel-card-flipper .card-front {
            z-index: 2;
            transform: rotateY(0deg);
            transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)0.4s,
                box-shadow 0.25s ease-out,
                border-color 0.25s ease-out;
        }

        .panel-card-flipper .card-back {
            transform: rotateY(180deg);
            background-color: var(--purple-secondary);
            /* Fondo para la cara trasera "PRÓXIMAMENTE" */
            color: var(--text-on-purple);
            /* Las clases de Bootstrap d-flex, align-items-center, justify-content-center se usan en el HTML */
        }

        .panel-card-flipper:hover .card-front {
            transform: rotateY(-180deg);
        }

        .panel-card-flipper:hover .card-back {
            transform: rotateY(0deg);
            box-shadow: 0 10px 20px var(--card-hover-shadow);
            /* Sombra al mostrarse la cara trasera */
        }

        /*  Cara trasera "¡COMENZAR YA!" (solo la del Cuestionario) */
        .panel-card-flipper:has(a > .card-back-action) .card-back-action {
            background: linear-gradient(135deg, var(--purple-primary) 0%, var(--purple-secondary) 100%);
        }

        /*  Efecto de hover mejorado para la cara trasera "¡COMENZAR YA!" */
        .panel-card-flipper:has(a > .card-back-action):hover .card-back-action {
            box-shadow: 0 10px 25px var(--card-hover-shadow),
                /* Sombra base un poco más intensa */
                0 0 20px var(--purple-primary);
            /* Resplandor morado añadido */
            filter: brightness(1.15);
        }

        .soon-text-flip,
        .action-text-flip {
            font-family: 'Dancing Script', cursive;
            font-size: 2.2rem;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            padding: 15px;
            text-align: center;
        }

        /* Animación de pulso para el texto "¡COMENZAR YA!" (opcional) */
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

        /* NUEVO: Aplicar animación al texto de acción cuando la tarjeta está hover y es la de acción */
        .panel-card-flipper:has(a > .card-back-action):hover .action-text-flip {
            animation: pulse-glow-text 2s infinite ease-in-out;
        }

        /* .card-back-action ya tiene un fondo específico por la regla con :has() */
        /* Ya no es necesaria la regla separada .card-back-action { background-color: var(--purple-primary); } */


        .button-disabled {
            background-color: red;
            opacity: 0.6;
            /* Mantenemos la opacidad para la tarjeta deshabilitada explícitamente */
            pointer-events: none;
            border-color: red;
            height: 260px;
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
        }

        .button-disabled .card-icon {
            opacity: 0.5;
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

    <header class="header-panel">
        <h1>
            <h1>¡Hola, {{empty($displayNick) ? $nick : $displayNick }}!</h1>
        </h1>
        <p>Bienvenid@ a tu espacio en HairLife</p>
    </header>

    <section class="buttons-panel-section">
        <div class="container cards-container">
            <div class="row justify-content-center g-4 g-lg-5">

                {{-- Tarjeta "Mi Pelo" --}}
                <div class="col-lg-4 col-md-6 col-sm-10 d-flex"> {{-- d-flex para alinear tarjetas si tienen alturas variables --}}
                    <div class="panel-card-flipper w-100">
                        <div class="card panel-card card-front">
                            <div class="card-body"> {{-- Bootstrap text-center se aplica aquí o en el padre si todo es centrado --}}
                                <i class="bi bi-person-hearts card-icon"></i>
                                <span class="button-title">Mi Pelo</span>
                                <span class="button-description">Gestiona tu perfil capilar.</span>
                            </div>
                        </div>
                        <div class="card panel-card card-back d-flex align-items-center justify-content-center">
                            <span class="soon-text-flip">PRÓXIMAMENTE</span>
                        </div>
                    </div>
                </div>

                {{-- Tarjeta "Cuestionario" --}}
                <div class="col-lg-4 col-md-6 col-sm-10 d-flex">
                    <div class="panel-card-flipper w-100">
                        @if ($cuestionarioOficial)
                        <a href="{{ route('cuestionarios.mostrarParaNick', ['nick' => $nick, 'id_cuestionario' => $cuestionarioOficial->id]) }}" class="panel-card-link-flipper">
                            <div class="card panel-card card-front">
                                <div class="card-body">
                                    <i class="bi bi-clipboard2-pulse card-icon"></i>
                                    <span class="button-title">Cuestionario</span>
                                    <span class="button-description">Obtén recomendaciones personalizadas.</span>
                                </div>
                            </div>
                            <div class="card panel-card card-back card-back-action d-flex align-items-center justify-content-center">
                                <span class="action-text-flip">¡COMENZAR YA!</span>
                            </div>
                        </a>
                        @else
                        {{-- Fallback si el cuestionario no está disponible (sin efecto de giro) --}}
                        <div class="card panel-card button-disabled"> {{-- No necesita panel-card-flipper --}}
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

    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} HairLife. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>