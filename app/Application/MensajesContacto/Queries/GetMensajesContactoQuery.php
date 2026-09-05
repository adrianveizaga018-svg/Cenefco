<?php

namespace App\Application\MensajesContacto\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetMensajesContactoQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
