<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CobroEstadoSettings extends Settings
{
    public string $completo_label;

    public string $completo_color;

    public string $parcial_label;

    public string $parcial_color;

    public string $sin_pagos_label;

    public string $sin_pagos_color;

    public static function group(): string
    {
        return 'cobro_estado';
    }
}
