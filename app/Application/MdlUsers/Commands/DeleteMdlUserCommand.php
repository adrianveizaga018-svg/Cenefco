<?php

namespace App\Application\MdlUsers\Commands;

final readonly class DeleteMdlUserCommand
{
    public function __construct(public int $id) {}
}
