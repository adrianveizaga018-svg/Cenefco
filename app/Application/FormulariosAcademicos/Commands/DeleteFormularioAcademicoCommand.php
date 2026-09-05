<?php
namespace App\Application\FormulariosAcademicos\Commands;
final readonly class DeleteFormularioAcademicoCommand {
    public function __construct(public int $idFormulario) {}
}
