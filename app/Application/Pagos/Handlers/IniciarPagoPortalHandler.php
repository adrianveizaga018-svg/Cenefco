<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\IniciarPagoPortalCommand;
use App\Infrastructure\Cursos\Models\Curso;
use App\Infrastructure\Inscripciones\Models\Inscripcion;
use App\Infrastructure\Pagos\Models\PagoOnlineSession;
use App\Infrastructure\Pasarelas\PagosYaService;
use App\Infrastructure\UsuariosAcademicos\Models\UsuarioAcademico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IniciarPagoPortalHandler
{
    public function __construct(private readonly PagosYaService $pagosYa) {}

    public function handle(IniciarPagoPortalCommand $command): array
    {
        return DB::transaction(function () use ($command) {

            $usuario = UsuarioAcademico::where('ci', $command->ci)->first();

            if (! $usuario) {

                $nextId = (DB::table('t_usuario')->lockForUpdate()->max('id_us') ?? 0) + 1;
                $usuario = UsuarioAcademico::create([
                    'id_us'     => $nextId,
                    'id_us_reg' => 0,
                    'nombre'    => $command->nombre,
                    'appaterno' => $command->appaterno,
                    'ci'        => $command->ci,
                    'expedido'  => $command->expedido ?? 'LP',
                    'email'     => $command->email,
                    'celular'   => $command->telefono,
                    'estado'    => 1,
                    'fecha_reg' => now()->toDateTimeString(),
                ]);
            }

            $programa = Curso::where('id_programa', $command->idPrograma)->firstOrFail();
            $idImp    = $programa->id_imp;

            if (! $idImp) {
                abort(422, 'El programa no tiene una impartición activa configurada.');
            }

            $inscripcionExistente = DB::table('t_inscripcion')
                ->where('id_us', $usuario->id_us)
                ->where('id_imp', $idImp)
                ->where('estado', 1)
                ->first();

            if ($inscripcionExistente) {
                $idIns = $inscripcionExistente->id_ins;
            } else {
                $idIns = DB::table('t_inscripcion')->insertGetId([
                    'id_us'       => $usuario->id_us,
                    'id_imp'      => $idImp,
                    'id_plan'     => $programa->id_plan,
                    'canal_venta' => 'portal',
                    'id_vendedor' => null,
                    'estado'      => 1,
                    'fecha_ins'   => now()->toDateString(),
                    'fecha_reg'   => now()->toDateTimeString(),
                    'id_us_reg'   => 0,
                ], 'id_ins');
            }

            if ($command->idFechapago) {
                $sesionPendiente = PagoOnlineSession::where('id_ins', $idIns)
                    ->where('id_fechapago', $command->idFechapago)
                    ->where('estado', 'pendiente')
                    ->where('expires_at', '>', now())
                    ->first();

                if ($sesionPendiente) {
                    return [
                        'session_id'   => $sesionPendiente->id,
                        'checkout_url' => $sesionPendiente->checkout_url,
                    ];
                }
            }

            $orderId   = 'cenefco-' . $idIns . '-' . Str::upper(Str::random(6));
            $reference = 'ref-' . Str::uuid()->toString();

            $checkout = $this->pagosYa->crearCheckout([
                'order_id'    => $orderId,
                'amount'      => $command->monto,
                'currency'    => 'BOB',
                'description' => "Inscripción CENEFCO — {$programa->nombre_programa}",
                'customer'    => [
                    'name'  => trim("{$command->nombre} {$command->appaterno}"),
                    'email' => $command->email,
                    'phone' => $command->telefono ?? '',
                ],
                'success_url' => $command->successUrl,
                'cancel_url'  => $command->cancelUrl,
                'reference'   => $reference,
            ]);

            $sesion = PagoOnlineSession::create([
                'id_ins'       => $idIns,
                'id_fechapago' => $command->idFechapago,
                'proveedor'    => 'pagosya',
                'checkout_id'  => $checkout->checkout_id,
                'reference'    => $reference,
                'checkout_url' => $checkout->checkout_url,
                'order_id'     => $orderId,
                'monto'        => $command->monto,
                'moneda'       => 'BOB',
                'estado'       => 'pendiente',
                'expires_at'   => now()->addHours(2),
            ]);

            return [
                'session_id'   => $sesion->id,
                'checkout_url' => $sesion->checkout_url,
            ];
        });
    }
}
