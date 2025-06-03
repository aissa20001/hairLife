<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ htmlspecialchars($cuestionario->titulo) }} - {{ htmlspecialchars($nick) }} - HairLife</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
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
            --border-color: var(--purple-very-light);
            --input-focus-color: var(--purple-secondary);
            --input-focus-box-shadow: rgba(142, 68, 173, 0.25);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: #333;
            line-height: 1.6;
            padding-top: 80px;
            /* Espacio para el botón home fijo */
            position: relative;
            /* Necesario para el pseudo-elemento ::before */
            z-index: 0;
            box-shadow:
                /* Sombra principal más grande y difusa para el efecto de "flotación" */
                0 15px 35px rgba(var(--purple-primary-rgb), 0, 7),
                /* Sombra secundaria más cercana y un poco más definida para el borde */
                0 5px 15px rgba(var(--purple-primary-rgb), 0, 8);
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
            /* Opacidad MUY BAJA para un efecto extremadamente sutil. Cámbiala a 0 si quieres que sea totalmente invisible. */

            z-index: -1;
            /* Se coloca detrás de todo el contenido del body */
            pointer-events: none;
            /* Para asegurar que no interfiera con clics u otras interacciones */
        }

        .home-button-container {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1030;
        }

        .home-button {
            background-color: var(--purple-primary);
            color: var(--text-on-purple);
            border: 1px solid var(--purple-dark);
            padding: 0.5rem 0.9rem;
            font-size: 1.2rem;
            border-radius: 0.5rem;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2),
                0 0 0 2px rgba(var(--text-on-purple), 0.2);
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        }

        .home-button:hover,
        .home-button:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
            border-color: var(--purple-dark);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3),
                0 0 0 2px rgba(var(--text-on-purple), 0.3);
            transform: translateY(-2px);

        }

        .questionnaire-main-container {
            max-width: 900px;
            margin: 30px auto;
            position: relative;
            z-index: 3;


        }

        .questionnaire-container {
            background-color: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(var(--purple-primary-rgb), 0.15),
                0 0 15px rgba(var(--purple-primary-rgb), 0.05);
            padding: 30px 35px;
            margin-bottom: 40px;
            position: relative;
            /* Si no lo tenía ya */
            z-index: 5;
        }

        .questionnaire-title {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-primary);
            text-align: center;
            font-size: 4em;
            margin-bottom: 0.5rem;
        }

        .questionnaire-subtitle {
            text-align: center;
            color: var(--purple-medium);
            margin-bottom: 25px;
            font-size: 1.15em;
        }

        .questionnaire-description {
            color: #444;
            font-size: 1em;
            margin-bottom: 35px;
            padding-left: 15px;
            border-left: 4px solid var(--purple-light);
        }

        .question-slider {
            position: relative;
            overflow: hidden;
            min-height: 350px;
            margin-bottom: 25px;
        }

        .pregunta-bloque {
            background-color: #fdfaff;
            /* Un fondo ligeramente diferente para el bloque de pregunta */
            border: 1px solid var(--purple-very-light);
            border-radius: 8px;
            padding: 25px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transform: translateX(100%);
            /* Estado inicial para entrar desde la derecha */
            transition: transform 0.5s ease-in-out, opacity 0.4s ease-in-out;
            visibility: hidden;
            display: flex;
            flex-direction: column;

        }

        .pregunta-bloque.active {
            opacity: 1;
            transform: translateX(0%);
            visibility: visible;
            z-index: 10;
            border: 2px solid var(--purple-primary);
            box-shadow: 0 6px 18px rgba(106, 13, 173, 0.2);
            background-color: var(--purple-light);
            /* Asegura que el bloque activo esté por encima de otros */
        }

        /* Esta clase podría usarse si se quiere una animación específica al salir a la izquierda,
           actualmente el JS lo maneja con transform directo.
        .pregunta-bloque.slide-out-left {
            transform: translateX(-100%);
            opacity: 0;
        }
        */

        .pregunta-enunciado {
            display: block;
            /* Ya es block por defecto en label, pero explícito */
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--purple-primary);
            font-size: 1.3em;
            flex-shrink: 0;
            /* Evita que el enunciado se encoja */
        }

        .pregunta-opciones-scrollable {
            overflow-y: auto;
            max-height: 220px;
            /* Altura máxima antes de que aparezca el scroll */
            padding-right: 10px;
            /* Espacio para la barra de scroll */
            flex-grow: 1;
            /* Ocupa el espacio vertical restante */
        }

        .pregunta-opciones-scrollable::-webkit-scrollbar {
            width: 8px;
        }

        .pregunta-opciones-scrollable::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .pregunta-opciones-scrollable::-webkit-scrollbar-thumb {
            background: var(--purple-light);
            border-radius: 10px;
        }

        .pregunta-opciones-scrollable::-webkit-scrollbar-thumb:hover {
            background: var(--purple-medium);
        }

        .form-check {
            margin-bottom: 0.85rem !important;
            /* Mantener !important si es necesario para sobreescribir Bootstrap */
        }

        .form-check-label {
            cursor: pointer;
            font-size: 1.05em;
        }

        .form-control {
            /* Estilo general para inputs de texto, textarea */
            font-size: 1em;
        }

        .form-check-input:checked {
            background-color: var(--purple-primary);
            border-color: var(--purple-primary);
        }

        .form-check-input:focus,
        .form-control:focus,
        .form-select:focus {
            border-color: var(--input-focus-color);
            box-shadow: 0 0 0 0.25rem var(--input-focus-box-shadow);
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            border-color: var(--border-color);
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .btn-nav {
            /* Para Anterior/Siguiente */
            background-color: var(--purple-secondary);
            border-color: var(--purple-secondary);
            min-width: 120px;
        }

        .btn-nav:hover,
        .btn-nav:focus {
            background-color: var(--purple-primary);
            border-color: var(--purple-primary);
        }

        .btn-nav:disabled {
            background-color: var(--purple-light);
            border-color: var(--purple-light);
            opacity: 0.7;
        }

        .btn-submit-final {
            background-color: var(--purple-primary);
            border-color: var(--purple-primary);
            min-width: 150px;
        }

        .btn-submit-final:hover,
        .btn-submit-final:focus {
            background-color: var(--purple-dark);
            border-color: var(--purple-dark);
        }

        .alert-danger {
            /* Ya viene con Bootstrap, pero se puede personalizar */
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-radius: 8px;
        }

        .error-text {
            color: #dc3545;
            /* Color de error estándar de Bootstrap */
            font-size: 0.875em;
            display: block;
            margin-top: 4px;
        }

        .site-footer {
            background-color: var(--purple-dark);
            background-size: cover;
            color: rgba(255, 255, 255, 0.8);
            padding: 5px 0;
            text-align: center;
            font-size: 0.9em;
            margin-top: auto;
            margin-bottom: auto;

        }
    </style>
</head>

<body>
    <div class="home-button-container">
        <a href="{{ route('user.dashboard', ['nick' => $nick]) }}" class="btn home-button" title="Volver al Panel">
            <i class="bi bi-house-fill"></i>
        </a>
    </div>

    <div class="questionnaire-main-container">
        <div class="questionnaire-container">
            <h1 class="questionnaire-title">{{ htmlspecialchars($cuestionario->titulo) }}</h1>
            <p class="questionnaire-subtitle">Respondiendo como: <strong>{{ htmlspecialchars($nick) }}</strong></p>

            @if(!empty($cuestionario->descripcion))
            <p class="questionnaire-description">{{ htmlspecialchars($cuestionario->descripcion) }}</p>
            @endif

            @if ($errors->any() && !$preguntas->isEmpty())
            <div class="alert alert-danger mb-4">
                <strong>Por favor, corrige los siguientes errores:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @php
            // Estas variables vienen del controlador
            $previousAnswers = $previousAnswers ?? [];
            $idPreguntaFiltro = $idPreguntaFiltro ?? null; // ID de la pregunta a no rellenar
            @endphp

            <form method="POST" action="{{ route('cuestionarios.enviarParaNick', ['nick' => $nick, 'id_cuestionario' => $cuestionario->id]) }}" id="formularioCuestionarioInteractivo">
                @csrf

                @if($preguntas->isEmpty())
                <p class="text-center my-5">Este cuestionario aún no tiene preguntas configuradas.</p>
                @else
                <div class="question-slider">
                    @foreach ($preguntas as $index => $pregunta)
                    @php
                    // Determinar si esta pregunta debe usar las respuestas previas o solo old()
                    $usarRespuestaPrevia = ($pregunta->id != $idPreguntaFiltro && isset($previousAnswers[$pregunta->id]));
                    $valorOld = old('respuestas.' . $pregunta->id);
                    @endphp
                    <div class="pregunta-bloque" id="pregunta-{{ $pregunta->id }}" data-pregunta-id="{{ $pregunta->id }}">
                        <label class="pregunta-enunciado mb-3">{{ $loop->iteration }}. {{ $pregunta->enunciado }}
                            @if(collect(json_decode($pregunta->reglas_validacion, true) ?? [])->has('required'))
                            <span class="text-danger">*</span>
                            @endif
                        </label>

                        <div class="pregunta-opciones-scrollable">
                            @if ($pregunta->tipo_input === 'radio' && $pregunta->opciones->isNotEmpty())
                            @foreach ($pregunta->opciones as $opcion)
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                    id="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}"
                                    name="respuestas[{{ $pregunta->id }}]"
                                    value="{{ $opcion->valor_opcion }}"
                                    @if($usarRespuestaPrevia && $previousAnswers[$pregunta->id] == $opcion->valor_opcion)
                                checked
                                @elseif(!$usarRespuestaPrevia && $valorOld == $opcion->valor_opcion) {{-- Usar old() si no es rellenable o no hay previa --}}
                                checked
                                @endif
                                @if(collect(json_decode($pregunta->reglas_validacion, true) ?? [])->has('required')) required @endif
                                >
                                <label class="form-check-label" for="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}">
                                    {{$opcion->texto_opcion }}
                                </label>
                            </div>
                            @endforeach
                            @elseif ($pregunta->tipo_input === 'checkbox' && $pregunta->opciones->isNotEmpty())
                            @foreach ($pregunta->opciones as $opcion)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    id="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}"
                                    name="respuestas[{{ $pregunta->id }}][]"
                                    value="{{ $opcion->valor_opcion }}"
                                    @if($usarRespuestaPrevia && is_array($previousAnswers[$pregunta->id]) && in_array($opcion->valor_opcion, $previousAnswers[$pregunta->id]))
                                checked
                                @elseif(!$usarRespuestaPrevia && is_array($valorOld) && in_array($opcion->valor_opcion, $valorOld))
                                checked
                                @endif
                                >
                                <label class="form-check-label" for="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}">
                                    {{ $opcion->texto_opcion }}
                                </label>
                            </div>
                            @endforeach
                            @elseif ($pregunta->tipo_input === 'textarea')
                            <textarea class="form-control"
                                id="pregunta_{{ $pregunta->id }}"
                                name="respuestas[{{ $pregunta->id }}]"
                                rows="4"
                                @if(collect(json_decode($pregunta->reglas_validacion, true) ?? [])->has('required')) required @endif
                                >{{ $usarRespuestaPrevia ? $previousAnswers[$pregunta->id] : $valorOld }}</textarea>
                            @else {{-- text, email, number, etc. --}}
                            <input type="{{$pregunta->tipo_input }}" class="form-control"
                                id="pregunta_{{ $pregunta->id }}"
                                name="respuestas[{{ $pregunta->id }}]"
                                value="{{ $usarRespuestaPrevia ? $previousAnswers[$pregunta->id] : $valorOld }}"
                                @if(collect(json_decode($pregunta->reglas_validacion, true) ?? [])->has('required')) required @endif
                            >
                            @endif
                        </div>

                        @error('respuestas.' . $pregunta->id)
                        <div class="error-text mt-2">{{ $message }}</div>
                        @enderror
                        @if ($pregunta->tipo_input === 'checkbox' && $errors->has('respuestas.' . $pregunta->id . '.*'))
                        <div class="error-text mt-2">{{ $errors->first('respuestas.' . $pregunta->id . '.*') }}</div>
                        @elseif ($pregunta->tipo_input === 'checkbox' && $errors->has('respuestas.' . $pregunta->id) && !$errors->has('respuestas.' . $pregunta->id . '.*'))
                        <div class="error-text mt-2">{{ $errors->first('respuestas.' . $pregunta->id) }}</div>
                        @endif
                    </div>
                    @endforeach

                </div>


                <div class="navigation-buttons">
                    <button type="button" class="btn btn-secondary btn-nav" id="botonPreguntaAnterior" disabled>
                        <i class="bi bi-arrow-left-circle-fill me-2"></i>Anterior
                    </button>
                    <button type="button" class="btn btn-primary btn-nav" id="botonSiguientePregunta">
                        Siguiente<i class="bi bi-arrow-right-circle-fill ms-2"></i>
                    </button>
                    <button type="submit" class="btn btn-primary btn-submit-final" id="botonEnviarRespuestas" style="display: none;">
                        <i class="bi bi-check-circle-fill me-2"></i>Enviar Respuestas
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} HairLife. Todos los derechos reservados.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bloquesDePregunta = document.querySelectorAll('.pregunta-bloque');
            const botonAnterior = document.getElementById('botonPreguntaAnterior');
            const botonSiguiente = document.getElementById('botonSiguientePregunta');
            const botonEnviar = document.getElementById('botonEnviarRespuestas');
            // const formulario = document.getElementById('formularioCuestionarioInteractivo'); // Descomentar si se usa para validación JS

            let indicePreguntaActual = 0;
            const totalDePreguntas = bloquesDePregunta.length;

            if (totalDePreguntas === 0) {
                if (botonAnterior) botonAnterior.style.display = 'none';
                if (botonSiguiente) botonSiguiente.style.display = 'none';
                if (botonEnviar) botonEnviar.style.display = 'none';
                // Considerar mostrar un mensaje o deshabilitar el formulario si no hay preguntas.
                return;
            }

            // Función para establecer el estado inicial de los bloques de pregunta (visibilidad y posición)
            function prepararBloques() {
                bloquesDePregunta.forEach((bloque, indice) => {
                    // Si no es la pregunta que debe mostrarse inicialmente, se posiciona fuera de la pantalla
                    if (indice !== indicePreguntaActual) {
                        bloque.style.transform = indice < indicePreguntaActual ? 'translateX(-100%)' : 'translateX(100%)';
                        bloque.style.opacity = '0';
                        bloque.style.visibility = 'hidden';
                        bloque.classList.remove('active');
                    }
                    // La clase 'slide-out-left' no se usa activamente en la lógica actual de JS para transiciones,
                    // pero es bueno limpiarla si existiera por alguna razón.
                    bloque.classList.remove('slide-out-left');
                });
            }

            function actualizarEstadoBotones() {
                if (!botonAnterior || !botonSiguiente || !botonEnviar) return; // Seguridad

                botonAnterior.disabled = indicePreguntaActual === 0;

                if (indicePreguntaActual === totalDePreguntas - 1) {
                    botonSiguiente.style.display = 'none';
                    botonEnviar.style.display = 'inline-block';
                } else {
                    botonSiguiente.style.display = 'inline-block';
                    botonEnviar.style.display = 'none';
                }

                // Caso especial: si solo hay una pregunta
                if (totalDePreguntas === 1) {
                    botonAnterior.disabled = true;
                    // botonSiguiente ya estaría none por la condición anterior si totalDePreguntas - 1 === 0
                }
            }

            function mostrarPregunta(indiceToShow) {
                if (indiceToShow < 0 || indiceToShow >= totalDePreguntas) {
                    return; // Índice fuera de rango
                }

                const bloqueActualDOM = bloquesDePregunta[indicePreguntaActual];
                const bloqueNuevoDOM = bloquesDePregunta[indiceToShow];

                // Si es la misma pregunta, solo asegurar estado y salir (útil en la carga inicial)
                if (indiceToShow === indicePreguntaActual && bloqueNuevoDOM) {
                    bloqueNuevoDOM.style.transform = 'translateX(0%)';
                    bloqueNuevoDOM.style.opacity = '1';
                    bloqueNuevoDOM.style.visibility = 'visible';
                    bloqueNuevoDOM.classList.add('active');
                    actualizarEstadoBotones();
                    return;
                }

                let direccionSalidaActual = '';
                let posicionEntradaNuevo = '';

                if (indiceToShow > indicePreguntaActual) { // Moviendo hacia adelante
                    direccionSalidaActual = 'translateX(-100%)'; // Sale a la izquierda
                    posicionEntradaNuevo = 'translateX(100%)'; // Entra desde la derecha
                } else { // Moviendo hacia atrás
                    direccionSalidaActual = 'translateX(100%)'; // Sale a la derecha
                    posicionEntradaNuevo = 'translateX(-100%)'; // Entra desde la izquierda
                }

                // Preparar el nuevo bloque para la entrada (aún invisible y fuera de pantalla)
                if (bloqueNuevoDOM) {
                    bloqueNuevoDOM.style.visibility = 'hidden'; // Prevenir parpadeo
                    bloqueNuevoDOM.style.opacity = '0';
                    bloqueNuevoDOM.style.transform = posicionEntradaNuevo;
                    bloqueNuevoDOM.classList.remove('active', 'slide-out-left'); // Limpiar clases
                }

                // Animar el bloque actual para que salga
                if (bloqueActualDOM && bloqueActualDOM !== bloqueNuevoDOM) {
                    bloqueActualDOM.style.transform = direccionSalidaActual;
                    bloqueActualDOM.style.opacity = '0';
                    bloqueActualDOM.classList.remove('active');

                    // Cuando la transición de salida termina, ocultarlo completamente
                    const onTransitionEnd = () => {
                        bloqueActualDOM.style.visibility = 'hidden';
                        bloqueActualDOM.removeEventListener('transitionend', onTransitionEnd);
                    };
                    bloqueActualDOM.addEventListener('transitionend', onTransitionEnd);
                }

                // Usar requestAnimationFrame para asegurar que el navegador aplique los estilos
                // de "entrada" y luego los de "activo" en ciclos de renderizado separados,
                // permitiendo que la transición CSS ocurra.
                requestAnimationFrame(() => {
                    if (bloqueNuevoDOM) {
                        bloqueNuevoDOM.style.visibility = 'visible';
                        bloqueNuevoDOM.style.opacity = '1';
                        bloqueNuevoDOM.style.transform = 'translateX(0%)';
                        bloqueNuevoDOM.classList.add('active');
                    }
                });

                indicePreguntaActual = indiceToShow;
                actualizarEstadoBotones();
            }

            // Event Listeners para los botones
            if (botonSiguiente) {
                botonSiguiente.addEventListener('click', () => {
                    // Aquí se podría añadir validación JS para la pregunta actual antes de pasar a la siguiente
                    if (indicePreguntaActual < totalDePreguntas - 1) {
                        mostrarPregunta(indicePreguntaActual + 1);
                    }
                });
            }

            if (botonAnterior) {
                botonAnterior.addEventListener('click', () => {
                    if (indicePreguntaActual > 0) {
                        mostrarPregunta(indicePreguntaActual - 1);
                    }
                });
            }
            // --- INICIO DE MODIFICACIÓN ---
            // Determinar la pregunta inicial (prioridad: ancla URL > error Laravel > primera pregunta)
            let preguntaInicial = 0; // Por defecto, la primera pregunta (índice 0)
            const urlHash = window.location.hash; // Ejemplo: "#pregunta-35"

            if (urlHash && urlHash.startsWith('#pregunta-')) { //
                const idPreguntaAncla = urlHash.substring('#pregunta-'.length); // Extrae "35"
                if (idPreguntaAncla && bloquesDePregunta.length > 0) {
                    for (let i = 0; i < totalDePreguntas; i++) {
                        // Buscamos el bloque de pregunta que tiene el 'data-pregunta-id' correspondiente
                        if (bloquesDePregunta[i].dataset.preguntaId === idPreguntaAncla) { //
                            preguntaInicial = i; // Establece el índice de la pregunta a mostrar
                            break;
                        }
                    }
                }
            } else {

                // Determinar la pregunta inicial (si hay errores de Laravel, ir a la primera con error)
                const hayErroresLaravel = "{{ $errors->any() ? 'true' : 'false' }}";
                if (hayErroresLaravel && totalDePreguntas > 0) {
                    for (let i = 0; i < totalDePreguntas; i++) {
                        if (bloquesDePregunta[i].querySelector('.error-text')) {
                            preguntaInicial = i;
                            break;
                        }
                    }
                }
            }

            // Inicialización del slider
            if (totalDePreguntas > 0) {
                indicePreguntaActual = preguntaInicial; // Establecer el índice antes de preparar los bloques
                prepararBloques(); // Posiciona todos los bloques correctamente
                mostrarPregunta(indicePreguntaActual); // Muestra la pregunta inicial (ya maneja el caso de ser la misma)
            } else {
                // Si no hay preguntas, los botones ya se ocultaron, pero actualizamos por si acaso.
                actualizarEstadoBotones();
                // Esto se llamaría si no hay preguntas, aunque Blade ya maneja este caso.
                // Si había un error y los botones están ocultos, se mostrarían aquí.
                // Sin embargo, con totalDePreguntas = 0, la lógica anterior de ocultarlos es más apropiada.
                // Vamos a refinar esto ligeramente para el caso de 0 preguntas:
                if (botonAnterior) botonAnterior.style.display = 'none';
                if (botonSiguiente) botonSiguiente.style.display = 'none';
                if (botonEnviar) botonEnviar.style.display = 'none';
            }
        });
    </script>
</body>

</html>