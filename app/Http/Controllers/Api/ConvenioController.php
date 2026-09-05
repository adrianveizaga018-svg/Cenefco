<?php

namespace App\Http\Controllers\Api;

use App\Application\Convenios\Commands\CreateConvenioCommand;
use App\Application\Convenios\Commands\DeleteConvenioCommand;
use App\Application\Convenios\Commands\UpdateConvenioCommand;
use App\Application\Convenios\Handlers\CreateConvenioHandler;
use App\Application\Convenios\Handlers\DeleteConvenioHandler;
use App\Application\Convenios\Handlers\UpdateConvenioHandler;
use App\Application\Convenios\Queries\GetConvenioByIdQuery;
use App\Application\Convenios\Queries\GetConveniosActivosQuery;
use App\Application\Convenios\Queries\GetConveniosQuery;
use App\Application\Convenios\QueryHandlers\GetConvenioByIdQueryHandler;
use App\Application\Convenios\QueryHandlers\GetConveniosActivosQueryHandler;
use App\Application\Convenios\QueryHandlers\GetConveniosQueryHandler;
use App\Domain\Convenios\Exceptions\ConvenioNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Convenios\StoreConvenioRequest;
use App\Http\Requests\Convenios\UpdateConvenioRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConvenioController extends Controller
{
    public function __construct(
        private readonly GetConveniosQueryHandler $getConveniosHandler,
        private readonly GetConvenioByIdQueryHandler $getByIdHandler,
        private readonly GetConveniosActivosQueryHandler $getActivosHandler,
        private readonly CreateConvenioHandler $createHandler,
        private readonly UpdateConvenioHandler $updateHandler,
        private readonly DeleteConvenioHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => 'orden',
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->getConveniosHandler->handle(
                new GetConveniosQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function all(): JsonResponse
    {
        return response()->json($this->getActivosHandler->handle(new GetConveniosActivosQuery));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetConvenioByIdQuery($id)));
    }

    public function showPublico(int $id): JsonResponse
    {
        $dto = $this->getByIdHandler->handle(new GetConvenioByIdQuery($id));

        if ($dto->estado !== 'activo') {
            throw new ConvenioNotFoundException($id);
        }

        return response()->json($dto);
    }

    public function store(StoreConvenioRequest $request): JsonResponse
    {
        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $logoUrl = $request->file('logo')->store('convenios', 'public');
        }

        $documentoUrl = null;
        if ($request->hasFile('documento')) {
            $documentoUrl = $request->file('documento')->store('convenios/docs', 'public');
        }

        $dto = $this->createHandler->handle(new CreateConvenioCommand(
            nombre:             $request->nombre,
            institucion:        $request->institucion,
            tipo:               $request->tipo,
            descripcion:        $request->descripcion,
            responsable:        $request->responsable,
            contacto_email:     $request->contacto_email,
            contacto_telefono:  $request->contacto_telefono,
            fecha_inicio:       $request->fecha_inicio,
            fecha_fin:          $request->fecha_fin,
            documento_url:      $documentoUrl ?? $request->documento_url,
            logo_url:           $logoUrl,
            estado:             $request->input('estado', 'activo'),
            orden:              $request->integer('orden', 0),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateConvenioRequest $request, int $id): JsonResponse
    {
        $current = $this->getByIdHandler->handle(new GetConvenioByIdQuery($id));

        $logoUrl = null;
        if ($request->hasFile('logo')) {
            if ($current->logo_url) Storage::disk('public')->delete($current->logo_url);
            $logoUrl = $request->file('logo')->store('convenios', 'public');
        } elseif ($request->input('quitar_logo') === '1') {
            if ($current->logo_url) Storage::disk('public')->delete($current->logo_url);
            $logoUrl = '';
        }

        $documentoUrl = null;
        if ($request->hasFile('documento')) {
            if ($current->documento_url) Storage::disk('public')->delete($current->documento_url);
            $documentoUrl = $request->file('documento')->store('convenios/docs', 'public');
        } elseif ($request->input('quitar_documento') === '1') {
            if ($current->documento_url) Storage::disk('public')->delete($current->documento_url);
            $documentoUrl = '';
        }

        return response()->json($this->updateHandler->handle(new UpdateConvenioCommand(
            id:                $id,
            nombre:            $request->nombre,
            institucion:       $request->institucion,
            tipo:              $request->tipo,
            descripcion:       $request->descripcion,
            responsable:       $request->responsable,
            contacto_email:    $request->contacto_email,
            contacto_telefono: $request->contacto_telefono,
            fecha_inicio:      $request->fecha_inicio,
            fecha_fin:         $request->fecha_fin,
            documento_url:     $documentoUrl,
            logo_url:          $logoUrl,
            estado:            $request->estado,
            orden:             $request->has('orden') ? $request->integer('orden') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteConvenioCommand($id));

        return response()->json(null, 204);
    }
}
