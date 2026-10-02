<?php

namespace App\Application\Noticias\Queries;

final readonly class GetNoticiaBySlugQuery
{
    public function __construct(public string $slug) {}
}
