<?php

namespace App\Application\MdlUsers\Queries;

final readonly class GetMdlUserByIdQuery
{
    public function __construct(public int $id) {}
}
