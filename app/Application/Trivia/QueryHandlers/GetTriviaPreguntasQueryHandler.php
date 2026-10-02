<?php

namespace App\Application\Trivia\QueryHandlers;

use App\Application\Trivia\Queries\GetTriviaPreguntasQuery;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;

class GetTriviaPreguntasQueryHandler
{
    public function __construct(private readonly TriviaPreguntaRepositoryInterface $repository) {}

    public function handle(GetTriviaPreguntasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, [
            'categoria_id' => $query->categoriaId,
            'nivel_id' => $query->nivelId,
        ]);
    }
}
