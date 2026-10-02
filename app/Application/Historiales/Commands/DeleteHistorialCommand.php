<?php
namespace App\Application\Historiales\Commands;
final readonly class DeleteHistorialCommand {
    public function __construct(public int $idHistorial) {}
}
