<?php

namespace App\Infrastructure\Usuarios\Repositories;

use App\Application\Roles\DTOs\RoleDTO;
use App\Domain\Roles\Exceptions\RoleNotFoundException;
use App\Domain\Usuarios\Contracts\RoleRepositoryInterface;
use App\Infrastructure\Permisos\Models\Permiso;
use App\Infrastructure\Usuarios\Models\Role;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function all(): array
    {
        return Role::where('activo', true)
            ->with('permisos')
            ->get()
            ->map(fn ($r) => RoleDTO::fromModel($r))
            ->all();
    }

    public function findById(int $id): RoleDTO
    {
        $role = Role::with('permisos')->find($id);
        if (! $role) {
            throw new RoleNotFoundException($id);
        }

        return RoleDTO::fromModel($role);
    }

    public function create(array $data): RoleDTO
    {
        $permisos = $data['permisos'] ?? [];
        unset($data['permisos']);

        $role = Role::create($data);
        $role->permisos()->sync($this->idsDePermisos($permisos));

        return RoleDTO::fromModel($role->load('permisos'));
    }

    public function update(int $id, array $data): RoleDTO
    {
        $role = Role::with('permisos')->find($id);
        if (! $role) {
            throw new RoleNotFoundException($id);
        }

        
        
        
        if (array_key_exists('permisos', $data)) {
            $role->permisos()->sync($this->idsDePermisos($data['permisos'] ?? []));
            unset($data['permisos']);
        }

        $role->update($data);

        return RoleDTO::fromModel($role->load('permisos'));
    }

    private function idsDePermisos(array $codigos): array
    {
        if (empty($codigos)) {
            return [];
        }

        return Permiso::whereIn('codigo', $codigos)->pluck('id')->all();
    }

    public function delete(int $id): void
    {
        $role = Role::find($id);
        if (! $role) {
            throw new RoleNotFoundException($id);
        }

        $role->delete();
    }
}
