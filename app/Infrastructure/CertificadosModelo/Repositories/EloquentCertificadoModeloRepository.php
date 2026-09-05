<?php
namespace App\Infrastructure\CertificadosModelo\Repositories;
use App\Application\CertificadosModelo\DTOs\CertificadoModeloDTO;
use App\Domain\CertificadosModelo\Contracts\CertificadoModeloRepositoryInterface;
use App\Domain\CertificadosModelo\Exceptions\CertificadoModeloNotFoundException;
use App\Infrastructure\CertificadosModelo\Models\CertificadoModelo;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentCertificadoModeloRepository implements CertificadoModeloRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array {
        $q = CertificadoModelo::query();
        if (!$conInactivos) $q->where('estado', 1);
        $paginated = $q->orderBy('id_certmod', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => CertificadoModeloDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = CertificadoModelo::find($id);
        if (!$row) throw new CertificadoModeloNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return CertificadoModelo::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
