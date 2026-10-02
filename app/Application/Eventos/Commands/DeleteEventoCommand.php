<?php
namespace App\Application\Eventos\Commands;
final readonly class DeleteEventoCommand {
    public function __construct(public int $id) {}
}
