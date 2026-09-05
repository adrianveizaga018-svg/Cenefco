<?php

namespace App\Http\Controllers\Api;

use App\Application\Planillas\Commands\DeletePlanillaCommand;
use App\Application\Planillas\Commands\GenerarPlanillaCommand;
use App\Application\Planillas\Handlers\DeletePlanillaHandler;
use App\Application\Planillas\Handlers\GenerarPlanillaHandler;
use App\Application\Planillas\Queries\GetPlanillaByIdQuery;
use App\Application\Planillas\Queries\GetPlanillaPreviewQuery;
use App\Application\Planillas\Queries\GetPlanillasQuery;
use App\Application\Planillas\QueryHandlers\GetPlanillaByIdQueryHandler;
use App\Application\Planillas\QueryHandlers\GetPlanillaPreviewQueryHandler;
use App\Application\Planillas\QueryHandlers\GetPlanillasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Planillas\GenerarPlanillaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Planillas')]
class PlanillaController extends Controller
{
    public function __construct(
        private readonly GetPlanillasQueryHandler    $getListHandler,
        private readonly GetPlanillaByIdQueryHandler $getByIdHandler,
        private readonly GenerarPlanillaHandler       $generarHandler,
        private readonly DeletePlanillaHandler        $deleteHandler,
        private readonly GetPlanillaPreviewQueryHandler $previewHandler,
    ) {}

    #[OA\Get(path: '/api/v1/planillas/preview', summary: 'Vista previa de la planilla de un mes (sueldo + ajustes pendientes)', tags: ['Planillas'])]
    public function preview(GenerarPlanillaRequest $request): JsonResponse
    {
        $items = $this->previewHandler->handle(new GetPlanillaPreviewQuery(
            anio: (int) $request->anio,
            mes:  (int) $request->mes,
        ));

        return response()->json(['data' => $items]);
    }

    #[OA\Get(path: '/api/v1/planillas', summary: 'Listar historial de planillas', tags: ['Planillas'])]
    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 12),
        ], 'anio');

        return response()->json($this->getListHandler->handle(new GetPlanillasQuery($pagination)));
    }

    #[OA\Get(path: '/api/v1/planillas/{id}', summary: 'Detalle de una planilla', tags: ['Planillas'])]
    public function show(int $planilla): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetPlanillaByIdQuery($planilla)));
    }

    #[OA\Post(path: '/api/v1/planillas/generar', summary: 'Generar planilla del mes', tags: ['Planillas'])]
    public function generar(GenerarPlanillaRequest $request): JsonResponse
    {
        $dto = $this->generarHandler->handle(new GenerarPlanillaCommand(
            anio: (int) $request->anio,
            mes:  (int) $request->mes,
        ));

        return response()->json($dto, 201);
    }

    #[OA\Delete(path: '/api/v1/planillas/{id}', summary: 'Eliminar planilla del mes en curso', tags: ['Planillas'])]
    public function destroy(int $planilla): JsonResponse
    {
        $this->deleteHandler->handle(new DeletePlanillaCommand($planilla));

        return response()->json(null, 204);
    }

    private const NOMBRES_MES = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    #[OA\Get(path: '/api/v1/planillas/{id}/pdf', summary: 'Descargar planilla en PDF', tags: ['Planillas'])]
    public function pdf(int $planilla): Response
    {
        $dto = $this->getByIdHandler->handle(new GetPlanillaByIdQuery($planilla));

        $nombreMes = self::NOMBRES_MES[$dto->mes] ?? (string) $dto->mes;

        $pdf = Pdf::loadView('pdf.planilla', [
            'planilla'  => $dto,
            'nombreMes' => $nombreMes,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("planilla-{$dto->anio}-{$dto->mes}.pdf");
    }

    #[OA\Get(path: '/api/v1/planillas/{id}/excel', summary: 'Descargar planilla en Excel', tags: ['Planillas'])]
    public function excel(int $planilla): StreamedResponse
    {
        $dto = $this->getByIdHandler->handle(new GetPlanillaByIdQuery($planilla));

        $nombreMes = self::NOMBRES_MES[$dto->mes] ?? (string) $dto->mes;

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Planilla');

        $sheet->setCellValue('A1', "Planilla de sueldos — {$nombreMes} {$dto->anio}");
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A2', 'Total empleados');
        $sheet->setCellValue('B2', count($dto->detalle));
        $sheet->setCellValue('C2', 'Total planilla');
        $sheet->setCellValue('D2', (float) $dto->total);
        $sheet->getStyle('D2')->getNumberFormat()->setFormatCode('"Bs. "#,##0.00');
        $sheet->getStyle('A2:D2')->getFont()->setBold(true);

        $filaEncabezado = 4;
        $sheet->setCellValue("A{$filaEncabezado}", '#');
        $sheet->setCellValue("B{$filaEncabezado}", 'Nombre completo');
        $sheet->setCellValue("C{$filaEncabezado}", 'Cargo');
        $sheet->setCellValue("D{$filaEncabezado}", 'Sueldo base');
        $sheet->setCellValue("E{$filaEncabezado}", 'Descuentos');
        $sheet->setCellValue("F{$filaEncabezado}", 'Bonos');
        $sheet->setCellValue("G{$filaEncabezado}", 'Neto pagado');

        $estiloEncabezado = $sheet->getStyle("A{$filaEncabezado}:G{$filaEncabezado}");
        $estiloEncabezado->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $estiloEncabezado->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1A1A2E');
        $estiloEncabezado->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $fila = $filaEncabezado + 1;
        foreach ($dto->detalle as $i => $d) {
            $sheet->setCellValue("A{$fila}", $i + 1);
            $sheet->setCellValue("B{$fila}", $d->nombre_completo);
            $sheet->setCellValue("C{$fila}", $d->cargo);
            $sheet->setCellValue("D{$fila}", (float) ($d->monto_base ?? $d->monto));
            $sheet->setCellValue("E{$fila}", (float) $d->total_descuentos);
            $sheet->setCellValue("F{$fila}", (float) $d->total_bonos);
            $sheet->setCellValue("G{$fila}", (float) $d->monto);
            foreach (['D', 'E', 'F', 'G'] as $col) {
                $sheet->getStyle("{$col}{$fila}")->getNumberFormat()->setFormatCode('"Bs. "#,##0.00');
            }
            $fila++;
        }

        $sheet->setCellValue("F{$fila}", 'Total');
        $sheet->setCellValue("G{$fila}", (float) $dto->total);
        $sheet->getStyle("F{$fila}:G{$fila}")->getFont()->setBold(true);
        $sheet->getStyle("G{$fila}")->getNumberFormat()->setFormatCode('"Bs. "#,##0.00');
        $sheet->getStyle("F{$fila}:G{$fila}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = "planilla-{$dto->anio}-{$dto->mes}.xlsx";

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
