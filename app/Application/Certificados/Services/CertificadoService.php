<?php

namespace App\Application\Certificados\Services;

use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CertificadoService
{
    private static array $FONT_CANDIDATES = [
        'C:/Windows/Fonts/arialbd.ttf',
        'C:/Windows/Fonts/arial.ttf',
        'C:/Windows/Fonts/OpenSans-Bold.ttf',
        'C:/Windows/Fonts/Montserrat-Bold.ttf',
        'C:/Windows/Fonts/verdana.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        '/usr/share/fonts/truetype/ubuntu/Ubuntu-B.ttf',
        '/System/Library/Fonts/Helvetica.ttc',
    ];

    private ImageManager $manager;

    public function __construct(
        private readonly ListaAprobadosRepositoryInterface $listaAprobadosRepository,
    ) {
        $this->manager = new ImageManager(new Driver);
    }

    private function resolveFont(?string $campoFuente = null): string
    {
        if ($campoFuente && file_exists($campoFuente)) {
            return $campoFuente;
        }
        $envFont = env('CERT_FONT_PATH');
        if ($envFont && file_exists($envFont)) {
            return $envFont;
        }
        foreach (self::$FONT_CANDIDATES as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $fuenteEmpaquetada = base_path('assets/fonts/Asap_700.ttf');
        if (file_exists($fuenteEmpaquetada)) {
            return $fuenteEmpaquetada;
        }

        throw new \RuntimeException(
            'No se encontró ninguna fuente TTF para generar el certificado. '.
            'Configure CERT_FONT_PATH en el archivo .env con la ruta a una fuente .ttf válida.'
        );
    }

    public function previewPlantilla(int $plantillaId, string $format = 'jpg'): array
    {
        $plantilla = DB::table('t_cert_plantilla')->find($plantillaId);
        if (! $plantilla) {
            throw new \RuntimeException('Plantilla no encontrada.');
        }

        $campos = DB::table('t_cert_plantilla_campo')
            ->where('plantilla_id', $plantillaId)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        $bgPath = $this->resolveStoragePath($plantilla->imagen_url);
        if (! file_exists($bgPath)) {
            throw new \RuntimeException('Imagen de plantilla no encontrada: '.$plantilla->imagen_url);
        }

        $codigoEjemplo = 'cenefco-'.now()->year.'-PREVIEW';
        $verifyUrl     = rtrim(config('services.certificados.portal_url'), '/').'/verificar-certificado?codigo='.$codigoEjemplo;

        $nombreEjemplo  = 'Juan Carlos Pérez Mamani';
        $programaEjemplo = 'Diplomado en Gestión Pública y Administración del Estado';

        $valores = [
            'nombre_completo'     => $nombreEjemplo,
            'nombre_participante' => $nombreEjemplo,
            'participante'        => $nombreEjemplo,
            'nombre'              => 'Juan Carlos',
            'apellidos'           => 'Pérez Mamani',
            'alumno'              => $nombreEjemplo,
            'estudiante'          => $nombreEjemplo,
            'programa'            => $programaEjemplo,
            'nombre_programa'     => $programaEjemplo,
            'curso'               => $programaEjemplo,
            'diplomado'           => $programaEjemplo,
            'materia'             => $programaEjemplo,
            'condicion'           => 'Aprobado',
            'nota'                => '87.50',
            'nota_final'          => '87.50',
            'calificacion'        => '87.50',
            'horas'               => '120',
            'horas_academicas'    => '120 horas académicas',
            'horas_lectivas'      => '120',
            'creditaje'           => '4',
            'fecha_inicio'        => '01/03/'.now()->year,
            'fecha_fin'           => '30/06/'.now()->year,
            'fecha_emision'       => now()->format('d/m/Y'),
            'fecha'               => now()->format('d/m/Y'),
            'fecha_graduacion'    => now()->format('d/m/Y'),
            'texto_certifica'     => 'otorga el presente certificado a:',
            'texto_intro'         => 'Por haber completado satisfactoriamente el programa de:',
            'texto_firma'         => 'En constancia de lo cual se firma y sella.',
            'codigo'              => $codigoEjemplo,
            'codigo_verificacion' => $codigoEjemplo,
            'codigo_cert'         => $codigoEjemplo,
            'ci'                  => '7854321',
            'carnet'              => '7854321',
        ];

        $image = $this->manager->read($bgPath);

        $ancho = $image->width();
        $alto  = $image->height();

        $escalaFuente = $ancho / 800.0;

        foreach ($campos as $campo) {
            $x = (int) round((float) $campo->pos_x_pct / 100 * $ancho);
            $y = (int) round((float) $campo->pos_y_pct / 100 * $alto);

            $esQr = $campo->tipo === 'imagen' || str_contains(strtolower($campo->clave), 'qr');
            if ($esQr) {
                $qrSize  = $campo->ancho_pct
                    ? (int) round((float) $campo->ancho_pct / 100 * $ancho)
                    : 300;
                $tempQr  = tempnam(sys_get_temp_dir(), 'qr_preview_').'.png';
                $qrObj   = QrCode::create($verifyUrl)->setSize($qrSize)->setMargin(4);
                file_put_contents($tempQr, (new PngWriter)->write($qrObj)->getString());
                $image->place($tempQr, 'top-left', $x, $y);
                @unlink($tempQr);
            } else {
                $texto = $campo->valor_fijo
                    ?: ($valores[$campo->clave] ?? ($campo->etiqueta ?: $campo->clave));

                $texto = match ($campo->mayusculas) {
                    'upper' => mb_strtoupper((string) $texto),
                    'lower' => mb_strtolower((string) $texto),
                    'title' => mb_convert_case((string) $texto, MB_CASE_TITLE),
                    default => (string) $texto,
                };

                $fontSize = (int) max(8, round($campo->tamano_pt * $escalaFuente));
                $color    = $campo->color ?: ($plantilla->color_default ?? '#000000');
                $align    = $campo->alineacion ?: 'center';
                $fontFile = $this->resolveFont($campo->fuente ?? null);

                $image->text($texto, $x, $y, function ($font) use ($fontFile, $fontSize, $color, $align) {
                    $font->filename($fontFile);
                    $font->size($fontSize);
                    $font->color($color);
                    $font->align($align);
                    $font->valign('top');
                });
            }
        }

        if ($format === 'pdf') {
            $jpgData  = $image->toJpeg(quality: (int) ($plantilla->calidad_jpg ?? 85));
            $b64      = base64_encode((string) $jpgData);
            $widthPt  = round($ancho * 0.75);
            $heightPt = round($alto  * 0.75);
            $html = <<<HTML
                <!DOCTYPE html><html><head>
                <style>* {margin:0;padding:0;} body {width:{$widthPt}pt;height:{$heightPt}pt;overflow:hidden;} img {width:100%;height:100%;display:block;}</style>
                </head><body><img src="data:image/jpeg;base64,{$b64}" /></body></html>
                HTML;
            $pdfBytes = Pdf::loadHTML($html)
                ->setPaper([0, 0, $widthPt, $heightPt])
                ->setOptions(['dpi' => 96, 'isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true])
                ->output();

            return ['bytes' => $pdfBytes, 'mime' => 'application/pdf', 'ext' => 'pdf'];
        }

        $jpgBytes = (string) $image->toJpeg(quality: (int) ($plantilla->calidad_jpg ?? 85));

        return ['bytes' => $jpgBytes, 'mime' => 'image/jpeg', 'ext' => 'jpg'];
    }

    public function generarLote(int $imparteId, int $plantillaId, ?array $usuarioIds = null): array
    {
        $plantilla = DB::table('t_cert_plantilla')->find($plantillaId);
        if (! $plantilla) {
            throw new \RuntimeException('Plantilla no encontrada.');
        }

        $campos = DB::table('t_cert_plantilla_campo')
            ->where('plantilla_id', $plantillaId)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        $imparte = DB::table('t_imparte as i')
            ->leftJoin('t_materia as m', 'i.id_mat', '=', 'm.id_mat')
            ->leftJoin('t_programa as prog', function ($j) {
                $j->on('prog.id_imp', '=', 'i.id_imp')
                  ->whereRaw('prog.id_us_reg = (SELECT MIN(p2.id_us_reg) FROM t_programa p2 WHERE p2.id_imp = prog.id_imp)');
            })
            ->select('i.*', 'm.nombre as nombre_materia', 'prog.slug as programa_slug')
            ->where('i.id_imp', $imparteId)
            ->whereRaw('i.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = i.id_imp)')
            ->first();

        if (! $imparte) {
            throw new \RuntimeException('Apertura de curso no encontrada (id_imp='.$imparteId.').');
        }

        $aprobados = $this->listaAprobadosRepository->aprobadosPendientesDeCertificado($imparteId, $usuarioIds);

        $generados = [];
        $errores = [];
        $yaGenerados = [];

        foreach ($aprobados as $aprobado) {
            try {
                $cert = DB::transaction(fn () => $this->generarCertificado($aprobado, $plantilla, $campos, $imparte));
                $generados[] = $cert;
            } catch (\Throwable $e) {
                if ($this->esViolacionUnicidadListaAprobado($e)) {
                    $yaGenerados[] = [
                        'usuario_id' => $aprobado->usuario_id,
                        'nombre'     => trim(($aprobado->appaterno ?? '').' '.($aprobado->nombre ?? '')),
                        'mensaje'    => 'Ya se había generado por otro proceso concurrente.',
                    ];

                    continue;
                }

                $errores[] = [
                    'usuario_id' => $aprobado->usuario_id,
                    'nombre'     => trim(($aprobado->appaterno ?? '').' '.($aprobado->nombre ?? '')),
                    'error'      => $e->getMessage(),
                ];
            }
        }

        $datosEstudiantesDjango = [];
        foreach ($generados as $cert) {
            $datosEstudiantesDjango[] = [
                'nombre' => $cert->nombre_en_certificado,
                'codigo' => $cert->codigo_verificacion,
            ];
        }

        if (!empty($datosEstudiantesDjango)) {
            $this->notificarDjangoParaPdf($imparteId, $plantilla, $imparte, $datosEstudiantesDjango);
        }

        return [
            'generados' => count($generados),
            'total_aprobados' => $aprobados->count(),
            'errores' => $errores,
            'ya_generados' => $yaGenerados,
            'certificados' => $generados,
        ];
    }

    private function notificarDjangoParaPdf(int $imparteId, object $plantilla, object $imparte, array $estudiantes): void
    {
        try {
            $djangoUrl = config('certificados_externos.url', 'http://localhost:8001');
            
            // Convertimos la imagen_url relativa a URL absoluta local
            // Suponiendo que plantilla->imagen_url es como "plantillas/xxx.jpg"
            $plantillaUrl = url('/storage/' . $plantilla->imagen_url);

            $payload = [
                'lote_id' => $imparteId,
                'curso'   => $imparte->titulo_personalizado ?: ($imparte->nombre_materia ?: 'Curso'),
                'plantilla_url' => $plantillaUrl,
                'estudiantes' => $estudiantes,
                'config' => [
                    'texto_x' => 400, // Django usa centro absoluto, el frontend usa %. Django necesitara ajustar esto o usamos un default.
                    'texto_y' => 300,
                    'tamano_fuente' => 40,
                    'color_texto' => $plantilla->color_default ?? '#000000',
                    'qr_activo' => true,
                    'qr_x' => 100,
                    'qr_y' => 500,
                    'qr_tamano' => 120,
                    // Idealmente pasariamos las coordenadas exactas de la tabla t_cert_plantilla_campo
                ]
            ];

            \Illuminate\Support\Facades\Http::timeout(10)
                ->post("{$djangoUrl}/api/generar-lote-pdf/", $payload);
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error notificando a Django para PDFs: " . $e->getMessage());
        }
    }

    private function esViolacionUnicidadListaAprobado(\Throwable $e): bool
    {
        if (! $e instanceof \Illuminate\Database\QueryException) {
            return false;
        }

        $sqlState = $e->errorInfo[0] ?? null;
        if ($sqlState !== '23505') {
            return false;
        }

        return str_contains($e->getMessage(), 't_certificado_lista_aprobado_id_unique');
    }

    private function generarCertificado(
        object $aprobado,
        object $plantilla,
        $campos,
        object $imparte
    ): object {
        $codigo = $this->generarCodigo();

        $nombreCompleto = trim(implode(' ', array_filter([
            $aprobado->appaterno,
            $aprobado->apmaterno,
            $aprobado->nombre,
        ])));

        $programaNombre = $imparte->titulo_personalizado ?: ($imparte->nombre_materia ?: '');
        $portalUrl = rtrim(config('services.certificados.portal_url'), '/');
        $verifyUrl = $imparte->programa_slug
            ? $portalUrl.'/cursos/'.$imparte->programa_slug.'-participantes'
            : $portalUrl.'/verificar-certificado?codigo='.$codigo;

        $bgPath = $this->resolveStoragePath($plantilla->imagen_url);
        if (! file_exists($bgPath)) {
            throw new \RuntimeException("Imagen de plantilla no encontrada: {$plantilla->imagen_url}");
        }

        $image = $this->manager->read($bgPath);

        $ancho = $image->width();
        $alto  = $image->height();

        $escalaFuente = $ancho / 800.0;

        $valores = [
            'nombre_completo'     => mb_strtoupper($nombreCompleto),
            'nombre_participante' => mb_strtoupper($nombreCompleto),
            'participante'        => mb_strtoupper($nombreCompleto),
            'nombre_programa'     => mb_strtoupper($programaNombre),
            'nombre'              => $aprobado->nombre ?? '',
            'alumno'              => mb_strtoupper($nombreCompleto),
            'estudiante'          => mb_strtoupper($nombreCompleto),
            'apellidos'           => mb_strtoupper(trim(($aprobado->appaterno ?? '').' '.($aprobado->apmaterno ?? ''))),
            'programa'            => mb_strtoupper($programaNombre),
            'curso'               => mb_strtoupper($programaNombre),
            'diplomado'           => mb_strtoupper($programaNombre),
            'materia'             => mb_strtoupper($programaNombre),
            'condicion'           => mb_strtoupper($aprobado->condicion ?? 'APROBADO'),
            'nota'                => $aprobado->nota_final !== null ? number_format((float) $aprobado->nota_final, 2) : '',
            'nota_final'          => $aprobado->nota_final !== null ? number_format((float) $aprobado->nota_final, 2) : '',
            'calificacion'        => $aprobado->nota_final !== null ? number_format((float) $aprobado->nota_final, 2) : '',
            'horas_academicas'    => $imparte->horas_academicas ?? '',
            'horas'               => $imparte->horas_academicas ?? '',
            'creditaje'           => $imparte->creditaje ?? '',
            'fecha_inicio'        => $imparte->imparte_fecha_inicio ?? '',
            'fecha_fin'           => $imparte->imparte_fecha_fin ?? '',
            'fecha_emision'       => now()->format('d/m/Y'),
            'fecha'               => now()->format('d/m/Y'),
            'codigo'              => $codigo,
            'codigo_verificacion' => $codigo,
            'codigo_cert'         => $codigo,
            'ci'                  => $aprobado->ci ?? '',
            'carnet'              => $aprobado->ci ?? '',
        ];

        $dir = 'certificados/'.$imparte->id_imp;
        Storage::disk('public')->makeDirectory($dir);

        $qrFilename = $codigo.'_qr.png';
        $qrSavePath = Storage::disk('public')->path($dir.'/'.$qrFilename);
        $qr = QrCode::create($verifyUrl)->setSize(300)->setMargin(4);
        $qrPng = (new PngWriter)->write($qr)->getString();
        file_put_contents($qrSavePath, $qrPng);
        $qrUrl = '/storage/'.$dir.'/'.$qrFilename;

        foreach ($campos as $campo) {
            $x = (int) round((float) $campo->pos_x_pct / 100 * $ancho);
            $y = (int) round((float) $campo->pos_y_pct / 100 * $alto);

            $esQr = $campo->tipo === 'imagen' || str_contains(strtolower($campo->clave), 'qr');
            if ($esQr) {
                $qrSize = $campo->ancho_pct
                    ? (int) round((float) $campo->ancho_pct / 100 * $ancho)
                    : 300;

                $tempQr = tempnam(sys_get_temp_dir(), 'qr_').'.png';
                $qrT = QrCode::create($verifyUrl)->setSize($qrSize)->setMargin(4);
                $qrPngT = (new PngWriter)->write($qrT)->getString();
                file_put_contents($tempQr, $qrPngT);
                $image->place($tempQr, 'top-left', $x, $y);
                @unlink($tempQr);
            } else {
                $texto = $campo->valor_fijo ?: ($valores[$campo->clave] ?? '');
                if ((string) $texto === '') {
                    continue;
                }

                $texto = match ($campo->mayusculas) {
                    'upper' => mb_strtoupper((string) $texto),
                    'lower' => mb_strtolower((string) $texto),
                    'title' => mb_convert_case((string) $texto, MB_CASE_TITLE),
                    default => (string) $texto,
                };

                $fontSize = (int) max(8, round($campo->tamano_pt * $escalaFuente));
                $color    = $campo->color ?: ($plantilla->color_default ?? '#000000');
                $align    = $campo->alineacion ?: 'center';
                $fontFile = $this->resolveFont($campo->fuente ?? null);

                $image->text($texto, $x, $y, function ($font) use ($fontFile, $fontSize, $color, $align) {
                    $font->filename($fontFile);
                    $font->size($fontSize);
                    $font->color($color);
                    $font->align($align);
                    $font->valign('top');
                });
            }
        }

        $jpgData = $image->toJpeg(quality: (int) ($plantilla->calidad_jpg ?? 85));
        $b64Image = base64_encode((string) $jpgData);

        $widthPt = round($ancho * 0.75);
        $heightPt = round($alto * 0.75);

        // --- MODO HEADLESS: Ya no generamos el PDF con DOMPDF aqui ---
        // Solo generamos el JPEG de previsualizacion.
        // Django se encargara de generar el PDF real y enviarlo luego.
        $pdfFilename = $codigo.'.pdf';
        $archivoUrl = '/storage/'.$dir.'/'.$pdfFilename;

        $previewPath = Storage::disk('public')->path($dir.'/'.$codigo.'_preview.jpg');
        $image->scale(width: 800)->save($previewPath, quality: 70);
        $previewUrl = '/storage/'.$dir.'/'.$codigo.'_preview.jpg';

        $certId = DB::table('t_certificado')->insertGetId([
            'lista_aprobado_id' => $aprobado->id,
            'plantilla_id' => $plantilla->id,
            'usuario_id' => $aprobado->usuario_id,
            'imparte_id' => $aprobado->imparte_id,
            'nombre_en_certificado' => $nombreCompleto,
            'programa_en_certificado' => $programaNombre,
            'condicion' => $aprobado->condicion ?? 'aprobado',
            'nota_final' => $aprobado->nota_final,
            'fecha_inicio_curso' => $imparte->imparte_fecha_inicio ?? null,
            'fecha_fin_curso' => $imparte->imparte_fecha_fin ?? null,
            'codigo_verificacion' => $codigo,
            'qr_url' => $qrUrl,
            'archivo_url' => $archivoUrl,
            'estado' => 'generado',
            'id_us_reg' => auth()->id() ?? 0,
            'created_at' => now(),
        ]);

        DB::table('t_lista_aprobados')
            ->where('id', $aprobado->id)
            ->update(['estado_certificado' => 'generado', 'updated_at' => now()]);

        return DB::table('t_certificado')->find($certId);
    }

    public function generarCertificadoDirecto(
        int $plantillaId,
        string $nombreCompleto,
        string $nombrePrograma,
        string $ci,
    ): object {
        $plantilla = DB::table('t_cert_plantilla')->find($plantillaId);
        if (! $plantilla) {
            throw new \RuntimeException('Plantilla no encontrada.');
        }

        $campos = DB::table('t_cert_plantilla_campo')
            ->where('plantilla_id', $plantillaId)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        $codigo    = $this->generarCodigo();
        $portalUrl = rtrim(config('services.certificados.portal_url', ''), '/');
        $verifyUrl = $portalUrl.'/verificar-certificado?codigo='.$codigo;

        $bgPath = $this->resolveStoragePath($plantilla->imagen_url);
        if (! file_exists($bgPath)) {
            throw new \RuntimeException("Imagen de plantilla no encontrada: {$plantilla->imagen_url}");
        }

        $image = $this->manager->read($bgPath);
        $ancho = $image->width();
        $alto  = $image->height();
        $escalaFuente = $ancho / 800.0;

        $valores = [
            'nombre_completo'     => mb_strtoupper($nombreCompleto),
            'nombre_participante' => mb_strtoupper($nombreCompleto),
            'participante'        => mb_strtoupper($nombreCompleto),
            'nombre_programa'     => mb_strtoupper($nombrePrograma),
            'nombre'              => mb_strtoupper($nombreCompleto),
            'alumno'              => mb_strtoupper($nombreCompleto),
            'estudiante'          => mb_strtoupper($nombreCompleto),
            'apellidos'           => mb_strtoupper($nombreCompleto),
            'programa'            => mb_strtoupper($nombrePrograma),
            'curso'               => mb_strtoupper($nombrePrograma),
            'diplomado'           => mb_strtoupper($nombrePrograma),
            'materia'             => mb_strtoupper($nombrePrograma),
            'condicion'           => 'PARTICIPANTE',
            'nota'                => '',
            'nota_final'          => '',
            'horas_academicas'    => '',
            'fecha_inicio'        => '',
            'fecha_fin'           => '',
            'fecha_emision'       => now()->format('d/m/Y'),
            'fecha'               => now()->format('d/m/Y'),
            'codigo'              => $codigo,
            'codigo_verificacion' => $codigo,
            'codigo_cert'         => $codigo,
            'ci'                  => $ci,
            'carnet'              => $ci,
        ];

        $dir = 'certificados/modal';
        Storage::disk('public')->makeDirectory($dir);

        $qrFilename = $codigo.'_qr.png';
        $qrSavePath = Storage::disk('public')->path($dir.'/'.$qrFilename);
        $qr    = QrCode::create($verifyUrl)->setSize(300)->setMargin(4);
        $qrPng = (new PngWriter)->write($qr)->getString();
        file_put_contents($qrSavePath, $qrPng);
        $qrUrl = '/storage/'.$dir.'/'.$qrFilename;

        foreach ($campos as $campo) {
            $x = (int) round((float) $campo->pos_x_pct / 100 * $ancho);
            $y = (int) round((float) $campo->pos_y_pct / 100 * $alto);

            $esQr = $campo->tipo === 'imagen' || str_contains(strtolower($campo->clave), 'qr');
            if ($esQr) {
                $qrSize = $campo->ancho_pct
                    ? (int) round((float) $campo->ancho_pct / 100 * $ancho)
                    : 300;
                $tempQr = tempnam(sys_get_temp_dir(), 'qr_').'.png';
                $qrT    = QrCode::create($verifyUrl)->setSize($qrSize)->setMargin(4);
                file_put_contents($tempQr, (new PngWriter)->write($qrT)->getString());
                $image->place($tempQr, 'top-left', $x, $y);
                @unlink($tempQr);
            } else {
                $texto = $campo->valor_fijo ?: ($valores[$campo->clave] ?? '');
                if ((string) $texto === '') {
                    continue;
                }
                $texto = match ($campo->mayusculas) {
                    'upper' => mb_strtoupper((string) $texto),
                    'lower' => mb_strtolower((string) $texto),
                    'title' => mb_convert_case((string) $texto, MB_CASE_TITLE),
                    default => (string) $texto,
                };
                $fontSize = (int) max(8, round($campo->tamano_pt * $escalaFuente));
                $color    = $campo->color ?: ($plantilla->color_default ?? '#000000');
                $align    = $campo->alineacion ?: 'center';
                $fontFile = $this->resolveFont($campo->fuente ?? null);

                $image->text($texto, $x, $y, function ($font) use ($fontFile, $fontSize, $color, $align) {
                    $font->filename($fontFile);
                    $font->size($fontSize);
                    $font->color($color);
                    $font->align($align);
                    $font->valign('top');
                });
            }
        }

        $jpgData  = $image->toJpeg(quality: (int) ($plantilla->calidad_jpg ?? 85));
        $b64Image = base64_encode((string) $jpgData);
        $widthPt  = round($ancho * 0.75);
        $heightPt = round($alto * 0.75);

        $html = <<<HTML
            <!DOCTYPE html><html><head>
            <style>* { margin:0; padding:0; } body { width:{$widthPt}pt; height:{$heightPt}pt; overflow:hidden; } img { width:100%; height:100%; display:block; }</style>
            </head><body><img src="data:image/jpeg;base64,{$b64Image}" /></body></html>
            HTML;

        $pdf = Pdf::loadHTML($html)
            ->setPaper([0, 0, $widthPt, $heightPt])
            ->setOptions(['dpi' => 96, 'isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true]);

        $pdfFilename = $codigo.'.pdf';
        $pdfSavePath = Storage::disk('public')->path($dir.'/'.$pdfFilename);
        file_put_contents($pdfSavePath, $pdf->output());
        $archivoUrl = '/storage/'.$dir.'/'.$pdfFilename;

        $previewPath = Storage::disk('public')->path($dir.'/'.$codigo.'_preview.jpg');
        $image->scale(width: 800)->save($previewPath, quality: 70);
        $previewUrl = '/storage/'.$dir.'/'.$codigo.'_preview.jpg';

        $certId = DB::table('t_certificado')->insertGetId([
            'lista_aprobado_id'      => null,
            'plantilla_id'           => $plantilla->id,
            'usuario_id'             => 0,
            'imparte_id'             => 0,
            'nombre_en_certificado'  => $nombreCompleto,
            'programa_en_certificado'=> $nombrePrograma,
            'condicion'              => 'participante',
            'nota_final'             => null,
            'codigo_verificacion'    => $codigo,
            'qr_url'                 => $qrUrl,
            'archivo_url'            => $archivoUrl,
            'archivo_miniatura_url'  => $previewUrl,
            'estado'                 => 'generado',
            'id_us_reg'              => auth()->id() ?? 0,
            'created_at'             => now(),
        ]);

        return DB::table('t_certificado')->find($certId);
    }

    private function generarCodigo(): string
    {
        $year = now()->year;
        do {
            $random = strtoupper(Str::random(6));
            $codigo = "cenefco-{$year}-{$random}";
        } while (DB::table('t_certificado')->where('codigo_verificacion', $codigo)->exists());

        return $codigo;
    }

    private function resolveStoragePath(string $url): string
    {
        $relative = ltrim(str_replace('/storage/', '', $url), '/');

        return Storage::disk('public')->path($relative);
    }
}
