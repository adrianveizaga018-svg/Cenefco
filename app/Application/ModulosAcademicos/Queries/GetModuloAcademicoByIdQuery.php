<?php
namespace App\Application\ModulosAcademicos\Queries;
final readonly class GetModuloAcademicoByIdQuery {
    public function __construct(public int $idMod) {}
}
