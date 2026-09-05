<?php

namespace App\Http\Controllers\Api;

use App\Application\DocentesPerfil\Commands\CreateDocentePerfilCommand;
use App\Application\DocentesPerfil\Commands\DeleteDocentePerfilCommand;
use App\Application\DocentesPerfil\Commands\UpdateDocentePerfilCommand;
use App\Application\DocentesPerfil\Handlers\CreateDocentePerfilHandler;
use App\Application\DocentesPerfil\Handlers\DeleteDocentePerfilHandler;
use App\Application\DocentesPerfil\Handlers\UpdateDocentePerfilHandler;
use App\Application\DocentesPerfil\Queries\GetDocentePerfilByIdQuery;
use App\Application\DocentesPerfil\Queries\GetDocentePerfilBySlugQuery;
use App\Application\DocentesPerfil\Queries\GetDocentesPerfilQuery;
use App\Application\DocentesPerfil\Queries\GetReporteMateriasQuery;
use App\Application\DocentesPerfil\QueryHandlers\GetDocentePerfilByIdQueryHandler;
use App\Application\DocentesPerfil\QueryHandlers\GetDocentePerfilBySlugQueryHandler;
use App\Application\DocentesPerfil\QueryHandlers\GetDocentesPerfilQueryHandler;
use App\Application\DocentesPerfil\QueryHandlers\GetReporteMateriasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocentesPerfil\StoreDocentePerfilRequest;
use App\Http\Requests\DocentesPerfil\UpdateDocentePerfilRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocentePerfilController extends Controller
{
    public function __construct(
        private readonly GetDocentesPerfilQueryHandler      $getDocentesHandler,
        private readonly GetDocentePerfilByIdQueryHandler   $getByIdHandler,
        private readonly GetDocentePerfilBySlugQueryHandler $getBySlugHandler,
        private readonly GetReporteMateriasQueryHandler     $getReporteHandler,
        private readonly CreateDocentePerfilHandler         $createHandler,
        private readonly UpdateDocentePerfilHandler         $updateHandler,
        private readonly DeleteDocentePerfilHandler         $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'orden'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getDocentesHandler->handle(
                new GetDocentesPerfilQuery($pagination, $request->boolean('soloVisibles', false))
            )
        );
    }

    public function reporteMaterias(Request $request): JsonResponse
    {
        return response()->json(
            $this->getReporteHandler->handle(new GetReporteMateriasQuery(
                gestion: $request->get('gestion') ?: null,
                periodo: $request->get('periodo') ?: null,
            ))
        );
    }

    public function store(StoreDocentePerfilRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateDocentePerfilCommand(
            nombre_completo:  $request->nombre_completo,
            usuario_id:       $request->usuario_id ? (int) $request->usuario_id : null,
            titulo_academico: $request->titulo_academico,
            especialidad:     $request->especialidad,
            biografia:        $request->biografia,
            foto_url:         $request->foto_url,
            foto_alt:         $request->foto_alt,
            email_publico:    $request->email_publico,
            telefono:         $request->telefono,
            linkedin_url:     $request->linkedin_url,
            twitter_url:      $request->twitter_url,
            sitio_web_url:    $request->sitio_web_url,
            tipo:             $request->input('tipo', 'docente'),
            mostrar_en_web:   $request->boolean('mostrar_en_web', true),
            orden:            $request->integer('orden', 0),
            estado:           $request->input('estado', 'publicado'),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetDocentePerfilByIdQuery($id)));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->getBySlugHandler->handle(new GetDocentePerfilBySlugQuery($slug)));
    }

    public function update(UpdateDocentePerfilRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateDocentePerfilCommand(
            id:               $id,
            usuario_id:       $request->has('usuario_id') ? (int) $request->usuario_id : null,
            nombre_completo:  $request->nombre_completo,
            titulo_academico: $request->titulo_academico,
            especialidad:     $request->especialidad,
            biografia:        $request->biografia,
            foto_url:         $request->foto_url,
            foto_alt:         $request->foto_alt,
            email_publico:    $request->email_publico,
            telefono:         $request->telefono,
            linkedin_url:     $request->linkedin_url,
            twitter_url:      $request->twitter_url,
            sitio_web_url:    $request->sitio_web_url,
            tipo:             $request->tipo,
            mostrar_en_web:   $request->has('mostrar_en_web') ? $request->boolean('mostrar_en_web') : null,
            orden:            $request->has('orden') ? $request->integer('orden') : null,
            estado:           $request->estado,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteDocentePerfilCommand($id));

        return response()->json(null, 204);
    }
}
