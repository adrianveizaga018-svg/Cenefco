<?php

namespace App\Application\Noticias\Commands;

final readonly class DeleteNoticiaCommand
{
    public function __construct(public int $id) {}
}
