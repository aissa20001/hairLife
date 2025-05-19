<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ htmlspecialchars($cuestionario->titulo) }} - {{ htmlspecialchars($nick) }} - Mi Pelo</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 750px;
            margin: 30px auto;
            padding: 25px 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #7952b3;
            text-align: center;
            margin-bottom: 10px;
            font-size: 2em;
        }

        .sub-header {
            text-align: center;
            color: #555;
            margin-bottom: 25px;
            font-size: 1.1em;
        }

        p.descripcion {
            color: #444;
            font-size: 1em;
            margin-bottom: 30px;
            text-align: left;
            padding-left: 10px;
            border-left: 3px solid #7952b3;
        }

        .pregunta-bloque {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
        }

        .pregunta-enunciado {
            display: block;
            font-weight: 600;
            margin-bottom: 15px;
            color: #495057;
            font-size: 1.1em;
        }

        .input-option {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .input-option input[type="radio"],
        .input-option input[type="checkbox"] {
            margin-right: 10px;
            width: auto;
            accent-color: #7952b3;
        }

        .input-option label {
            font-weight: normal;
            font-size: 1em;
            color: #333;
            margin-bottom: 0;
            cursor: pointer;
        }

        input[type="text"],
        textarea {
            width: calc(100% - 24px);
            padding: 12px;
            margin-bottom: 5px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
            color: #495057;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #7952b3;
            box-shadow: 0 0 0 0.2rem rgba(121, 82, 179, 0.25);
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px 15px;
            background-color: #7952b3;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            margin-top: 20px;
        }

        .btn-submit:hover {
            background-color: #5a3d8a;
        }

        .error-message-container {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .error-message-container strong {
            display: block;
            margin-bottom: 5px;
        }

        .error-message-container ul {
            margin: 0;
            padding-left: 20px;
        }

        .error-message-container li {
            font-size: 0.95em;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.875em;
            display: block;
            margin-top: 4px;
        }

        .navigation-links {
            margin-top: 30px;
            text-align: center;
        }

        .navigation-links a {
            color: #7952b3;
            text-decoration: none;
            font-weight: 500;
            padding: 5px 10px;
        }

        .navigation-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>{{ htmlspecialchars($cuestionario->titulo) }}</h1>
        <p class="sub-header">Respondiendo como: {{ htmlspecialchars($nick) }}</p>

        @if(isset($cuestionario->descripcion) && !empty($cuestionario->descripcion))
        <p class="descripcion">{{ htmlspecialchars($cuestionario->descripcion) }}</p>
        @endif

        @if ($errors->any())
        <div class="error-message-container">
            <strong>Por favor, corrige los siguientes errores:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('cuestionarios.enviarParaNick', ['nick' => $nick, 'id_cuestionario' => $cuestionario->id]) }}">
            @csrf

            @if($preguntas->isEmpty())
            <p>Este cuestionario aún no tiene preguntas configuradas. Por favor, contacta al administrador.</p>
            @else
            @foreach ($preguntas as $pregunta)
            <div class="pregunta-bloque">
                <span class="pregunta-enunciado">{{ $loop->iteration }}. {{ htmlspecialchars($pregunta->enunciado) }}</span>

                {{-- Renderizar input según pregunta->tipo_input --}}
                @if ($pregunta->tipo_input === 'radio' && $pregunta->opciones->isNotEmpty())
                @foreach ($pregunta->opciones as $opcion)
                <div class="input-option">
                    <input type="radio"
                        id="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}"
                        name="respuestas[{{ $pregunta->id }}]"
                        value="{{ $opcion->valor_opcion }}"
                        {{ old('respuestas.' . $pregunta->id) == $opcion->valor_opcion ? 'checked' : '' }}
                        required>
                    <label for="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}">{{ htmlspecialchars($opcion->texto_opcion) }}</label>
                </div>
                @endforeach
                @elseif ($pregunta->tipo_input === 'checkbox' && $pregunta->opciones->isNotEmpty())
                @foreach ($pregunta->opciones as $opcion)
                <div class="input-option">
                    <input type="checkbox"
                        id="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}"
                        name="respuestas[{{ $pregunta->id }}][]" {{-- Nombre como array para checkboxes --}}
                        value="{{ $opcion->valor_opcion }}"
                        {{ (is_array(old('respuestas.' . $pregunta->id)) && in_array($opcion->valor_opcion, old('respuestas.' . $pregunta->id, []))) ? 'checked' : '' }}>
                    <label for="pregunta_{{ $pregunta->id }}_opcion_{{ $opcion->id }}">{{ htmlspecialchars($opcion->texto_opcion) }}</label>
                </div>
                @endforeach
                @elseif ($pregunta->tipo_input === 'textarea')
                <textarea id="pregunta_{{ $pregunta->id }}"
                    name="respuestas[{{ $pregunta->id }}]"
                    required
                    aria-describedby="error_pregunta_{{ $pregunta->id }}">{{ old('respuestas.' . $pregunta->id) }}</textarea>
                @else {{-- 'text' por defecto o cualquier otro no especificado --}}
                <input type="text"
                    id="pregunta_{{ $pregunta->id }}"
                    name="respuestas[{{ $pregunta->id }}]"
                    value="{{ old('respuestas.' . $pregunta->id) }}"
                    required
                    aria-describedby="error_pregunta_{{ $pregunta->id }}">
                @endif

                {{-- Manejo de errores para la pregunta actual --}}
                @error('respuestas.' . $pregunta->id)
                <span class="error-text" id="error_pregunta_{{ $pregunta->id }}">{{ $message }}</span>
                @enderror
                {{-- Manejo específico para errores de array en checkboxes (si la validación es a nivel de array 'respuestas.PREGUNTA_ID.*') --}}
                @if ($pregunta->tipo_input === 'checkbox' && $errors->has('respuestas.' . $pregunta->id . '.*'))
                <span class="error-text">{{ $errors->first('respuestas.' . $pregunta->id . '.*') }}</span>
                {{-- Si el error es porque el campo checkbox es requerido y no se envió nada (el array no existe en la request) --}}
                @elseif ($pregunta->tipo_input === 'checkbox' && $errors->has('respuestas.' . $pregunta->id) && !$errors->has('respuestas.' . $pregunta->id . '.*'))
                <span class="error-text">{{ $errors->first('respuestas.' . $pregunta->id) }}</span>
                @endif
            </div>
            @endforeach
            @endif

            @if(!$preguntas->isEmpty())
            <button type="submit" class="btn-submit">Enviar Respuestas</button>
            @endif
        </form>

        <div class="navigation-links">
            <a href="{{ route('user.dashboard', ['nick' => $nick]) }}">Volver al Panel</a>
        </div>
    </div>
</body>

</html>