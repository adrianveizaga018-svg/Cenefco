<?php

namespace App\Http\Controllers\Api;

use App\Application\MdlCourses\Commands\CreateMdlCourseCommand;
use App\Application\MdlCourses\Commands\DeleteMdlCourseCommand;
use App\Application\MdlCourses\Commands\UpdateMdlCourseCommand;
use App\Application\MdlCourses\Handlers\CreateMdlCourseHandler;
use App\Application\MdlCourses\Handlers\DeleteMdlCourseHandler;
use App\Application\MdlCourses\Handlers\UpdateMdlCourseHandler;
use App\Application\MdlCourses\Queries\GetMdlCourseByIdQuery;
use App\Application\MdlCourses\Queries\GetMdlCoursesQuery;
use App\Application\MdlCourses\QueryHandlers\GetMdlCourseByIdQueryHandler;
use App\Application\MdlCourses\QueryHandlers\GetMdlCoursesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\MdlCourses\StoreMdlCourseRequest;
use App\Http\Requests\MdlCourses\UpdateMdlCourseRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MdlCourseController extends Controller
{
    public function __construct(
        private readonly GetMdlCoursesQueryHandler $listHandler,
        private readonly GetMdlCourseByIdQueryHandler $showHandler,
        private readonly CreateMdlCourseHandler $createHandler,
        private readonly UpdateMdlCourseHandler $updateHandler,
        private readonly DeleteMdlCourseHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
        ]);

        return response()->json($this->listHandler->handle(new GetMdlCoursesQuery(
            pagination:   $pagination,
            idUsReg:      $request->integer('id_us_reg') ?: null,
            idDocente:    $request->integer('id_docente') ?: null,
            conInactivos: (bool) $request->get('conInactivos', false),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetMdlCourseByIdQuery($id)));
    }

    public function store(StoreMdlCourseRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateMdlCourseCommand(
            id:                      $request->integer('id'),
            numModcurso:             $request->integer('num_modcurso') ?: null,
            fullname:                $request->input('fullname'),
            shortname:               $request->input('shortname'),
            idDocente:               $request->integer('id_docente') ?: null,
            category:                $request->integer('category') ?: null,
            sigla:                   $request->input('sigla'),
            paralelo:                $request->input('paralelo'),
            cupo:                    $request->input('cupo'),
            gradoAcademico1:         $request->input('grado_academico1'),
            gradoAcademico2:         $request->input('grado_academico2'),
            observacionImp:          $request->input('observacion_imp'),
            imparteFechaInicio:      $request->input('imparte_fecha_inicio'),
            imparteFechaFin:         $request->input('imparte_fecha_fin'),
            imparteFechaActa:        $request->input('imparte_fecha_acta'),
            ocultarCoordinadorActa:  $request->integer('ocultar_coordinador_acta') ?: null,
            idCoordinador:           $request->integer('id_coordinador') ?: null,
            gradoCoordinador1:       $request->input('grado_coordinador1'),
            gradoCoordinador2:       $request->input('grado_coordinador2'),
            tituloPersonalizado:     $request->input('titulo_personalizado'),
            subtituloPersonalizado:  $request->input('subtitulo_personalizado'),
            gestion:                 $request->input('gestion'),
            perModificar:            $request->integer('per_modificar') ?: null,
            idUsReg:                 auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateMdlCourseRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateMdlCourseCommand(
            id:                      $id,
            fullname:                $request->input('fullname'),
            shortname:               $request->input('shortname'),
            idDocente:               $request->integer('id_docente') ?: null,
            category:                $request->integer('category') ?: null,
            sigla:                   $request->input('sigla'),
            paralelo:                $request->input('paralelo'),
            cupo:                    $request->input('cupo'),
            gradoAcademico1:         $request->input('grado_academico1'),
            gradoAcademico2:         $request->input('grado_academico2'),
            observacionImp:          $request->input('observacion_imp'),
            imparteFechaInicio:      $request->input('imparte_fecha_inicio'),
            imparteFechaFin:         $request->input('imparte_fecha_fin'),
            imparteFechaActa:        $request->input('imparte_fecha_acta'),
            ocultarCoordinadorActa:  $request->integer('ocultar_coordinador_acta') ?: null,
            idCoordinador:           $request->integer('id_coordinador') ?: null,
            gradoCoordinador1:       $request->input('grado_coordinador1'),
            gradoCoordinador2:       $request->input('grado_coordinador2'),
            tituloPersonalizado:     $request->input('titulo_personalizado'),
            subtituloPersonalizado:  $request->input('subtitulo_personalizado'),
            gestion:                 $request->input('gestion'),
            estado:                  $request->integer('estado') ?: null,
            perModificar:            $request->integer('per_modificar') ?: null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteMdlCourseCommand($id));

        return response()->json(null, 204);
    }
}
