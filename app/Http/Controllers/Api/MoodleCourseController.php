<?php

namespace App\Http\Controllers\Api;

use App\Application\Moodle\Commands\CreateMoodleCourseCommand;
use App\Application\Moodle\Handlers\CreateMoodleCourseHandler;
use App\Application\Moodle\QueryHandlers\GetMoodleCoursesQueryHandler;
use App\Domain\ProgramasAcademicos\Contracts\ProgramaAcademicoRepositoryInterface;
use App\Domain\ProgramasAcademicos\Exceptions\ProgramaAcademicoNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\MoodleCourse\StoreMoodleCourseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class MoodleCourseController extends Controller
{
    public function __construct(
        private readonly CreateMoodleCourseHandler $createHandler,
        private readonly GetMoodleCoursesQueryHandler $getCoursesHandler,
        private readonly ProgramaAcademicoRepositoryInterface $programaRepo,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->getCoursesHandler->handle());
    }

    public function store(StoreMoodleCourseRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateMoodleCourseCommand(
            fullname:   $request->string('fullname')->toString(),
            shortname:  $request->string('shortname')->toString(),
            categoryid: $request->integer('categoryid', 1),
            summary:    $request->string('summary', '')->toString(),
        ));

        return response()->json($dto, 201);
    }

    public function fromCurso(int $id): JsonResponse
    {
        try {
            $programa = $this->programaRepo->findById($id);
        } catch (ProgramaAcademicoNotFoundException) {
            return response()->json(['message' => 'Curso no encontrado'], 404);
        }

        $shortname = $programa->nombre_programa
            ? Str::upper(Str::limit(Str::slug($programa->nombre_programa), 30, ''))
            : 'CURSO-'.$id;

        $summary = implode("\n\n", array_filter([
            strip_tags($programa->descripcion ?? ''),
            $programa->objetivo   ? 'Objetivo: '.strip_tags($programa->objetivo)   : null,
            $programa->dirigido   ? 'Dirigido a: '.strip_tags($programa->dirigido) : null,
            $programa->requisitos ? 'Requisitos: '.strip_tags($programa->requisitos) : null,
        ]));

        $startdate = $programa->inicio_actividades
            ? strtotime($programa->inicio_actividades)
            : 0;

        $enddate = $programa->finalizacion_actividades
            ? strtotime($programa->finalizacion_actividades)
            : 0;

        $imageUrl = null;
        if (! empty($programa->foto)) {
            $foto = ltrim($programa->foto, '/');
            $imageUrl = rtrim(config('app.url'), '/').'/'.$foto;
        }

        logger()->debug('MoodleCurso imagen', ['foto' => $programa->foto, 'imageUrl' => $imageUrl]);

        $dto = $this->createHandler->handle(new CreateMoodleCourseCommand(
            fullname:  $programa->nombre_programa,
            shortname: $shortname,
            categoryid: 1,
            summary:   $summary,
            startdate: $startdate,
            enddate:   $enddate,
            imageUrl:  $imageUrl,
        ));

        return response()->json($dto, 201);
    }
}
