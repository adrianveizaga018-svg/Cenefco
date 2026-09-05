<?php

namespace App\Domain\RevistasCientificas\Exceptions;

class RevistaCientificaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Revista científica '{$id}' no encontrada.", 404);
    }
}
