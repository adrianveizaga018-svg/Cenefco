<?php

namespace App\Http\Controllers\Api;

use App\Application\UsuariosAcademicos\Commands\CreateUsuarioAcademicoCommand;
use App\Application\UsuariosAcademicos\Commands\DeleteUsuarioAcademicoCommand;
use App\Application\UsuariosAcademicos\Commands\UpdateUsuarioAcademicoCommand;
use App\Application\UsuariosAcademicos\Handlers\CreateUsuarioAcademicoHandler;
use App\Application\UsuariosAcademicos\Handlers\DeleteUsuarioAcademicoHandler;
use App\Application\UsuariosAcademicos\Handlers\UpdateUsuarioAcademicoHandler;
use App\Application\UsuariosAcademicos\Queries\GetUsuarioAcademicoByIdQuery;
use App\Application\UsuariosAcademicos\Queries\GetUsuariosAcademicosQuery;
use App\Application\UsuariosAcademicos\QueryHandlers\GetUsuarioAcademicoByIdQueryHandler;
use App\Application\UsuariosAcademicos\QueryHandlers\GetUsuariosAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\UsuariosAcademicos\StoreUsuarioAcademicoRequest;
use App\Http\Requests\UsuariosAcademicos\UpdateUsuarioAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioAcademicoController extends Controller
{
    public function __construct(
        private readonly GetUsuariosAcademicosQueryHandler  $getUsuariosHandler,
        private readonly GetUsuarioAcademicoByIdQueryHandler $getUsuarioByIdHandler,
        private readonly CreateUsuarioAcademicoHandler       $createHandler,
        private readonly UpdateUsuarioAcademicoHandler       $updateHandler,
        private readonly DeleteUsuarioAcademicoHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'appaterno'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getUsuariosHandler->handle(new GetUsuariosAcademicosQuery(
                pagination:    $pagination,
                conInactivos:  $request->boolean('conInactivos', false),
                tipoestudiante: $request->filled('tipoestudiante') ? $request->get('tipoestudiante') : null,
                idNiv:         $request->filled('id_niv') ? (int) $request->get('id_niv') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getUsuarioByIdHandler->handle(new GetUsuarioAcademicoByIdQuery($id))
        );
    }

    public function store(StoreUsuarioAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateUsuarioAcademicoCommand(
            id_us:           $request->integer('id_us'),
            id_us_reg:       $request->filled('id_us_reg') ? $request->integer('id_us_reg') : null,
            tipoestudiante:  $request->input('tipoestudiante'),
            nombre_usuario:  $request->input('nombre_usuario'),
            categoria:       $request->input('categoria'),
            titulo_academico: $request->input('titulo_academico'),
            appaterno:       $request->input('appaterno'),
            apmaterno:       $request->input('apmaterno'),
            nombre:          $request->input('nombre'),
            ci:              $request->input('ci'),
            expedido:        $request->filled('expedido') ? $request->integer('expedido') : null,
            telefono:        $request->input('telefono'),
            celular:         $request->input('celular'),
            genero:          $request->filled('genero') ? $request->integer('genero') : null,
            email:           $request->input('email'),
            direccion:       $request->input('direccion'),
            ciudad:          $request->input('ciudad'),
            pais:            $request->input('pais'),
            id_universidad:  $request->filled('id_universidad') ? $request->integer('id_universidad') : null,
            id_carrera:      $request->filled('id_carrera') ? $request->integer('id_carrera') : null,
            estado:          $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateUsuarioAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateUsuarioAcademicoCommand(
            id:              $id,
            tipoestudiante:  $request->input('tipoestudiante'),
            nombre_usuario:  $request->input('nombre_usuario'),
            categoria:       $request->input('categoria'),
            titulo_academico: $request->input('titulo_academico'),
            appaterno:       $request->input('appaterno'),
            apmaterno:       $request->input('apmaterno'),
            nombre:          $request->input('nombre'),
            ci:              $request->input('ci'),
            expedido:        $request->filled('expedido') ? $request->integer('expedido') : null,
            telefono:        $request->input('telefono'),
            celular:         $request->input('celular'),
            genero:          $request->filled('genero') ? $request->integer('genero') : null,
            email:           $request->input('email'),
            direccion:       $request->input('direccion'),
            ciudad:          $request->input('ciudad'),
            pais:            $request->input('pais'),
            id_universidad:  $request->filled('id_universidad') ? $request->integer('id_universidad') : null,
            id_carrera:      $request->filled('id_carrera') ? $request->integer('id_carrera') : null,
            estado:          $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteUsuarioAcademicoCommand($id));

        return response()->json(null, 204);
    }
}
