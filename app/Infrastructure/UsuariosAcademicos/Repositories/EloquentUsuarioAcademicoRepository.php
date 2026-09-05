<?php

namespace App\Infrastructure\UsuariosAcademicos\Repositories;

use App\Application\UsuariosAcademicos\DTOs\UsuarioAcademicoDTO;
use App\Domain\UsuariosAcademicos\Contracts\UsuarioAcademicoRepositoryInterface;
use App\Domain\UsuariosAcademicos\Exceptions\UsuarioAcademicoNotFoundException;
use App\Infrastructure\UsuariosAcademicos\Models\UsuarioAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentUsuarioAcademicoRepository implements UsuarioAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?string $tipoestudiante, ?int $idNiv): array
    {
        $q = UsuarioAcademico::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(function ($sq) use ($search) {
                $sq->where('nombre', 'like', "%{$search}%")
                    ->orWhere('appaterno', 'like', "%{$search}%")
                    ->orWhere('ci', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($tipoestudiante !== null) {
            $q->where('tipoestudiante', $tipoestudiante);
        }

        if ($idNiv !== null) {
            $q->where('id_niv', $idNiv);
        }

        $paginated = $q->select([
            'id_us', 'tipoestudiante', 'nombre_usuario', 'categoria',
            'titulo_academico', 'appaterno', 'apmaterno', 'nombre',
            'ci', 'expedido', 'telefono', 'celular', 'genero',
            'email', 'ciudad', 'pais', 'id_universidad', 'id_carrera',
            'estado', 'fecha_reg',
        ])
        ->orderBy('appaterno')
        ->orderBy('nombre')
        ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => UsuarioAcademicoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): UsuarioAcademicoDTO
    {
        $usuario = UsuarioAcademico::where('id_us', $id)->where('id_us_reg', 0)->first();

        if (! $usuario) {
            throw new UsuarioAcademicoNotFoundException($id);
        }

        return UsuarioAcademicoDTO::fromModel($usuario);
    }

    public function create(array $data): UsuarioAcademicoDTO
    {
        $usuario = UsuarioAcademico::create($data);
        return UsuarioAcademicoDTO::fromModel($usuario);
    }

    public function update(int $id, array $data): UsuarioAcademicoDTO
    {
        $usuario = UsuarioAcademico::where('id_us', $id)->where('id_us_reg', 0)->first();

        if (! $usuario) {
            throw new UsuarioAcademicoNotFoundException($id);
        }

        $usuario->update($data);
        return UsuarioAcademicoDTO::fromModel($usuario->fresh());
    }

    public function delete(int $id): void
    {
        $usuario = UsuarioAcademico::where('id_us', $id)->where('id_us_reg', 0)->first();

        if (! $usuario) {
            throw new UsuarioAcademicoNotFoundException($id);
        }

        $usuario->update(['estado' => 0]);
    }
}
