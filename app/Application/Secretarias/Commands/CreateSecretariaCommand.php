<?php

namespace App\Application\Secretarias\Commands;

final readonly class CreateSecretariaCommand
{
    public function __construct(
        public string $nombre,
        public ?string $sigla = null,
        public ?string $atribuciones = null,
        public ?string $direccion_fisica = null,
        public ?string $telefono = null,
        public ?string $email = null,
        public ?string $horario_atencion = null,
        public ?string $foto_titular_url = null,
        public int $orden_organigrama = 0,
        public bool $activa = true,
    ) {}
}
