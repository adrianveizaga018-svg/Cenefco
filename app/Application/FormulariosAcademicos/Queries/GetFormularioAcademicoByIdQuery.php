<?php
namespace App\Application\FormulariosAcademicos\Queries;
final readonly class GetFormularioAcademicoByIdQuery {
    public function __construct(public int $idFormulario) {}
}
