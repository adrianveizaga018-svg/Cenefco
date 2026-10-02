<?php

namespace App\Application\Usuarios\Services;

use Illuminate\Support\Facades\DB;

/**
 * Reglas de unicidad e identidad para cuentas del portal/panel.
 *
 * El CI enlaza la cuenta (usuarios) con el historial académico (t_usuario),
 * así que no basta con que sea único: quien reclama un CI que ya existe en
 * el legado debe coincidir con el correo o el celular registrados allí.
 */
class IdentidadUnicaService
{
    /**
     * @param  int|null  $usuarioId  cuenta que se está editando (null al registrar)
     * @param  bool  $ciYaEnlazado  la cuenta ya tenía ese CI: no se vuelve a exigir coincidencia con el legado
     * @return array<string, string> errores por campo (vacío = válido)
     */
    public function errores(?int $usuarioId, ?string $email, ?string $ci, ?string $telefono, bool $ciYaEnlazado = false): array
    {
        $ci       = trim((string) $ci);
        $telefono = trim((string) $telefono);
        $email    = mb_strtolower(trim((string) $email));
        $errores  = [];

        if ($ci !== '') {
            if ($this->existeEnOtraCuenta('ci', $ci, $usuarioId)) {
                $errores['ci'] = 'Ese carnet de identidad ya está asociado a otra cuenta. Inicia sesión con esa cuenta o recupera tu contraseña.';
            } elseif (! $ciYaEnlazado && ! $this->coincideConLegado($ci, $email, $telefono)) {
                $errores['ci'] = 'Ese carnet ya figura en nuestros registros con otro correo y celular. Usa los datos con los que te inscribiste o comunícate con CENEFCO.';
            }
        }

        if ($telefono !== '') {
            if ($this->existeEnOtraCuenta('telefono', $telefono, $usuarioId)) {
                $errores['telefono'] = 'Ese número de celular ya está asociado a otra cuenta.';
            } elseif ($ci !== '' && $this->legadoDeOtraPersona('celular', $telefono, $ci)) {
                $errores['telefono'] = 'Ese número de celular ya está registrado a nombre de otra persona.';
            }
        }

        if (! $ciYaEnlazado && $email !== '' && $ci !== '' && ! isset($errores['ci']) && $this->legadoDeOtraPersona('email', $email, $ci)) {
            $errores['ci'] = 'Tu correo figura en nuestros registros con otro carnet de identidad. Revisa el número o comunícate con CENEFCO.';
        }

        return $errores;
    }

    private function existeEnOtraCuenta(string $columna, string $valor, ?int $usuarioId): bool
    {
        return DB::table('usuarios')
            ->where($columna, $valor)
            ->whereNull('deleted_at')
            ->when($usuarioId, fn ($q) => $q->where('id', '!=', $usuarioId))
            ->exists();
    }

    /** Si el CI ya existe en t_usuario, el correo o el celular deben coincidir con lo registrado. */
    private function coincideConLegado(string $ci, string $email, string $telefono): bool
    {
        $registros = DB::table('t_usuario')->where('ci', $ci)->get(['email', 'celular']);

        $conContacto = $registros->filter(fn ($r) => ! empty($r->email) || ! empty($r->celular));
        if ($conContacto->isEmpty()) {
            return true;
        }

        return $conContacto->contains(function ($r) use ($email, $telefono) {
            $mismoEmail   = $email !== '' && mb_strtolower(trim((string) $r->email)) === $email;
            $mismoCelular = $telefono !== ''
                && $this->soloDigitos($r->celular) !== ''
                && $this->soloDigitos($r->celular) === $this->soloDigitos($telefono);

            return $mismoEmail || $mismoCelular;
        });
    }

    /** El valor ya pertenece en t_usuario a alguien con un CI distinto. */
    private function legadoDeOtraPersona(string $columna, string $valor, string $ci): bool
    {
        $ciNormalizado = $this->normalizarCi($ci);

        return DB::table('t_usuario')
            ->where($columna, $valor)
            ->whereNotNull('ci')
            ->pluck('ci')
            ->contains(fn ($otroCi) => $this->normalizarCi($otroCi) !== '' && $this->normalizarCi($otroCi) !== $ciNormalizado);
    }

    /** Ignora espacios, guiones y mayúsculas; conserva el complemento (p. ej. 1234567-1A). */
    private function normalizarCi(?string $ci): string
    {
        return mb_strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', (string) $ci));
    }

    private function soloDigitos(?string $valor): string
    {
        return preg_replace('/\D+/', '', (string) $valor);
    }
}
