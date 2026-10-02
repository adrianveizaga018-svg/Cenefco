<?php
namespace App\Application\FormatosHojaSolicitud\Commands;
final readonly class DeleteFormatoHojaSolicitudCommand {
    public function __construct(public int $idFormatoHojaSolicitud) {}
}
