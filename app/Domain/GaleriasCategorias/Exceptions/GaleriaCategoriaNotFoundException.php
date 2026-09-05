<?php
namespace App\Domain\GaleriasCategorias\Exceptions;
use RuntimeException;
class GaleriaCategoriaNotFoundException extends RuntimeException {
    public function __construct(int|string $id) { parent::__construct("Categoría de galería '{$id}' no encontrada.", 404); }
}
