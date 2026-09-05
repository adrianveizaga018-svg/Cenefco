<?php

namespace App\Http\Controllers\Api;

use App\Application\InscripcionesDiplomado\Commands\ConvertirInscripcionDiplomadoCommand;
use App\Application\InscripcionesDiplomado\Commands\CreateInscripcionDiplomadoCommand;
use App\Application\InscripcionesDiplomado\Commands\DeleteInscripcionDiplomadoCommand;
use App\Application\InscripcionesDiplomado\Commands\UpdateInscripcionDiplomadoCommand;
use App\Application\InscripcionesDiplomado\Handlers\ConvertirInscripcionDiplomadoHandler;
use App\Application\InscripcionesDiplomado\Handlers\CreateInscripcionDiplomadoHandler;
use App\Application\InscripcionesDiplomado\Handlers\DeleteInscripcionDiplomadoHandler;
use App\Application\InscripcionesDiplomado\Handlers\UpdateInscripcionDiplomadoHandler;
use App\Application\InscripcionesDiplomado\Queries\GetInscripcionDiplomadoByIdQuery;
use App\Application\InscripcionesDiplomado\Queries\GetInscripcionesDiplomadoQuery;
use App\Application\InscripcionesDiplomado\QueryHandlers\GetInscripcionDiplomadoByIdQueryHandler;
use App\Application\InscripcionesDiplomado\QueryHandlers\GetInscripcionesDiplomadoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\InscripcionesDiplomado\StoreInscripcionDiplomadoRequest;
use App\Http\Requests\InscripcionesDiplomado\UpdateInscripcionDiplomadoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscripcionDiplomadoController extends Controller
{
    public function __construct(
        private readonly GetInscripcionesDiplomadoQueryHandler  $getInscripcionesHandler,
        private readonly GetInscripcionDiplomadoByIdQueryHandler $getByIdHandler,
        private readonly CreateInscripcionDiplomadoHandler       $createHandler,
        private readonly UpdateInscripcionDiplomadoHandler       $updateHandler,
        private readonly DeleteInscripcionDiplomadoHandler       $deleteHandler,
        private readonly ConvertirInscripcionDiplomadoHandler    $convertirHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getInscripcionesHandler->handle(new GetInscripcionesDiplomadoQuery(
                pagination:  $pagination,
                estado:      $request->filled('estado') ? $request->get('estado') : null,
                programaId:  $request->filled('programa_id') ? (int) $request->get('programa_id') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getByIdHandler->handle(new GetInscripcionDiplomadoByIdQuery($id))
        );
    }

    public function store(StoreInscripcionDiplomadoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateInscripcionDiplomadoCommand(
            nombre:                    $request->string('nombre'),
            email:                     $request->string('email'),
            programa_id:               $request->filled('programa_id') ? $request->integer('programa_id') : null,
            apellido_paterno:          $request->input('apellido_paterno'),
            apellido_materno:          $request->input('apellido_materno'),
            fecha_nacimiento:          $request->input('fecha_nacimiento'),
            ci:                        $request->input('ci'),
            expedido_id:               $request->filled('expedido_id') ? $request->integer('expedido_id') : null,
            telefono_grupo_inscritos:  $request->input('telefono_grupo_inscritos'),
            archivo_ci:                $request->input('archivo_ci'),
            archivo_titulo:            $request->input('archivo_titulo'),
            archivo_cv:                $request->input('archivo_cv'),
            archivo_foto_3x3:          $request->input('archivo_foto_3x3'),
            ciudad_residencia_id:      $request->filled('ciudad_residencia_id') ? $request->integer('ciudad_residencia_id') : null,
            provincia_especificar:     $request->input('provincia_especificar'),
            medio_pago:                $request->input('medio_pago'),
            medio_pago_id:             $request->filled('medio_pago_id') ? $request->integer('medio_pago_id') : null,
            monto_pagado:              $request->filled('monto_pagado') ? (float) $request->monto_pagado : null,
            archivo_comprobante_pago:  $request->input('archivo_comprobante_pago'),
            sugerencia_curso:          $request->input('sugerencia_curso'),
            recomendar_docente:        $request->boolean('recomendar_docente', false),
            detalle_docente:           $request->input('detalle_docente'),
            origen:                    $request->input('origen'),
            ip_origen:                 $request->ip(),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateInscripcionDiplomadoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateInscripcionDiplomadoCommand(
            id:                        $id,
            estado:                    $request->input('estado'),
            notificado:                $request->filled('notificado') ? $request->boolean('notificado') : null,
            nombre:                    $request->input('nombre'),
            apellido_paterno:          $request->input('apellido_paterno'),
            apellido_materno:          $request->input('apellido_materno'),
            fecha_nacimiento:          $request->input('fecha_nacimiento'),
            email:                     $request->input('email'),
            ci:                        $request->input('ci'),
            expedido_id:               $request->filled('expedido_id') ? $request->integer('expedido_id') : null,
            telefono_grupo_inscritos:  $request->input('telefono_grupo_inscritos'),
            archivo_ci:                $request->input('archivo_ci'),
            archivo_titulo:            $request->input('archivo_titulo'),
            archivo_cv:                $request->input('archivo_cv'),
            archivo_foto_3x3:          $request->input('archivo_foto_3x3'),
            ciudad_residencia_id:      $request->filled('ciudad_residencia_id') ? $request->integer('ciudad_residencia_id') : null,
            provincia_especificar:     $request->input('provincia_especificar'),
            medio_pago:                $request->input('medio_pago'),
            medio_pago_id:             $request->filled('medio_pago_id') ? $request->integer('medio_pago_id') : null,
            monto_pagado:              $request->filled('monto_pagado') ? (float) $request->monto_pagado : null,
            archivo_comprobante_pago:  $request->input('archivo_comprobante_pago'),
            sugerencia_curso:          $request->input('sugerencia_curso'),
            recomendar_docente:        $request->filled('recomendar_docente') ? $request->boolean('recomendar_docente') : null,
            detalle_docente:           $request->input('detalle_docente'),
            programa_id:               $request->filled('programa_id') ? $request->integer('programa_id') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteInscripcionDiplomadoCommand($id));
        return response()->json(null, 204);
    }

    public function buscarCi(Request $request): JsonResponse
    {
        $ci = trim((string) $request->get('ci', ''));
        if (strlen($ci) < 3) {
            return response()->json(null);
        }

        $usuario = DB::table('t_usuario')
            ->where('ci', $ci)
            ->orderByDesc('id_us')
            ->select(['nombre', 'appaterno', 'apmaterno', 'email', 'celular', 'ci', 'expedido'])
            ->first();

        if (! $usuario) {
            return response()->json(null);
        }

        return response()->json([
            'nombre'           => $usuario->nombre,
            'apellido_paterno' => $usuario->appaterno,
            'apellido_materno' => $usuario->apmaterno,
            'ci'               => $usuario->ci,
            'complemento'      => null,
            'expedido_id'      => $usuario->expedido ?? null,
            'email'            => $usuario->email,
            'telefono'         => $usuario->celular,
        ]);
    }

    public function convertirInscripcion(Request $request, int $id): JsonResponse
    {
        $result = $this->convertirHandler->handle(new ConvertirInscripcionDiplomadoCommand(
            id:      $id,
            idImp:   $request->filled('id_imp')  ? (int) $request->input('id_imp')  : null,
            idPlan:  $request->filled('id_plan') ? (int) $request->input('id_plan') : null,
        ));

        $statusCode = isset($result['inscripcion_id']) && ! ($result['usuario_nuevo'] ?? false) ? 200 : 201;

        return response()->json($result, $statusCode);
    }
}
