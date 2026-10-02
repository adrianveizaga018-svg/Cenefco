<?php
namespace App\Application\FormatosHojaSolicitud\QueryHandlers;
use App\Application\FormatosHojaSolicitud\Queries\GetFormatosHojaSolicitudQuery;
use App\Domain\FormatosHojaSolicitud\Contracts\FormatoHojaSolicitudRepositoryInterface;
class GetFormatosHojaSolicitudQueryHandler {
    public function __construct(private readonly FormatoHojaSolicitudRepositoryInterface $repository) {}
    public function handle(GetFormatosHojaSolicitudQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->conInactivos);
    }
}
