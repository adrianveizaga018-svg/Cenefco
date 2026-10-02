<?php

namespace App\Domain\MdlUsers\Exceptions;

class MdlUserNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Usuario Moodle '{$id}' no encontrado.", 404);
    }
}
