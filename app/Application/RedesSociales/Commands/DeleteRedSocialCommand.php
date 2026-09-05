<?php

namespace App\Application\RedesSociales\Commands;

use App\Shared\Kernel\Contracts\CommandInterface;

final readonly class DeleteRedSocialCommand implements CommandInterface
{
    public function __construct(public int|array $ids) {}
}
