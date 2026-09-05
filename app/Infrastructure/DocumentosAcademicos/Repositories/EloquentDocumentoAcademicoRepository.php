<?php

namespace App\Infrastructure\DocumentosAcademicos\Repositories;

use App\Application\DocumentosAcademicos\DTOs\DocumentoAcademicoDTO;
use App\Domain\DocumentosAcademicos\Contracts\DocumentoAcademicoRepositoryInterface;
use App\Domain\DocumentosAcademicos\Exceptions\DocumentoAcademicoNotFoundException;
use App\Infrastructure\DocumentosAcademicos\Models\DocumentoAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentDocumentoAcademicoRepository implements DocumentoAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idUs, ?int $idFechapago, ?int $idFechadoc): array
    {
        $q = DocumentoAcademico::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }

        if ($idFechapago !== null) {
            $q->where('id_fechapago', $idFechapago);
        }

        if ($idFechadoc !== null) {
            $q->where('id_fechadoc', $idFechadoc);
        }

        $paginated = $q->orderBy('id_documento', 'desc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => DocumentoAcademicoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): DocumentoAcademicoDTO
    {
        $doc = DocumentoAcademico::where('id_documento', $id)->first();

        if (! $doc) {
            throw new DocumentoAcademicoNotFoundException($id);
        }

        return DocumentoAcademicoDTO::fromModel($doc);
    }

    public function create(array $data): DocumentoAcademicoDTO
    {
        $doc = DocumentoAcademico::create($data);
        return DocumentoAcademicoDTO::fromModel($doc);
    }

    public function update(int $id, array $data): DocumentoAcademicoDTO
    {
        $doc = DocumentoAcademico::where('id_documento', $id)->first();

        if (! $doc) {
            throw new DocumentoAcademicoNotFoundException($id);
        }

        $doc->update($data);
        return DocumentoAcademicoDTO::fromModel($doc->fresh());
    }

    public function delete(int $id): void
    {
        $doc = DocumentoAcademico::where('id_documento', $id)->first();

        if (! $doc) {
            throw new DocumentoAcademicoNotFoundException($id);
        }

        $doc->update(['estado' => 0]);
    }
}
