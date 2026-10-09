<?php

namespace App\Http\Controllers\Api;

use App\Application\Usuarios\Services\CredencialesEstudianteService;
use App\Domain\Usuarios\Exceptions\CredencialesException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CredencialesEstudianteController extends Controller
{
    public function __construct(
        private readonly CredencialesEstudianteService $credenciales,
    ) {}

    /** POST /caja/estudiantes/{idUs}/enviar-credenciales */
    public function enviar(int $idUs): JsonResponse
    {
        try {
            $resultado = $this->credenciales->enviar($idUs);
        } catch (CredencialesException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

        return response()->json([
            'message'      => 'Credenciales enviadas a '.$resultado['email'].'.',
            'email'        => $resultado['email'],
            'cuenta_nueva' => $resultado['cuenta_nueva'],
        ]);
    }
}
