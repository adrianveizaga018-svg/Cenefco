<?php

namespace App\Application\WebMenuItems\Commands;

final readonly class DeleteWebMenuItemCommand
{
    public function __construct(public int $id) {}
}
