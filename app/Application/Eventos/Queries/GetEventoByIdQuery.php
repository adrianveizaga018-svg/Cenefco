<?php
namespace App\Application\Eventos\Queries;
final readonly class GetEventoByIdQuery {
    public function __construct(public int $id) {}
}
