<?php
namespace App\Application\HojasEvaluacion\Commands;
final readonly class CreateHojaEvaluacionCommand {
    public function __construct(
        public int $idHojaevaluacion,
        public ?int $idUs,
        public ?int $idUsTut,
        public ?string $observacion,
        public int $idUsReg,
    ) {}
}
