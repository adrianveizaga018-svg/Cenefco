<?php
namespace App\Application\HojasEvaluacion\Queries;
final readonly class GetHojaEvaluacionByIdQuery {
    public function __construct(public int $idHojaevaluacion) {}
}
