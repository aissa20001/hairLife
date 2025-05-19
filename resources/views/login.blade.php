<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>

    <h2>Iniciar sesión</h2>
    @if ($errors->any())
    <div class="error">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if (session('error_login'))
    <div class="error">{{ session('error_login') }}</div>
    @endif

    <form action="/login" method="POST">
        @csrf
        <input type="text" name="Nombre" placeholder="Nombre de usuario" required><br>
        <input type="password" name="Clave" placeholder="Clave" required><br>
        <button type="submit">Iniciar sesión</button>
    </form>

</body>

</html>