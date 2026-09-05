<?php
namespace App\Application\TestsAcademicos\Commands;
final readonly class DeleteTestAcademicoCommand {
    public function __construct(public int $idTest) {}
}
