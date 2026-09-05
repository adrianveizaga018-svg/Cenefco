<?php

namespace Database\Seeders;

use App\Infrastructure\CursosMigrados\Models\CursoMigrado;
use App\Infrastructure\CursosMigrados\Models\ParticipanteMigrado;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CursoMigradoSeeder extends Seeder
{
    private const MESES = [
        'ENERO' => 1, 'FEBRERO' => 2, 'MARZO' => 3, 'ABRIL' => 4,
        'MAYO' => 5, 'JUNIO' => 6, 'JULIO' => 7, 'AGOSTO' => 8,
        'SEPTIEMBRE' => 9, 'OCTUBRE' => 10, 'NOVIEMBRE' => 11, 'DICIEMBRE' => 12,
    ];

    public function run(): void
    {
        $jsonPath = base_path('../api_qr_cenefco/output/cursos_info_cenefco.json');

        if (! file_exists($jsonPath)) {
            $this->command->warn("  ⚠ JSON no encontrado en: {$jsonPath}");
            $this->command->warn('  Ajusta la ruta en CursoMigradoSeeder::run()');
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        Storage::disk('public')->makeDirectory('qr/cursos_migrados');
        $qrDir = Storage::disk('public')->path('qr/cursos_migrados');

        $totalCursos       = 0;
        $totalParticipantes = 0;
        $omitidos          = 0;

        foreach ($data as $entry) {
            $url    = $entry['url'] ?? null;
            $nombre = trim($entry['curso'] ?? '');
            $slug   = $url ? ltrim(str_replace('https://cenefco.com/', '', $url), '/') : null;

            if (! $slug || ! $nombre) {
                continue;
            }

            if (CursoMigrado::where('slug', $slug)->exists()) {
                $omitidos++;
                continue;
            }

            [$mes, $anio] = $this->extraerPeriodo($entry['mes'] ?? '');

            $periodo    = ($mes && $anio) ? "{$mes}-{$anio}" : null;
            $gestion    = $anio ? (string) $anio : null;
            $fechaInicio = ($mes && $anio)
                ? \Carbon\Carbon::createFromDate($anio, self::MESES[$mes], 1)->toDateString()
                : null;

            $qrFilename = $slug . '_qr.png';
            $qrFilepath = $qrDir . DIRECTORY_SEPARATOR . $qrFilename;
            $qrPath     = 'qr/cursos_migrados/' . $qrFilename;

            try {
                $qr  = QrCode::create($url)->setSize(300)->setMargin(4);
                $png = (new PngWriter)->write($qr)->getString();
                file_put_contents($qrFilepath, $png);
            } catch (\Throwable $e) {
                $qrPath = null;
            }

            $curso = CursoMigrado::create([
                'nombre'      => $nombre,
                'slug'        => $slug,
                'url'         => $url,
                'qr_path'     => $qrPath,
                'periodo'     => $periodo,
                'gestion'     => $gestion,
                'fecha_inicio' => $fechaInicio,
            ]);

            $totalCursos++;

            $vistos = [];
            foreach ($entry['estudiantes'] ?? [] as $nombreCompleto) {
                $limpio = trim($nombreCompleto);
                if ($limpio === '' || in_array($limpio, $vistos, true)) {
                    continue;
                }
                $vistos[] = $limpio;

                ParticipanteMigrado::create([
                    'curso_id'        => $curso->id,
                    'nombre_completo' => $limpio,
                ]);

                $totalParticipantes++;
            }
        }

        $this->command->info("✓ {$totalCursos} cursos migrados | {$totalParticipantes} participantes | {$omitidos} omitidos (ya existían).");
    }

    private function extraerPeriodo(string $mesStr): array
    {
        if (! $mesStr) {
            return [null, null];
        }

        $meses = implode('|', array_keys(self::MESES));
        preg_match("/({$meses})\s+(\d{4})/i", strtoupper($mesStr), $m);

        return isset($m[1]) ? [strtoupper($m[1]), (int) $m[2]] : [null, null];
    }
}
