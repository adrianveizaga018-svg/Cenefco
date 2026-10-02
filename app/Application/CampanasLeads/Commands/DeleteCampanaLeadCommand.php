<?php

namespace App\Application\CampanasLeads\Commands;

final readonly class DeleteCampanaLeadCommand
{
    public function __construct(public int $id) {}
}
