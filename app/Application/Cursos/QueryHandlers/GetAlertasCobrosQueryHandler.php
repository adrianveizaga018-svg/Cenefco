<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\DTOs\AlertaCobrosCursoDTO;
use App\Application\Cursos\Queries\GetAlertasCobrosQuery;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class GetAlertasCobrosQueryHandler
{
    public function __construct(
        private readonly CursoRepositoryInterface $repository
    ) {}

    public function handle(GetAlertasCobrosQuery $query): array
    {
        $filas = $this->repository->alertasCobrosPorImparticion($query->diasProximos, $query->idImpPermitidos);

        return array_map(
            fn (array $f) => new AlertaCobrosCursoDTO(
                id_imp:   $f['id_imp'],
                proximas: $f['proximas'],
                vencidas: $f['vencidas'],
            ),
            $filas
        );
    }
}
