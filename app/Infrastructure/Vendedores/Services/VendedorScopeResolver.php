<?php

namespace App\Infrastructure\Vendedores\Services;

use App\Infrastructure\Usuarios\Models\User;
use App\Infrastructure\Vendedores\Models\Vendedor;
use Illuminate\Support\Facades\DB;

class VendedorScopeResolver
{
    public function esVendedorRestringido(User $user): bool
    {

        return ! $user->tienePermiso('*') && $user->roles->contains('restringido_a_vendedor', true);
    }

    private function vendedorIdsPermitidos(User $user): ?array
    {
        if (! $this->esVendedorRestringido($user)) {
            return null;
        }

        return Vendedor::where('usuario_id', $user->id)->pluck('id')->all();
    }

    public function assertAccesoVendedor(User $user, int $vendedorId): void
    {
        $idsPermitidos = $this->vendedorIdsPermitidos($user);
        if ($idsPermitidos === null) {
            return;
        }
        if (! in_array($vendedorId, $idsPermitidos, true)) {
            abort(403, 'No tienes acceso a este vendedor.');
        }
    }

    public function programaIdsPermitidos(User $user): ?array
    {
        if (! $this->esVendedorRestringido($user)) {
            return null;
        }

        $vendedorIds = Vendedor::where('usuario_id', $user->id)->pluck('id');
        if ($vendedorIds->isEmpty()) {
            return [];
        }

        return DB::table('t_programa')
            ->whereIn('vendedor_id', $vendedorIds)
            ->pluck('id_programa')
            ->all();
    }

    public function idImpPermitidos(User $user): ?array
    {
        if (! $this->esVendedorRestringido($user)) {
            return null;
        }

        $vendedorIds = Vendedor::where('usuario_id', $user->id)->pluck('id');
        if ($vendedorIds->isEmpty()) {
            return [];
        }

        return DB::table('t_programa')
            ->whereIn('vendedor_id', $vendedorIds)
            ->whereNotNull('id_imp')
            ->pluck('id_imp')
            ->all();
    }

    public function assertAccesoPrograma(User $user, ?int $idPrograma): void
    {
        $idsPermitidos = $this->programaIdsPermitidos($user);
        if ($idsPermitidos === null) {
            return;
        }
        if ($idPrograma === null || ! in_array($idPrograma, $idsPermitidos, true)) {
            abort(403, 'No tienes acceso a este curso.');
        }
    }

    public function assertAccesoImparte(User $user, ?int $idImp): void
    {
        $idImpPermitidos = $this->idImpPermitidos($user);
        if ($idImpPermitidos === null) {
            return;
        }
        if ($idImp === null || ! in_array($idImp, $idImpPermitidos, true)) {
            abort(403, 'No tienes acceso a este curso.');
        }
    }

    public function assertAccesoInscripcion(User $user, ?int $idIns): void
    {
        $idImpPermitidos = $this->idImpPermitidos($user);
        if ($idImpPermitidos === null) {
            return;
        }

        $idImp = $idIns ? DB::table('t_inscripcion')->where('id_ins', $idIns)->value('id_imp') : null;
        if ($idImp === null || ! in_array($idImp, $idImpPermitidos, true)) {
            abort(403, 'No tienes acceso a esta inscripción.');
        }
    }
}
