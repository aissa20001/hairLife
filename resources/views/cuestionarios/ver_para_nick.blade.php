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
            --purple-primary: #6a0dad;
            /* Morado principal (HairLife) */
            --purple-secondary: #8e44ad;
            /* Morado secundario/más suave */
            --purple-dark: #4c0b56;
            /* Morado oscuro para hovers/texto */
            --purple-medium: #7952b3;
            /* Un morado intermedio */
            --purple-light: #c3a2d9;
            /* Un lila más claro */
            --purple-very-light: #e0cce8;
            /* Un lila muy pálido para bordes o fondos sutiles */
            --purple-background: #f8f5f9;
            /* Fondo de página casi blanco con tinte lila */

            --text-on-purple: #ffffff;
            --card-bg: #ffffff;
            --border-color: var(--purple-very-light);
            /* Borde general */
            --input-focus-color: var(--purple-secondary);
            --input-focus-box-shadow: rgba(142, 68, 173, 0.25);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--purple-background);
            color: #333;
            line-height: 1.6;
            padding-top: 80px;
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
            /* Borde más definido */
        }

        .home-button:hover,
        .home-button:focus {
            background-color: var(--purple-dark);
            color: var(--text-on-purple);
            border-color: var(--purple-dark);
        }

        .questionnaire-main-container {
            max-width: 900px;
            margin: 30px auto;
        }

        .questionnaire-container {
            background-color: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            /* Borde sutil al contenedor principal */
            box-shadow: 0 8px 24px rgba(106, 13, 173, 0.1);
            /* Sombra más suave */
            padding: 30px 35px;
            margin-bottom: 40px;
        }

        .questionnaire-title {
            font-family: 'Dancing Script', cursive;
            color: var(--purple-primary);
            text-align: center;
            font-size: 3em;
            /* Un poco más grande */
            margin-bottom: 0.5rem;
        }

        .questionnaire-subtitle {
            text-align: center;
            color: var(--purple-medium);
            /* Usamos un morado intermedio */
            margin-bottom: 25px;
            font-size: 1.15em;
            /* Un poco más grande */
        }

        .questionnaire-description {
            color: #444;
            font-size: 1em;
            margin-bottom: 35px;
            padding-left: 15px;
            border-left: 4px solid var(--purple-light);
            /* Borde con lila claro */
        }

        .question-slider {
            position: relative;
            overflow: hidden;
            min-height: 350px;
            /* Aumentamos un poco más la altura mínima */
            margin-bottom: 25px;
        }

        .pregunta-bloque {
            background-color: #fdfaff;
            border: 1px solid var(--purple-very-light);
            /* Borde más sutil con lila muy pálido */
            border-radius: 8px;
            padding: 25px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transform: translateX(100%);
            transition: transform 0.5s ease-in-out, opacity 0.4s ease-in-out;
            visibility: hidden;
            /* max-height: 350px; Ya no es necesario aquí, el scroll va en el div de opciones */
            display: flex;
            flex-direction: column;
            /* Para que el enunciado y las opciones se apilen */
        }

        .pregunta-bloque.active {
            opacity: 1;
            transform: translateX(0%);
            visibility: visible;
            z-index: 10;
        }

        .pregunta-bloque.slide-out-left {
            transform: translateX(-100%);
            opacity: 0;
        }

        .pregunta-enunciado {
            display: block;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--purple-primary);
            /* Texto de la pregunta en morado principal */
            font-size: 1.3em;
            /* Un poco más grande */
            flex-shrink: 0;
            /* Evita que el enunciado se encoja si las opciones son muchas */
        }

        /* Contenedor para opciones con scroll */
        .pregunta-opciones-scrollable {
            overflow-y: auto;
            /* Scroll vertical si es necesario */
            max-height: 220px;
            /* Altura máxima para el área de opciones (ajustar según necesidad) */
            padding-right: 10px;
            /* Espacio para la barra de scroll si aparece */
            flex-grow: 1;
            /* Ocupa el espacio restante */
        }

        /* Estilo para la barra de scroll (opcional, para Webkit/Blink) */
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
        }

        .form-check-label {
            cursor: pointer;
            font-size: 1.05em;
        }

        .form-control {
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
            /* Botones Anterior/Siguiente */
            background-color: var(--purple-secondary);
            border-color: var(--purple-secondary);
            min-width: 120px;
        }

        .btn-nav:hover,
        .btn-nav:focus {
            background-color: var(--purple-primary);
            /* Hover más intenso */
            border-color: var(--purple-primary);
        }

        .btn-nav:disabled {
            /* Estilo para botón deshabilitado (Anterior) */
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
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-radius: 8px;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.875em;
            display: block;
            margin-top: 4px;
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

            @if(isset($cuestionario->descripcion) && !empty($cuestionario->descripcion))
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

            <form method="POST" action="{{ route('cuestionarios.enviarParaNick', ['nick' => $nick, 'id_cuestionario' => $cuestionario->id]) }}" id="formularioCuestionarioInteractivo">
                @csrf

                @if($preguntas->isEmpty())
                <p class="text-center my-5">Este cuestionario aún no tiene preguntas configuradas. Por favor, contacta al administrador.</p>
                @else
                <div class="question-slider">
                    @foreach ($preguntas as $index => $pregunta)
                    <div class="pregunta-bloque" id="pregunta-{{ $index }}">
                        <label class="pregunta-enunciado mb-3">{{ $loop->iteration }}. {{ htmlspecialchars($pregunta->enunciado) }}
                            @if(collect(json_decode($pregunta->reglas_validacion, true) ?? [])->has('required'))
                            <span class="text-danger">*</span>
                            @endif
                        </label>

                        {{-- Nuevo div para el contenido scrolleable de las opciones --}}
                        <div class="pregunta-opciones-scrollable">
                            @if ($pregunta->tipo_input === 'radio' && $pregunta->opciones->isNotEmpty())
                            @foreach ($pregunta->opciones as $opcion)
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                    id="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}"
                                    name="respuestas[{{ $pregunta->id }}]"
                                    value="{{ $opcion->valor_opcion }}"
                                    {{ old('respuestas.' . $pregunta->id) == $opcion->valor_opcion ? 'checked' : '' }}
                                    @if(collect(json_decode($pregunta->reglas_validacion, true) ?? [])->has('required')) required @endif
                                >
                                <label class="form-check-label" for="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}">
                                    {{ htmlspecialchars($opcion->texto_opcion) }}
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
                                    {{ (is_array(old('respuestas.' . $pregunta->id)) && in_array($opcion->valor_opcion, old('respuestas.' . $pregunta->id, []))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}">
                                    {{ htmlspecialchars($opcion->texto_opcion) }}
                                </label>
                            </div>
                            @endforeach
                            @elseif ($pregunta->tipo_input === 'textarea')
                            <textarea class="form-control"
                                id="pregunta_{{ $pregunta->id }}"
                                name="respuestas[{{ $pregunta->id }}]"
                                rows="5"
                                @if(collect(json_decode($pregunta->reglas_validacion, true) ?? [])->has('required')) required @endif
                                    aria-describedby="error_pregunta_{{ $pregunta->id }}"
                                >{{ old('respuestas.' . $pregunta->id) }}</textarea>
                            @else
                            <input type="text" class="form-control"
                                id="pregunta_{{ $pregunta->id }}"
                                name="respuestas[{{ $pregunta->id }}]"
                                value="{{ old('respuestas.' . $pregunta->id) }}"
                                @if(collect(json_decode($pregunta->reglas_validacion, true) ?? [])->has('required')) required @endif
                            aria-describedby="error_pregunta_{{ $pregunta->id }}"
                            >
                            @endif
                        </div> {{-- Fin de .pregunta-opciones-scrollable --}}


                        @error('respuestas.' . $pregunta->id)
                        <div class="error-text mt-2" id="error_pregunta_{{ $pregunta->id }}">{{ $message }}</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // El JavaScript permanece igual que en la versión anterior que funcionaba
        document.addEventListener('DOMContentLoaded', function() {
            const bloquesDePregunta = document.querySelectorAll('.pregunta-bloque');
            const botonAnterior = document.getElementById('botonPreguntaAnterior');
            const botonSiguiente = document.getElementById('botonSiguientePregunta');
            const botonEnviar = document.getElementById('botonEnviarRespuestas');

            let indicePreguntaActual = 0;
            const totalDePreguntas = bloquesDePregunta.length;

            if (totalDePreguntas === 0) {
                if (botonAnterior) botonAnterior.style.display = 'none';
                if (botonSiguiente) botonSiguiente.style.display = 'none';
                if (botonEnviar) botonEnviar.style.display = 'none';
                return;
            }

            function prepararBloquesNoActivos() {
                bloquesDePregunta.forEach((bloque, indice) => {
                    if (indice !== indicePreguntaActual) {
                        bloque.style.transform = indice < indicePreguntaActual ? 'translateX(-100%)' : 'translateX(100%)';
                        bloque.style.opacity = '0';
                        bloque.style.visibility = 'hidden';
                    }
                    bloque.classList.remove('slide-out-left');
                    if (indice !== indicePreguntaActual) bloque.classList.remove('active');
                });
            }

            function actualizarEstadoBotones() {
                if (!botonAnterior || !botonSiguiente || !botonEnviar) return;

                botonAnterior.disabled = indicePreguntaActual === 0;

                if (indicePreguntaActual === totalDePreguntas - 1) {
                    botonSiguiente.style.display = 'none';
                    botonEnviar.style.display = 'inline-block';
                } else {
                    botonSiguiente.style.display = 'inline-block';
                    botonEnviar.style.display = 'none';
                }

                if (totalDePreguntas === 1) {
                    botonAnterior.disabled = true;
                    botonSiguiente.style.display = 'none';
                    botonEnviar.style.display = 'inline-block';
                }
            }

            function mostrarPregunta(indiceToShow) {
                if (indiceToShow < 0 || indiceToShow >= totalDePreguntas) return;

                const bloqueActualDOM = bloquesDePregunta[indicePreguntaActual];
                const bloqueNuevoDOM = bloquesDePregunta[indiceToShow];

                let direccionSalidaActual = '';
                let posicionEntradaNuevo = '';

                if (indiceToShow > indicePreguntaActual) {
                    direccionSalidaActual = 'translateX(-100%)';
                    posicionEntradaNuevo = 'translateX(100%)';
                } else if (indiceToShow < indicePreguntaActual) {
                    direccionSalidaActual = 'translateX(100%)';
                    posicionEntradaNuevo = 'translateX(-100%)';
                } else {
                    if (bloqueNuevoDOM) {
                        bloqueNuevoDOM.style.transform = 'translateX(0%)';
                        bloqueNuevoDOM.style.opacity = '1';
                        bloqueNuevoDOM.style.visibility = 'visible';
                        bloqueNuevoDOM.classList.add('active');
                    }
                    actualizarEstadoBotones();
                    return;
                }

                if (bloqueNuevoDOM) {
                    bloqueNuevoDOM.style.visibility = 'hidden';
                    bloqueNuevoDOM.style.opacity = '0';
                    bloqueNuevoDOM.style.transform = posicionEntradaNuevo;
                    bloqueNuevoDOM.classList.remove('active', 'slide-out-left');
                }

                if (bloqueActualDOM && bloqueActualDOM !== bloqueNuevoDOM) {
                    bloqueActualDOM.style.transform = direccionSalidaActual;
                    bloqueActualDOM.style.opacity = '0';
                    bloqueActualDOM.classList.remove('active');

                    const handler = () => {
                        bloqueActualDOM.style.visibility = 'hidden';
                        bloqueActualDOM.removeEventListener('transitionend', handler);
                    };
                    bloqueActualDOM.addEventListener('transitionend', handler);
                }

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

            if (botonSiguiente) {
                botonSiguiente.addEventListener('click', () => {
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

            const hayErroresLaravel = "{{ $errors->any() ? 'true' : 'false' }}";
            let preguntaInicial = 0;

            if (hayErroresLaravel) {
                let errorEncontrado = false;
                if (bloquesDePregunta && bloquesDePregunta.length > 0) {
                    bloquesDePregunta.forEach((bloque, indice) => {
                        if (!errorEncontrado && bloque.querySelector('.error-text')) {
                            preguntaInicial = indice;
                            errorEncontrado = true;
                        }
                    });
                }
            }

            if (totalDePreguntas > 0) {
                prepararBloquesNoActivos();
                indicePreguntaActual = preguntaInicial;
                mostrarPregunta(indicePreguntaActual);
            } else {
                actualizarEstadoBotones();
            }

        });
    </script>
</body>

</html>