<?php

namespace App\Http\Controllers\Api;

use App\Application\Crm\Services\CrmService;
use App\Application\Crm\Services\ImportarContactosCrmService;
use App\Application\Crm\Services\TableroCrmService;
use App\Http\Controllers\Controller;
use App\Infrastructure\CampanasLeads\Models\Lead;
use App\Shared\Kernel\Support\SqlCompat;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Embudo de ventas. Un vendedor (crm.ver) trabaja solo sus interesados;
 * quien supervisa (crm.supervisar) ve a todos, reasigna y ve el tablero completo.
 */
class CrmController extends Controller
{
    public function __construct(
        private readonly CrmService $crm,
        private readonly TableroCrmService $tablero,
    ) {}

    public function leads(Request $request): JsonResponse
    {
        $soloMio = $this->soloVendedor($request);
        $limite = now()->subHours(CrmService::HORAS_SIN_ATENCION);

        $leads = $this->consultaLeads()
            ->when($soloMio, fn ($q) => $q->where('l.vendedor_asignado_id', $soloMio))
            ->when(! $soloMio && $request->filled('vendedor_id'), fn ($q) => $request->vendedor_id === 'sin'
                ? $q->whereNull('l.vendedor_asignado_id')
                : $q->where('l.vendedor_asignado_id', (int) $request->vendedor_id))
            ->when($request->filled('origen'), fn ($q) => $q->where('l.origen', $request->origen))
            ->when($request->filled('q'), function ($q) use ($request) {
                $t = '%'.mb_strtolower(trim((string) $request->q)).'%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(l.nombre) LIKE ?', [$t])->orWhere('l.celular', 'like', $t)
                    ->orWhereRaw('LOWER(l.correo) LIKE ?', [$t])->orWhere('l.ci', 'like', $t));
            })
            // Los cerrados solo de los últimos 30 días para no llenar el tablero
            ->where(fn ($q) => $q->whereIn('l.estado', CrmService::ABIERTAS)->orWhere('l.cerrado_at', '>=', now()->subDays(30)))
            ->orderByRaw('CASE WHEN l.proxima_accion_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('l.proxima_accion_at')
            ->orderByDesc('l.id')
            ->limit(600)
            ->get()
            ->map(function ($l) use ($limite) {
                $ref = $l->ultimo_contacto_at ?? $l->created_at;
                $l->sin_atender = in_array($l->estado, ['nuevo', 'contactado', 'interesado'], true) && $ref && Carbon::parse($ref)->lt($limite);
                $l->horas_sin_contacto = $ref ? (int) Carbon::parse($ref)->diffInHours(now(), true) : null;

                return $l;
            });

        return response()->json(['data' => $leads, 'etapas' => CrmService::ETAPAS]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $lead = $this->consultaLeads()->where('l.id', $id)->first();
        abort_unless($lead && $this->puedeVer($request, $lead->vendedor_asignado_id), 404, 'Interesado no encontrado.');

        $seguimientos = DB::table('lead_seguimientos as s')
            ->leftJoin('usuarios as u', 'u.id', '=', 's.vendedor_id')
            ->where('s.lead_id', $id)->orderByDesc('s.created_at')
            ->get(['s.id', 's.tipo', 's.resultado', 's.nota', 's.proxima_accion', 's.fecha_proxima_accion', 's.created_at',
                DB::raw(SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.apellido,'')").' as autor')]);

        $inscripciones = $lead->id_us
            ? DB::table('t_inscripcion as i')
                ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'i.id_imp')
                ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'imp.id_mat')
                ->where('i.id_us', $lead->id_us)->orderByDesc('i.id_ins')
                ->get(['i.id_ins', 'i.estado', 'i.fecha_ins', DB::raw('COALESCE(prog.nombre_programa, imp.titulo_personalizado) as programa')])
            : collect();

        return response()->json(['lead' => $lead, 'seguimientos' => $seguimientos, 'inscripciones' => $inscripciones]);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:150',
            'celular'     => 'nullable|string|max:30',
            'correo'      => 'nullable|email|max:150',
            'ci'          => 'nullable|string|max:30',
            'programa_id' => 'nullable|integer',
            'origen'      => 'nullable|in:'.implode(',', CrmService::ORIGENES),
            'nota'        => 'nullable|string|max:2000',
            'vendedor_id' => 'nullable|integer',
        ]);
        // El vendedor que lo carga se lo queda; el supervisor puede elegir o dejar el reparto por turnos
        $datos['vendedor_id'] = $this->soloVendedor($request) ?? ($datos['vendedor_id'] ?? null);

        try {
            $lead = $this->crm->registrar($datos, $datos['origen'] ?? 'manual');
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Interesado registrado.', 'id' => $lead->id], 201);
    }

    public function contacto(Request $request, int $id): JsonResponse
    {
        $lead = $this->leadPermitido($request, $id);
        $datos = $request->validate([
            'tipo'              => 'required|in:llamada,whatsapp,correo,reunion,otro',
            'resultado'         => 'required|in:no_contesto,contactado,interesado,no_interesado',
            'nota'              => 'nullable|string|max:2000',
            'proxima_accion'    => 'nullable|string|max:200',
            'proxima_accion_at' => 'nullable|date|after_or_equal:today',
        ]);

        $this->crm->anotar($lead, $datos['tipo'], $datos['resultado'], $datos['nota'] ?? null, $datos['proxima_accion_at'] ?? null, true, $datos['proxima_accion'] ?? null);

        return response()->json(['message' => 'Contacto registrado.', 'estado' => $lead->fresh()->estado]);
    }

    public function etapa(Request $request, int $id): JsonResponse
    {
        $lead = $this->leadPermitido($request, $id);
        $datos = $request->validate(['etapa' => 'required|string', 'motivo' => 'nullable|string|max:200']);

        try {
            $this->crm->moverEtapa($lead, $datos['etapa'], $datos['motivo'] ?? null);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Etapa actualizada.']);
    }

    public function asignar(Request $request, int $id): JsonResponse
    {
        $lead = Lead::findOrFail($id);
        $datos = $request->validate(['vendedor_id' => 'required|integer']);

        try {
            $this->crm->asignar($lead, (int) $datos['vendedor_id']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Interesado reasignado.']);
    }

    /** El mismo contacto a varios interesados (p. ej. "les envié el flyer por WhatsApp"). */
    public function contactoMasivo(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'ids'       => 'required|array|min:1|max:200',
            'ids.*'     => 'integer',
            'tipo'      => 'required|in:llamada,whatsapp,correo,reunion,otro',
            'resultado' => 'required|in:no_contesto,contactado,interesado,no_interesado',
            'nota'      => 'nullable|string|max:2000',
        ]);
        $soloMio = $this->soloVendedor($request);
        $leads = Lead::whereIn('id', $datos['ids'])->whereIn('estado', CrmService::ABIERTAS)
            ->when($soloMio, fn ($q) => $q->where('vendedor_asignado_id', $soloMio))->get();

        foreach ($leads as $lead) {
            $this->crm->anotar($lead, $datos['tipo'], $datos['resultado'], $datos['nota'] ?? null, null);
        }

        return response()->json(['message' => "Contacto registrado a {$leads->count()} interesado(s).", 'registrados' => $leads->count()]);
    }

    public function asignarMasivo(Request $request): JsonResponse
    {
        $datos = $request->validate(['ids' => 'required|array|min:1|max:500', 'ids.*' => 'integer', 'vendedor_id' => 'required|integer']);

        try {
            $leads = Lead::whereIn('id', $datos['ids'])->get();
            foreach ($leads as $lead) {
                $this->crm->asignar($lead, (int) $datos['vendedor_id']);
            }
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => "{$leads->count()} interesado(s) reasignados."]);
    }

    /** Lo que el vendedor tiene que hacer hoy: próximas acciones vencidas o de hoy. */
    public function tareas(Request $request): JsonResponse
    {
        $soloMio = $this->soloVendedor($request);
        $finDeHoy = now()->endOfDay();

        $tareas = $this->consultaLeads()
            ->whereIn('l.estado', CrmService::ABIERTAS)
            ->where('l.proxima_accion_at', '<=', $finDeHoy)
            ->when($soloMio, fn ($q) => $q->where('l.vendedor_asignado_id', $soloMio))
            ->when(! $soloMio && $request->filled('vendedor_id'), fn ($q) => $q->where('l.vendedor_asignado_id', (int) $request->vendedor_id))
            ->orderBy('l.proxima_accion_at')
            ->limit(300)
            ->get()
            ->map(function ($l) {
                $l->vencida = Carbon::parse($l->proxima_accion_at)->lt(now()->startOfDay());
                $l->proxima_accion = DB::table('lead_seguimientos')->where('lead_id', $l->id)->whereNotNull('proxima_accion')->orderByDesc('id')->value('proxima_accion');
                $l->intentos_sin_respuesta = DB::table('lead_seguimientos')->where('lead_id', $l->id)->where('resultado', 'no_contesto')->count();

                return $l;
            });

        return response()->json(['data' => $tareas, 'vencidas' => $tareas->where('vencida', true)->count()]);
    }

    public function importar(Request $request, ImportarContactosCrmService $importador): JsonResponse
    {
        $datos = $request->validate([
            'archivo'               => 'required|file|max:5120|mimes:xlsx,xls,csv,txt',
            'origen'                => 'required|in:excel,whatsapp_grupo,manual',
            'vendedor_id'           => 'nullable|integer',
            'programa_id'           => 'nullable|integer',
            'campana_publicidad_id' => 'nullable|integer',
            'nota'                  => 'nullable|string|max:300',
        ]);
        $archivo = $request->file('archivo');

        try {
            $res = $importador->importar($archivo->getRealPath(), $archivo->getClientOriginalExtension(), $datos);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo leer el archivo. Guárdalo como Excel (.xlsx) o CSV e inténtalo de nuevo.'], 422);
        }

        $nombres = DB::table('vendedores')->whereIn('id', array_filter(array_keys($res['por_vendedor']), 'is_numeric'))
            ->get(['id', 'nombre', 'apellido'])->mapWithKeys(fn ($v) => [(string) $v->id => trim($v->nombre.' '.$v->apellido)]);
        $res['por_vendedor'] = collect($res['por_vendedor'])
            ->map(fn ($n, $id) => ['vendedor' => $nombres[$id] ?? 'Sin asignar', 'cantidad' => $n])->values();

        return response()->json($res);
    }

    public function plantilla()
    {
        return response(ImportarContactosCrmService::plantillaCsv(), 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla-contactos-cenefco.csv"',
        ]);
    }

    public function tablero(Request $request): JsonResponse
    {
        $desde = $request->filled('desde') ? Carbon::parse($request->desde)->startOfDay() : now()->startOfMonth();
        $hasta = $request->filled('hasta') ? Carbon::parse($request->hasta) : now();

        return response()->json($this->tablero->resumen($desde, $hasta, $this->soloVendedor($request)));
    }

    public function vendedores(): JsonResponse
    {
        return response()->json(DB::table('vendedores')->where('activo', true)->whereNull('deleted_at')->orderBy('nombre')
            ->get(['id', DB::raw(SqlCompat::trimConcat("COALESCE(nombre,'')", "' '", "COALESCE(apellido,'')").' as nombre'), 'meta_ventas']));
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function consultaLeads()
    {
        return DB::table('leads as l')
            ->leftJoin('vendedores as v', 'v.id', '=', 'l.vendedor_asignado_id')
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 'l.programa_id')
            ->whereNull('l.deleted_at')
            ->select([
                'l.id', 'l.nombre', 'l.celular', 'l.correo', 'l.ci', 'l.estado', 'l.origen', 'l.motivo_perdida',
                'l.vendedor_asignado_id', 'l.programa_id', 'l.id_us', 'l.id_ins', 'l.created_at', 'l.asignado_at',
                'l.ultimo_contacto_at', 'l.proxima_accion_at', 'l.cerrado_at',
                'prog.nombre_programa as programa',
                DB::raw(SqlCompat::trimConcat("COALESCE(v.nombre,'')", "' '", "COALESCE(v.apellido,'')").' as vendedor'),
            ]);
    }

    /** Id del vendedor si el usuario solo puede ver lo suyo; null si supervisa. */
    private function soloVendedor(Request $request): ?int
    {
        $user = $request->user();
        if ($user->tienePermiso('crm.supervisar')) {
            return null;
        }
        $id = DB::table('vendedores')->where('usuario_id', $user->id)->whereNull('deleted_at')->value('id');
        abort_unless($id, 403, 'Tu cuenta no está vinculada a un vendedor. Pide que te vinculen en Vendedores.');

        return (int) $id;
    }

    private function puedeVer(Request $request, ?int $vendedorId): bool
    {
        $mio = $this->soloVendedor($request);

        return $mio === null || $mio === (int) $vendedorId;
    }

    private function leadPermitido(Request $request, int $id): Lead
    {
        $lead = Lead::findOrFail($id);
        abort_unless($this->puedeVer($request, $lead->vendedor_asignado_id), 404, 'Interesado no encontrado.');

        return $lead;
    }
}
