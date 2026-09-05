<?php

namespace App\Application\CampanasLeads\Queries;

final readonly class GetCampanaLeadByIdQuery
{
    public function __construct(public int $id) {}
}
