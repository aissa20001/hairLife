<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HairLife</title>

    <!-- Importamos Bootstrap CSS. Esto nos proporciona un framework de estilos
         que facilita la creación de diseños responsivos y nos da acceso a componentes
         de interfaz de usuario con un estilo consistente (como formularios, botones y alertas). -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Preconexión y carga de Google Fonts. Usamos 'Dancing Script' para títulos
         y 'Montserrat' para el texto general, lo que le da un toque estético a la página. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Definición de variables CSS globales para la paleta de colores y temas.
           Esto es una excelente práctica para mantener la consistencia en el diseño
           y facilitar cambios de color de forma centralizada en el futuro. */
        :root {
            --morado-primario: RebeccaPurple;
            --morado-secundario: MediumPurple;
            --morado-oscuro: Indigo;
            --morado-medio: MediumSlateBlue;
            --morado-claro: Thistle;
            --morado-muy-claro: Lavender;
            --fondo-pagina: GhostWhite;
            /* Color de fondo general de la página. */

            /* Versiones RGB de los colores morados principales para usar en transparencias (rgba). */
            --morado-primario-rgb: 102, 51, 153;
            --morado-secundario-rgb: 147, 112, 219;

            /* Otros colores base. */
            --texto-sobre-morado: white;
            /* Color de texto para usar sobre fondos morados oscuros. */
            --fondo-tarjeta: white;
            /* Color de fondo para contenedores tipo "tarjeta". */
            --color-texto-principal: #333;
            --color-texto-secundario: #555;
            --color-borde-suave: var(--morado-claro);
            /* Color para bordes sutiles. */

            /* Sombra para la tarjeta de login. */
            --color-sombra-tarjeta: rgba(var(--morado-primario-rgb), 0.2);

            /* Variables de foco para campos de entrada (inputs). */
            --borde-input-foco: var(--morado-secundario);
            --sombra-input-foco: 0 0 0 0.25rem rgba(var(--morado-secundario-rgb), 0.25);
        }

        /* Estilos generales para el cuerpo (body) de la página.
           Usamos `display: flex` y `flex-direction: column` para que el contenido
           principal y el pie de página se organicen verticalmente.
           `min-height: 100vh` asegura que el cuerpo ocupe al menos toda la altura de la pantalla. */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--fondo-pagina);
            font-family: 'Montserrat', sans-serif;
            position: relative;
            z-index: 0;
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

        /* Contenedor principal del contenido de login.
           `flex-grow: 1` hace que este div ocupe todo el espacio vertical disponible,
           empujando el footer hacia abajo. Usa Flexbox para centrar el `login-container`. */
        .main-login-wrapper {
            flex-grow: 1;
            display: flex;
            align-items: center;
            /* Centra el `login-container` verticalmente. */
            justify-content: center;
            /* Centra el `login-container` horizontalmente. */
            width: 100%;
            padding: 20px 0;
        }

        /* Contenedor principal de la sección de login.
           Utiliza `display: flex` y `flex-wrap: wrap` para organizar la sección de imagen
           y la sección de formulario. Esto permite que se muestren lado a lado en pantallas
           grandes y se apilen en pantallas más pequeñas (gracias a `flex-wrap`). */
        .login-container {
            display: flex;
            flex-wrap: wrap;
            background-color: var(--fondo-tarjeta);
            border-radius: 12px;
            box-shadow: 0 8px 24px var(--color-sombra-tarjeta);
            overflow: hidden;
            /* Asegura que el contenido no se desborde de los bordes redondeados. */
            max-width: 950px;
            width: 90%;
            /* Ancho relativo para responsividad. */
            margin-top: 20px;
            margin-bottom: 20px;
        }

        /* Sección para la imagen decorativa del login.
           `flex: 1 1 40%` controla cómo se reparte el espacio en Flexbox.
           `order: 2` la coloca a la derecha en desktop (ya que el formulario es `order: 1`). */
        .login-image-section {
            flex: 1 1 40%;
            background-image: url('/storage/imagenes/logo_morado.png');
            /* Imagen de fondo. */
            background-repeat: no-repeat;
            background-size: cover;
            /* Asegura que la imagen cubra toda la sección. */
            background-position: center;
            min-height: 450px;
            order: 2;
        }

        /* Sección para el formulario de login.
           `flex: 1 1 60%` le da más espacio que a la imagen.
           Usa Flexbox para centrar el contenido del formulario verticalmente.
           `order: 1` la coloca a la izquierda en desktop. */
        .login-form-section {
            flex: 1 1 60%;
            padding: 40px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            order: 1;
        }

        /* Estilos para el título principal de bienvenida. */
        .welcome-title {
            color: var(--morado-primario);
            font-family: 'Dancing Script', cursive;
            font-weight: 700;
            font-size: 6rem;
            text-align: center;
            margin-bottom: 0px;
            line-height: 1.3;
        }

        /* Estilos para el subtítulo de bienvenida. */
        .welcome-subtitle {
            color: var(--morado-secundario);
            font-family: 'Montserrat', sans-serif;
            font-size: 1.15rem;
            text-align: center;
            margin-bottom: 35px;
            margin-top: 8px;
            font-weight: 400;
        }

        /* Estilo para el título dentro del formulario ("Iniciar sesión"). */
        .login-form-section h2 {
            color: var(--morado-oscuro);
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.9rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        /* Estilo para las etiquetas de los campos del formulario. */
        .form-label {
            color: var(--morado-oscuro);
            font-weight: 500;
        }

        /* Estilos base para los campos de entrada del formulario (inputs).
           La clase `form-control` es proporcionada por Bootstrap para un estilo consistente. */
        .form-control {
            border-radius: 6px;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--color-borde-suave);
        }

        /* Estilos para los campos de entrada cuando reciben foco.
           Utilizan las variables de foco definidas en `:root` para un efecto visual consistente. */
        .form-control:focus {
            border-color: var(--borde-input-foco);
            box-shadow: var(--sombra-input-foco);
        }

        /* Estilos para el botón principal de acción ("Iniciar sesión").
           Sobreescribe los estilos por defecto de `btn-primary` de Bootstrap con nuestros colores. */
        .btn-primary {
            background-color: var(--morado-primario);
            border-color: var(--morado-primario);
            color: var(--texto-sobre-morado);
            padding: 14px 20px;
            font-size: 1.2rem;
            font-weight: 500;
            border-radius: 6px;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        /* Estilos para el botón principal cuando el ratón está encima o tiene foco. */
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--morado-oscuro);
            border-color: var(--morado-oscuro);
            color: var(--texto-sobre-morado);
        }

        /* Ajustes de diseño para pantallas más pequeñas (responsividad). */
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
                /* La imagen aparece arriba en móvil. */
            }

            .login-form-section {
                flex-basis: 100%;
                /* El formulario ocupa todo el ancho disponible. */
                padding: 30px 25px;
                /* Menos espaciado interno. */
                order: 2;
                /* El formulario aparece abajo en móvil. */
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

        /* Ajustes para móviles pequeños (hasta 480px de ancho). */
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

        /* Estilos para el pie de página. */
        .site-footer {
            background-color: var(--morado-oscuro);
            color: rgba(255, 255, 255, 0.8);
            padding: 15px 0;
            text-align: center;
            font-size: 0.9em;
            width: 100%;
            flex-shrink: 0;
            /* Evita que el footer se encoja si el contenido es muy grande. */
        }
    </style>
</head>

<body>

    <!-- Contenedor principal que envuelve todo el contenido de login.
         Utiliza Flexbox para centrar el `login-container` en la pantalla. -->
    <div class="main-login-wrapper">
        <!-- Contenedor principal de la tarjeta de login.
             Usa `display: flex` para organizar la imagen y el formulario lado a lado
             en desktop, y `flex-wrap` para que se apilen en móvil. -->
        <div class="login-container">
            <!-- Sección de la imagen decorativa del login. La imagen se define en el CSS. -->
            <div class="login-image-section">
            </div>
            <!-- Sección del formulario de login. -->
            <div class="login-form-section">
                <!-- Título y subtítulo de bienvenida. -->
                <h1 class="welcome-title">Bienvenid@s a HairLife</h1>
                <p class="welcome-subtitle">Asesoramiento capilar</p>
                <h2>Iniciar sesión</h2>

                <!-- Bloque de errores de validación de Laravel.
                     Si hay errores, se muestra una alerta de Bootstrap (`alert alert-danger`).
                     `alert-dismissible fade show` permite que el usuario cierre la alerta. -->
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <!-- Botón de cierre de la alerta, proporcionado por Bootstrap. -->
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Mensaje de error de login específico de la sesión. -->
                @if (session('error_login'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error_login') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Formulario de login. -->
                <form action="/login" method="POST">
                    @csrf <!-- Directiva de Blade para protección CSRF, esencial para la seguridad. -->
                    <!-- Grupo de formulario para el nombre de usuario.
                         `mb-3` es una clase de utilidad de Bootstrap para añadir un margen inferior. -->
                    <div class="mb-3">
                        <label for="nombreUsuario" class="form-label">Nombre de usuario</label>
                        <!-- Input de texto para el nombre de usuario.
                             `form-control` es una clase de Bootstrap que estiliza los inputs. -->
                        <input type="text" class="form-control" id="nombreUsuario" name="Nombre" placeholder="Tu nombre de usuario" required>
                    </div>
                    <!-- Grupo de formulario para la contraseña. -->
                    <div class="mb-3">
                        <label for="claveUsuario" class="form-label">Clave</label>
                        <input type="password" class="form-control" id="claveUsuario" name="Clave" placeholder="Tu clave" required>
                    </div>
                    <!-- Contenedor para el botón de envío.
                         `d-grid` de Bootstrap hace que el botón ocupe todo el ancho disponible.
                         `mt-4` añade un margen superior. -->
                    <div class="d-grid mt-4">
                        <!-- Botón de envío del formulario.
                             `btn` es la clase base de botón de Bootstrap.
                             `btn-primary` aplica el estilo de botón primario. -->
                        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pie de página de la web. -->
    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} HairLife. Todos los derechos reservados.</p>
    </footer>

</body>

</html>