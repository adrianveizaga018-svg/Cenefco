<?php

namespace App\Domain\Moodle\Exceptions;

class MoodleNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Moodle '{$id}' no encontrado.", 404);
    }
}
