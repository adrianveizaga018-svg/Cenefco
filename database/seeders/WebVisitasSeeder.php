<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebVisitasSeeder extends Seeder
{
    private array $rutas = [
        '/'                                  => ['/', 'CENEFCO — Centro de Formación Continua', 35],
        '/cursos'                            => ['/cursos', 'Cursos y Diplomados | CENEFCO', 22],
        '/cursos/diplomado-derecho-civil'    => ['/cursos/diplomado-derecho-civil', 'Diplomado en Derecho Civil | CENEFCO', 14],
        '/cursos/diplomado-gestion-publica'  => ['/cursos/diplomado-gestion-publica', 'Diplomado en Gestión Pública | CENEFCO', 12],
        '/cursos/curso-excel-avanzado'       => ['/cursos/curso-excel-avanzado', 'Excel Avanzado | CENEFCO', 10],
        '/cursos/diplomado-educacion'        => ['/cursos/diplomado-educacion', 'Diplomado en Educación Superior | CENEFCO', 8],
        '/contactos'                         => ['/contactos', 'Contacto | CENEFCO', 9],
        '/institucional'                     => ['/institucional', 'Institucional | CENEFCO', 7],
        '/noticias'                          => ['/noticias', 'Noticias | CENEFCO', 6],
        '/preguntas-frecuentes'              => ['/preguntas-frecuentes', 'Preguntas Frecuentes | CENEFCO', 5],
        '/transparencia'                     => ['/transparencia', 'Transparencia | CENEFCO', 4],
        '/tramites'                          => ['/tramites', 'Trámites | CENEFCO', 3],
        '/consultar-tramite'                 => ['/consultar-tramite', 'Consultar Certificado | CENEFCO', 3],
    ];

    private array $paises = [
        ['Bolivia', 'La Paz', 55],
        ['Bolivia', 'Cochabamba', 18],
        ['Bolivia', 'Santa Cruz', 12],
        ['Argentina', 'Buenos Aires', 5],
        ['Peru', 'Lima', 4],
        ['Chile', 'Santiago', 3],
        ['Colombia', 'Bogotá', 2],
        ['Brasil', 'São Paulo', 1],
    ];

    private array $dispositivos = [
        ['mobile', 50],
        ['desktop', 42],
        ['tablet', 8],
    ];

    private array $navegadores = [
        ['Chrome', 55],
        ['Safari', 20],
        ['Firefox', 12],
        ['Edge', 8],
        ['Samsung Internet', 4],
        ['Opera', 1],
    ];

    private array $sos = [
        ['Android', 38],
        ['Windows', 35],
        ['iOS', 16],
        ['macOS', 8],
        ['Linux', 3],
    ];

    private array $referrers = [
        null,
        null,
        null,
        'https://www.google.com/search?q=diplomados+bolivia',
        'https://www.google.com/search?q=cursos+cenefco',
        'https://www.facebook.com/',
        'https://www.facebook.com/',
        'https://t.co/cenefco',
        'https://www.google.com/search?q=diplomado+derecho+civil+la+paz',
    ];

    public function run(): void
    {
        $rows    = [];
        $now     = now();
        $baseDay = $now->copy()->subDays(89)->startOfDay();

        
        for ($dayOffset = 0; $dayOffset < 90; $dayOffset++) {
            $date     = $baseDay->copy()->addDays($dayOffset);
            $dayOfWeek = (int) $date->format('N'); 

            
            $baseDayVisits = (int) round(60 + ($dayOffset / 89) * 80);

            
            if ($dayOfWeek >= 6) {
                $baseDayVisits = (int) round($baseDayVisits * 0.65);
            }

            
            $dailyVisits = max(10, (int) round($baseDayVisits * (0.85 + lcg_value() * 0.30)));

            
            $hourWeights = [
                0=>1, 1=>0.5, 2=>0.3, 3=>0.2, 4=>0.2, 5=>0.3,
                6=>0.8, 7=>1.5, 8=>2.5, 9=>3.5, 10=>4, 11=>4,
                12=>3, 13=>3.5, 14=>4, 15=>4.5, 16=>5, 17=>5,
                18=>4.5, 19=>4, 20=>3, 21=>2.5, 22=>2, 23=>1.5,
            ];
            $totalWeight = array_sum($hourWeights);

            foreach ($hourWeights as $hour => $weight) {
                $hourVisits = (int) round($dailyVisits * $weight / $totalWeight);
                if ($hourVisits <= 0) {
                    continue;
                }

                for ($v = 0; $v < $hourVisits; $v++) {
                    $ruta      = $this->weightedRandom($this->rutas);
                    $pais      = $this->weightedRandom($this->paises);
                    $disp      = $this->weightedRandom($this->dispositivos);
                    $nav       = $this->weightedRandom($this->navegadores);
                    $so        = $this->weightedRandom($this->sos);
                    $referrer  = $this->referrers[array_rand($this->referrers)];

                    $minute = rand(0, 59);
                    $second = rand(0, 59);
                    $ts     = $date->copy()->setTime($hour, $minute, $second)->toDateTimeString();

                    
                    $durMin  = str_contains($ruta[0], '/cursos/') ? 60 : 10;
                    $durMax  = str_contains($ruta[0], '/cursos/') ? 480 : 120;
                    $duracion = rand($durMin, $durMax);

                    $rows[] = [
                        'session_id'   => Str::uuid(),
                        'url'          => 'https://cenefco.edu.bo' . $ruta[0],
                        'ruta'         => $ruta[0],
                        'titulo'       => $ruta[1],
                        'referrer'     => $referrer,
                        'pais'         => $pais[0],
                        'ciudad'       => $pais[1],
                        'dispositivo'  => $disp[0],
                        'navegador'    => $nav[0],
                        'so'           => $so[0],
                        'duracion_seg' => $duracion,
                        'created_at'   => $ts,
                    ];

                    if (count($rows) >= 500) {
                        DB::table('web_visitas')->insertOrIgnore($rows);
                        $rows = [];
                    }
                }
            }
        }

        if (!empty($rows)) {
            DB::table('web_visitas')->insertOrIgnore($rows);
        }
    }

    
    private function weightedRandom(array $items): array
    {
        $weights = array_map(fn ($i) => $i[count($i) - 1], $items);
        $total   = array_sum($weights);
        $rand    = rand(1, $total);
        $cum     = 0;
        foreach ($items as $idx => $item) {
            $cum += $weights[$idx];
            if ($rand <= $cum) {
                return $item;
            }
        }
        return $items[0];
    }
}
