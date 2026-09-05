<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EnviarRecordatoriosCuotas extends Command
{
    protected $signature   = 'cuotas:recordatorios {--dias=3 : Días de anticipación antes del vencimiento}';
    protected $description = 'Envía recordatorio por email a estudiantes con cuotas por vencer en N días';

    public function handle(): int
    {
        $dias = (int) $this->option('dias');
        $fecha = now()->addDays($dias)->format('Y-m-d');

        $this->info("Buscando cuotas con vencimiento el {$fecha}...");

        $cuotas = DB::table('t_fechapago as fp')
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
            ->whereDate('fp.fecha_fin', $fecha)
            ->whereNotNull('u.email')
            ->where('u.email', '!=', '')
            ->select([
                'u.email',
                'u.nombre',
                'u.id_us',
                'fp.id_fechapago',
                'fp.monto_a_pagar',
                'fp.nro_pago',
                'fp.fecha_fin',
                'ins.id_ins',
                DB::raw("COALESCE(prog.nombre_programa, '') as nombre_programa"),
            ])
            ->get();

        if ($cuotas->isEmpty()) {
            $this->info('No hay cuotas próximas a vencer. Nada que hacer.');
            return self::SUCCESS;
        }

        $this->info("Encontradas {$cuotas->count()} cuotas. Enviando recordatorios...");
        $enviados = 0;

        foreach ($cuotas as $c) {
            try {
                $ventaFake = (object) [
                    'id_ins'          => $c->id_ins,
                    'nombre_programa' => $c->nombre_programa,
                ];

                Notification::route('mail', $c->email)
                    ->notifyNow(new \App\Notifications\RecordatorioCuotaNotification(
                        venta:        $ventaFake,
                        nroCuota:     $c->nro_pago ?? '—',
                        monto:        (float) $c->monto_a_pagar,
                        fechaVence:   $c->fecha_fin,
                        nombreEstudiante: $c->nombre,
                    ));

                $enviados++;
            } catch (\Throwable $e) {
                $this->warn("Error enviando a {$c->email}: {$e->getMessage()}");
            }
        }

        $this->info("Recordatorios enviados: {$enviados} / {$cuotas->count()}");

        return self::SUCCESS;
    }
}
