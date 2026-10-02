<?php

namespace App\Http\Controllers\Api;

use App\Application\CertPlantillas\Commands\CreateCertPlantillaCommand;
use App\Application\CertPlantillas\Commands\DeleteCertPlantillaCommand;
use App\Application\CertPlantillas\Commands\UpdateCertPlantillaCommand;
use App\Application\CertPlantillas\Handlers\CreateCertPlantillaHandler;
use App\Application\CertPlantillas\Handlers\DeleteCertPlantillaHandler;
use App\Application\CertPlantillas\Handlers\UpdateCertPlantillaHandler;
use App\Application\CertPlantillas\Queries\GetCertPlantillaByIdQuery;
use App\Application\CertPlantillas\Queries\GetCertPlantillasQuery;
use App\Application\CertPlantillas\QueryHandlers\GetCertPlantillaByIdQueryHandler;
use App\Application\CertPlantillas\QueryHandlers\GetCertPlantillasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertPlantillas\StoreCertPlantillaRequest;
use App\Http\Requests\CertPlantillas\UpdateCertPlantillaRequest;
use App\Application\Certificados\Services\CertificadoService;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CertPlantillaController extends Controller
{
    public function __construct(
        private readonly GetCertPlantillasQueryHandler    $listHandler,
        private readonly GetCertPlantillaByIdQueryHandler $showHandler,
        private readonly CreateCertPlantillaHandler       $createHandler,
        private readonly UpdateCertPlantillaHandler       $updateHandler,
        private readonly DeleteCertPlantillaHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetCertPlantillasQuery(
                pagination:  $pagination,
                tipo:        $request->filled('tipo') ? $request->get('tipo') : null,
                soloActivos: $request->boolean('soloActivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetCertPlantillaByIdQuery($id))
        );
    }

    public function store(StoreCertPlantillaRequest $request): JsonResponse
    {
        [$anchoDet, $altoDet] = $this->detectImageDimensions($request->input('imagen_url'));

        $anchoPx = $anchoDet ?? $request->integer('ancho_px', 3508);
        $altoPx  = $altoDet  ?? $request->integer('alto_px',  2480);

        $dto = $this->createHandler->handle(new CreateCertPlantillaCommand(
            nombre:         $request->input('nombre'),
            tipo:           $request->input('tipo', 'aprobacion'),
            imagen_url:     $request->input('imagen_url'),
            ancho_px:       $anchoPx,
            alto_px:        $altoPx,
            orientacion:    $anchoPx >= $altoPx ? 'horizontal' : 'vertical',
            formato_salida: $request->input('formato_salida', 'jpg'),
            calidad_jpg:    $request->integer('calidad_jpg', 95),
            fuente_default: $request->input('fuente_default', 'Arial'),
            color_default:  $request->input('color_default'),
            estado:         $request->input('estado', 'activo'),
            notas:          $request->input('notas'),
            id_us_reg:      $request->integer('id_us_reg', 0),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCertPlantillaRequest $request, int $id): JsonResponse
    {
        $imagenUrl   = $request->input('imagen_url');
        $orientacion = null;
        $anchoPx     = $request->filled('ancho_px') ? $request->integer('ancho_px') : null;
        $altoPx      = $request->filled('alto_px')  ? $request->integer('alto_px')  : null;

        if ($imagenUrl) {
            [$anchoDet, $altoDet] = $this->detectImageDimensions($imagenUrl);
            if ($anchoDet) {
                $anchoPx     = $anchoDet;
                $altoPx      = $altoDet;
                $orientacion = $anchoDet >= $altoDet ? 'horizontal' : 'vertical';
            }
        }

        $dto = $this->updateHandler->handle(new UpdateCertPlantillaCommand(
            id:             $id,
            nombre:         $request->filled('nombre')         ? $request->input('nombre')         : null,
            tipo:           $request->filled('tipo')           ? $request->input('tipo')           : null,
            imagen_url:     $imagenUrl,
            ancho_px:       $anchoPx,
            alto_px:        $altoPx,
            orientacion:    $orientacion ?? ($request->filled('orientacion') ? $request->input('orientacion') : null),
            formato_salida: $request->filled('formato_salida') ? $request->input('formato_salida') : null,
            calidad_jpg:    $request->filled('calidad_jpg')    ? $request->integer('calidad_jpg')  : null,
            fuente_default: $request->filled('fuente_default') ? $request->input('fuente_default') : null,
            color_default:  $request->filled('color_default')  ? $request->input('color_default')  : null,
            estado:         $request->filled('estado')         ? $request->input('estado')         : null,
            notas:          $request->filled('notas')          ? $request->input('notas')          : null,
        ));

        return response()->json($dto);
    }

    public function preview(Request $request, int $id, CertificadoService $service): Response
    {
        $format = in_array($request->get('format'), ['jpg', 'pdf']) ? $request->get('format') : 'jpg';

        try {
            $result = $service->previewPlantilla($id, $format);
        } catch (\RuntimeException $e) {
            abort(422, $e->getMessage());
        }

        $filename = 'preview-plantilla-'.$id.'.'.$result['ext'];

        return response($result['bytes'], 200, [
            'Content-Type'        => $result['mime'],
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control'       => 'no-store',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteCertPlantillaCommand($id));

        return response()->json(null, 204);
    }

    private function detectImageDimensions(string $imagenUrl): array
    {
        try {
            $relative = ltrim(str_replace('/storage/', '', $imagenUrl), '/');
            $path = Storage::disk('public')->path($relative);
            if (file_exists($path)) {
                $info = getimagesize($path);
                return [$info[0], $info[1]];
            }
        } catch (\Throwable) {}
        return [null, null];
    }
}
