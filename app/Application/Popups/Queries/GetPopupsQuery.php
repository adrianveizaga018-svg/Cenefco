<?php
namespace App\Application\Popups\Queries;
use App\Shared\Kernel\DTOs\PaginationDTO;
final readonly class GetPopupsQuery { public function __construct(public PaginationDTO $pagination) {} }
