<?php
namespace App\Application\TestsAcademicos\Commands;
final readonly class UpdateTestAcademicoCommand {
    public function __construct(
        public int $idTest,
        public ?string $nombre,
        public ?bool $habilitarTest,
    ) {}
}
