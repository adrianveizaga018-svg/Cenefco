<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('cobro_estado.completo_label', 'Pagado');
        $this->migrator->add('cobro_estado.completo_color', 'success');
        $this->migrator->add('cobro_estado.parcial_label', 'Parcial');
        $this->migrator->add('cobro_estado.parcial_color', 'warning');
        $this->migrator->add('cobro_estado.sin_pagos_label', 'Sin pagos');
        $this->migrator->add('cobro_estado.sin_pagos_color', 'danger');
    }
};
