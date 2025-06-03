<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título dinámico de la página. -->
    <title>Crear Nickname - HairLife</title>

    <!-- Importamos Bootstrap CSS. Esto nos proporciona un conjunto de estilos predefinidos
         que facilitan la creación de diseños responsivos y nos dan acceso a componentes
         de interfaz de usuario con un estilo consistente (como formularios y botones). -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Preconexión y carga de Google Fonts. Usamos 'Dancing Script' para títulos
         y 'Montserrat' para el texto general, lo que le da un toque estético a la página. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Definición de variables CSS para nuestra paleta de colores.
           Esto es una excelente práctica para mantener la consistencia en el diseño
           y facilitar cambios de color en el futuro, sin tener que buscar y reemplazar
           valores hexadecimales en todo el CSS. */
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
            /* Sombra para tarjetas. */
            --input-focus-border-color: var(--purple-secondary);
            --input-focus-box-shadow: 0 0 0 0.25rem rgba(147, 112, 219, 0.25);
            /* Efecto de foco para inputs. */

            /* Definimos los componentes RGB de los colores principales para usarlos en `rgba()`.
               Esto es necesario para que las sombras con opacidad funcionen correctamente. */
            --purple-primary-rgb: 102, 51, 153;
            /* RGB de RebeccaPurple */
            --purple-dark-rgb: 75, 0, 130;
            /* RGB de Indigo */
        }

        /* Estilos generales para el cuerpo de la página.
           Usamos `min-height: 100vh` para que ocupe al menos el alto completo de la ventana.
           `position: relative` es necesario para el pseudo-elemento de fondo. */
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
            /* Evita el scroll horizontal si las tarjetas laterales se desbordan. */
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
            /* Lo coloca detrás de todo el contenido. */
            pointer-events: none;
            /* Asegura que no interfiera con interacciones del usuario. */
        }

        /* Contenedor principal de la página. Usa Flexbox para centrar todo su contenido
           (el encabezado, el área principal y las tarjetas laterales). */
        .page-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            width: 100%;
            position: relative;
            /* Necesario para posicionar las tarjetas laterales de forma absoluta dentro de él. */
            overflow: hidden;
            /* Oculta cualquier parte de las tarjetas laterales que se salga del contenedor. */
        }

        /* Estilos para el encabezado principal de la página. */
        .main-header {
            text-align: center;
            margin-bottom: 30px;
            z-index: 1;
            /* Asegura que esté sobre el fondo. */
        }

        /* Estilos para el título "HairLife" en el encabezado. */
        .hairlife-title {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 6rem;
            font-weight: 700;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            /* Sombra de texto. */
        }

        /* Área principal de contenido que contiene el formulario de creación de nick. */
        .main-content-area {
            width: 100%;
            z-index: 1;
            /* Asegura que esté sobre el fondo. */
        }

        /* Contenedor del formulario de creación de nick.
           Estilizado como una "tarjeta" con fondo blanco, bordes redondeados y sombra.
           `margin: 0 auto` lo centra horizontalmente. */
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
        }

        /* Estilos para el título "Crear Nickname". */
        .page-title {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 3.0rem;
            margin-bottom: 25px;
        }

        /* Estilos personalizados para el input del formulario.
           Combina un estilo base con el efecto de foco de Bootstrap. */
        .form-control-custom {
            border-radius: 8px;
            border: 1px solid var(--purple-light);
            padding: 10px 15px;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        /* Efecto de foco para el input. Utiliza las variables definidas en `:root`. */
        .form-control-custom:focus {
            border-color: var(--input-focus-border-color);
            box-shadow: var(--input-focus-box-shadow);
            outline: 0;
        }

        /* Estilos para el botón de "Acceder".
           Utiliza las clases `btn` de Bootstrap para el comportamiento base,
           y luego se personaliza con nuestros colores y un efecto de hover. */
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

        /* Efecto de hover para el botón de "Acceder". */
        .btn-acceder:hover {
            background-color: var(--purple-dark);
            border-color: var(--purple-dark);
            color: var(--text-on-purple);
            transform: translateY(-2px);
            /* Pequeño efecto de elevación. */
        }

        /* Estilos para el mensaje de error. */
        .error-message {
            background-color: rgba(220, 53, 69, 0.1);
            /* Fondo rojo claro. */
            color: #842029;
            /* Texto rojo oscuro. */
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }

        /* --- Estilos para las Tarjetas Decorativas que se Voltean (Flip Cards) --- */
        .flip-card {
            background-color: transparent;
            width: 360px;
            /* Ancho fijo de la tarjeta. */
            height: 560px;
            /* Alto fijo de la tarjeta. */
            perspective: 1000px;
            /* Necesario para el efecto 3D de giro. */
            border-radius: 10px;
            z-index: 2;
            /* Asegura que estén sobre el contenido principal. */
        }

        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.7s;
            /* Transición para el giro. */
            transform-style: preserve-3d;
            /* Mantiene el contenido 3D durante la transformación. */
        }

        /* Al pasar el ratón sobre la tarjeta, el contenido interno gira 180 grados. */
        .flip-card:hover .flip-card-inner {
            transform: rotateY(180deg);
        }

        /* Estilos comunes para las caras frontal y trasera de la tarjeta.
           `backface-visibility: hidden` es crucial para que la cara opuesta no sea visible
           durante la animación de giro. */
        .flip-card-front,
        .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            /* Compatibilidad con navegadores antiguos. */
            backface-visibility: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(var(--purple-primary-rgb), 0.25);
            /* Sombra de la tarjeta. */
        }

        /* Estilos para la cara frontal de la tarjeta. Incluye una imagen de fondo. */
        .flip-card-front {
            background-color: var(--purple-light);
            background-size: cover;
            background-position: center;
            color: white;
        }

        /* Estilos para la cara trasera de la tarjeta.
           Inicialmente está girada 180 grados para que no sea visible.
           Usa Flexbox para centrar su contenido. */
        .flip-card-back {
            color: var(--text-on-purple);
            transform: rotateY(180deg);
            /* Gira 180 grados para estar "oculta" inicialmente. */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Montserrat', sans-serif;
            background-size: cover;
            background-position: center;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Pseudo-elemento para crear una superposición de gradiente en la cara trasera. */
        .flip-card-back::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(var(--purple-primary-rgb), 0.65), rgba(var(--purple-dark-rgb), 0.8));
            border-radius: 10px;
            /* Coincide con el radio de la tarjeta. */
            z-index: -1;
            /* Se coloca detrás del contenido de la cara trasera. */
        }

        /* Estilos para el título y el párrafo en la cara trasera. */
        .flip-card-back h3 {
            font-family: 'Dancing Script', cursive;
            font-size: 3rem;
            margin-bottom: 8px;
            text-shadow: 0px 0px 2px rgba(0, 0, 0, 0.7), 1px 1px 5px rgba(0, 0, 0, 0.8), 0 0 10px var(--purple-medium);
            z-index: 2;
            color: white;
        }

        .flip-card-back p {
            font-size: 1.3rem;
            font-weight: 500;
            margin-bottom: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.9);
            position: relative;
            z-index: 2;
            color: whitesmoke;
        }

        /* --- Posicionamiento específico para las tarjetas laterales --- */
        /* Estas clases posicionan las tarjetas decorativas a los lados del contenido principal. */
        .flip-card-page-left {
            position: absolute;
            left: 40px;
            /* Distancia desde el borde izquierdo del `page-container`. */
            top: 50%;
            transform: translateY(-50%);
            /* Centra verticalmente. */
        }

        .flip-card-page-right {
            position: absolute;
            right: 40px;
            /* Distancia desde el borde derecho del `page-container`. */
            top: 50%;
            transform: translateY(-50%);
            /* Centra verticalmente. */
        }

        /* --- Media Queries para Responsividad --- */
        /* Ajustes para pantallas medianas (hasta 1200px de ancho). */
        @media (max-width: 1200px) {
            .flip-card-page-left {
                left: 20px;
                /* Acercamos las tarjetas a los bordes. */
            }

            .flip-card-page-right {
                right: 20px;
                /* Acercamos las tarjetas a los bordes. */
            }

            .flip-card {
                width: 250px;
                /* Reducimos el tamaño de las tarjetas. */
                height: 430px;
            }
        }

        /* Ajustes para pantallas más pequeñas (hasta 992px de ancho).
           Aquí ocultamos las tarjetas laterales para no saturar la vista. */
        @media (max-width: 992px) {

            .flip-card-page-left,
            .flip-card-page-right {
                display: none;
                /* Oculta las tarjetas laterales. */
            }

            .hairlife-title {
                font-size: 5rem;
                /* Reduce el tamaño del título principal. */
            }

            .page-title {
                font-size: 2.5rem;
                /* Reduce el tamaño del título del formulario. */
            }

            .nick-creation-container {
                max-width: 100%;
                /* Permite que el contenedor del formulario ocupe todo el ancho. */
                padding: 20px;
                /* Ajusta el padding. */
            }
        }

        /* Estilos para el pie de página, que siempre se mantiene en la parte inferior. */
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
    <!-- Contenedor principal de la página, que gestiona el diseño general. -->
    <div class="page-container">

        <!-- Tarjeta decorativa izquierda con efecto de giro. -->
        <div class="flip-card flip-card-page-left">
            <div class="flip-card-inner">
                <!-- Cara frontal de la tarjeta con una imagen de fondo. -->
                <div class="flip-card-front" style="background-image: url('/storage/imagenes/crearnick.jpg');">
                </div>
                <!-- Cara trasera de la tarjeta con otra imagen de fondo y texto. -->
                <div class="flip-card-back" style="background-image: url('/storage/imagenes/pelofan.jpg');">
                    <h3>HairLife</h3>
                    <p>Estilo & Cuidado</p>
                </div>
            </div>
        </div>

        <!-- Encabezado principal de la aplicación. -->
        <header class="main-header">
            <h1 class="hairlife-title">HairLife</h1>
        </header>

        <!-- Área de contenido principal que contiene el formulario. -->
        <div class="main-content-area">
            <!-- Contenedor del formulario de creación de nick, estilizado como una tarjeta. -->
            <div class="nick-creation-container">
                <h2 class="page-title">Crear Nickname</h2>
                <!-- Formulario para enviar el nick. -->
                <form action="/guardar-nick" method="POST">
                    @csrf <!-- Directiva de Blade para protección CSRF, esencial para la seguridad. -->
                    <!-- Grupo de formulario de Bootstrap para el input del nick.
                         `mb-3` es una clase de utilidad de Bootstrap para añadir un margen inferior. -->
                    <div class="mb-3">
                        <!-- Input de texto para el nick.
                             `form-control` es una clase de Bootstrap que estiliza los inputs.
                             `form-control-custom` añade nuestros estilos personalizados. -->
                        <input type="text" name="nick" class="form-control form-control-custom" id="nickInput" placeholder="Ingrese su nick" required>
                    </div>
                    <!-- Botón de envío del formulario.
                         `btn` es la clase base de botón de Bootstrap.
                         `btn-acceder` es nuestra clase personalizada para el estilo de color.
                         `w-100` es una clase de utilidad de Bootstrap para hacer que el botón ocupe el 100% del ancho. -->
                    <button type="submit" class="btn btn-acceder w-100">Acceder</button>
                </form>
                <!-- Bloque de Blade para mostrar mensajes de error de sesión. -->
                @if(session('error'))
                <div class="error-message mt-3">
                    {{ session('error') }}
                </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta decorativa derecha con efecto de giro. -->
        <div class="flip-card flip-card-page-right">
            <div class="flip-card-inner">
                <!-- Cara frontal de la tarjeta con una imagen de fondo. -->
                <div class="flip-card-front" style="background-image: url('/storage/imagenes/logocrear.jpeg');">
                </div>
                <!-- Cara trasera de la tarjeta con otra imagen de fondo y texto. -->
                <div class="flip-card-back" style="background-image: url('/storage/imagenes/pelofan1.jpg');">
                    <h3>HairLife</h3>
                    <p>Innovación Capilar</p>
                </div>
            </div>
        </div>

    </div>
    <!-- Pie de página de la web. -->
    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} HairLife. Todos los derechos reservados.</p>
    </footer>

</body>

</html>