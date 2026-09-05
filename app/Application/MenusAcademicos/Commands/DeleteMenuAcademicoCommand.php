<?php
namespace App\Application\MenusAcademicos\Commands;
final readonly class DeleteMenuAcademicoCommand {
    public function __construct(public int $idMen) {}
}
