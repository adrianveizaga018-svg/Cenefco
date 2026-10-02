<?php
namespace App\Application\ModulosAcademicos\Commands;
final readonly class DeleteModuloAcademicoCommand {
    public function __construct(public int $idMod) {}
}
