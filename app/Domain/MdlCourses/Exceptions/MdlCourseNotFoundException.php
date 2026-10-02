<?php

namespace App\Domain\MdlCourses\Exceptions;

class MdlCourseNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Curso Moodle '{$id}' no encontrado.", 404);
    }
}
