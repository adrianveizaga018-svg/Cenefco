<?php
namespace App\Application\Eventos\Queries;
final readonly class GetEventoBySlugQuery {
    public function __construct(public string $slug) {}
}
