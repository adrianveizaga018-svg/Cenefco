<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriviaPremioSeeder extends Seeder
{
    public function run(): void
    {
        $premios = [
            [
                'nombre'       => '10% de descuento en tu próximo curso',
                'descripcion'  => 'Cupón de descuento aplicable a la inscripción de cualquier curso o diplomado CENEFCO.',
                'tipo'         => 'descuento',
                'imagen_url'   => null,
                'costo_puntos' => 300,
                'stock'        => null,
                'activo'       => true,
                'orden'        => 1,
            ],
            [
                'nombre'       => '20% de descuento en diplomados',
                'descripcion'  => 'Cupón de descuento aplicable a la inscripción de cualquier diplomado CENEFCO.',
                'tipo'         => 'descuento',
                'imagen_url'   => null,
                'costo_puntos' => 600,
                'stock'        => null,
                'activo'       => true,
                'orden'        => 2,
            ],
            [
                'nombre'       => 'Botella deportiva CENEFCO',
                'descripcion'  => 'Botella reutilizable con el logo institucional. Recógela en secretaría.',
                'tipo'         => 'souvenir',
                'imagen_url'   => null,
                'costo_puntos' => 150,
                'stock'        => 40,
                'activo'       => true,
                'orden'        => 3,
            ],
            [
                'nombre'       => 'Mochila CENEFCO',
                'descripcion'  => 'Mochila institucional resistente, ideal para llevar tus materiales de estudio.',
                'tipo'         => 'souvenir',
                'imagen_url'   => null,
                'costo_puntos' => 500,
                'stock'        => 15,
                'activo'       => true,
                'orden'        => 4,
            ],
            [
                'nombre'       => 'Certificado de participación digital',
                'descripcion'  => 'Certificado descargable que acredita tu participación destacada en la Trivia CENEFCO.',
                'tipo'         => 'otro',
                'imagen_url'   => null,
                'costo_puntos' => 100,
                'stock'        => null,
                'activo'       => true,
                'orden'        => 5,
            ],
            [
                'nombre'       => 'Kit de escritorio CENEFCO',
                'descripcion'  => 'Set de cuaderno, lapicero y libreta con el logo institucional.',
                'tipo'         => 'souvenir',
                'imagen_url'   => null,
                'costo_puntos' => 250,
                'stock'        => 25,
                'activo'       => true,
                'orden'        => 6,
            ],
        ];

        foreach ($premios as $premio) {
            DB::table('trivia_premios')->updateOrInsert(
                ['nombre' => $premio['nombre']],
                $premio + ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->command->info('✓ Premios de trivia creados: ' . count($premios));
    }
}
