<?php
namespace App\Application\FormatosHojaSolicitud\QueryHandlers;
use App\Application\FormatosHojaSolicitud\DTOs\FormatoHojaSolicitudDTO;
use App\Application\FormatosHojaSolicitud\Queries\GetFormatoHojaSolicitudByIdQuery;
use App\Domain\FormatosHojaSolicitud\Contracts\FormatoHojaSolicitudRepositoryInterface;
class GetFormatoHojaSolicitudByIdQueryHandler {
    public function __construct(private readonly FormatoHojaSolicitudRepositoryInterface $repository) {}
    public function handle(GetFormatoHojaSolicitudByIdQuery $query): FormatoHojaSolicitudDTO {
        return FormatoHojaSolicitudDTO::fromRow($this->repository->findById($query->idFormatoHojaSolicitud));
    }
}
