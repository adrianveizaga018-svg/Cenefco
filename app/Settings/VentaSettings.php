<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class VentaSettings extends Settings
{
    public bool $requiere_cliente;

    public bool $permite_precio_manual;

    public float $descuento_maximo;

    public string $moneda_defecto;

    public static function group(): string
    {
        return 'venta';
    }
}
