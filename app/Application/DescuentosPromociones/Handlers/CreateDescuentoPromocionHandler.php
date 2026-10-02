<?php

namespace App\Application\DescuentosPromociones\Handlers;

use App\Application\DescuentosPromociones\Commands\CreateDescuentoPromocionCommand;
use App\Application\DescuentosPromociones\DTOs\DescuentoPromocionDTO;
use App\Domain\DescuentosPromociones\Contracts\DescuentoPromocionRepositoryInterface;

class CreateDescuentoPromocionHandler
{
    public function __construct(private readonly DescuentoPromocionRepositoryInterface $repository) {}

    public function handle(CreateDescuentoPromocionCommand $c): DescuentoPromocionDTO
    {
        return $this->repository->create([
            'codigo'           => $c->codigo,
            'nombre'           => $c->nombre,
            'descripcion'      => $c->descripcion,
            'tipo_descuento'   => $c->tipo_descuento,
            'valor'            => $c->valor,
            'monto_minimo'     => $c->monto_minimo,
            'usos_maximos'     => $c->usos_maximos,
            'usos_por_usuario' => $c->usos_por_usuario,
            'programa_id'      => $c->programa_id,
            'activo'           => $c->activo,
            'fecha_inicio'     => $c->fecha_inicio,
            'fecha_fin'        => $c->fecha_fin,
        ]);
    }
}
