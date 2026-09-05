<?php
namespace App\Http\Controllers\Api;

use App\Application\CampanasLeads\Commands\CreateLeadSeguimientoCommand;
use App\Application\CampanasLeads\Commands\UpdateLeadEstadoCommand;
use App\Application\CampanasLeads\Handlers\CreateLeadSeguimientoHandler;
use App\Application\CampanasLeads\Handlers\UpdateLeadEstadoHandler;
use App\Application\CampanasLeads\DTOs\LeadSeguimientoDTO;
use App\Infrastructure\CampanasLeads\Models\Lead;
use App\Infrastructure\CampanasLeads\Models\LeadSeguimiento;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadSeguimientoController extends Controller
{
    public function __construct(
        private readonly CreateLeadSeguimientoHandler $createHandler,
        private readonly UpdateLeadEstadoHandler      $updateEstadoHandler,
    ) {}

    // GET /api/v1/campanas-leads/{campanaLeadId}/leads/{leadId}/seguimientos
    public function index(int $campanaLeadId, int $leadId): JsonResponse
    {
        $lead = Lead::where('campana_lead_id', $campanaLeadId)->findOrFail($leadId);
        $seguimientos = LeadSeguimiento::where('lead_id', $lead->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($s) => LeadSeguimientoDTO::fromModel($s));
        return response()->json($seguimientos);
    }

    // POST /api/v1/campanas-leads/{campanaLeadId}/leads/{leadId}/seguimientos
    public function store(Request $request, int $campanaLeadId, int $leadId): JsonResponse
    {
        $lead = Lead::where('campana_lead_id', $campanaLeadId)->findOrFail($leadId);
        $validated = $request->validate([
            'tipo'                 => ['required', 'string', 'in:llamada,whatsapp,correo,reunion,otro'],
            'resultado'            => ['required', 'string', 'in:no_contesto,contactado,interesado,no_interesado,inscrito'],
            'nota'                 => ['nullable', 'string', 'max:2000'],
            'proxima_accion'       => ['nullable', 'string', 'max:200'],
            'fecha_proxima_accion' => ['nullable', 'date'],
        ]);

        $dto = $this->createHandler->handle(new CreateLeadSeguimientoCommand(
            leadId:        $lead->id,
            vendedorId:    auth()->id(),
            tipo:          $validated['tipo'],
            resultado:     $validated['resultado'],
            nota:          $validated['nota'] ?? null,
            proximaAccion: $validated['proxima_accion'] ?? null,
            fechaProxima:  $validated['fecha_proxima_accion'] ?? null,
        ));

        // Actualizar estado del lead automaticamente segun resultado
        $estadoMap = [
            'inscrito'       => 'inscrito',
            'interesado'     => 'interesado',
            'contactado'     => 'contactado',
            'no_interesado'  => 'descartado',
            'no_contesto'    => null, // no cambia estado
        ];
        $nuevoEstado = $estadoMap[$validated['resultado']] ?? null;
        if ($nuevoEstado) {
            $lead->update(['estado' => $nuevoEstado]);
        }

        return response()->json($dto, 201);
    }

    // PATCH /api/v1/campanas-leads/{campanaLeadId}/leads/{leadId}/estado
    public function actualizarEstado(Request $request, int $campanaLeadId, int $leadId): JsonResponse
    {
        $validated = $request->validate([
            'estado'               => ['required', 'string', 'in:nuevo,contactado,interesado,inscrito,descartado'],
            'vendedor_asignado_id' => ['nullable', 'integer'],
            'programa_id'          => ['nullable', 'integer'],
        ]);
        $dto = $this->updateEstadoHandler->handle(new UpdateLeadEstadoCommand(
            campanaLeadId:      $campanaLeadId,
            id:                 $leadId,
            estado:             $validated['estado'],
            vendedorAsignadoId: $validated['vendedor_asignado_id'] ?? null,
            programaId:         $validated['programa_id'] ?? null,
        ));
        return response()->json($dto);
    }
}
