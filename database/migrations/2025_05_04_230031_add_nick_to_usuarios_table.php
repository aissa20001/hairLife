<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Comprueba si la columna 'nick' NO existe antes de intentar añadirla
        if (!Schema::hasColumn('usuarios', 'nick')) {
            Schema::table('usuarios', function (Blueprint $table) {
                // Asegúrate que la definición aquí coincida con lo que necesitas
                // y con la posición deseada (ej. ->after('Nombre'))
                $table->string('nick', 50)->nullable()->default(null)->after('Nombre');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('usuarios', 'nick')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('nick');  // Eliminar el campo 'nick' si es necesario revertir la migración
            });
        }
    }
};
