<?php

namespace App\Domain\RedesSociales\Events;

use Illuminate\Foundation\Events\Dispatchable;

class RedSocialCreated
{
    use Dispatchable;

    public function __construct(public readonly int $redSocialId) {}
}
