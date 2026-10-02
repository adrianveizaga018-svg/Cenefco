<?php
namespace App\Application\WhatsappGrupos\Handlers;
use App\Application\WhatsappGrupos\Commands\UpdateWhatsappGrupoCommand;
use App\Application\WhatsappGrupos\DTOs\WhatsappGrupoDTO;
use App\Domain\WhatsappGrupos\Contracts\WhatsappGrupoRepositoryInterface;
class UpdateWhatsappGrupoHandler {
    public function __construct(private readonly WhatsappGrupoRepositoryInterface $repository) {}
    public function handle(UpdateWhatsappGrupoCommand $c): WhatsappGrupoDTO {
        $data = array_filter([
            'imparte_id' => $c->imparte_id,
            'nombre' => $c->nombre,
            'enlace_invitacion' => $c->enlace_invitacion,
            'capacidad_maxima' => $c->capacidad_maxima,
            'miembros_actuales' => $c->miembros_actuales,
            'descripcion' => $c->descripcion,
            'activo' => $c->activo,
            'orden' => $c->orden,
            'fecha_expiracion_enlace' => $c->fecha_expiracion_enlace,
        ], fn($v) => $v !== null);
        return $this->repository->update($c->id, $data);
    }
}
