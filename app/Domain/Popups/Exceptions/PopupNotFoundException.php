<?php
namespace App\Domain\Popups\Exceptions;
use RuntimeException;
class PopupNotFoundException extends RuntimeException {
    public function __construct(int|string $id) { parent::__construct("Popup '{$id}' no encontrado.", 404); }
}
