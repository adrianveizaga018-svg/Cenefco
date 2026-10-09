<?php

namespace App\Application\Usuarios\Services;

use App\Domain\Usuarios\Exceptions\CredencialesException;
use App\Infrastructure\Usuarios\Models\Role;
use App\Infrastructure\Usuarios\Models\User;
use App\Notifications\CredencialesAccesoNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Envía al estudiante inscrito en Caja sus credenciales de acceso.
 *
 * Caja solo crea la ficha académica (t_usuario). Aquí se crea —o se reutiliza—
 * su cuenta de la plataforma (usuarios), sin contraseña, y se le envía un enlace
 * para que la cree él mismo. El cajero nunca ve ni define contraseñas.
 */
class CredencialesEstudianteService
{
    public function __construct(
        private readonly PromocionEstudianteService $promocion,
    ) {}

    /**
     * @param  int  $idUs  estudiante en t_usuario
     * @return array{email: string, cuenta_nueva: bool}
     */
    public function enviar(int $idUs): array
    {
        $ficha = DB::table('t_usuario')
            ->where('id_us', $idUs)
            ->first(['nombre', 'appaterno', 'apmaterno', 'ci', 'email', 'celular']);

        if (! $ficha) {
            throw new CredencialesException('No se encontró al estudiante.', 404);
        }

        $email = mb_strtolower(trim((string) $ficha->email));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new CredencialesException('El estudiante no tiene un correo válido registrado. Agrégalo en su ficha y vuelve a intentar.');
        }

        [$user, $cuentaNueva] = DB::transaction(fn () => $this->cuentaDe($ficha, $email));

        if ($this->tienePagoActivo($idUs)) {
            $this->promocion->promoverPorIdUs($idUs);
        }

        $token = Password::broker('activacion')->createToken($user);

        try {
            $user->notify(new CredencialesAccesoNotification($token, $this->programasDe($idUs)));
        } catch (\Throwable $e) {
            report($e);
            throw new CredencialesException('No se pudo enviar el correo. Revisa la configuración de correo del sistema e inténtalo de nuevo.', 502);
        }

        return ['email' => $user->email, 'cuenta_nueva' => $cuentaNueva];
    }

    /** @return array{0: User, 1: bool} */
    private function cuentaDe(object $ficha, string $email): array
    {
        $porCi    = $ficha->ci ? User::where('ci', $ficha->ci)->first() : null;
        $porEmail = User::where('email', $email)->first();

        // El correo de la ficha ya es de otra persona: no se mezclan cuentas
        if ($porEmail && (($porCi && $porCi->id !== $porEmail->id) || (! $porCi && $porEmail->ci && $porEmail->ci !== $ficha->ci))) {
            throw new CredencialesException('El correo de este estudiante ya pertenece a la cuenta de otra persona. Corrige el correo en su ficha.');
        }

        // Ya tiene cuenta: se reutiliza (el correo se envía al correo de su cuenta)
        if ($user = $porCi ?? $porEmail) {
            if (! $user->ci && $ficha->ci) {
                $user->forceFill(['ci' => $ficha->ci])->save();
            }

            return [$user, false];
        }

        $user = User::create([
            'nombre'           => $ficha->nombre ?: 'Estudiante',
            'apellido'         => $ficha->appaterno ?? '',
            'apellido_materno' => $ficha->apmaterno,
            'email'            => $email,
            'ci'               => $ficha->ci,
            'telefono'         => $ficha->celular,
            // Contraseña imposible de adivinar: la real la crea el estudiante con el enlace
            'password_hash'    => Hash::make(Str::random(48)),
            'tipo'             => 'ciudadano',
            'activo'           => true,
            'email_verificado' => false,
        ]);

        if ($rol = Role::where('nombre', 'participante')->value('id')) {
            $user->roles()->attach($rol, ['asignado_at' => now(), 'asignado_por' => $user->id]);
        }

        return [$user, true];
    }

    private function tienePagoActivo(int $idUs): bool
    {
        return DB::table('t_pago')->where('id_us', $idUs)->where('estado', 1)->exists();
    }

    /** @return string[] */
    private function programasDe(int $idUs): array
    {
        return DB::table('t_inscripcion as ins')
            ->join('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
            ->join('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
            ->where('ins.id_us', $idUs)
            ->where('ins.estado', 1)
            ->orderByDesc('ins.id_ins')
            ->limit(5)
            ->pluck('prog.nombre_programa')
            ->unique()
            ->values()
            ->all();
    }
}
