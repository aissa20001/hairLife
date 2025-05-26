HTML

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
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-light-bg);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header-panel {
            background: linear-gradient(135deg, var(--purple-primary), var(--purple-secondary)), url('https://placehold.co/1920x400/4c0b56/8e44ad.png?text=HairLife+Banner&font=dancing-script') no-repeat center center;
            background-size: cover;
            background-blend-mode: overlay;
            color: var(--text-on-purple);
            padding: 60px 20px;
            text-align: center;
            position: relative;
        }

        .header-panel h1 {
            font-family: 'Dancing Script', cursive;
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .header-panel p {
            font-size: 1.4rem;
            opacity: 0.95;
            margin-bottom: 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .buttons-panel-section {
            padding-top: 50px;
            padding-bottom: 50px;
            background-color: var(--section-bg);
            border-bottom: 1px solid #eee;
            border-top: 1px solid #eee;
        }

        .cards-container {
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .panel-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }

        .panel-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border-color);
            border-radius: 15px;
            box-shadow: 0 6px 12px var(--card-shadow);
            transition: transform 0.25s ease-in-out, box-shadow 0.25s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .panel-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 10px 20px var(--card-hover-shadow);
        }

        .panel-card .card-icon {
            font-size: 3rem;
            color: var(--icon-color);
            margin-bottom: 15px;
            transition: transform 0.25s ease-in-out;
        }

        .panel-card:hover .card-icon {
            transform: scale(1.1);
        }

        .panel-card .card-body {
            padding: 30px 25px;
            text-align: center;
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

        .button-disabled {
            background-color: #f8f9fa;
            opacity: 0.6;
            pointer-events: none;
            border-color: #e0e0e0;
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
            padding: 30px 0;
            text-align: center;
            font-size: 0.9em;
            margin-top: auto;
        }

        .site-footer p {
            margin-bottom: 0;
        }
    </style>
</head>

<body>

    <header class="header-panel">
        <h1>¡Hola, {{ htmlspecialchars($nick) }}!</h1>
        <p>Bienvenid@ a tu espacio en HairLife</p>
    </header>

    <section class="buttons-panel-section">
        <div class="container cards-container">
            {{-- LÍNEA CORREGIDA --}}
            <div class="row justify-content-center g-4 g-lg-5">

                {{-- LÍNEA CORREGIDA --}}
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <a href="{{ route('user.mipelo', ['nick' => $nick]) }}" class="panel-card-link">
                        <div class="card panel-card">
                            <div class="card-body">
                                <i class="bi bi-person-hearts card-icon"></i>
                                <span class="button-title">Mi Pelo</span>
                                <span class="button-description">Gestiona tu perfil capilar. (En desarrollo)</span>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- LÍNEA CORREGIDA --}}
                <div class="col-lg-4 col-md-6 col-sm-10">
                    @if ($cuestionarioOficial)
                    <a href="{{ route('cuestionarios.mostrarParaNick', ['nick' => $nick, 'id_cuestionario' => $cuestionarioOficial->id]) }}" class="panel-card-link">
                        <div class="card panel-card">
                            <div class="card-body">
                                <i class="bi bi-clipboard2-pulse card-icon"></i>
                                <span class="button-title">Cuestionario</span>
                                <span class="button-description">Obtén recomendaciones personalizadas.</span>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="card panel-card button-disabled">
                        <div class="card-body">
                            <i class="bi bi-clipboard2-x card-icon"></i>
                            <span class="button-title">Cuestionario</span>
                            <span class="button-description">No disponible actualmente.</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- LÍNEA CORREGIDA --}}
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <a href="{{ route('user.peinadoscortes', ['nick' => $nick]) }}" class="panel-card-link">
                        <div class="card panel-card">
                            <div class="card-body">
                                <i class="bi bi-scissors card-icon"></i>
                                <span class="button-title">Peinados y Cortes</span>
                                <span class="button-description">Inspírate para tu nuevo look. (En desarrollo)</span>
                            </div>
                        </div>
                    </a>
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