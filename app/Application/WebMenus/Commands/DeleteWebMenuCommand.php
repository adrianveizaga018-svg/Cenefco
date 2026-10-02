<?php

namespace App\Application\WebMenus\Commands;

final readonly class DeleteWebMenuCommand
{
    public function __construct(public int $id) {}
}
