<?php

namespace App\Application\Reglamentos\QueryHandlers;

use App\Application\Reglamentos\DTOs\ReglamentoDTO;
use App\Application\Reglamentos\Queries\GetReglamentoByProgramaQuery;
use App\Domain\Reglamentos\Contracts\ReglamentoRepositoryInterface;

class GetReglamentoByProgramaQueryHandler
{
    public function __construct(
        private readonly ReglamentoRepositoryInterface $repository
    ) {}

    public function handle(GetReglamentoByProgramaQuery $query): ReglamentoDTO
    {
        $row = $this->repository->findByPrograma($query->idPrograma);

        return ReglamentoDTO::fromRow($query->idPrograma, $row);
    }
}
