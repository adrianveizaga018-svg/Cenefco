<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\CursosMigrados\Models\CursoMigrado;
use App\Infrastructure\CursosMigrados\Models\CursoMigradoLogo;
use App\Infrastructure\CursosMigrados\Models\ParticipanteMigrado;
use App\Infrastructure\WhatsApp\Models\WhatsappConversacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CursoMigradoController extends Controller
{
    private const MESES = [
        1  => 'ENERO',
        2  => 'FEBRERO',
        3  => 'MARZO',
        4  => 'ABRIL',
        5  => 'MAYO',
        6  => 'JUNIO',
        7  => 'JULIO',
        8  => 'AGOSTO',
        9  => 'SEPTIEMBRE',
        10 => 'OCTUBRE',
        11 => 'NOVIEMBRE',
        12 => 'DICIEMBRE',
    ];

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre'        => ['required', 'string', 'max:300'],
            'url'           => ['required', 'url', 'max:500'],
            'periodo'       => ['nullable', 'string', 'max:50'],
            'gestion'       => ['nullable', 'string', 'max:10'],
            'fecha_inicio'  => ['nullable', 'date'],
            'carga_horaria' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ]);

        $slug = Str::slug($data['nombre']);
        $base = $slug;
        $i    = 1;
        while (CursoMigrado::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        $data['slug'] = $slug;

        $curso = CursoMigrado::create($data);

        return response()->json($curso, 201);
    }

    public function importarJson(Request $request): JsonResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'max:10240'],
        ]);

        if ($request->file('archivo')->getClientOriginalExtension() !== 'json') {
            return response()->json(['message' => 'El archivo debe tener extensión .json'], 422);
        }

        $contenido = file_get_contents($request->file('archivo')->getRealPath());
        $items     = json_decode($contenido, true);

        if (! is_array($items)) {
            return response()->json(['message' => 'El archivo no contiene un JSON válido.'], 422);
        }

        $creados   = 0;
        $omitidos  = 0;
        $errores   = [];

        foreach ($items as $index => $item) {
            try {
                if (! is_array($item)) { $omitidos++; continue; }

                $nombreRaw   = trim($item['curso'] ?? '');
                $urlRaw      = trim($item['url']   ?? '');
                $mesRaw      = trim($item['mes']   ?? '');
                $status      = strtolower(trim($item['status'] ?? 'ok'));
                $estudiantes = is_array($item['estudiantes'] ?? null) ? $item['estudiantes'] : [];

                if ($nombreRaw === '' || $urlRaw === '' || $status !== 'ok') {
                    $omitidos++;
                    continue;
                }

                $slug = Str::slug(basename(rtrim($urlRaw, '/')));

                if (CursoMigrado::where('slug', $slug)->orWhere('url', $urlRaw)->exists()) {
                    $omitidos++;
                    continue;
                }

                $periodo = null;
                $gestion = null;
                if ($mesRaw !== '') {
                    $partes  = explode(' ', $mesRaw);
                    $gestion = end($partes);
                    $periodo = $mesRaw;
                }

                $curso = DB::transaction(function () use (
                    $nombreRaw, $urlRaw, $slug, $periodo, $gestion, $estudiantes
                ) {
                    $c = CursoMigrado::create([
                        'nombre'  => Str::title(strtolower($nombreRaw)),
                        'url'     => $urlRaw,
                        'slug'    => $slug,
                        'periodo' => $periodo,
                        'gestion' => $gestion,
                    ]);

                    $vistos = [];
                    foreach ($estudiantes as $nombre) {
                        $nombre = mb_strtoupper(trim((string) $nombre));
                        if ($nombre === '' || isset($vistos[$nombre])) continue;
                        $vistos[$nombre] = true;
                        ParticipanteMigrado::create([
                            'curso_id'        => $c->id,
                            'nombre_completo' => $nombre,
                        ]);
                    }

                    return $c;
                });

                $creados++;
            } catch (\Throwable $e) {
                $errores[] = "Ítem #{$index} ({$item['curso']} ): {$e->getMessage()}";
            }
        }

        return response()->json([
            'creados'  => $creados,
            'omitidos' => $omitidos,
            'errores'  => $errores,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $pageSize        = (int) $request->get('pageSize', 15);
        $pageIndex       = (int) $request->get('pageIndex', 1);
        $query           = trim((string) $request->get('query', ''));
        $mes             = (int) $request->get('mes', 0);
        $participantesMin = $request->get('participantesMin');
        $participantesMax = $request->get('participantesMax');

        $q = CursoMigrado::withCount('participantes');

        if ($query !== '') {
            $q->where('nombre', 'ilike', "%{$query}%");
        }

        if ($mes >= 1 && $mes <= 12) {
            $nombreMes = self::MESES[$mes];
            $q->where(function ($sub) use ($nombreMes, $mes) {
                $sub->where('periodo', 'ilike', "%{$nombreMes}%")
                    ->orWhereMonth('fecha_inicio', $mes);
            });
        }

        if ($participantesMin !== null && $participantesMin !== '') {
            $q->has('participantes', '>=', (int) $participantesMin);
        }

        if ($participantesMax !== null && $participantesMax !== '') {
            $q->has('participantes', '<=', (int) $participantesMax);
        }

        $paginated = $q->orderByDesc('id')->paginate($pageSize, ['*'], 'page', $pageIndex);

        return response()->json([
            'data'  => $paginated->items(),
            'total' => $paginated->total(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $curso = CursoMigrado::with(['participantes', 'logos'])->findOrFail($id);

        return response()->json($curso);
    }

    public function buscarParticipantes(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('query', ''));

        if ($query === '') {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $pageSize  = (int) $request->get('pageSize', 15);
        $pageIndex = (int) $request->get('pageIndex', 1);

        $paginated = ParticipanteMigrado::with('curso')
            ->where('nombre_completo', 'ilike', "%{$query}%")
            ->orderBy('nombre_completo')
            ->paginate($pageSize, ['*'], 'page', $pageIndex);

        return response()->json([
            'data'  => $paginated->items(),
            'total' => $paginated->total(),
        ]);
    }

    public function uploadLogo(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'logo'   => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'nombre' => 'nullable|string|max:200',
        ]);

        $curso = CursoMigrado::findOrFail($id);

        $path = $request->file('logo')->store('cursos-migrados/logos', 'public');

        $logo = CursoMigradoLogo::create([
            'curso_id' => $curso->id,
            'path'     => $path,
            'nombre'   => $request->input('nombre'),
            'orden'    => CursoMigradoLogo::where('curso_id', $id)->max('orden') + 1,
        ]);

        return response()->json($logo, 201);
    }

    public function deleteLogo(int $id, int $logoId): JsonResponse
    {
        $logo = CursoMigradoLogo::where('curso_id', $id)->findOrFail($logoId);

        Storage::disk('public')->delete($logo->path);
        $logo->delete();

        return response()->json(null, 204);
    }

    public function destroy(int $id): JsonResponse
    {
        $curso = CursoMigrado::findOrFail($id);

        if ($curso->imagen_path) {
            Storage::disk('public')->delete($curso->imagen_path);
        }
        if ($curso->qr_path) {
            Storage::disk('public')->delete($curso->qr_path);
        }
        foreach ($curso->logos ?? [] as $logo) {
            Storage::disk('public')->delete($logo->path);
        }

        $curso->delete();

        return response()->json(null, 204);
    }

    public function generarQr(int $id): JsonResponse
    {
        $curso = CursoMigrado::findOrFail($id);

        try {
            $qrDir = Storage::disk('public')->path('qr/cursos_migrados');
            if (! is_dir($qrDir)) {
                mkdir($qrDir, 0755, true);
            }
            $qrFilename = $curso->slug . '_qr.png';
            $qrFilepath = $qrDir . DIRECTORY_SEPARATOR . $qrFilename;
            $qr  = QrCode::create($curso->url)->setSize(300)->setMargin(4);
            $png = (new PngWriter)->write($qr)->getString();
            file_put_contents($qrFilepath, $png);
            $qrPath = 'qr/cursos_migrados/' . $qrFilename;
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo generar el QR: ' . $e->getMessage()], 500);
        }

        $curso->update(['qr_path' => $qrPath]);

        return response()->json(['qr_path' => $qrPath]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total_cursos'        => CursoMigrado::count(),
            'total_participantes' => ParticipanteMigrado::count(),
            'total_conversaciones_whatsapp' => WhatsappConversacion::count(),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $curso = CursoMigrado::findOrFail($id);

        $data = $request->validate([
            'carga_horaria' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'periodo'       => ['nullable', 'string', 'max:50'],
            'gestion'       => ['nullable', 'string', 'max:10'],
            'fecha_inicio'  => ['nullable', 'date'],
        ]);

        $curso->update($data);

        return response()->json($curso);
    }

    public function uploadImagen(Request $request, int $id): JsonResponse
    {
        $curso = CursoMigrado::findOrFail($id);

        $request->validate([
            'imagen' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        if ($curso->imagen_path) {
            Storage::disk('public')->delete($curso->imagen_path);
        }

        $path = $request->file('imagen')->store('cursos-migrados/imagenes', 'public');
        $curso->update(['imagen_path' => $path]);

        return response()->json(['imagen_path' => $path]);
    }

    public function deleteImagen(int $id): JsonResponse
    {
        $curso = CursoMigrado::findOrFail($id);

        if ($curso->imagen_path) {
            Storage::disk('public')->delete($curso->imagen_path);
            $curso->update(['imagen_path' => null]);
        }

        return response()->json(null, 204);
    }

    public function addParticipante(Request $request, int $id): JsonResponse
    {
        $curso  = CursoMigrado::findOrFail($id);
        $nombre = mb_strtoupper(trim($request->validate([
            'nombre_completo' => ['required', 'string', 'max:300'],
        ])['nombre_completo']));

        $existe = ParticipanteMigrado::where('curso_id', $id)
            ->whereRaw('UPPER(nombre_completo) = ?', [$nombre])
            ->exists();

        if ($existe) {
            return response()->json(['message' => 'El participante ya está registrado en este curso.'], 422);
        }

        $participante = ParticipanteMigrado::create([
            'curso_id'        => $curso->id,
            'nombre_completo' => $nombre,
        ]);

        return response()->json($participante, 201);
    }

    public function updateParticipante(Request $request, int $id, int $participanteId): JsonResponse
    {
        $participante = ParticipanteMigrado::where('curso_id', $id)->findOrFail($participanteId);
        $nombre = mb_strtoupper(trim($request->validate([
            'nombre_completo' => ['required', 'string', 'max:300'],
        ])['nombre_completo']));

        $existe = ParticipanteMigrado::where('curso_id', $id)
            ->where('id', '!=', $participanteId)
            ->whereRaw('UPPER(nombre_completo) = ?', [$nombre])
            ->exists();

        if ($existe) {
            return response()->json(['message' => 'Ya existe un participante con ese nombre en este curso.'], 422);
        }

        $participante->update(['nombre_completo' => $nombre]);

        return response()->json($participante);
    }

    public function importarExcel(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'archivo' => ['required', 'file', 'max:5120',
                'mimes:xlsx,xls,csv,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ]);

        $curso = CursoMigrado::findOrFail($id);

        $path       = $request->file('archivo')->getRealPath();
        $extension  = strtolower($request->file('archivo')->getClientOriginalExtension());

        try {
            $readerType = match ($extension) {
                'xlsx'  => 'Xlsx',
                'xls'   => 'Xls',
                default => 'Csv',
            };
            $spreadsheet = IOFactory::createReader($readerType)->load($path);
            $filas       = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo leer el archivo: ' . $e->getMessage()], 422);
        }

        $primera = array_values(array_filter((array) ($filas[0] ?? []), fn ($v) => $v !== null));
        if (count($primera) > 0) {
            $primeraCelda = mb_strtolower(trim((string) $primera[0]));
            $esEncabezado = in_array($primeraCelda, [
                'nombre', 'nombres', 'nombre completo', 'apellidos y nombres',
                'estudiante', 'participante', 'alumno',
            ], true);
            if ($esEncabezado) {
                array_shift($filas);
            }
        }

        $insertados = 0;
        $omitidos   = 0;
        $errores    = [];

        DB::transaction(function () use ($curso, $filas, &$insertados, &$omitidos, &$errores) {
            foreach ($filas as $i => $fila) {
                $celdas = array_values(array_filter((array) $fila, fn ($v) => $v !== null && trim((string) $v) !== ''));
                if (empty($celdas)) {
                    continue;
                }
                $nombre = mb_strtoupper(trim((string) $celdas[0]));
                if ($nombre === '') {
                    continue;
                }
                if (mb_strlen($nombre) > 300) {
                    $errores[] = "Fila " . ($i + 1) . ": nombre demasiado largo (máx. 300 caracteres).";
                    $omitidos++;
                    continue;
                }

                $existe = ParticipanteMigrado::where('curso_id', $curso->id)
                    ->whereRaw('UPPER(nombre_completo) = ?', [$nombre])
                    ->exists();

                if ($existe) {
                    $omitidos++;
                    continue;
                }

                ParticipanteMigrado::create([
                    'curso_id'        => $curso->id,
                    'nombre_completo' => $nombre,
                ]);
                $insertados++;
            }
        });

        return response()->json([
            'insertados' => $insertados,
            'omitidos'   => $omitidos,
            'errores'    => $errores,
        ]);
    }

    public function deleteParticipante(int $id, int $participanteId): JsonResponse
    {
        $participante = ParticipanteMigrado::where('curso_id', $id)->findOrFail($participanteId);
        $participante->delete();

        return response()->json(null, 204);
    }

    public function exportAllPdf(): \Illuminate\Http\Response
    {
        $cursos = CursoMigrado::withCount('participantes')
            ->orderByDesc('id')
            ->get();

        $totalCursos        = $cursos->count();
        $totalParticipantes = $cursos->sum('participantes_count');

        $pdf = Pdf::loadView('pdf.cursos_migrados_listado', compact('cursos', 'totalCursos', 'totalParticipantes'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('cursos-migrados-listado.pdf');
    }

    public function exportAllExcel(): StreamedResponse
    {
        $cursos = CursoMigrado::withCount('participantes')
            ->orderByDesc('id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cursos migrados');

        $encabezados = ['#', 'Nombre del Curso', 'Período', 'Gestión', 'Fecha Inicio', 'Participantes'];
        $columnas    = ['A', 'B', 'C', 'D', 'E', 'F'];

        foreach ($columnas as $idx => $col) {
            $sheet->setCellValue("{$col}1", $encabezados[$idx]);
        }

        $estiloEncabezado = $sheet->getStyle('A1:F1');
        $estiloEncabezado->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $estiloEncabezado->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1A1A2E');
        $estiloEncabezado->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $fila = 2;
        foreach ($cursos as $i => $curso) {
            $sheet->setCellValue("A{$fila}", $i + 1);
            $sheet->setCellValue("B{$fila}", $curso->nombre);
            $sheet->setCellValue("C{$fila}", $curso->periodo ?? '—');
            $sheet->setCellValue("D{$fila}", $curso->gestion ?? '—');
            $sheet->setCellValue("E{$fila}", $curso->fecha_inicio ?? '—');
            $sheet->setCellValue("F{$fila}", $curso->participantes_count ?? 0);
            $fila++;
        }

        $sheet->setCellValue("E{$fila}", 'TOTAL');
        $sheet->setCellValue("F{$fila}", $cursos->sum('participantes_count'));
        $sheet->getStyle("E{$fila}:F{$fila}")->getFont()->setBold(true);
        $sheet->getStyle("E{$fila}:F{$fila}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        foreach ($columnas as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'cursos-migrados-listado.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(int $id): \Illuminate\Http\Response
    {
        $curso = CursoMigrado::withCount('participantes')->findOrFail($id);
        $participantes = $curso->participantes()->orderBy('nombre_completo')->get();

        $pdf = Pdf::loadView('pdf.curso_migrado', compact('curso', 'participantes'))
            ->setPaper('a4', 'portrait');

        $filename = 'participantes-' . $curso->slug . '.pdf';

        return $pdf->download($filename);
    }

    public function exportExcel(int $id): StreamedResponse
    {
        $curso = CursoMigrado::findOrFail($id);
        $participantes = $curso->participantes()->orderBy('nombre_completo')->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Participantes');

        $sheet->setCellValue('A1', 'Curso');
        $sheet->setCellValue('B1', $curso->nombre);
        $sheet->setCellValue('A2', 'Período');
        $sheet->setCellValue('B2', $curso->periodo ?? '—');
        $sheet->setCellValue('A3', 'Gestión');
        $sheet->setCellValue('B3', $curso->gestion ?? '—');
        $sheet->setCellValue('A4', 'URL');
        $sheet->setCellValue('B4', $curso->url);
        $sheet->setCellValue('A5', 'Fecha inicio');
        $sheet->setCellValue('B5', $curso->fecha_inicio ?? '—');
        $sheet->setCellValue('A6', 'Total participantes');
        $sheet->setCellValue('B6', $participantes->count());
        $sheet->getStyle('A1:A6')->getFont()->setBold(true);

        $filaEncabezado = 8;
        $sheet->setCellValue("A{$filaEncabezado}", '#');
        $sheet->setCellValue("B{$filaEncabezado}", 'Nombre completo');

        $estiloEncabezado = $sheet->getStyle("A{$filaEncabezado}:B{$filaEncabezado}");
        $estiloEncabezado->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $estiloEncabezado->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1A1A2E');
        $estiloEncabezado->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $fila = $filaEncabezado + 1;
        foreach ($participantes as $i => $p) {
            $sheet->setCellValue("A{$fila}", $i + 1);
            $sheet->setCellValue("B{$fila}", $p->nombre_completo);
            $fila++;
        }

        foreach (['A', 'B'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'participantes-' . $curso->slug . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
