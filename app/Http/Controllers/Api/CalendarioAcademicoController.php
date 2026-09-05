<?php

namespace App\Http\Controllers\Api;

use App\Application\CalendarioAcademico\Commands\CreateCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\Commands\DeleteCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\Commands\UpdateCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\Handlers\CreateCalendarioAcademicoHandler;
use App\Application\CalendarioAcademico\Handlers\DeleteCalendarioAcademicoHandler;
use App\Application\CalendarioAcademico\Handlers\UpdateCalendarioAcademicoHandler;
use App\Application\CalendarioAcademico\DTOs\CalendarioAcademicoPublicoDTO;
use App\Application\CalendarioAcademico\Queries\GetCalendarioAcademicoByIdQuery;
use App\Application\CalendarioAcademico\Queries\GetCalendarioAcademicoQuery;
use App\Application\CalendarioAcademico\Queries\GetCalendarioAcademicoReporteQuery;
use App\Application\CalendarioAcademico\Queries\GetCursosVigentesQuery;
use App\Application\CalendarioAcademico\QueryHandlers\GetCalendarioAcademicoByIdQueryHandler;
use App\Application\CalendarioAcademico\QueryHandlers\GetCalendarioAcademicoQueryHandler;
use App\Application\CalendarioAcademico\QueryHandlers\GetCalendarioAcademicoReporteQueryHandler;
use App\Application\CalendarioAcademico\QueryHandlers\GetCursosVigentesQueryHandler;
use App\Domain\CalendarioAcademico\Exceptions\CalendarioAcademicoNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalendarioAcademico\StoreCalendarioAcademicoRequest;
use App\Http\Requests\CalendarioAcademico\UpdateCalendarioAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalendarioAcademicoController extends Controller
{
    private const TIPO_LABELS = [
        'inscripciones' => 'Inscripciones',
        'inicio_clases' => 'Inicio de Clases',
        'finalizacion'  => 'Finalización / Cierre',
        'evaluacion'    => 'Evaluación / Examen',
        'graduacion'    => 'Graduación / Ceremonia',
        'feriado'       => 'Feriado',
        'otro'          => 'Otro',
    ];

    public function __construct(
        private readonly GetCalendarioAcademicoQueryHandler $getCalendarioHandler,
        private readonly GetCalendarioAcademicoByIdQueryHandler $getByIdHandler,
        private readonly GetCursosVigentesQueryHandler $getCursosVigentesHandler,
        private readonly GetCalendarioAcademicoReporteQueryHandler $getReporteHandler,
        private readonly CreateCalendarioAcademicoHandler $createHandler,
        private readonly UpdateCalendarioAcademicoHandler $updateHandler,
        private readonly DeleteCalendarioAcademicoHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'fecha_inicio'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getCalendarioHandler->handle(
                new GetCalendarioAcademicoQuery($pagination, $request->boolean('soloPublicos', false), $request->get('tipo') ?: null)
            )
        );
    }

    public function vigentes(): JsonResponse
    {
        return response()->json($this->getCursosVigentesHandler->handle(new GetCursosVigentesQuery()));
    }

    public function portalIndex(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => $request->get('query', ''),
            'sortKey'   => 'fecha_inicio',
            'sortOrder' => 'asc',
        ]);

        $paginado = $this->getCalendarioHandler->handle(
            new GetCalendarioAcademicoQuery($pagination, soloPublicos: true, tipo: $request->get('tipo') ?: null)
        )->through(fn ($dto) => CalendarioAcademicoPublicoDTO::fromDTO($dto));

        return response()->json($paginado);
    }

    public function portalShow(int $id): JsonResponse
    {
        $dto = $this->getByIdHandler->handle(new GetCalendarioAcademicoByIdQuery($id));

        if (! $dto->publico) {
            throw new CalendarioAcademicoNotFoundException($id);
        }

        return response()->json(CalendarioAcademicoPublicoDTO::fromDTO($dto));
    }

    public function store(StoreCalendarioAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCalendarioAcademicoCommand(
            titulo:       $request->titulo,
            descripcion:  $request->descripcion,
            tipo:         $request->tipo,
            color:        $request->color,
            programa_id:  $request->programa_id ? (int) $request->programa_id : null,
            vendedor_id:  $request->vendedor_id ? (int) $request->vendedor_id : null,
            pagina:       $request->pagina,
            duracion_dias: $request->duracion_dias !== null ? (int) $request->duracion_dias : null,
            costo_inflado: $request->costo_inflado !== null ? (float) $request->costo_inflado : null,
            descuento:    $request->descuento !== null ? (float) $request->descuento : null,
            precio_vip:   $request->precio_vip !== null ? (float) $request->precio_vip : null,
            observaciones: $request->observaciones,
            fecha_inicio: $request->fecha_inicio,
            fecha_fin:    $request->fecha_fin,
            todo_el_dia:  $request->boolean('todo_el_dia', true),
            destacado:    $request->boolean('destacado', false),
            publico:      $request->boolean('publico', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetCalendarioAcademicoByIdQuery($id)));
    }

    public function update(UpdateCalendarioAcademicoRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateCalendarioAcademicoCommand(
            id:           $id,
            titulo:       $request->titulo,
            descripcion:  $request->descripcion,
            tipo:         $request->tipo,
            color:        $request->color,
            programa_id:  $request->has('programa_id') ? (int) $request->programa_id : null,
            vendedor_id:  $request->has('vendedor_id') ? (int) $request->vendedor_id : null,
            pagina:       $request->has('pagina') ? $request->pagina : null,
            duracion_dias: $request->has('duracion_dias') ? (int) $request->duracion_dias : null,
            costo_inflado: $request->has('costo_inflado') ? (float) $request->costo_inflado : null,
            descuento:    $request->has('descuento') ? (float) $request->descuento : null,
            precio_vip:   $request->has('precio_vip') ? (float) $request->precio_vip : null,
            observaciones: $request->has('observaciones') ? $request->observaciones : null,
            fecha_inicio: $request->fecha_inicio,
            fecha_fin:    $request->fecha_fin,
            todo_el_dia:  $request->has('todo_el_dia') ? $request->boolean('todo_el_dia') : null,
            destacado:    $request->has('destacado') ? $request->boolean('destacado') : null,
            publico:      $request->has('publico') ? $request->boolean('publico') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCalendarioAcademicoCommand($id));

        return response()->json(null, 204);
    }

    private function reporteData(Request $request): array
    {
        return $this->getReporteHandler->handle(new GetCalendarioAcademicoReporteQuery(
            query:        (string) $request->get('query', ''),
            tipo:         $request->get('tipo') ?: null,
            soloPublicos: $request->boolean('soloPublicos', false),
        ));
    }

    public function exportPdf(Request $request): Response
    {
        $eventos = $this->reporteData($request);
        $tipoLabels = self::TIPO_LABELS;

        $pdf = Pdf::loadView('pdf.calendario_academico_listado', [
            'eventos'    => $eventos,
            'total'      => count($eventos),
            'tipoLabels' => $tipoLabels,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('calendario-academico-listado.pdf');
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $eventos    = $this->reporteData($request);
        $tipoLabels = self::TIPO_LABELS;

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Calendario Académico');

        $sheet->setCellValue('A1', 'Calendario Académico — Cronograma');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A2', 'Generado: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A3', 'Total de eventos: ' . count($eventos));
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $filaEncabezado = 5;
        $columnas = ['#', 'Tipo', 'Título', 'Programa', 'Vendedor', 'Página', 'Fecha Inicio', 'Fecha Fin', 'Duración (días)', 'Costo Inflado', 'Descuento', 'Precio VIP', 'Público'];
        foreach (array_values($columnas) as $i => $titulo) {
            $col = chr(ord('A') + $i);
            $sheet->setCellValue("{$col}{$filaEncabezado}", $titulo);
        }

        $ultimaCol = chr(ord('A') + count($columnas) - 1);
        $estiloEncabezado = $sheet->getStyle("A{$filaEncabezado}:{$ultimaCol}{$filaEncabezado}");
        $estiloEncabezado->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $estiloEncabezado->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1A1A2E');
        $estiloEncabezado->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $fila = $filaEncabezado + 1;
        foreach ($eventos as $i => $ev) {
            $sheet->setCellValue("A{$fila}", $i + 1);
            $sheet->setCellValue("B{$fila}", $tipoLabels[$ev->tipo] ?? ($ev->tipo ?? '—'));
            $sheet->setCellValue("C{$fila}", $ev->titulo);
            $sheet->setCellValue("D{$fila}", $ev->nombre_programa ?? '—');
            $sheet->setCellValue("E{$fila}", $ev->vendedor_nombre ?? '—');
            $sheet->setCellValue("F{$fila}", $ev->pagina ?? '—');
            $sheet->setCellValue("G{$fila}", $ev->fecha_inicio ? substr($ev->fecha_inicio, 0, 10) : '—');
            $sheet->setCellValue("H{$fila}", $ev->fecha_fin ? substr($ev->fecha_fin, 0, 10) : '—');
            $sheet->setCellValue("I{$fila}", $ev->duracion_dias ?? '—');
            $sheet->setCellValue("J{$fila}", $ev->costo_inflado);
            $sheet->setCellValue("K{$fila}", $ev->descuento);
            $sheet->setCellValue("L{$fila}", $ev->precio_vip);
            $sheet->setCellValue("M{$fila}", $ev->publico ? 'Sí' : 'No');
            foreach (['J', 'K', 'L'] as $col) {
                $sheet->getStyle("{$col}{$fila}")->getNumberFormat()->setFormatCode('"Bs. "#,##0.00');
            }
            $fila++;
        }

        foreach (range('A', $ultimaCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'calendario-academico-listado.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
