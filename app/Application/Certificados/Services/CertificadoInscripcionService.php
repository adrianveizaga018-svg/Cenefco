<?php

namespace App\Application\Certificados\Services;

use App\Application\Certificados\Commands\GenerarCertificadosLoteCommand;
use App\Application\Certificados\Handlers\GenerarCertificadosLoteHandler;
use App\Application\Inscripciones\Queries\GetInscripcionDetalleQuery;
use App\Application\Inscripciones\QueryHandlers\GetInscripcionDetalleQueryHandler;
use Illuminate\Support\Facades\DB;

/**
 * Emisión de certificado para UNA inscripción (un estudiante en un programa).
 *
 * Reglas:
 *  - Solo si el estudiante no debe nada (pago al contado o plan de cuotas completo).
 *  - Solo con las plantillas asignadas a ese programa en "Config. Post-Inscripción".
 *  - Solo para ese estudiante (no para todo el grupo de la versión).
 */
class CertificadoInscripcionService
{
    public function __construct(
        private readonly GetInscripcionDetalleQueryHandler $detalleHandler,
        private readonly GenerarCertificadosLoteHandler $generarLote,
    ) {}

    /**
     * @return array{
     *   puede_emitir: bool, motivo: ?string, pendiente: ?float, plan_no_asignado: bool,
     *   plantillas: array<int, array{id: int, nombre: string}>, emitidos: array<int, object>
     * }
     */
    public function opciones(int $idIns): array
    {
        $ins       = $this->inscripcion($idIns);
        $resumen   = $this->detalleHandler->handle(new GetInscripcionDetalleQuery($idIns))->resumen;
        $pendiente = $resumen->pendiente;
        $sinPlan   = (bool) $resumen->plan_no_asignado;

        $plantillas = $this->plantillasDelPrograma((int) $ins->id_programa);
        $emitidos   = $this->emitidos((int) $ins->id_us, (int) $ins->id_imp);

        $motivo = match (true) {
            (int) $ins->estado !== 1 => 'La inscripción está dada de baja.',
            $sinPlan                 => 'La inscripción no tiene un plan de pago asignado, así que no se puede confirmar que esté pagada.',
            $pendiente > 0           => sprintf('El estudiante aún debe Bs. %s. Los certificados se emiten solo con el pago completo.', number_format($pendiente, 2)),
            $plantillas === []       => 'Este programa no tiene plantillas de certificado asignadas. Asígnalas en Certificados → Config. Post-Inscripción.',
            count($emitidos) > 0     => 'Ya se emitió el certificado de esta inscripción.',
            default                  => null,
        };

        return [
            'puede_emitir'     => $motivo === null,
            'motivo'           => $motivo,
            'pendiente'        => $pendiente,
            'plan_no_asignado' => $sinPlan,
            'plantillas'       => $plantillas,
            'emitidos'         => $emitidos,
        ];
    }

    /** @return array<string, mixed> resultado del lote (generados, errores, certificados) */
    public function emitir(int $idIns, int $plantillaId): array
    {
        $opciones = $this->opciones($idIns);

        if (! $opciones['puede_emitir']) {
            throw new \DomainException($opciones['motivo']);
        }

        if (! in_array($plantillaId, array_column($opciones['plantillas'], 'id'), true)) {
            throw new \DomainException('Esa plantilla no está asignada al programa de esta inscripción.');
        }

        $ins = $this->inscripcion($idIns);

        // La generación trabaja sobre la lista de aprobados: se registra solo a este estudiante
        DB::table('t_lista_aprobados')->insertOrIgnore([
            'imparte_id'         => $ins->id_imp,
            'usuario_id'         => $ins->id_us,
            'inscripcion_id'     => $idIns,
            'condicion'          => 'aprobado',
            'estado_certificado' => 'pendiente',
            'registrado_por'     => auth()->id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return $this->generarLote->handle(new GenerarCertificadosLoteCommand(
            imparteId:   (int) $ins->id_imp,
            plantillaId: $plantillaId,
            usuarioIds:  [(int) $ins->id_us],
        ));
    }

    private function inscripcion(int $idIns): object
    {
        $ins = DB::table('t_inscripcion as ins')
            ->join('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
            ->where('ins.id_ins', $idIns)
            ->first(['ins.id_ins', 'ins.id_us', 'ins.id_imp', 'ins.estado', 'imp.id_mat as id_programa']);

        if (! $ins) {
            throw new \DomainException('Inscripción no encontrada.');
        }

        return $ins;
    }

    /** @return array<int, array{id: int, nombre: string}> */
    private function plantillasDelPrograma(int $idPrograma): array
    {
        return DB::table('web_cert_config_item as item')
            ->join('web_cert_config_programa as cfg', 'cfg.id', '=', 'item.config_id')
            ->join('t_cert_plantilla as pl', 'pl.id', '=', 'item.plantilla_id')
            ->where('cfg.programa_id', $idPrograma)
            ->where('cfg.activo', true)
            ->where('item.activo', true)
            ->where('pl.estado', 'activo')
            ->whereNull('pl.deleted_at')
            // Sin imagen de fondo no se puede generar el certificado
            ->whereNotNull('pl.imagen_url')
            ->where('pl.imagen_url', '!=', '')
            ->orderBy('item.orden')
            ->get(['pl.id', DB::raw('COALESCE(item.nombre_cert, pl.nombre) as nombre')])
            ->unique('id')
            ->map(fn ($p) => ['id' => (int) $p->id, 'nombre' => (string) $p->nombre])
            ->values()
            ->all();
    }

    /** @return array<int, object> */
    private function emitidos(int $idUs, int $idImp): array
    {
        return DB::table('t_certificado')
            ->where('usuario_id', $idUs)
            ->where('imparte_id', $idImp)
            ->where('estado', '!=', 'anulado')
            ->orderByDesc('id')
            ->get(['id', 'plantilla_id', 'codigo_verificacion', 'archivo_url', 'estado', 'created_at'])
            ->all();
    }
}
