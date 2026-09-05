<?php
namespace App\Application\Historiales\Queries;
final readonly class GetHistorialByIdQuery {
    public function __construct(public int $idHistorial) {}
}
