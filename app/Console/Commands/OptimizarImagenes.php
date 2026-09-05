<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class OptimizarImagenes extends Command
{
    protected $signature = 'imagenes:optimizar
                            {--carpeta=uploads/images : Carpeta dentro del disco public a procesar}
                            {--calidad=82 : Calidad WebP (1-100)}
                            {--max-ancho=1920 : Ancho máximo en píxeles}
                            {--max-alto=1920 : Alto máximo en píxeles}
                            {--dry-run : Simula el proceso sin modificar archivos}
                            {--force : Reconvierte archivos que ya son .webp}';

    protected $description = 'Convierte imágenes existentes en storage/public a WebP y las redimensiona';

    private const EXTENSIONES = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'tif'];

    public function handle(): int
    {
        $carpeta  = $this->option('carpeta');
        $calidad  = (int) $this->option('calidad');
        $maxAncho = (int) $this->option('max-ancho');
        $maxAlto  = (int) $this->option('max-alto');
        $dryRun   = $this->option('dry-run');
        $force    = $this->option('force');

        if ($calidad < 1 || $calidad > 100) {
            $this->error('La calidad debe estar entre 1 y 100.');
            return self::FAILURE;
        }

        $this->info("Escaneando: storage/app/public/{$carpeta}");

        if (! Storage::disk('public')->exists($carpeta)) {
            $this->error("La carpeta '{$carpeta}' no existe en el disco public.");
            return self::FAILURE;
        }

        $archivos = $this->obtenerImagenes($carpeta, $force);

        if (empty($archivos)) {
            $this->info('No se encontraron imágenes para procesar.');
            return self::SUCCESS;
        }

        $total      = count($archivos);
        $procesadas = 0;
        $errores    = 0;
        $ahorroTotal = 0;

        $this->info(sprintf('Imágenes encontradas: %d', $total));

        if ($dryRun) {
            $this->warn('Modo --dry-run: no se modificará ningún archivo.');
        }

        $this->newLine();

        $manager = new ImageManager(new Driver);
        $bar     = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        foreach ($archivos as $path) {
            $bar->setMessage(basename($path));

            try {
                $absolutePath = Storage::disk('public')->path($path);
                $tamanoOriginal = filesize($absolutePath);

                if (! $dryRun) {
                    $webpPath = $this->convertir(
                        manager:  $manager,
                        path:     $absolutePath,
                        diskPath: $path,
                        calidad:  $calidad,
                        maxAncho: $maxAncho,
                        maxAlto:  $maxAlto,
                    );

                    $tamanoNuevo = Storage::disk('public')->size($webpPath);
                    $ahorro = $tamanoOriginal - $tamanoNuevo;
                    $ahorroTotal += $ahorro;
                }

                $procesadas++;
            } catch (\Throwable $e) {
                $errores++;
                $this->newLine();
                $this->warn("  Error en {$path}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total encontradas',  $total],
                ['Procesadas OK',      $procesadas],
                ['Errores',            $errores],
                ['Ahorro en disco',    $dryRun ? 'N/A (dry-run)' : $this->formatBytes($ahorroTotal)],
            ]
        );

        return $errores === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function obtenerImagenes(string $carpeta, bool $force): array
    {
        $todos = Storage::disk('public')->allFiles($carpeta);

        return array_values(array_filter($todos, function (string $archivo) use ($force): bool {
            $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

            if ($force && $ext === 'webp') {
                return true;
            }

            return in_array($ext, self::EXTENSIONES, true);
        }));
    }

    private function convertir(
        ImageManager $manager,
        string $path,
        string $diskPath,
        int $calidad,
        int $maxAncho,
        int $maxAlto,
    ): string {
        $webpBytes = (string) $manager
            ->read($path)
            ->scaleDown(width: $maxAncho, height: $maxAlto)
            ->toWebp(quality: $calidad);

        $sinExtension = pathinfo($diskPath, PATHINFO_DIRNAME)
            . '/'
            . pathinfo($diskPath, PATHINFO_FILENAME);

        $webpPath = $sinExtension . '.webp';

        Storage::disk('public')->put($webpPath, $webpBytes);

        if ($webpPath !== $diskPath) {
            Storage::disk('public')->delete($diskPath);
        }

        return $webpPath;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 0) {
            return '-' . $this->formatBytes(abs($bytes));
        }
        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 2) . ' MB';
        }
        if ($bytes >= 1_024) {
            return round($bytes / 1_024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
