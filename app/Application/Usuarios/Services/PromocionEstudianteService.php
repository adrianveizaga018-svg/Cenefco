<?php

namespace App\Application\Usuarios\Services;

use Illuminate\Support\Facades\DB;

/**
 * Única regla de ascenso participante → Estudiante.
 *
 * Una cuenta nace como "participante" y pasa a "Estudiante" cuando se registra
 * el primer pago de una inscripción (caja, cobranzas, pago manual u online).
 * Llamar siempre DESPUÉS de confirmar la transacción del pago: un fallo aquí
 * nunca debe revertir ni bloquear un pago.
 */
class PromocionEstudianteService
{
    private const ROL_ESTUDIANTE = 'Estudiante';

    private const ROLES_PREVIOS = ['participante', 'Ciudadano'];

    /** @param int|null $idUs id del estudiante en t_usuario (legado) */
    public function promoverPorIdUs(?int $idUs): void
    {
        if (! $idUs) {
            return;
        }

        try {
            $usuarioId = $this->resolverCuenta($idUs);
            if ($usuarioId) {
                $this->promover($usuarioId);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** La cuenta web (usuarios) se enlaza con el legado (t_usuario) por CI y, si falta, por email. */
    private function resolverCuenta(int $idUs): ?int
    {
        $legado = DB::table('t_usuario')->where('id_us', $idUs)->first(['ci', 'email']);
        if (! $legado) {
            return null;
        }

        $cuentas = DB::table('usuarios')->whereNull('deleted_at');

        if (! empty($legado->ci)) {
            $id = (clone $cuentas)->where('ci', $legado->ci)->value('id');
            if ($id) {
                return (int) $id;
            }
        }

        if (! empty($legado->email)) {
            $id = (clone $cuentas)->where('email', $legado->email)->value('id');
            if ($id) {
                return (int) $id;
            }
        }

        return null;
    }

    private function promover(int $usuarioId): void
    {
        $rolEstudiante = DB::table('roles')->where('nombre', self::ROL_ESTUDIANTE)->value('id');
        if (! $rolEstudiante) {
            return;
        }

        $rolesPrevios  = DB::table('roles')->whereIn('nombre', self::ROLES_PREVIOS)->pluck('id')->all();
        $rolesActuales = DB::table('usuarios_roles')->where('usuario_id', $usuarioId)->pluck('rol_id')->all();

        // Solo asciende a quien es participante: el personal conserva su rol
        if (in_array($rolEstudiante, $rolesActuales) || ! array_intersect($rolesActuales, $rolesPrevios)) {
            return;
        }

        DB::transaction(function () use ($usuarioId, $rolEstudiante, $rolesPrevios) {
            DB::table('usuarios_roles')
                ->where('usuario_id', $usuarioId)
                ->whereIn('rol_id', $rolesPrevios)
                ->delete();

            DB::table('usuarios_roles')->insert([
                'usuario_id'   => $usuarioId,
                'rol_id'       => $rolEstudiante,
                'asignado_at'  => now(),
                'asignado_por' => $usuarioId,
            ]);
        });
    }
}
