<?php

namespace App\Application\CertPlantillas\QueryHandlers;

use App\Application\CertPlantillas\DTOs\CertPlantillaDTO;
use App\Application\CertPlantillas\Queries\GetCertPlantillaByIdQuery;
use App\Application\CertPlantillaCampos\DTOs\CertPlantillaCampoDTO;
use App\Domain\CertPlantillas\Contracts\CertPlantillaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GetCertPlantillaByIdQueryHandler
{
    public function __construct(
        private readonly CertPlantillaRepositoryInterface $repository
    ) {}

    public function handle(GetCertPlantillaByIdQuery $query): CertPlantillaDTO
    {
        $plantilla = $this->repository->findById($query->id);

        $campos = DB::table('t_cert_plantilla_campo')
            ->where('plantilla_id', $query->id)
            ->orderBy('orden')
            ->orderBy('clave')
            ->get()
            ->map(fn ($row) => CertPlantillaCampoDTO::fromRow($row))
            ->all();

        return CertPlantillaDTO::fromRow($plantilla, $campos);
    }
}
