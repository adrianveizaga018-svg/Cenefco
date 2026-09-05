<?php

namespace App\Domain\Ajustes\Contracts;

use App\Application\Ajustes\DTOs\AjusteDTO;

interface AjusteRepositoryInterface
{
    
    public function all(): array;

    public function findByKey(string $key): AjusteDTO;

    public function update(string $key, array $data): AjusteDTO;

    public function create(array $data): AjusteDTO;
}
