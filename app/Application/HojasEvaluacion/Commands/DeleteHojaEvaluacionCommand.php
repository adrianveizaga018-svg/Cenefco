<?php
namespace App\Application\HojasEvaluacion\Commands;
final readonly class DeleteHojaEvaluacionCommand {
    public function __construct(public int $idHojaevaluacion) {}
}
