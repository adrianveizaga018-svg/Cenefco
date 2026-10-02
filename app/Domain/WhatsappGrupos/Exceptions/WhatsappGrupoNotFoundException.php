<?php
namespace App\Domain\WhatsappGrupos\Exceptions;
use RuntimeException;
class WhatsappGrupoNotFoundException extends RuntimeException {
    public function __construct(int|string $id) { parent::__construct("Grupo de WhatsApp '{$id}' no encontrado.", 404); }
}
