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
        :root {
            /* Paleta de Morados con Nombres de Colores */
            --purple-primary: RebeccaPurple;
            /* Morado principal para acciones */
            --purple-secondary: MediumPurple;
            /* Morado secundario para acentos */
            --purple-dark: Indigo;
            /* Morado oscuro para textos importantes */
            --purple-medium: MediumSlateBlue;
            /* Morado medio */
            --purple-light: Thistle;
            /* Morado claro para fondos sutiles o bordes */
            --purple-very-light: Lavender;
            /* Morado muy claro */
            --purple-background: GhostWhite;
            /* Fondo general de la página, casi blanco */

            /* Otros colores base */
            --text-on-purple: white;
            /* Texto para usar sobre fondos morados oscuros */
            --card-bg: white;
            /* Fondo para contenedores tipo tarjeta */
            --text-color-primary: #333;
            /* Color de texto principal */
            --text-color-secondary: #555;
            /* Color de texto secundario */
            --border-color: var(--purple-light);
            /* Color de borde suave */

            /* Sombra elegante */
            --card-shadow-elegant: rgba(102, 51, 153, 0.15);
            /* Sombra con tono morado */

            /* Variables de foco para inputs */
            --input-focus-border-color: var(--purple-secondary);
            --input-focus-box-shadow: 0 0 0 0.25rem rgba(147, 112, 219, 0.25);
            /* Sombra de foco con tono MediumPurple */
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: var(--text-color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            margin: 0;
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

        .nick-creation-container {
            background-color: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 8px 25px var(--card-shadow-elegant);
            max-width: 500px;
            width: 100%;
            padding: 30px 40px;
            /* Más padding horizontal */
            text-align: center;
        }

        .page-title {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-dark);
            font-size: 3.5rem;
            /* Letras grandes para el título */
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 500;
            color: var(--purple-dark);
            margin-bottom: 8px;
            display: block;
            /* Para que ocupe su propia línea si es necesario */
        }

        .form-control-custom {
            /* Clase personalizada para el input */
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
            /* Quitar el outline por defecto del navegador */
        }

        .btn-acceder {
            /* Botón personalizado con Bootstrap y tema morado */
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
            /* Un morado más oscuro al pasar el ratón */
            border-color: var(--purple-dark);
            color: var(--text-on-purple);
            transform: translateY(-2px);
            /* Ligero efecto de elevación */
        }

        .error-message {
            /* Estilo para el mensaje de error */
            background-color: rgba(220, 53, 69, 0.1);
            /* Fondo rojo claro translúcido */
            color: #842029;
            /* Texto rojo oscuro para errores */
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="nick-creation-container">
        <h1 class="page-title">Crear Nickname</h1>

        <form action="/guardar-nick" method="POST"> @csrf <div class="mb-3">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>