<?php

declare(strict_types=1);

namespace App\Domain\CalendarioAcademico\Exceptions;

use RuntimeException;

class CalendarioAcademicoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Evento de calendario '{$id}' no encontrado.", 404);
    }
}
