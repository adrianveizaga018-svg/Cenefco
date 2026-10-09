<?php

namespace App\Http\Controllers\Api;

use App\Application\EnviosCertificado\Commands\CreateEnvioCertificadoCommand;
use App\Application\EnviosCertificado\Commands\DeleteEnvioCertificadoCommand;
use App\Application\EnviosCertificado\DTOs\EnvioCertificadoDTO;
use App\Application\EnviosCertificado\Handlers\CreateEnvioCertificadoHandler;
use App\Application\EnviosCertificado\Handlers\DeleteEnvioCertificadoHandler;
use App\Application\EnviosCertificado\Queries\GetEnviosCertificadoQuery;
use App\Application\EnviosCertificado\QueryHandlers\GetEnviosCertificadoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnviosCertificado\StoreEnvioCertificadoRequest;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EnvioCertificadoController extends Controller
{
    /** Los 9 departamentos válidos de Bolivia */
    public const DEPARTAMENTOS = [
        'La Paz', 'Cochabamba', 'Santa Cruz', 'Oruro',
        'Potosí', 'Chuquisaca', 'Tarija', 'Beni', 'Pando',
    ];

    public function __construct(
        private readonly GetEnviosCertificadoQueryHandler $getListHandler,
        private readonly CreateEnvioCertificadoHandler    $createHandler,
        private readonly DeleteEnvioCertificadoHandler    $deleteHandler,
        private readonly VendedorScopeResolver            $vendedorScope,
    ) {}

    // ── 1. Listar por inscripción ──────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $request->validate(['id_ins' => ['required', 'integer']]);
        $idIns = (int) $request->id_ins;

        $this->vendedorScope->assertAccesoInscripcion(auth()->user(), $idIns);

        return response()->json(
            $this->getListHandler->handle(new GetEnviosCertificadoQuery($idIns))
        );
    }

    // ── 2. Dashboard general de envíos ────────────────────────────────────

    public function dashboard(Request $request): JsonResponse
    {
        $q = DB::table('envios_certificado as ec')
            ->join('t_inscripcion as i',  'i.id_ins',  '=', 'ec.id_ins')
            ->join('t_usuario as u',      'u.id_us',   '=', 'i.id_us')
            ->leftJoin(
                DB::raw('(SELECT id_imp, MIN(nombre_programa) as nombre_programa FROM t_programa GROUP BY id_imp) as prog'),
                'prog.id_imp', '=', 'i.id_imp'
            )
            ->select(
                'ec.*',
                DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.appaterno,'')") . ' as estudiante_nombre'),
                'u.ci as estudiante_ci',
                'u.celular as estudiante_celular',
                DB::raw("COALESCE(prog.nombre_programa,'') as programa_nombre"),
            );

        // Filtros
        if ($request->filled('estado'))       $q->where('ec.estado', $request->estado);
        if ($request->filled('departamento')) $q->where('ec.departamento', $request->departamento);
        if ($request->filled('ci'))           $q->where('u.ci', 'like', '%' . $request->ci . '%');
        if ($request->filled('fecha_desde'))  $q->where('ec.fecha_envio', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta'))  $q->where('ec.fecha_envio', '<=', $request->fecha_hasta);

        $resultado = $q->orderByRaw("CASE ec.estado WHEN 'pendiente' THEN 0 WHEN 'enviado' THEN 1 ELSE 2 END")
            ->orderBy('ec.fecha_envio', 'asc')
            ->paginate($request->get('per_page', 25));

        return response()->json($resultado);
    }

    // ── 3. Crear registro de envío (desde inscripción en Caja) ────────────

    public function store(StoreEnvioCertificadoRequest $request): JsonResponse
    {
        $imagenPath = null;
        if ($request->hasFile('imagen_guia')) {
            $imagenPath = $request->file('imagen_guia')->store('envios-certificado', 'public');
        }

        $dto = $this->createHandler->handle(new CreateEnvioCertificadoCommand(
            id_ins:         (int) $request->id_ins,
            departamento:   $request->departamento,
            ciudad_destino: $this->normalizarCiudad($request->ciudad_destino, $request->departamento),
            fecha_envio:    $request->fecha_envio,
            imagen_guia:    $imagenPath ?? '',
            aclaraciones:   $request->aclaraciones,
            costo:          $request->filled('costo') ? (float) $request->costo : null,
            id_us_reg:      auth()->id(),
        ));

        return response()->json($dto, 201);
    }

    // ── 4. Actualizar envío (marcar enviado, agregar agencia, etc.) ────────

    public function update(Request $request, int $id): JsonResponse
    {
        $envio = DB::table('envios_certificado')->where('id', $id)->first();
        if (! $envio) {
            return response()->json(['error' => 'Envío no encontrado.'], 404);
        }

        $data = $request->validate([
            'estado'          => ['sometimes', 'string', 'in:pendiente,enviado,entregado'],
            'agencia'         => ['nullable', 'string', 'max:150'],
            'nro_seguimiento' => ['nullable', 'string', 'max:100'],
            'fecha_entrega'   => ['nullable', 'date'],
            'aclaraciones'    => ['nullable', 'string'],
            'notificado'      => ['sometimes', 'boolean'],
            'ciudad_destino'  => ['sometimes', 'string', 'max:150'],
            'departamento'    => ['sometimes', 'string', 'in:' . implode(',', self::DEPARTAMENTOS)],
        ]);

        // Al marcar como enviado, registrar quién y cuándo
        if (isset($data['estado']) && $data['estado'] === 'enviado' && $envio->estado !== 'enviado') {
            $data['enviado_por'] = auth()->id();
            $data['enviado_at']  = now();
        }

        // Normalizar ciudad si viene
        if (isset($data['ciudad_destino'])) {
            $data['ciudad_destino'] = $this->normalizarCiudad($data['ciudad_destino'], $data['departamento'] ?? $envio->departamento);
        }

        $data['updated_at'] = now();
        DB::table('envios_certificado')->where('id', $id)->update($data);

        $updated = DB::table('envios_certificado')->where('id', $id)->first();
        return response()->json(EnvioCertificadoDTO::fromModel($updated));
    }

    // ── 5. Eliminar ────────────────────────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $envio = DB::table('envios_certificado')->where('id', $id)->first();
        if ($envio && $envio->imagen_guia) {
            Storage::disk('public')->delete($envio->imagen_guia);
        }

        $this->deleteHandler->handle(new DeleteEnvioCertificadoCommand($id));

        return response()->json(null, 204);
    }

    // ── 6. Autocomplete de ciudades/provincias ya registradas ─────────────

    public function autocomplete(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));
        $departamento = $request->get('departamento');

        $q = DB::table('envios_certificado')
            ->selectRaw('ciudad_destino, COUNT(*) as frecuencia')
            ->groupBy('ciudad_destino')
            ->orderByDesc('frecuencia')
            ->limit(10);

        // Sin distinguir mayúsculas (PostgreSQL sí las distingue con LIKE)
        if (mb_strlen($query) >= 2) {
            $q->whereRaw('LOWER(ciudad_destino) LIKE ?', ['%'.mb_strtolower($query, 'UTF-8').'%']);
        }

        // Incluye destinos antiguos guardados sin departamento
        if ($departamento) {
            $q->where(fn ($w) => $w->where('departamento', $departamento)->orWhereNull('departamento')->orWhere('departamento', ''));
        }

        $sugerencias = $q->pluck('ciudad_destino')->values();

        return response()->json($sugerencias);
    }

    // ── Helper: normalizar texto libre para evitar duplicados ─────────────

    /**
     * Si ya existe el mismo destino escrito de otra forma ("yanacachi sud yungas" vs
     * "Yanacachi - Sud Yungas"), se reutiliza el ya registrado para no duplicarlo.
     */
    private function normalizarCiudad(string $ciudad, ?string $departamento = null): string
    {
        $limpia = preg_replace('/\s+/u', ' ', trim($ciudad));
        $clave  = $this->claveCiudad($limpia);

        $existente = DB::table('envios_certificado')
            ->when($departamento, fn ($q) => $q->where(fn ($w) => $w->where('departamento', $departamento)->orWhereNull('departamento')->orWhere('departamento', '')))
            ->distinct()
            ->pluck('ciudad_destino')
            ->first(fn ($c) => $this->claveCiudad((string) $c) === $clave);

        return $existente ?? mb_convert_case($limpia, MB_CASE_TITLE, 'UTF-8');
    }

    /** Clave de comparación: sin mayúsculas, tildes, guiones ni espacios. */
    private function claveCiudad(string $ciudad): string
    {
        $sinTildes = strtr(mb_strtolower($ciudad, 'UTF-8'), ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);

        return preg_replace('/[^a-z0-9]/', '', $sinTildes);
    }
}
