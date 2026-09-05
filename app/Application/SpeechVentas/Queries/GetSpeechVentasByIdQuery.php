<?php

namespace App\Application\SpeechVentas\Queries;

final readonly class GetSpeechVentasByIdQuery
{
    public function __construct(public int $id) {}
}
