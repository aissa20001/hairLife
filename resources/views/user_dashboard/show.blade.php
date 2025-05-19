<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de {{ htmlspecialchars($nick) }} - Mi Pelo</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .header-container {
            width: 100%;
            color: black;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-container h1 {
            margin: 0;
            font-size: 2.5em;
        }

        .header-container p {
            margin-top: 5px;
            font-size: 1.2em;
            opacity: 0.9;
        }

        .buttons-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
            width: 100%;
            max-width: 600px;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .panel-button {
            display: block;
            width: 100%;
            padding: 30px 20px;
            background-color: #ffffff;
            color: #333333;
            text-decoration: none;
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out, border-color 0.2s ease-in-out;
            box-sizing: border-box;
        }

        .panel-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);

        }

        .panel-button .button-title {
            display: block;
            margin-bottom: 8px;

        }

        .panel-button .button-description {
            font-size: 0.7em;
            color: #555555;
            font-weight: normal;
        }

        .button-disabled {
            background-color: #e9ecef;
            pointer-events: none;
        }

        .button-disabled .button-title {}

        .site-footer {
            margin-top: auto;
            padding: 25px 0;
            text-align: center;
            font-size: 0.9em;
            width: 100%;
        }

        @media (min-width: 768px) {
            .buttons-panel {
                flex-direction: row;
                justify-content: space-around;
                max-width: 900px;
            }

            .panel-button {
                width: calc(33.333% - 20px);
                min-height: 180px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <div class="header-container">
        <h1>¡Hola, {{ htmlspecialchars($nick) }}!</h1>
        <p>Selecciona una opción para continuar</p>
    </div>

    <div class="buttons-panel">
        {{-- Botón 1: Mi Pelo (Placeholder) --}}
        <a href="{{ route('user.mipelo', ['nick' => $nick]) }}" class="panel-button">
            <span class="button-title">Mi Pelo</span>
            <span class="button-description">Gestiona tu perfil capilar. (En desarrollo)</span>
        </a>

        {{-- Botón 2: Cuestionario (Funcional) --}}
        @if ($cuestionarioOficial)
        <a href="{{ route('cuestionarios.mostrarParaNick', ['nick' => $nick, 'id_cuestionario' => $cuestionarioOficial->id]) }}" class="panel-button">
            <span class="button-title">Cuestionario</span>
            <span class="button-description">Obtén recomendaciones personalizadas.</span>
        </a>
        @else
        {{-- Se muestra si no hay cuestionario activo --}}
        <div class="panel-button button-disabled">
            <span class="button-title">Cuestionario</span>
            <span class="button-description">No disponible actualmente.</span>
        </div>
        @endif

        {{-- Botón 3: Peinados y Cortes de Pelo (Placeholder) --}}
        <a href="{{ route('user.peinadoscortes', ['nick' => $nick]) }}" class="panel-button">
            <span class="button-title">Peinados y Cortes</span>
            <span class="button-description">Inspírate para tu nuevo look. (En desarrollo)</span>
        </a>
    </div>

    <footer class="site-footer">

    </footer>

</body>

</html>