<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertVerificacionSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('t_cert_verificacion')->count() > 0) {
            $this->command->info('✓ Verificaciones: ya existen registros, se omite el seeder.');
            return;
        }

        $now  = now();
        $rows = [];

        
        $certIds = DB::table('t_certificado')->pluck('id', 'codigo_verificacion');

        
        $casos = [
            
            [
                'codigo'    => 'cenefco-2025-A4X9K2',
                'resultado' => 'valido',
                'ip'        => '190.129.45.12',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
                'dias'      => 2,
            ],
            [
                'codigo'    => 'cenefco-2025-A4X9K2',
                'resultado' => 'valido',
                'ip'        => '186.23.10.88',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
                'dias'      => 5,
            ],
            [
                'codigo'    => 'cenefco-2025-B7KM3P',
                'resultado' => 'valido',
                'ip'        => '200.58.96.14',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (Android 13; Mobile; rv:109.0) Gecko/109.0 Firefox/117.0',
                'dias'      => 7,
            ],
            [
                'codigo'    => 'cenefco-2025-C2RW8N',
                'resultado' => 'valido',
                'ip'        => '177.234.142.30',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
                'dias'      => 10,
            ],
            [
                'codigo'    => 'cenefco-2025-D9QL5F',
                'resultado' => 'valido',
                'ip'        => '190.129.45.12',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (Linux; Android 12; SM-A325F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Mobile Safari/537.36',
                'dias'      => 12,
            ],
            [
                'codigo'    => 'cenefco-2026-E3HT7J',
                'resultado' => 'valido',
                'ip'        => '181.112.64.5',
                'pais'      => 'Argentina',
                'ua'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
                'dias'      => 15,
            ],
            [
                'codigo'    => 'cenefco-2026-F6YU2S',
                'resultado' => 'valido',
                'ip'        => '190.129.20.5',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (iPad; CPU OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
                'dias'      => 18,
            ],
            
            [
                'codigo'    => 'cenefco-2025-FAKE01',
                'resultado' => 'invalido',
                'ip'        => '45.87.12.200',
                'pais'      => 'Brasil',
                'ua'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
                'dias'      => 3,
            ],
            [
                'codigo'    => 'cenefco-2025-XXXXX1',
                'resultado' => 'invalido',
                'ip'        => '190.129.45.99',
                'pais'      => 'Bolivia',
                'ua'        => 'curl/8.1.2',
                'dias'      => 8,
            ],
            [
                'codigo'    => 'cenefco-2024-G1ZP9M',
                'resultado' => 'invalido',
                'ip'        => '103.21.244.0',
                'pais'      => null,
                'ua'        => 'python-requests/2.31.0',
                'dias'      => 20,
            ],
            [
                'codigo'    => 'cenefco-2025-A4X9K2',
                'resultado' => 'valido',
                'ip'        => '186.23.10.55',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
                'dias'      => 1,
            ],
            [
                'codigo'    => 'cenefco-2026-H5NX4W',
                'resultado' => 'valido',
                'ip'        => '200.105.108.14',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
                'dias'      => 4,
            ],
            [
                'codigo'    => 'cenefco-2025-TAMPER',
                'resultado' => 'invalido',
                'ip'        => '45.87.12.200',
                'pais'      => 'Brasil',
                'ua'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'dias'      => 6,
            ],
            [
                'codigo'    => 'cenefco-2026-I8VB1R',
                'resultado' => 'valido',
                'ip'        => '190.129.21.100',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Mobile Safari/537.36',
                'dias'      => 9,
            ],
            [
                'codigo'    => 'cenefco-2026-J2MC6T',
                'resultado' => 'valido',
                'ip'        => '177.234.100.5',
                'pais'      => 'Bolivia',
                'ua'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
                'dias'      => 14,
            ],
        ];

        foreach ($casos as $caso) {
            $certId = $certIds[$caso['codigo']] ?? null;

            $rows[] = [
                'certificado_id'   => $certId,
                'codigo_consultado'=> $caso['codigo'],
                'resultado'        => $caso['resultado'],
                'ip_origen'        => $caso['ip'],
                'user_agent'       => $caso['ua'],
                'pais'             => $caso['pais'],
                'created_at'       => $now->copy()->subDays($caso['dias'])->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ];
        }

        
        usort($rows, fn ($a, $b) => strcmp((string) $a['created_at'], (string) $b['created_at']));

        DB::table('t_cert_verificacion')->insertOrIgnore($rows);

        $validos   = collect($rows)->where('resultado', 'valido')->count();
        $invalidos = collect($rows)->where('resultado', 'invalido')->count();

        $this->command->info("✓ Verificaciones: {$validos} válidas y {$invalidos} inválidas insertadas en t_cert_verificacion.");
    }
}
