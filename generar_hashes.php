<?php
// Carga el autoload de Composer y el bootstrap de Laravel para tener acceso a sus facades.
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Contraseñas de tu archivo .sql
$clavesOriginales = [
    'aissa' => '1234',
    'carlota' => '5678',
    'ana' => '9876',
    // Añade más usuarios si es necesario
];

echo "--- Hashes Generados ---\n";
foreach ($clavesOriginales as $nombre => $clave) {
    $hash = Illuminate\Support\Facades\Hash::make($clave);
    echo "Usuario: " . $nombre . ", Clave Original: " . $clave . ", Hash: " . $hash . "\n";
    // Podrías generar directamente las sentencias SQL aquí si lo prefieres:
    // echo "UPDATE usuarios SET Clave = '" . $hash . "' WHERE Nombre = '" . $nombre . "'; -- Para actualizar existentes\n";
    // echo "INSERT INTO usuarios (Nombre, Clave, Rol) VALUES ('" . $nombre . "', '" . $hash . "', 0); -- Para nuevas inserciones en tu SQL\n";
}
echo "------------------------\n";
