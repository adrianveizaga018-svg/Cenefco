<?php
namespace App\Application\MenusAcademicos\Queries;
final readonly class GetMenuAcademicoByIdQuery {
    public function __construct(public int $idMen) {}
}
