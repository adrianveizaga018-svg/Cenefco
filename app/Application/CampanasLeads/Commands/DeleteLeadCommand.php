<?php

namespace App\Application\CampanasLeads\Commands;

final readonly class DeleteLeadCommand
{
    public function __construct(
        public int $campanaLeadId,
        public int $id,
    ) {}
}
