<?php

namespace App\Console\Commands;

use App\Application\Certificados\Services\CertificadoService;
use Illuminate\Console\Command;

/**
 * Genera el PDF de los certificados emitidos que se quedaron sin archivo
 * (el lote lo dejaba a un servicio Django que ya no existe). Conserva el código.
 */
class RepararPdfCertificadosCommand extends Command
{
    protected $signature = 'certificados:reparar-pdf {--imparte= : solo esta versión (id_imp)}';

    protected $description = 'Genera el PDF de los certificados emitidos que no tienen archivo';

    public function handle(CertificadoService $certificados): int
    {
        $res = $certificados->regenerarPdfFaltantes($this->option('imparte') ? (int) $this->option('imparte') : null);

        $this->info("{$res['reparados']} certificado(s) con PDF regenerado.");
        foreach ($res['errores'] as $e) {
            $this->error("Certificado #{$e['id']}: {$e['error']}");
        }

        return $res['errores'] ? self::FAILURE : self::SUCCESS;
    }
}
