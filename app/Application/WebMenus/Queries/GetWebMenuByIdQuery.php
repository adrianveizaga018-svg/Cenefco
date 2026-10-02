<?php

namespace App\Application\WebMenus\Queries;

final readonly class GetWebMenuByIdQuery
{
    public function __construct(public int $id) {}
}
