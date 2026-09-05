<?php

namespace App\Application\Noticias\Queries;

final readonly class GetNoticiaByIdQuery
{
    public function __construct(public int $id) {}
}
