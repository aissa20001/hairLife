<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HairLife</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Definición de variables globales de CSS para la paleta de colores y temas */
        :root {

            --morado-primario: RebeccaPurple;

            --morado-secundario: MediumPurple;

            --morado-oscuro: Indigo;

            --morado-medio: MediumSlateBlue;

            --morado-claro: Thistle;

            --morado-muy-claro: Lavender;

            /* Color de fondo general de la página, un blanco muy suave. (Ej: #F8F8FF) */

            /* --- Versiones RGB para hacer transparencias de los colores morados principales */
            --morado-primario-rgb: 102, 51, 153;
            --morado-secundario-rgb: 147, 112, 219;


            /* --- Otros colores base (en español) --- */
            --texto-sobre-morado: white;
            /* Color de texto para usar sobre fondos morados oscuros .*/
            --fondo-tarjeta: white;
            /* Color de fondo para contenedores tipo "tarjeta". */
            --color-texto-principal: #333;
            /* Color de texto oscuro principal (no usado directamente aquí, pero definido para consistencia). */
            --color-texto-secundario: #555;
            /* Color de texto oscuro secundario (no usado directamente aquí). */
            --color-borde-suave: var(--morado-claro);
            /* Color para bordes que no necesitan mucho énfasis. */

            /* --- Sombra para la tarjeta de login (en español) --- */
            --color-sombra-tarjeta: rgba(var(--morado-primario-rgb), 0.2);
            /* Sombra con un ligero tinte del morado primario. */

            /* --- Variables de foco para campos de entrada (inputs) (en español) --- */
            --borde-input-foco: var(--morado-secundario);
            /* Color del borde cuando un input está seleccionado. */
            --sombra-input-foco: 0 0 0 0.25rem rgba(var(--morado-secundario-rgb), 0.25);
            /* Sombra exterior para el input seleccionado, con tinte del morado secundario. */
        }

        /* --- Estilos generales para el cuerpo (body) de la página --- */
        body {
            display: flex;
            /* Activa Flexbox para centrar contenido. */
            align-items: center;
            /* Centra verticalmente el contenido hijo (el .login-container). */
            justify-content: center;
            /* Centra horizontalmente el contenido hijo. */
            min-height: 100vh;
            /* Asegura que el cuerpo ocupe al menos toda la altura de la pantalla. */
            background-color: var(--fondo-pagina);
            /* Aplica el color de fondo general. */
            font-family: 'Montserrat', sans-serif;
            /* Define la fuente principal para toda la página. */
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

        /* --- Contenedor principal de la sección de login --- */
        .login-container {
            display: flex;
            /* Organiza los hijos (sección de imagen y sección de formulario) en una fila. */
            flex-wrap: wrap;
            /* Permite que los hijos pasen a la siguiente línea si no caben. */
            background-color: var(--fondo-tarjeta);
            /* Fondo blanco para el contenedor. */
            border-radius: 12px;
            /* Bordes redondeados para suavizar la apariencia. */
            box-shadow: 0 8px 24px var(--color-sombra-tarjeta);
            /* Sombra para dar profundidad. */
            overflow: hidden;
            /* Asegura que el contenido que se desborde (ej. imagen) no rompa los bordes redondeados. */
            max-width: 950px;
            /* Ancho máximo del contenedor. */
            width: 90%;
            /* Ancho relativo, útil en pantallas más pequeñas que el max-width. */
            margin-top: 20px;
            /* Margen superior. */
            margin-bottom: 20px;
            /* Margen inferior. */
        }

        /* --- Sección para la imagen decorativa del login (lado derecho en desktop) --- */
        .login-image-section {
            flex: 1 1 40%;
            /* Controla cómo se reparte el espacio (crece, decrece, base del 40%). */
            /* ATENCIÓN: El color #8e44ad en la URL de la imagen de placeholder es un valor fijo.
               Si quieres que coincida con tu paleta de variables (ej. MediumPurple #9370DB, que es var(--morado-secundario)),
               deberás generar una nueva URL de placeholder con el color hexadecimal deseado
               o usar una imagen propia. CSS no puede cambiar colores dentro de una URL de imagen.
               Placeholder actual: https://placehold.co/600x800/8e44ad/ffffff?text=HairLife&font=dancing-script
               Para --morado-secundario (MediumPurple #9370DB): https://placehold.co/600x800/9370DB/ffffff?text=HairLife&font=dancing-script
            */
            background-image: url('storage/imagenes/logo_morado.png');
            /* Imagen de fondo. */
            background-size: cover;
            /* Asegura que la imagen cubra toda la sección, puede recortar partes. */
            background-position: center;
            /* Centra la imagen de fondo. */
            min-height: 450px;
            /* Altura mínima para esta sección. */
            order: 2;
            /* En desktop, esta sección aparecerá segunda (a la derecha). */
        }

        /* --- Sección para el formulario de login (lado izquierdo en desktop) --- */
        .login-form-section {
            flex: 1 1 60%;
            /* Ocupa más espacio que la imagen. */
            padding: 40px 50px;
            /* Espaciado interno. */
            display: flex;
            /* Activa Flexbox para sus hijos. */
            flex-direction: column;
            /* Organiza los hijos del formulario verticalmente. */
            justify-content: center;
            /* Centra los hijos del formulario verticalmente dentro de esta sección. */
            order: 1;
            /* En desktop, esta sección aparecerá primera (a la izquierda). */
        }

        /* --- Estilo para el título principal de bienvenida (ej: 'Bienvenid@s a HairLife') --- */
        .welcome-title {
            color: var(--morado-primario);
            /* Color del título. */
            font-family: 'Dancing Script', cursive;
            /* Fuente decorativa. */
            font-weight: 700;
            /* Grosor de la fuente. */
            font-size: 6rem;
            /* Tamaño grande para el título. */
            text-align: center;
            /* Texto centrado. */
            margin-bottom: 0px;
            /* Sin margen inferior (el subtítulo lo gestionará). */
            line-height: 1.3;
            /* Altura de línea. */
        }

        /* --- Estilo para el subtítulo de bienvenida (ej: 'Asesoramiento capilar') --- */
        .welcome-subtitle {
            color: var(--morado-secundario);
            /* Color del subtítulo. */
            font-family: 'Montserrat', sans-serif;
            /* Fuente estándar. */
            font-size: 1.15rem;
            /* Tamaño del subtítulo. */
            text-align: center;
            /* Texto centrado. */
            margin-bottom: 35px;
            /* Margen inferior para separar del formulario. */
            margin-top: 8px;
            /* Margen superior para separar del título principal. */
            font-weight: 400;
            /* Grosor de fuente normal. */
        }

        /* --- Estilo para el título dentro del formulario (ej: 'Iniciar sesión') --- */
        .login-form-section h2 {
            color: var(--morado-oscuro);
            /* Color del título "Iniciar sesión". */
            text-align: center;
            /* Texto centrado. */
            margin-bottom: 30px;
            /* Margen inferior. */
            font-size: 1.9rem;
            /* Tamaño del título. */
            font-family: 'Montserrat', sans-serif;
            /* Fuente estándar. */
            font-weight: 700;
            /* Fuente en negrita. */
        }

        /* --- Estilo para las etiquetas de los campos del formulario (ej: 'Nombre de usuario', 'Clave') --- */
        .form-label {
            color: var(--morado-oscuro);
            /* Color de las etiquetas. */
            font-weight: 500;
            /* Grosor de fuente medio. */
        }

        /* --- Estilos base para los campos de entrada del formulario (inputs) --- */
        .form-control {
            /* Clase de Bootstrap para inputs */
            border-radius: 6px;
            /* Bordes redondeados para los inputs. */
            padding: 0.6rem 0.75rem;
            /* Espaciado interno de los inputs. */
            border: 1px solid var(--color-borde-suave);
            /* Borde sutil por defecto. */
        }

        /* --- Estilos para los campos de entrada cuando reciben foco (el usuario hace clic o navega a ellos) --- */
        .form-control:focus {
            border-color: var(--borde-input-foco);
            /* Cambia el color del borde al enfocar. */
            box-shadow: var(--sombra-input-foco);
            /* Añade una sombra exterior al enfocar. */
        }

        /* --- Estilos para el botón principal de acción (Iniciar sesión), sobreescribiendo Bootstrap --- */
        .btn-primary {
            background-color: var(--morado-primario);
            /* Color de fondo del botón. */
            border-color: var(--morado-primario);
            /* Color del borde del botón. */
            color: var(--texto-sobre-morado);
            /* Color del texto del botón. */
            padding: 14px 20px;
            /* Espaciado interno del botón. */
            font-size: 1.2rem;
            /* Tamaño del texto del botón. */
            font-weight: 500;
            /* Grosor del texto del botón. */
            border-radius: 6px;
            /* Bordes redondeados del botón. */
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
            /* Transición suave para efectos hover/focus. */
        }

        /* --- Estilos para el botón principal cuando el ratón está encima o tiene foco --- */
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--morado-oscuro);
            /* Cambia el color de fondo a un morado más oscuro. */
            border-color: var(--morado-oscuro);
            /* Cambia el color del borde. */
            color: var(--texto-sobre-morado);
            /* Mantiene el color del texto. */
        }

        /* --- Ajustes de diseño para pantallas más pequeñas (responsividad) --- */
        /* Para tablets y móviles grandes en horizontal (hasta 768px de ancho) */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                /* Apila la imagen y el formulario verticalmente. */
                margin-top: 10px;
                margin-bottom: 10px;
                max-width: 450px;
                /* Contenedor más estrecho. */
            }

            .login-image-section {
                flex-basis: 100%;
                /* La imagen ocupa todo el ancho disponible. */
                min-height: 200px;
                /* Altura mínima reducida para la imagen. */
                order: 1;
                /* Imagen arriba en móvil. */
            }

            .login-form-section {
                flex-basis: 100%;
                /* El formulario ocupa todo el ancho disponible. */
                padding: 30px 25px;
                /* Menos espaciado interno. */
                order: 2;
                /* Formulario abajo en móvil. */
            }

            .welcome-title {
                font-size: 3rem;
                /* Tamaño de fuente reducido para el título principal. */
            }

            .welcome-subtitle {
                font-size: 1rem;
                /* Tamaño de fuente reducido para el subtítulo. */
            }

            .login-form-section h2 {
                font-size: 1.6rem;
                /* Tamaño de fuente reducido para "Iniciar sesión". */
            }
        }

        /* Para móviles pequeños (hasta 480px de ancho) */
        @media (max-width: 480px) {
            .welcome-title {
                font-size: 2.5rem;
                /* Aún más pequeño el título principal. */
            }

            .login-form-section {
                padding: 25px 20px;
                /* Menos espaciado interno. */
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="login-image-section">
        </div>


        <div class="login-form-section">
            <h1 class="welcome-title">Bienvenid@s a HairLife</h1>
            <p class="welcome-subtitle">Asesoramiento capilar</p>

            <h2>Iniciar sesión</h2>

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (session('error_login'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error_login') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nombreUsuario" class="form-label">Nombre de usuario</label>
                    <input type="text" class="form-control" id="nombreUsuario" name="Nombre" placeholder="Tu nombre de usuario" required>
                </div>
                <div class="mb-3">
                    <label for="claveUsuario" class="form-label">Clave</label>
                    <input type="password" class="form-control" id="claveUsuario" name="Clave" placeholder="Tu clave" required>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>