<?php
namespace App\Application\Popups\Handlers;
use App\Application\Popups\Commands\UpdatePopupCommand;
use App\Application\Popups\DTOs\PopupDTO;
use App\Domain\Popups\Contracts\PopupRepositoryInterface;
class UpdatePopupHandler {
    public function __construct(private readonly PopupRepositoryInterface $repository) {}
    public function handle(UpdatePopupCommand $c): PopupDTO {
        $data = array_filter([
            'titulo' => $c->titulo, 'contenido' => $c->contenido,
            'imagen_url' => $c->imagen_url, 'enlace_url' => $c->enlace_url,
            'enlace_texto' => $c->enlace_texto, 'posicion' => $c->posicion,
            'delay_segundos' => $c->delay_segundos,
            'mostrar_una_vez_sesion' => $c->mostrar_una_vez_sesion,
            'mostrar_una_vez_siempre' => $c->mostrar_una_vez_siempre,
            'paginas_mostrar' => $c->paginas_mostrar, 'activo' => $c->activo,
            'fecha_inicio' => $c->fecha_inicio, 'fecha_fin' => $c->fecha_fin,
        ], fn($v) => $v !== null);
        return $this->repository->update($c->id, $data);
    }
}
