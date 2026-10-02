<?php

namespace App\Console\Commands;

use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerarAlertasNotificaciones extends Command
{
    protected $signature   = 'notificaciones:generar-alertas';
    protected $description = 'Genera alertas automáticas: cuotas próximas a vencer e inscripciones sin pago';

    public function __construct(private readonly NotificacionService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->alertasCuotasProximas();
        $this->alertasInscripcionesSinPago();

        $this->info('Alertas generadas correctamente.');
        return self::SUCCESS;
    }

    private function alertasCuotasProximas(): void
    {
        $limite = Carbon::now()->addDays(3)->toDateString();
        $hoy    = Carbon::now()->toDateString();

        $cuotas = DB::table('t_fechapago as fp')
            ->join('t_inscripcion as ins', 'ins.id_plan', '=', 'fp.id_plan')
            ->join('t_usuario as usu', function ($j) {
                $j->on('ins.id_us', '=', 'usu.id_us')
                  ->whereRaw('usu.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = usu.id_us)');
            })
            ->join('usuarios', 'usuarios.email', '=', 'usu.email')
            ->leftJoin('t_pago as pag', function ($j) {
                $j->on('pag.id_fechapago', '=', 'fp.id_fechapago')->where('pag.estado', 1);
            })
            ->whereNull('pag.id_pago')
            ->where('fp.estado', 1)
            ->where('ins.estado', 1)
            ->whereDate('fp.fecha_fin', '>=', $hoy)
            ->whereDate('fp.fecha_fin', '<=', $limite)
            ->select('usuarios.id as usuario_id', 'usu.nombre', 'fp.id_fechapago', 'fp.fecha_fin', 'fp.monto_a_pagar')
            ->distinct()
            ->get();

        $enviadas = 0;

        foreach ($cuotas as $cuota) {
            if ($this->yaNotificado($cuota->usuario_id, TipoNotificacionEnum::CUOTA_PROXIMA, 'fechapago', $cuota->id_fechapago)) {
                continue;
            }

            $dias = Carbon::now()->diffInDays(Carbon::parse($cuota->fecha_fin));
            $this->service->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         TipoNotificacionEnum::CUOTA_PROXIMA,
                titulo:       'Cuota próxima a vencer',
                mensaje:      "Tienes una cuota de Bs. {$cuota->monto_a_pagar} que vence el {$cuota->fecha_fin} ({$dias} día(s)).",
                prioridad:    PrioridadEnum::ALTA,
                usuarioId:    $cuota->usuario_id,
                urlAccion:    '/cenefco/pagos-academicos',
                icono:        'lucideAlarmClock',
                color:        '#f59e0b',
            );

            $this->marcarNotificado($cuota->usuario_id, TipoNotificacionEnum::CUOTA_PROXIMA, 'fechapago', $cuota->id_fechapago);
            $enviadas++;
        }

        $this->line("  Cuotas próximas: {$enviadas} alertas nuevas (de {$cuotas->count()} candidatas)");
    }

    private function alertasInscripcionesSinPago(): void
    {
        $limite = Carbon::now()->subDays(7)->toDateString();

        $inscripciones = DB::table('t_inscripcion as ins')
            ->join('t_usuario as usu', function ($j) {
                $j->on('ins.id_us', '=', 'usu.id_us')
                  ->whereRaw('usu.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = usu.id_us)');
            })
            ->join('usuarios', 'usuarios.email', '=', 'usu.email')
            ->leftJoin('t_pago as pag', function ($j) {
                $j->on('pag.id_ins', '=', 'ins.id_ins')->where('pag.estado', 1);
            })
            ->whereNull('pag.id_pago')
            ->where('ins.estado', 1)
            ->whereDate('ins.fecha_ins', '<=', $limite)
            ->select('usuarios.id as usuario_id', 'usu.nombre', 'ins.id_ins', 'ins.fecha_ins')
            ->distinct()
            ->get();

        $enviadas = 0;

        foreach ($inscripciones as $ins) {
            if ($this->yaNotificado($ins->usuario_id, TipoNotificacionEnum::PAGO_VENCIDO, 'inscripcion', $ins->id_ins)) {
                continue;
            }

            $this->service->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         TipoNotificacionEnum::PAGO_VENCIDO,
                titulo:       'Inscripción sin pago registrado',
                mensaje:      "Tu inscripción del {$ins->fecha_ins} no tiene pagos registrados. Regulariza tu situación.",
                prioridad:    PrioridadEnum::ALTA,
                usuarioId:    $ins->usuario_id,
                urlAccion:    '/cenefco/pagos-academicos',
                icono:        'lucideCircleAlert',
                color:        '#ef4444',
            );

            $this->marcarNotificado($ins->usuario_id, TipoNotificacionEnum::PAGO_VENCIDO, 'inscripcion', $ins->id_ins);
            $enviadas++;
        }

        $this->line("  Sin pago > 7 días: {$enviadas} alertas nuevas (de {$inscripciones->count()} candidatas)");
    }

    private function yaNotificado(int $usuarioId, TipoNotificacionEnum $tipo, string $referenciaTipo, int $referenciaId): bool
    {
        return DB::table('alertas_notificaciones_enviadas')
            ->where('usuario_id', $usuarioId)
            ->where('tipo', $tipo->value)
            ->where('referencia_tipo', $referenciaTipo)
            ->where('referencia_id', $referenciaId)
            ->exists();
    }

    private function marcarNotificado(int $usuarioId, TipoNotificacionEnum $tipo, string $referenciaTipo, int $referenciaId): void
    {
        DB::table('alertas_notificaciones_enviadas')->insert([
            'usuario_id'      => $usuarioId,
            'tipo'            => $tipo->value,
            'referencia_tipo' => $referenciaTipo,
            'referencia_id'   => $referenciaId,
            'created_at'      => now(),
        ]);
    }
}
