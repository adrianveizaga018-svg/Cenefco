<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CertFuentesController extends Controller
{
    /**
     * Catálogo de fuentes disponibles para el editor de certificados.
     * Cada entrada incluye:
     *   - label  : nombre amigable para mostrar al usuario
     *   - value  : ruta relativa (desde base_path) o ruta de sistema que CertificadoService::resolveFont puede resolver
     *   - css    : font-family CSS para la preview en el canvas Angular
     *   - grupo  : 'proyecto' | 'sistema'
     */
    private static array $CATALOGO = [
        // ── Fuentes empaquetadas en el proyecto ──────────────────────────────
        [
            'label' => 'Predeterminada del sistema',
            'value' => null,
            'css'   => 'Arial, sans-serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Asap (negrita)',
            'value' => 'assets/fonts/Asap_700.ttf',
            'css'   => '"Asap", sans-serif',
            'grupo' => 'proyecto',
        ],
        [
            'label' => 'Roboto',
            'value' => 'assets/fonts/Roboto_regular.ttf',
            'css'   => '"Roboto", sans-serif',
            'grupo' => 'proyecto',
        ],
        [
            'label' => 'Open Sans',
            'value' => 'assets/fonts/Open_Sans_regular.ttf',
            'css'   => '"Open Sans", sans-serif',
            'grupo' => 'proyecto',
        ],
        [
            'label' => 'Ubuntu',
            'value' => 'assets/fonts/Ubuntu_regular.ttf',
            'css'   => '"Ubuntu", sans-serif',
            'grupo' => 'proyecto',
        ],
        [
            'label' => 'Khand (condensada)',
            'value' => 'assets/fonts/Khand_500.ttf',
            'css'   => '"Khand", sans-serif',
            'grupo' => 'proyecto',
        ],
        [
            'label' => 'ABeeZee (redondeada)',
            'value' => 'assets/fonts/ABeeZee_regular.ttf',
            'css'   => '"ABeeZee", sans-serif',
            'grupo' => 'proyecto',
        ],
        // ── Fuentes del sistema (Windows) ────────────────────────────────────
        [
            'label' => 'Arial',
            'value' => 'C:/Windows/Fonts/arial.ttf',
            'css'   => '"Arial", sans-serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Arial Bold',
            'value' => 'C:/Windows/Fonts/arialbd.ttf',
            'css'   => '"Arial", sans-serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Times New Roman',
            'value' => 'C:/Windows/Fonts/times.ttf',
            'css'   => '"Times New Roman", serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Georgia',
            'value' => 'C:/Windows/Fonts/georgia.ttf',
            'css'   => '"Georgia", serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Verdana',
            'value' => 'C:/Windows/Fonts/verdana.ttf',
            'css'   => '"Verdana", sans-serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Trebuchet MS',
            'value' => 'C:/Windows/Fonts/trebuc.ttf',
            'css'   => '"Trebuchet MS", sans-serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Century Gothic',
            'value' => 'C:/Windows/Fonts/GOTHIC.TTF',
            'css'   => '"Century Gothic", sans-serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Palatino Linotype',
            'value' => 'C:/Windows/Fonts/pala.ttf',
            'css'   => '"Palatino Linotype", serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Garamond',
            'value' => 'C:/Windows/Fonts/GARA.TTF',
            'css'   => '"Garamond", serif',
            'grupo' => 'sistema',
        ],
        [
            'label' => 'Book Antiqua',
            'value' => 'C:/Windows/Fonts/BKANT.TTF',
            'css'   => '"Book Antiqua", "Palatino Linotype", serif',
            'grupo' => 'sistema',
        ],
    ];

    public function index(): JsonResponse
    {
        // Filtrar sólo las fuentes que realmente existen en este servidor
        $disponibles = array_filter(self::$CATALOGO, function ($f) {
            if ($f['value'] === null) return true; // predeterminada siempre disponible
            // Ruta relativa al proyecto
            if (!str_starts_with($f['value'], '/') && !preg_match('/^[A-Z]:\//i', $f['value'])) {
                return file_exists(base_path($f['value']));
            }
            return file_exists($f['value']);
        });

        return response()->json(array_values($disponibles));
    }
}
