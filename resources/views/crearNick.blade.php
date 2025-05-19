<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>Crear Nickname</h2>

    <form action="/guardar-nick" method="POST">
        @csrf
        <input type="text" name="nick" placeholder="Ingrese su nick" required><br>
        <button type="submit">Guardar Nick</button>
    </form>

    @if(session('error'))
    <p style="color: red;">{{ session('error') }}</p>
    @endif
</body>

</html>