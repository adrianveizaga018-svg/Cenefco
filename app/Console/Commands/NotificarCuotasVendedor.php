<?php

namespace App\Console\Commands;

use App\Application\CompromisosCobro\Services\VendedorDeInscripcionResolver;
use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class NotificarCuotasVendedor extends Command
{
    protected $signature   = 'cuotas:notificar-vendedor {--dias=3 : Días de anticipación antes del vencimiento}';
    protected $description = 'Notifica al vendedor asignado al curso las cuotas de sus estudiantes que están por vencer o que vencieron sin pago';

    public function handle(
        VendedorDeInscripcionResolver $vendedorResolver,
        NotificacionService $notificacionService,
    ): int {
        $dias = (int) $this->option('dias');

        $this->notificarProximas($dias, $vendedorResolver, $notificacionService);
        $this->notificarVencidas($vendedorResolver, $notificacionService);

        return self::SUCCESS;
    }

    private function baseQuery(): Builder
    {
        return DB::table('t_fechapago as fp')
            ->join('t_inscripcion as ins', 'ins.id_plan', '=', 'fp.id_plan')
            ->join('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_programa as prog', function ($j) {
                $j->on('prog.id_imp', '=', 'ins.id_imp')
                  ->whereRaw('prog.id_us_reg = (SELECT MIN(p2.id_us_reg) FROM t_programa p2 WHERE p2.id_imp = prog.id_imp)');
            })
            ->leftJoin('t_pago as p', function ($j) {
                $j->on('p.id_fechapago', '=', 'fp.id_fechapago')
                  ->where('p.estado', 1);
            })
            ->where('fp.estado', 1)
            ->where('ins.estado', 1)
            ->whereNull('p.id_pago')
            ->select([
                'fp.id_fechapago', 'fp.nro_pago', 'fp.monto_a_pagar', 'fp.fecha_fin',
                'ins.id_ins',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as estudiante_nombre"),
                DB::raw("COALESCE(prog.nombre_programa, '') as nombre_programa"),
            ]);
    }

    private function notificarProximas(int $dias, VendedorDeInscripcionResolver $vendedorResolver, NotificacionService $notificacionService): void
    {
        $fecha  = now()->addDays($dias)->format('Y-m-d');
        $cuotas = $this->baseQuery()->whereDate('fp.fecha_fin', $fecha)->get();

        $this->info("Cuotas próximas a vencer el {$fecha}: {$cuotas->count()}");

        foreach ($cuotas as $c) {
            $this->notificarUna($c, $vendedorResolver, $notificacionService,
                tipo: TipoNotificacionEnum::CUOTA_PROXIMA,
                titulo: 'Cuota por vencer',
                mensaje: "La cuota {$c->nro_pago} de " . ($c->estudiante_nombre ?: 'un estudiante')
                    . " (Bs. {$c->monto_a_pagar}) vence el {$c->fecha_fin}."
                    . ($c->nombre_programa ? " Curso: {$c->nombre_programa}." : ''),
                icono: 'lucideCalendarClock',
                color: '#f59e0b',
            );
        }
    }

    private function notificarVencidas(VendedorDeInscripcionResolver $vendedorResolver, NotificacionService $notificacionService): void
    {
        $fecha  = now()->subDay()->format('Y-m-d');
        $cuotas = $this->baseQuery()->whereDate('fp.fecha_fin', $fecha)->get();

        $this->info("Cuotas vencidas ayer ({$fecha}) sin pago: {$cuotas->count()}");

        foreach ($cuotas as $c) {
            $this->notificarUna($c, $vendedorResolver, $notificacionService,
                tipo: TipoNotificacionEnum::PAGO_VENCIDO,
                titulo: 'Cuota vencida sin pago',
                mensaje: "La cuota {$c->nro_pago} de " . ($c->estudiante_nombre ?: 'un estudiante')
                    . " (Bs. {$c->monto_a_pagar}) venció el {$c->fecha_fin} y sigue sin pago registrado."
                    . ($c->nombre_programa ? " Curso: {$c->nombre_programa}." : ''),
                icono: 'lucideAlertTriangle',
                color: '#dc2626',
            );
        }
    }

    private function notificarUna(
        object $cuota,
        VendedorDeInscripcionResolver $vendedorResolver,
        NotificacionService $notificacionService,
        TipoNotificacionEnum $tipo,
        string $titulo,
        string $mensaje,
        string $icono,
        string $color,
    ): void {
        $usuarioVendedorId = $vendedorResolver->resolverUsuarioId($cuota->id_ins);
        $urlAccion = "/cenefco/inscripcion-detail/{$cuota->id_ins}";

        if ($usuarioVendedorId) {
            $notificacionService->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         $tipo,
                titulo:       $titulo,
                mensaje:      $mensaje,
                prioridad:    PrioridadEnum::ALTA,
                usuarioId:    $usuarioVendedorId,
                urlAccion:    $urlAccion,
                icono:        $icono,
                color:        $color,
            );
        } else {
            $notificacionService->enviarAPermiso(
                permiso:      'pagos.editar',
                tipo:         $tipo,
                titulo:       $titulo,
                mensaje:      $mensaje,
                prioridad:    PrioridadEnum::ALTA,
                urlAccion:    $urlAccion,
                icono:        $icono,
                color:        $color,
            );
        }
    }
}
