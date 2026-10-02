<?php
namespace App\Application\TestsAcademicos\Queries;
final readonly class GetTestAcademicoByIdQuery {
    public function __construct(public int $idTest) {}
}
