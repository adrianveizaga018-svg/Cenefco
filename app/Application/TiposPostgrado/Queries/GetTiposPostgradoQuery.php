<?php

namespace App\Application\TiposPostgrado\Queries;

final readonly class GetTiposPostgradoQuery
{
    public function __construct(
        public int $pageIndex,
        public int $pageSize,
        public ?int $idPlan,
        public ?int $idTipopago,
        public bool $conInactivos,
    ) {}
}
