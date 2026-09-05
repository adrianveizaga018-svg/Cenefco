<?php

namespace App\Http\Requests\Concerns;

use App\Infrastructure\Usuarios\Models\Role;

trait ValidaEscaladaPermisos
{
    private function permisosNoAutorizados(array $codigos): array
    {
        $user = $this->user();
        if (! $user || $user->tienePermiso('*')) {
            return [];
        }

        return array_values(array_filter($codigos, fn ($codigo) => ! $user->tienePermiso($codigo)));
    }

    private function permisosAgregadosNoAutorizados(array $permisosActuales, array $permisosNuevos): array
    {
        return $this->permisosNoAutorizados(array_values(array_diff($permisosNuevos, $permisosActuales)));
    }

    private function rolExcedePermisosDelSolicitante(?int $rolIdNuevo, ?int $rolIdActual = null): ?string
    {
        if (! $rolIdNuevo || $rolIdNuevo === $rolIdActual) {
            return null;
        }

        $user = $this->user();
        if (! $user || $user->tienePermiso('*')) {
            return null;
        }

        $codigos = Role::with('permisos')->find($rolIdNuevo)?->permisos->pluck('codigo')->all() ?? [];
        $noAutorizados = $this->permisosNoAutorizados($codigos);

        if (empty($noAutorizados)) {
            return null;
        }

        return 'No puedes asignar un rol con permisos que tú mismo no tienes: '.implode(', ', $noAutorizados).'.';
    }
}
