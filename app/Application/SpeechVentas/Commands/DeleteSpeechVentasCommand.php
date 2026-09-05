<?php

namespace App\Application\SpeechVentas\Commands;

final readonly class DeleteSpeechVentasCommand
{
    public function __construct(public int $id) {}
}
