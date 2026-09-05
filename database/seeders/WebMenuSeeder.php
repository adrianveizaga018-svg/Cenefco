<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'nombre'      => 'menu-principal',
                'descripcion' => 'Menú de navegación principal del portal',
                'activo'      => true,
            ],
            [
                'nombre'      => 'menu-footer',
                'descripcion' => 'Menú del pie de página',
                'activo'      => true,
            ],
            [
                'nombre'      => 'menu-footer-legal',
                'descripcion' => 'Menú de enlaces legales en el footer',
                'activo'      => true,
            ],
        ];

        foreach ($menus as $menu) {
            DB::table('web_menus')->updateOrInsert(
                ['nombre' => $menu['nombre']],
                $menu
            );
            $id = DB::table('web_menus')->where('nombre', $menu['nombre'])->value('id');

            $this->seedItems($id, $menu['nombre']);
        }
    }

    private function seedItems(int $menuId, string $nombre): void
    {
        $items = match ($nombre) {
            'menu-principal' => $this->itemsPrincipal($menuId),
            'menu-footer'    => $this->itemsFooter($menuId),
            'menu-footer-legal' => $this->itemsFooterLegal($menuId),
            default          => [],
        };

        foreach ($items as $item) {
            DB::table('web_menu_items')->updateOrInsert(
                ['menu_id' => $item['menu_id'], 'etiqueta' => $item['etiqueta'], 'parent_id' => $item['parent_id']],
                $item
            );
        }
    }

    private function itemsPrincipal(int $menuId): array
    {
        return [
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Inicio',         'url' => '/',                          'orden' => 1,  'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Nosotros',        'url' => '/nosotros',                  'orden' => 2,  'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Oferta Académica','url' => '/cursos',                    'orden' => 3,  'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Noticias',        'url' => '/noticias',                  'orden' => 4,  'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Contacto',        'url' => '/contacto',                  'orden' => 5,  'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Verificar Cert.', 'url' => '/verificar-certificado',     'orden' => 6,  'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
        ];
    }

    private function itemsFooter(int $menuId): array
    {
        return [
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Inicio',          'url' => '/',             'orden' => 1, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Oferta Académica','url' => '/cursos',        'orden' => 2, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Noticias',         'url' => '/noticias',     'orden' => 3, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Contacto',         'url' => '/contacto',     'orden' => 4, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Nosotros',         'url' => '/nosotros',     'orden' => 5, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Verificar Cert.',  'url' => '/verificar-certificado', 'orden' => 6, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
        ];
    }

    private function itemsFooterLegal(int $menuId): array
    {
        return [
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Política de Privacidad', 'url' => '/privacidad',         'orden' => 1, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Términos y Condiciones',  'url' => '/terminos',           'orden' => 2, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
            ['menu_id' => $menuId, 'parent_id' => null, 'etiqueta' => 'Mapa del Sitio',          'url' => '/mapa-del-sitio',     'orden' => 3, 'icono' => null, 'activo' => true, 'abrir_nueva_ventana' => false],
        ];
    }
}
