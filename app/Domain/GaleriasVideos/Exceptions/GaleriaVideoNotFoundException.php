<?php
namespace App\Domain\GaleriasVideos\Exceptions;
use RuntimeException;
class GaleriaVideoNotFoundException extends RuntimeException {
    public function __construct(int|string $id) { parent::__construct("Video de galería '{$id}' no encontrado.", 404); }
}
