<?php

namespace App\Application\Ventas\QueryHandlers;

use App\Application\Ventas\Queries\GetVentaByIdQuery;
use App\Domain\Ventas\Contracts\VentaRepositoryInterface;
use App\Domain\Ventas\Exceptions\VentaNotFoundException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class GenerarComprobantePdfHandler
{
    private const METODOS_LABEL = [
        'efectivo'          => 'Efectivo',
        'deposito_bancario' => 'Depósito',
        'pago_online'       => 'Online',
        'qr'                => 'QR',
    ];

    private const REGLAMENTO_DEFAULT = [
        'reglas_asistencia'  => "Asistir puntualmente a todas las sesiones programadas.\nMáximo 25% de inasistencias permitidas.\nJustificar ausencias con anticipación.",
        'reglas_evaluacion'  => "Entregar trabajos en los plazos establecidos.\nParticipación activa en actividades del programa.\nAprobación mínima: 51 sobre 100 puntos.",
        'reglas_pagos'       => "Cancelar cuotas en las fechas acordadas.\nPagos fuera de plazo generan mora del 5% mensual.\nSin mora pendiente para recibir certificado.",
        'reglas_conducta'    => "Trato respetuoso a docentes y compañeros.\nProhibido el uso de dispositivos en modo silencioso durante clases.\nCumplir el reglamento interno de CENEFCO.",
        'reglas_plataformas' => "Uso responsable de grupos de WhatsApp y Zoom.\nProhibido compartir contenidos del curso sin autorización.\nMantener micrófono silenciado cuando no se participe.",
        'reglas_derechos'    => "Acceso a material didáctico del programa.\nDerecho a certificado de aprobación.\nAtención personalizada por secretaría académica.",
    ];

    public function __construct(
        private readonly VentaRepositoryInterface $repository,
    ) {}

    public function handle(int $id): Response
    {
        ['venta' => $venta, 'pdf' => $pdf] = $this->generar($id);

        return $pdf->stream('comprobante-' . $venta->id_ins . '.pdf');
    }

    public function generar(int $id): array
    {
        $venta = $this->repository->findById($id);

        if (! isset($venta->estado_pago)) {
            $ta = (float) ($venta->total_a_pagar ?? 0);
            $tp = (float) ($venta->total_pagado  ?? 0);
            $venta->estado_pago    = match (true) {
                $ta > 0 && $tp >= $ta => 'pagado',
                $tp > 0               => 'parcial',
                default               => 'pendiente',
            };
            $venta->saldo_pendiente = max(0.0, $ta - $tp);
        }

        $estadoLabel = match ($venta->estado_pago) {
            'pagado'   => 'Pagado',
            'parcial'  => 'Pago Parcial',
            default    => 'Pendiente',
        };

        $reglamento = null;
        if (! empty($venta->id_programa)) {
            $reglamento = \DB::table('web_reglamento_programa')
                ->where('id_programa', $venta->id_programa)
                ->first();
        }

        $bienvenida = $reglamento?->bienvenida
            ?? "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al programa \"{$venta->nombre_programa}\". Estamos comprometidos a brindarle una formación académica de calidad, a través de docentes altamente calificados y metodologías actualizadas.";

        $secciones = [
            ['titulo' => 'Asistencia y Puntualidad',              'color' => 'azul',  'campo' => 'reglas_asistencia'],
            ['titulo' => 'Evaluaciones y Trabajos',               'color' => 'azul',  'campo' => 'reglas_evaluacion'],
            ['titulo' => 'Compromisos de Pago',                   'color' => 'azul',  'campo' => 'reglas_pagos'],
            ['titulo' => 'Convivencia y Respeto',                 'color' => 'azul',  'campo' => 'reglas_conducta'],
            ['titulo' => 'Plataformas Digitales (Zoom/WhatsApp)', 'color' => 'azul',  'campo' => 'reglas_plataformas'],
            ['titulo' => 'Derechos del Estudiante',               'color' => 'verde', 'campo' => 'reglas_derechos'],
        ];

        foreach ($secciones as &$s) {
            $texto = $reglamento?->{$s['campo']} ?? self::REGLAMENTO_DEFAULT[$s['campo']] ?? '';
            $s['items'] = array_filter(
                array_map('trim', explode("\n", $texto)),
                fn ($l) => $l !== ''
            );
        }
        unset($s);

        $pagos = collect($venta->pagos ?? []);
        $venta->pagos = $pagos;

        $pdf = Pdf::loadView('pdf.comprobante', [
            'venta'       => $venta,
            'estadoLabel' => $estadoLabel,
            'bienvenida'  => $bienvenida,
            'secciones'   => $secciones,
            'metodosLabel'=> self::METODOS_LABEL,
        ])->setPaper('letter', 'portrait');

        return ['venta' => $venta, 'pdf' => $pdf];
    }
}
