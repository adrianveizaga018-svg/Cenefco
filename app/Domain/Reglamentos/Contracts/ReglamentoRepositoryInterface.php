<?php

namespace App\Domain\Reglamentos\Contracts;

interface ReglamentoRepositoryInterface
{
    public function findByPrograma(int $idPrograma): mixed;
    public function upsert(int $idPrograma, array $data): mixed;
}
