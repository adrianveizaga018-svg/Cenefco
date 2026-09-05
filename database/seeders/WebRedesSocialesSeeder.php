<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebRedesSocialesSeeder extends Seeder
{
    public function run(): void
    {
        $redes = [
            ['red' => 'facebook',   'nombre_display' => 'Facebook',   'url' => '', 'icono_clase' => 'fab fa-facebook-f',  'pixel_id' => '', 'mostrar_footer' => true,  'mostrar_header' => false, 'orden' => 1, 'activo' => true],
            ['red' => 'instagram',  'nombre_display' => 'Instagram',  'url' => '', 'icono_clase' => 'fab fa-instagram',   'pixel_id' => '', 'mostrar_footer' => true,  'mostrar_header' => false, 'orden' => 2, 'activo' => true],
            ['red' => 'youtube',    'nombre_display' => 'YouTube',    'url' => '', 'icono_clase' => 'fab fa-youtube',     'pixel_id' => '', 'mostrar_footer' => true,  'mostrar_header' => false, 'orden' => 3, 'activo' => true],
            ['red' => 'whatsapp',   'nombre_display' => 'WhatsApp',   'url' => '', 'icono_clase' => 'fab fa-whatsapp',   'pixel_id' => '', 'mostrar_footer' => true,  'mostrar_header' => true,  'orden' => 4, 'activo' => true],
            ['red' => 'tiktok',     'nombre_display' => 'TikTok',     'url' => '', 'icono_clase' => 'fab fa-tiktok',     'pixel_id' => '', 'mostrar_footer' => false, 'mostrar_header' => false, 'orden' => 5, 'activo' => false],
            ['red' => 'linkedin',   'nombre_display' => 'LinkedIn',   'url' => '', 'icono_clase' => 'fab fa-linkedin-in', 'pixel_id' => '', 'mostrar_footer' => false, 'mostrar_header' => false, 'orden' => 6, 'activo' => false],
            ['red' => 'twitter',    'nombre_display' => 'X (Twitter)', 'url' => '', 'icono_clase' => 'fab fa-x-twitter',  'pixel_id' => '', 'mostrar_footer' => false, 'mostrar_header' => false, 'orden' => 7, 'activo' => false],
            ['red' => 'telegram',   'nombre_display' => 'Telegram',   'url' => '', 'icono_clase' => 'fab fa-telegram',   'pixel_id' => '', 'mostrar_footer' => false, 'mostrar_header' => false, 'orden' => 8, 'activo' => false],
        ];

        foreach ($redes as $red) {
            DB::table('web_redes_sociales')->updateOrInsert(
                ['red' => $red['red']],
                $red
            );
        }
    }
}
