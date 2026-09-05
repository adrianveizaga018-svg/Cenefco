<?php
namespace App\Application\WhatsappGrupos\Handlers;
use App\Application\WhatsappGrupos\Commands\CreateWhatsappGrupoCommand;
use App\Application\WhatsappGrupos\DTOs\WhatsappGrupoDTO;
use App\Domain\WhatsappGrupos\Contracts\WhatsappGrupoRepositoryInterface;
class CreateWhatsappGrupoHandler {
    public function __construct(private readonly WhatsappGrupoRepositoryInterface $repository) {}
    public function handle(CreateWhatsappGrupoCommand $c): WhatsappGrupoDTO {
        return $this->repository->create([
            'imparte_id' => $c->imparte_id,
            'nombre' => $c->nombre,
            'enlace_invitacion' => $c->enlace_invitacion,
            'capacidad_maxima' => $c->capacidad_maxima,
            'miembros_actuales' => $c->miembros_actuales,
            'descripcion' => $c->descripcion,
            'activo' => $c->activo,
            'orden' => $c->orden,
            'fecha_expiracion_enlace' => $c->fecha_expiracion_enlace,
        ]);
    }
}
