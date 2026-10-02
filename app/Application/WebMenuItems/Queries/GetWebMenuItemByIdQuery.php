<?php

namespace App\Application\WebMenuItems\Queries;

final readonly class GetWebMenuItemByIdQuery
{
    public function __construct(public int $id) {}
}
