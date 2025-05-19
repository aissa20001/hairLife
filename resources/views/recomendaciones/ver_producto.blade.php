<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Producto Recomendado: {{ htmlspecialchars($producto->nombre) }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .container {
            max-width: 850px;
            margin: 40px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .product-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .product-header h1 {
            color: #7952b3;
            font-size: 2.2em;
            margin-bottom: 5px;
        }

        .product-brand-category {
            color: #6c757d;
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        .product-image-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .product-image {
            max-width: 80%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            max-height: 450px;
        }

        .product-details h2 {
            color: #7952b3;
            font-size: 1.5em;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }

        .product-details p {
            line-height: 1.7;
            margin-bottom: 15px;
        }

        .purchase-link-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .purchase-link a {
            display: inline-block;
            padding: 12px 25px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .purchase-link a:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .justification {
            margin-top: 35px;
            padding: 20px;
            background-color: #f1f3f5;
            border-left: 5px solid #7952b3;
            border-radius: 5px;
        }

        .justification h3 {
            margin-top: 0;
            color: #7952b3;
        }

        .navigation-links {
            text-align: center;
            margin-top: 30px;
        }

        .navigation-links a {
            color: #7952b3;
            text-decoration: none;
            margin: 0 10px;
            font-weight: 500;
        }

        .navigation-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="product-header">
            <h1>{{ htmlspecialchars($producto->nombre) }}</h1>
            <p class="product-brand-category">
                <strong>Marca:</strong> {{ htmlspecialchars($producto->marca) }} |
                <strong>Categoría:</strong> {{ htmlspecialchars(ucfirst($producto->categoria)) }}
            </p>
        </div>

        @if($producto->foto)
        <div class="product-image-container">
            {{-- Asume que 'foto' guarda una URL o una ruta desde public/storage/ --}}
            <img src="{{ filter_var($producto->foto, FILTER_VALIDATE_URL) ? $producto->foto : asset('storage/' . $producto->foto) }}"
                alt="Foto de {{ htmlspecialchars($producto->nombre) }}" class="product-image">
        </div>
        @else
        <p style="text-align:center;">(Imagen no disponible)</p>
        @endif

        <div class="product-details">
            <h2>Descripción y Modo de Uso</h2>
            <p>{!! nl2br(e($producto->descripcion)) !!}</p> {{-- nl2br para saltos de línea, e() para escapar HTML --}}
        </div>

        @if($producto->url)
        <div class="purchase-link-container">
            <a href="{{ $producto->url }}" target="_blank" rel="noopener noreferrer">Ver o Comprar Producto</a>
        </div>
        @endif

        @if($recomendacion->justificacion_titulo || $recomendacion->justificacion_detalle)
        <div class="justification">
            <h3>{{ htmlspecialchars($recomendacion->justificacion_titulo ?: 'Nuestra Sugerencia Para Ti') }}</h3>
            @if($recomendacion->justificacion_detalle)
            <p>{!! nl2br(e($recomendacion->justificacion_detalle)) !!}</p>
            @endif
        </div>
        @endif

        <div class="navigation-links">
            @if (session('usuario_nombre')) {{-- Utiliza la clave de sesión que estés usando para el nick --}}
            <a href="{{ route('user.dashboard', ['nick' => session('usuario_nombre')]) }}">Volver a Mi Panel</a>
            @else
            {{-- Fallback si no hay nick en sesión --}}
            <a href="{{ route('crear.nick') }}">Inicio</a>
            @endif
            {{-- Este enlace te lleva a crear un nick, lo que reinicia el proceso --}}
            <a href="{{ route('crear.nick') }}">Comenzar de Nuevo</a>
        </div>
    </div>
</body>

</html>