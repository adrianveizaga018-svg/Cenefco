<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecretariasSeeder extends Seeder
{
    public function run(): void
    {
        $secretarias = [
            [
                'nombre'            => 'Dirección General',
                'sigla'             => 'DG',
                'atribuciones'      => 'Máxima autoridad ejecutiva de la institución. Dirige, coordina y supervisa todas las unidades.',
                'orden_organigrama' => 1,
            ],
            [
                'nombre'            => 'Secretaría Académica',
                'sigla'             => 'SA',
                'atribuciones'      => 'Gestión de la oferta académica, programas, docentes e inscripciones.',
                'orden_organigrama' => 2,
            ],
            [
                'nombre'            => 'Secretaría Administrativa y Financiera',
                'sigla'             => 'SAF',
                'atribuciones'      => 'Administración de recursos humanos, financieros y logísticos.',
                'orden_organigrama' => 3,
            ],
            [
                'nombre'            => 'Unidad de Tecnología e Innovación',
                'sigla'             => 'UTI',
                'atribuciones'      => 'Gestión de plataformas digitales, sistemas de información y soporte técnico.',
                'orden_organigrama' => 4,
            ],
            [
                'nombre'            => 'Unidad de Comunicación',
                'sigla'             => 'UCOM',
                'atribuciones'      => 'Difusión institucional, redes sociales, prensa y relaciones públicas.',
                'orden_organigrama' => 5,
            ],
        ];

        foreach ($secretarias as $sec) {
            $slug = Str::slug($sec['nombre']);
            DB::table('secretarias')->updateOrInsert(
                ['slug' => $slug],
                array_merge($sec, ['slug' => $slug, 'activa' => true])
            );
        }
    }
}
