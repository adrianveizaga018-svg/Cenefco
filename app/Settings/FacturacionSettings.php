<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FacturacionSettings extends Settings
{
    public string $nit;

    public string $razon_social;

    public bool $facturacion_habilitada;

    public int $porcentaje_iva;

    public static function group(): string
    {
        return 'facturacion';
    }
}
