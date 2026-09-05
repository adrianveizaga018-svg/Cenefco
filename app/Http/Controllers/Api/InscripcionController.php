<?php

namespace App\Http\Controllers\Api;

use App\Application\Inscripciones\Commands\CreateInscripcionCommand;
use App\Application\Inscripciones\Commands\DeleteInscripcionCommand;
use App\Application\Inscripciones\Commands\MarcarParticipanteCommand;
use App\Application\Inscripciones\Commands\MarcarParticipantesBulkCommand;
use App\Application\Inscripciones\Commands\RegistrarDocumentoCommand;
use App\Application\Inscripciones\Commands\UpdateInscripcionCommand;
use App\Application\Inscripciones\Handlers\CreateInscripcionHandler;
use App\Application\Inscripciones\Handlers\DeleteInscripcionHandler;
use App\Application\Inscripciones\Handlers\MarcarParticipanteHandler;
use App\Application\Inscripciones\Handlers\MarcarParticipantesBulkHandler;
use App\Application\Inscripciones\Handlers\RegistrarDocumentoHandler;
use App\Application\Inscripciones\Handlers\UpdateInscripcionHandler;
use App\Application\Inscripciones\Queries\GetDocumentosInscripcionQuery;
use App\Application\Inscripciones\Queries\GetInscripcionDetalleQuery;
use App\Application\Inscripciones\Queries\GetInscripcionesQuery;
use App\Application\Inscripciones\QueryHandlers\GetDocumentosInscripcionQueryHandler;
use App\Application\Inscripciones\QueryHandlers\GetInscripcionDetalleQueryHandler;
use App\Application\Inscripciones\QueryHandlers\GetInscripcionesQueryHandler;
use App\Application\Pagos\Commands\RegistrarAnticipoCommand;
use App\Application\Pagos\Handlers\RegistrarAnticipoHandler;
use App\Shared\Kernel\Support\SqlCompat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inscripciones\RegistrarDocumentoRequest;
use App\Http\Requests\Inscripciones\StoreInscripcionRequest;
use App\Http\Requests\Inscripciones\UpdateInscripcionRequest;
use App\Http\Requests\Pagos\StoreAnticipoRequest;
use App\Domain\Formularios\Services\CampoRaizResolver;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    public function __construct(
        private readonly GetInscripcionesQueryHandler       $getInscripcionesHandler,
        private readonly GetInscripcionDetalleQueryHandler  $getDetalleHandler,
        private readonly GetDocumentosInscripcionQueryHandler $getDocumentosHandler,
        private readonly CreateInscripcionHandler            $createHandler,
        private readonly UpdateInscripcionHandler            $updateHandler,
        private readonly DeleteInscripcionHandler            $deleteHandler,
        private readonly RegistrarDocumentoHandler           $registrarDocumentoHandler,
        private readonly RegistrarAnticipoHandler            $anticipoHandler,
        private readonly MarcarParticipanteHandler           $marcarParticipanteHandler,
        private readonly MarcarParticipantesBulkHandler      $marcarParticipantesBulkHandler,
        private readonly VendedorScopeResolver               $vendedorScope,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'fecha_ins'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getInscripcionesHandler->handle(new GetInscripcionesQuery(
                pagination:  $pagination,
                conInactivos: $request->boolean('conInactivos', false),
                idUs:        $request->filled('id_us') ? (int) $request->get('id_us') : null,
                idImp:       $request->filled('id_imp') ? (int) $request->get('id_imp') : null,
                programaId:  $request->filled('programa_id') ? (int) $request->get('programa_id') : null,
                periodo:     $request->filled('periodo') ? $request->get('periodo') : null,
                gestion:     $request->filled('gestion') ? $request->get('gestion') : null,
                idImpPermitidos: $this->vendedorScope->idImpPermitidos(auth()->user()),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        $detalle = $this->getDetalleHandler->handle(new GetInscripcionDetalleQuery($id));
        $this->vendedorScope->assertAccesoImparte(auth()->user(), $detalle->inscripcion->id_imp ?? null);

        return response()->json([
            'inscripcion'  => $detalle->inscripcion,
            'pagos'        => $detalle->pagos,
            'plan'         => $detalle->plan,
            'todas_cuotas' => $detalle->todas_cuotas,
            'documentos'   => $detalle->documentos,
            'devoluciones' => $detalle->devoluciones,
            'resumen'      => $detalle->resumen,
            'docentes'     => $detalle->docentes,
            'documentos_estudiante' => $detalle->documentos_estudiante,
            'formulario_campos' => $detalle->formulario_campos,
            'compromiso_cobro' => $detalle->compromiso_cobro,
        ]);
    }

    public function store(StoreInscripcionRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateInscripcionCommand(
            id_us_reg:       $request->filled('id_us_reg') ? $request->integer('id_us_reg') : null,
            fecha_ins:       $request->input('fecha_ins'),
            id_us:           $request->integer('id_us'),
            id_imp:          $request->integer('id_imp'),
            id_plan:         $request->filled('id_plan') ? $request->integer('id_plan') : null,
            observacion_ins: $request->input('observacion_ins'),
            observacion:     $request->input('observacion'),
            periodo:         $request->input('periodo'),
            gestion:         $request->input('gestion'),
            estado:          $request->integer('estado', 1),
            idVendedor:      $request->filled('id_vendedor') ? $request->integer('id_vendedor') : null,
            canalVenta:      $request->input('canal_venta', 'admin'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateInscripcionRequest $request, int $id): JsonResponse
    {
        
        
        
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $id);

        $dto = $this->updateHandler->handle(new UpdateInscripcionCommand(
            id:              $id,
            fecha_ins:       $request->input('fecha_ins'),
            id_plan:         $request->filled('id_plan') ? $request->integer('id_plan') : null,
            clearIdPlan:     $request->has('id_plan') && ! $request->filled('id_plan'),
            observacion_ins: $request->input('observacion_ins'),
            observacion:     $request->input('observacion'),
            estado:          $request->filled('estado') ? $request->integer('estado') : null,
            documentos:      $request->has('documentos') ? $request->input('documentos') : null,
            campos_extra:    $request->has('campos_extra') ? $request->input('campos_extra') : null,
        ));

        return response()->json($dto);
    }

    public function documentos(int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $id);

        $result = $this->getDocumentosHandler->handle(new GetDocumentosInscripcionQuery($id));

        return response()->json([
            'plan_id'    => $result->plan_id,
            'requeridos' => $result->requeridos,
        ]);
    }

    public function registrarDocumento(RegistrarDocumentoRequest $request, int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $id);

        $doc = $this->registrarDocumentoHandler->handle(new RegistrarDocumentoCommand(
            id_ins:                 $id,
            id_fechadoc:            $request->integer('id_fechadoc'),
            dejo_documento_fisico:  $request->boolean('dejo_documento_fisico', false),
            documento_digital:      $request->input('documento_digital'),
            observacion_doc:        $request->input('observacion_doc'),
            id_us_reg:              auth()->id() ?? 0,
        ));

        return response()->json($doc, 201);
    }

    public function registrarAnticipo(StoreAnticipoRequest $request, int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $id);

        $insDto = app(\App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface::class)->findById($id);

        $dto = $this->anticipoHandler->handle(new RegistrarAnticipoCommand(
            idIns:         $id,
            idUs:          $insDto->id_us,
            montoPagado:   (float) $request->monto_pagado,
            idUsReg:       auth()->id() ?? 0,
            nroBoleta:     $request->nro_boleta_bancaria,
            fechaDeposito: $request->fecha_deposito,
            observacion:   $request->observacion_pago,
            idUsCajero:    auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    public function marcarParticipante(int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $id);

        $dto = $this->marcarParticipanteHandler->handle(new MarcarParticipanteCommand(
            idIns:   $id,
            idUsReg: auth()->id() ?? 0,
        ));

        return response()->json($dto, 201);
    }

    public function marcarParticipantesBulk(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        foreach ($data['ids'] as $idIns) {
            $this->vendedorScope->assertAccesoInscripcion(auth()->user(), (int) $idIns);
        }

        $resultado = $this->marcarParticipantesBulkHandler->handle(new MarcarParticipantesBulkCommand(
            idsIns:  array_map('intval', $data['ids']),
            idUsReg: auth()->id() ?? 0,
        ));

        return response()->json($resultado);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $id);

        $this->deleteHandler->handle(new DeleteInscripcionCommand($id));
        return response()->json(null, 204);
    }

    public function cursos(Request $request): JsonResponse
    {
        $idImpPermitidos = $this->vendedorScope->idImpPermitidos(auth()->user());

        $conteo = DB::table('t_inscripcion')
            ->selectRaw('id_imp, COUNT(DISTINCT id_ins) as total_inscritos')
            ->groupBy('id_imp');

        $cursos = DB::table('t_imparte as imp')
            ->join('t_materia as mat', function ($j) {
                $j->on('imp.id_mat', '=', 'mat.id_mat')
                  ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
            })
            ->leftJoin('t_programa as prog', 'prog.id_imp', '=', 'imp.id_imp')
            ->leftJoin('t_usuario as doc', function ($j) {
                $j->on('imp.id_us', '=', 'doc.id_us')
                  ->whereRaw('doc.id_us_reg = (SELECT MIN(d2.id_us_reg) FROM t_usuario d2 WHERE d2.id_us = doc.id_us AND d2.tipoestudiante = \'1\')');
            })
            ->joinSub($conteo, 'cnt', fn ($j) => $j->on('cnt.id_imp', '=', 'imp.id_imp'))
            ->when($idImpPermitidos !== null, fn ($q) => $q->whereIn('imp.id_imp', $idImpPermitidos))
            ->groupBy(
                'imp.id_imp', 'imp.periodo', 'imp.gestion', 'imp.paralelo',
                'mat.id_mat', 'mat.nombre',
                'prog.id_programa', 'prog.nombre_programa',
                'doc.appaterno', 'doc.apmaterno', 'doc.nombre',
                'cnt.total_inscritos'
            )
            ->selectRaw("
                imp.id_imp,
                COALESCE(prog.nombre_programa, mat.nombre) as nombre,
                imp.periodo,
                imp.gestion,
                imp.paralelo,
                TRIM(CONCAT(COALESCE(doc.appaterno,''), ' ', COALESCE(doc.nombre,''))) as docente,
                cnt.total_inscritos
            ")
            ->orderByDesc('imp.gestion')
            ->orderBy('nombre')
            ->get();

        return response()->json(['data' => $cursos]);
    }

    public function reportes(Request $request): JsonResponse
    {
        $idImp = $request->filled('id_imp') ? (int) $request->get('id_imp') : null;

        if ($idImp) {
            $this->vendedorScope->assertAccesoImparte(auth()->user(), $idImp);
        }

        $idImpPermitidos = $idImp ? null : $this->vendedorScope->idImpPermitidos(auth()->user());

        $base = DB::table('t_inscripcion as ins')
            ->join('t_imparte as imp', 'ins.id_imp', '=', 'imp.id_imp')
            ->join('t_usuario as us', function ($j) {



                $j->on('ins.id_us', '=', 'us.id_us')
                  ->whereRaw("us.id_us_reg = COALESCE(
                        (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = us.id_us AND u2.tipoestudiante = '2'),
                        (SELECT MIN(u3.id_us_reg) FROM t_usuario u3 WHERE u3.id_us = us.id_us)
                    )");
            });

        if ($idImp) $base->where('ins.id_imp', $idImp);

        if ($idImpPermitidos !== null) $base->whereIn('ins.id_imp', $idImpPermitidos);

        
        
        
        
        
        
        $kpis = (clone $base)
            ->leftJoin('t_pago as p', function ($j) {
                $j->on('p.id_ins', '=', 'ins.id_ins')->where('p.estado', 1);
            })
            ->selectRaw('
                COUNT(DISTINCT ins.id_ins) as total,
                COUNT(DISTINCT CASE WHEN ins.estado = 1 THEN ins.id_ins END) as activas,
                COUNT(DISTINCT CASE WHEN ins.estado = 0 THEN ins.id_ins END) as bajas,
                SUM(CASE WHEN p.monto_pagado IS NOT NULL THEN CAST(p.monto_pagado AS DECIMAL(12,2)) ELSE 0 END) as recaudado
            ')
            ->first();


        $dimensiones = [];


        $formularioCampos = $idImp ? $this->camposFormularioParaImparte($idImp) : [];

        if ($idImp && $formularioCampos) {
            $dimensiones = array_merge($dimensiones, $this->dimensionesDesdeFormulario($idImp, $formularioCampos));
        } else {


        $dimensiones[] = [
            'key'       => 'canal',
            'titulo'    => '¿Por qué canal se inscribió?',
            'subtitulo' => 'Distribución por canal de venta',
            'tipo'      => 'donut',
            'color'     => '#6366f1',
            'datos'     => (clone $base)
                ->selectRaw("COALESCE(ins.canal_venta, 'admin') as label, COUNT(DISTINCT ins.id_ins) as value")
                ->groupBy('ins.canal_venta')
                ->orderByDesc('value')
                ->get()
                ->map(fn ($r) => ['label' => $r->label === 'admin' ? 'Presencial' : ucfirst($r->label), 'value' => (int) $r->value])
                ->values(),
        ];


        $dimensiones[] = [
            'key'       => 'estado',
            'titulo'    => '¿Cuál es el estado de la inscripción?',
            'subtitulo' => 'Activos vs Bajas',
            'tipo'      => 'donut',
            'color'     => '#22c55e',
            'datos'     => (clone $base)
                ->selectRaw('ins.estado as label, COUNT(DISTINCT ins.id_ins) as value')
                ->groupBy('ins.estado')
                ->get()
                ->map(fn ($r) => ['label' => $r->label == 1 ? 'Activo' : 'Baja', 'value' => (int) $r->value])
                ->values(),
        ];


        $dimensiones[] = [
            'key'       => 'genero',
            'titulo'    => '¿Cuál es el género de los inscritos?',
            'subtitulo' => 'Distribución por género',
            'tipo'      => 'donut',
            'color'     => '#ec4899',
            'datos'     => (clone $base)
                ->selectRaw('COALESCE(us.genero, 0) as label, COUNT(DISTINCT ins.id_ins) as value')
                ->groupBy('us.genero')
                ->orderByDesc('value')
                ->get()
                ->map(fn ($r) => ['label' => match((int)$r->label) { 1 => 'Masculino', 2 => 'Femenino', default => 'No especificado' }, 'value' => (int) $r->value])
                ->values(),
        ];


        $dimensiones[] = [
            'key'       => 'ciudad',
            'titulo'    => '¿De qué ciudad provienen?',
            'subtitulo' => 'Top ciudades de origen de los inscritos',
            'tipo'      => 'bar-h',
            'color'     => '#3b82f6',
            'datos'     => (clone $base)
                ->selectRaw("COALESCE(NULLIF(TRIM(us.ciudad),''), 'No especificada') as label, COUNT(DISTINCT ins.id_ins) as value")
                ->groupBy('us.ciudad')
                ->orderByDesc('value')
                ->limit(10)
                ->get()
                ->map(fn ($r) => ['label' => $r->label, 'value' => (int) $r->value])
                ->values(),
        ];


        $dimensiones[] = [
            'key'       => 'gestion',
            'titulo'    => '¿En qué gestión se inscribió?',
            'subtitulo' => 'Cantidad de inscripciones por año',
            'tipo'      => 'bar',
            'color'     => '#6366f1',
            'datos'     => (clone $base)
                ->selectRaw("COALESCE(ins.gestion, 'Sin gestión') as label, COUNT(DISTINCT ins.id_ins) as value")
                ->groupBy('ins.gestion')
                ->orderBy('ins.gestion')
                ->get()
                ->map(fn ($r) => ['label' => $r->label, 'value' => (int) $r->value])
                ->values(),
        ];


        $dimensiones[] = [
            'key'       => 'periodo',
            'titulo'    => '¿En qué período se inscribió?',
            'subtitulo' => 'Distribución por período o semestre',
            'tipo'      => 'bar',
            'color'     => '#f59e0b',
            'datos'     => (clone $base)
                ->selectRaw("COALESCE(ins.periodo, 'Sin período') as label, COUNT(DISTINCT ins.id_ins) as value")
                ->groupBy('ins.periodo')
                ->orderBy('ins.periodo')
                ->get()
                ->map(fn ($r) => ['label' => $r->label, 'value' => (int) $r->value])
                ->values(),
        ];


        $dimensiones[] = [
            'key'       => 'tipo_estudiante',
            'titulo'    => '¿Qué tipo de estudiante es?',
            'subtitulo' => 'Categoría o nivel académico del inscrito',
            'tipo'      => 'donut',
            'color'     => '#14b8a6',
            'datos'     => (clone $base)
                ->selectRaw("COALESCE(NULLIF(us.tipoestudiante,''), 'Sin especificar') as label, COUNT(DISTINCT ins.id_ins) as value")
                ->groupBy('us.tipoestudiante')
                ->orderByDesc('value')
                ->get()
                ->map(fn ($r) => ['label' => match($r->label) { '1' => 'Pregrado', '2' => 'Posgrado', '3' => 'Egresado', '4' => 'Profesional', default => $r->label }, 'value' => (int) $r->value])
                ->values(),
        ];

        }



        if (! $idImp) {
            $dimensiones[] = [
                'key'       => 'programa',
                'titulo'    => '¿En qué programa o materia?',
                'subtitulo' => 'Top 10 cursos con más inscripciones',
                'tipo'      => 'bar-h',
                'color'     => '#8b5cf6',
                'datos'     => (clone $base)
                    ->leftJoin('t_programa as prog', 'prog.id_imp', '=', 'imp.id_imp')
                    ->leftJoin('t_materia as mat', function ($j) {
                        $j->on('imp.id_mat', '=', 'mat.id_mat')
                          ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
                    })
                    ->selectRaw("imp.id_imp, COALESCE(prog.nombre_programa, mat.nombre, 'Sin programa') as label, COUNT(DISTINCT ins.id_ins) as value")
                    ->groupBy('imp.id_imp', 'prog.nombre_programa', 'mat.nombre')
                    ->get()
                    
                    ->groupBy('label')
                    ->map(fn ($g) => ['label' => $g->first()->label, 'value' => $g->sum('value')])
                    ->sortByDesc('value')
                    ->take(10)
                    ->values(),
            ];
        }

        
        $dimensiones[] = [
            'key'       => 'mes',
            'titulo'    => '¿En qué mes se realizaron las inscripciones?',
            'subtitulo' => 'Distribución mensual de inscripciones',
            'tipo'      => 'bar',
            'color'     => '#ef4444',
            'datos'     => (clone $base)
                ->whereNotNull('ins.fecha_ins')
                ->selectRaw(SqlCompat::dateFormat('ins.fecha_ins', '%Y-%m') . ' as label, COUNT(DISTINCT ins.id_ins) as value')
                ->groupByRaw(SqlCompat::dateFormat('ins.fecha_ins', '%Y-%m'))
                ->orderBy('label')
                ->get()
                ->map(fn ($r) => ['label' => $r->label, 'value' => (int) $r->value])
                ->values(),
        ];

        
        $curso = null;
        if ($idImp) {
            $curso = DB::table('t_imparte as imp')
                ->join('t_materia as mat', function ($j) {
                    $j->on('imp.id_mat', '=', 'mat.id_mat')
                      ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
                })
                ->leftJoin('t_programa as prog', 'prog.id_imp', '=', 'imp.id_imp')
                ->leftJoin('t_usuario as doc', function ($j) {
                    $j->on('imp.id_us', '=', 'doc.id_us')
                      ->whereRaw('doc.id_us_reg = (SELECT MIN(d2.id_us_reg) FROM t_usuario d2 WHERE d2.id_us = doc.id_us AND d2.tipoestudiante = \'1\')');
                })
                ->where('imp.id_imp', $idImp)
                ->groupBy(
                    'imp.id_imp', 'imp.periodo', 'imp.gestion', 'imp.paralelo',
                    'mat.id_mat', 'mat.nombre',
                    'prog.id_programa', 'prog.nombre_programa',
                    'doc.appaterno', 'doc.apmaterno', 'doc.nombre'
                )
                ->selectRaw("
                    imp.id_imp,
                    COALESCE(prog.nombre_programa, mat.nombre) as nombre,
                    imp.periodo, imp.gestion, imp.paralelo,
                    TRIM(CONCAT(COALESCE(doc.appaterno,''),' ',COALESCE(doc.nombre,''))) as docente
                ")
                ->first();
        }

        return response()->json([
            'curso'       => $curso,
            'resumen'     => [
                'total'     => (int)   ($kpis->total     ?? 0),
                'activas'   => (int)   ($kpis->activas   ?? 0),
                'bajas'     => (int)   ($kpis->bajas     ?? 0),
                'recaudado' => (float) ($kpis->recaudado ?? 0),
            ],
            'dimensiones' => $dimensiones,
        ]);
    }

    private function camposFormularioParaImparte(int $idImp): array
    {
        $formularioId = DB::table('t_programa')
            ->where('id_imp', $idImp)
            ->orderBy('id_us_reg')
            ->value('formulario_id');

        if (! $formularioId) {
            return [];
        }

        $camposJson = DB::table('web_formulario')->where('id', $formularioId)->value('campos');
        $campos = is_string($camposJson) ? (json_decode($camposJson, true) ?? []) : [];

        return array_values(array_filter(array_map(function ($campo) {
            $nombre = $campo['nombre_campo'] ?? null;
            if (! $nombre) return null;
            $campo['clave_raiz'] = CampoRaizResolver::resolverClave($nombre, $campo['rol_identidad'] ?? null);
            return $campo;
        }, $campos)));
    }

    private function dimensionesDesdeFormulario(int $idImp, array $formularioCampos): array
    {
        $inscripciones = DB::table('t_inscripcion')
            ->where('id_imp', $idImp)
            ->select(['id_us', 'email', 'telefono', 'campos_extra'])
            ->get();

        $usuariosPorId = DB::table('t_usuario')
            ->whereIn('id_us', $inscripciones->pluck('id_us')->unique())
            ->where('id_us_reg', 0)
            ->get()
            ->keyBy('id_us');

        $dimensiones = [];
        $colores = ['#8b5cf6', '#f59e0b', '#22c55e', '#ec4899', '#3b82f6', '#14b8a6', '#ef4444', '#6366f1'];
        $i = 0;

        foreach ($formularioCampos as $campo) {
            if (($campo['tipo'] ?? 'text') === 'file') continue;

            $nombreCampo = $campo['nombre_campo'];
            $claveRaiz   = $campo['clave_raiz'] ?? null;

            $conteo = [];
            foreach ($inscripciones as $ins) {
                $valor = match ($claveRaiz) {
                    'email'    => $ins->email,
                    'telefono' => $ins->telefono,
                    'ci', 'nombre', 'apellido_paterno', 'apellido_materno' =>
                        $usuariosPorId->get($ins->id_us)?->{[
                            'ci' => 'ci', 'nombre' => 'nombre',
                            'apellido_paterno' => 'appaterno', 'apellido_materno' => 'apmaterno',
                        ][$claveRaiz]} ?? null,
                    default => (json_decode($ins->campos_extra ?? '{}', true) ?? [])[$nombreCampo] ?? null,
                };

                $valor = is_string($valor) ? trim($valor) : $valor;
                if ($valor === null || $valor === '') continue;

                $label = is_array($valor) ? implode(', ', $valor) : (string) $valor;
                $conteo[$label] = ($conteo[$label] ?? 0) + 1;
            }

            if (! $conteo) continue;

            arsort($conteo);
            $esCorto = in_array($campo['tipo'] ?? 'text', ['select', 'checkbox', 'radio'], true) && count($conteo) <= 8;

            $dimensiones[] = [
                'key'       => 'form_' . $nombreCampo,
                'titulo'    => $campo['etiqueta'] ?? $nombreCampo,
                'subtitulo' => 'Respuestas del formulario de inscripción',
                'tipo'      => $esCorto ? 'donut' : 'bar-h',
                'color'     => $colores[$i % count($colores)],
                'datos'     => collect($conteo)
                    ->map(fn ($value, $label) => ['label' => $label, 'value' => $value])
                    ->take(15)
                    ->values(),
            ];
            $i++;
        }

        return $dimensiones;
    }
}
