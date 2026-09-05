<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class InventarioSettings extends Settings
{
    public bool $generar_codigos_barras_propios;

    public static function group(): string
    {
        return 'inventario';
    }
}
