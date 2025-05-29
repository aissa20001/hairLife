<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nickname - HairLife</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* ... (tus variables :root y estilos generales de body, body::before sin cambios) ... */
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
            --text-color-primary: #333;
            --text-color-secondary: #555;
            --border-color: var(--purple-light);
            --card-shadow-elegant: rgba(102, 51, 153, 0.15);
            --input-focus-border-color: var(--purple-secondary);
            --input-focus-box-shadow: 0 0 0 0.25rem rgba(147, 112, 219, 0.25);
            --purple-primary-rgb: 102, 51, 153;
            /* Para RebeccaPurple */
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: var(--text-color-primary);
            margin: 0;
            padding: 0;
            position: relative;
            z-index: 0;
            min-height: 100vh;
            overflow-x: hidden;
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

        .page-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            /* Padding para el contenido central, no afectará a las tarjetas absolutas */
            width: 100%;
            position: relative;
            /* Necesario para posicionar las tarjetas de forma absoluta dentro de él */
            overflow: hidden;
            /* Para evitar que las tarjetas causen scroll si el viewport es muy justo */
        }

        .main-header {
            text-align: center;
            margin-bottom: 30px;
            z-index: 1;
            /* Para que esté sobre el fondo pero potencialmente debajo de las tarjetas si se superponen */
        }

        .hairlife-title {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 6rem;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            /* Sombra más marcada */
        }

        .main-content-area {
            /* Ya no necesita flex para las imágenes laterales, solo para centrar el nick-creation-container si fuera necesario */
            /* display: flex; */
            /* align-items: center; */
            /* justify-content: center; */
            width: 100%;
            /* gap: 20px; */
            /* Ya no es necesario aquí */
            z-index: 1;
        }

        .nick-creation-container {
            background-color: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 8px 25px var(--card-shadow-elegant);
            max-width: 450px;
            width: 100%;
            padding: 30px 40px;
            text-align: center;
            margin: 0 auto;
            /* Para centrarlo si .main-content-area no es flex-center */
        }

        .page-title {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 3.0rem;
            margin-bottom: 25px;
        }

        .form-control-custom {
            border-radius: 8px;
            border: 1px solid var(--purple-light);
            padding: 10px 15px;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control-custom:focus {
            border-color: var(--input-focus-border-color);
            box-shadow: var(--input-focus-box-shadow);
            outline: 0;
        }

        .btn-acceder {
            background-color: var(--purple-primary);
            border-color: var(--purple-primary);
            color: var(--text-on-purple);
            font-weight: 700;
            font-size: 1.1rem;
            padding: 10px 30px;
            border-radius: 8px;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, transform 0.15s ease;
        }

        .btn-acceder:hover {
            background-color: var(--purple-dark);
            border-color: var(--purple-dark);
            color: var(--text-on-purple);
            transform: translateY(-2px);
        }

        .error-message {
            background-color: rgba(220, 53, 69, 0.1);
            color: #842029;
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        /* --- Estilos para las Tarjetas que se Voltean --- */
        .flip-card {
            background-color: transparent;
            width: 360px;
            /* Ancho rectangular */
            height: 560px;
            /* Alto rectangular */
            perspective: 1000px;
            border-radius: 10px;
            z-index: 2;
            /* Para que estén sobre el contenido central si hay superposición accidental */
        }

        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.7s;
            transform-style: preserve-3d;
        }

        .flip-card:hover .flip-card-inner {
            transform: rotateY(180deg);
        }

        .flip-card-front,
        .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(var(--purple-primary-rgb), 0.25);
        }

        .flip-card-front {
            background-color: var(--purple-light);
            background-size: cover;
            background-position: center;
            color: white;
        }

        .flip-card-back {
            background-color: var(--purple-primary);
            color: var(--text-on-purple);
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            font-family: 'Montserrat', sans-serif;
        }

        .flip-card-back h3 {
            font-family: 'Dancing Script', cursive;
            font-size: 1.8rem;
            /* Ajustado para el tamaño de tarjeta */
            margin-bottom: 8px;
        }

        .flip-card-back p {
            font-size: 0.85rem;
            /* Ajustado para el tamaño de tarjeta */
            margin-bottom: 0;
        }

        /* --- Posicionamiento específico para las tarjetas laterales --- */
        .flip-card-page-left {
            position: absolute;
            left: 40px;
            /* Distancia desde el borde izquierdo de page-container */
            top: 50%;
            transform: translateY(-50%);
        }

        .flip-card-page-right {
            position: absolute;
            right: 40px;
            /* Distancia desde el borde derecho de page-container */
            top: 50%;
            transform: translateY(-50%);
        }

        /* --- Media Queries --- */
        @media (max-width: 1200px) {

            /* Ajusta este breakpoint si es necesario */
            .flip-card-page-left {
                left: 20px;
                /* Más cerca del borde en pantallas medianas */
            }

            .flip-card-page-right {
                right: 20px;
                /* Más cerca del borde en pantallas medianas */
            }

            .flip-card {
                /* Reducir ligeramente el tamaño de las tarjetas */
                width: 140px;
                height: 230px;
            }
        }

        @media (max-width: 992px) {

            /* En pantallas más pequeñas, ocultamos las tarjetas laterales para no saturar */
            .flip-card-page-left,
            .flip-card-page-right {
                display: none;
            }

            .hairlife-title {
                font-size: 3.5rem;
            }

            .page-title {
                font-size: 2.5rem;
            }

            .nick-creation-container {
                max-width: 100%;
                padding: 20px;
            }
        }

        /* La media query anterior para .main-content-area (apilar elementos) ya no es relevante
           para las tarjetas, pero la de .nick-creation-container y títulos sigue siendo útil. */
    </style>
</head>

<body>
    <div class="page-container">

        <div class="flip-card flip-card-page-left">
            <div class="flip-card-inner">
                <div class="flip-card-front" style="background-image: url('/storage/imagenes/crearnick.jpg');">
                </div>
                <div class="flip-card-back">
                    <h3>HairLife</h3>
                    <p>Estilo & Cuidado</p>
                </div>
            </div>
        </div>

        <header class="main-header">
            <h1 class="hairlife-title">HairLife</h1>
        </header>

        <div class="main-content-area">
            <div class="nick-creation-container">
                <h2 class="page-title">Crear Nickname</h2>
                <form action="/guardar-nick" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="nick" class="form-control form-control-custom" id="nickInput" placeholder="Ingrese su nick" required>
                    </div>
                    <button type="submit" class="btn btn-acceder w-100">Acceder</button>
                </form>
                @if(session('error'))
                <div class="error-message mt-3">
                    {{ session('error') }}
                </div>
                @endif
            </div>
        </div>

        <div class="flip-card flip-card-page-right">
            <div class="flip-card-inner">
                <div class="flip-card-front" style="background-image: url('/storage/imagenes/logocrear.jpeg');">
                </div>
                <div class="flip-card-back">
                    <h3>HairLife</h3>
                    <p>Innovación Capilar</p>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>