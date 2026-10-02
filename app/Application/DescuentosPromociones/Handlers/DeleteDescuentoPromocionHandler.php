<?php

namespace App\Application\DescuentosPromociones\Handlers;

use App\Application\DescuentosPromociones\Commands\DeleteDescuentoPromocionCommand;
use App\Domain\DescuentosPromociones\Contracts\DescuentoPromocionRepositoryInterface;

class DeleteDescuentoPromocionHandler
{
    public function __construct(private readonly DescuentoPromocionRepositoryInterface $repository) {}

    public function handle(DeleteDescuentoPromocionCommand $c): bool
    {
        return $this->repository->delete($c->id);
    }
}
