<?php

namespace App\Infrastructure\Meta;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaAdsService
{
    private string $accessToken;

    public function __construct()
    {
        $this->accessToken = env('META_ACCESS_TOKEN', '');
    }

    /**
     * Obtiene el gasto acumulado y métricas de una campaña desde Meta.
     */
    public function getCampaignInsights(string $campaignId, ?string $desde = null, ?string $hasta = null): ?array
    {
        if (empty($this->accessToken) || empty($campaignId)) {
            Log::warning("MetaAdsService: Falta token o campaign ID");
            return null;
        }

        try {
            $params = [
                'access_token' => $this->accessToken,
                'fields' => 'spend,clicks,impressions,reach,actions',
            ];

            if ($desde && $hasta) {
                $params['time_range'] = json_encode(['since' => $desde, 'until' => $hasta]);
            } else {
                // Para obtener "todo el tiempo" sin golpear el límite de 37 meses de Meta, 
                // pedimos los últimos 36 meses.
                $hace36meses = now()->subMonths(36)->format('Y-m-d');
                $hoy = now()->format('Y-m-d');
                $params['time_range'] = json_encode(['since' => $hace36meses, 'until' => $hoy]);
            }

            // Documentación de Meta Graph API: https://graph.facebook.com/v19.0/{campaign-id}/insights
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ]
                ])
                ->get("https://graph.facebook.com/v19.0/{$campaignId}/insights", $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['data']) && count($data['data']) > 0) {
                    $insight = $data['data'][0];

                    // Extraer leads/resultados desde las acciones de Meta (formularios y mensajes de clientes potenciales)
                    $leads = 0;
                    if (isset($insight['actions']) && is_array($insight['actions'])) {
                        $formLeads = 0;
                        $msgLeads = 0;
                        
                        foreach ($insight['actions'] as $act) {
                            $type = $act['action_type'] ?? '';
                            $val = (int) ($act['value'] ?? 0);
                            
                            // 1. Leads directos (formularios instantáneos, clientes potenciales web)
                            if (in_array($type, ['lead', 'onsite_conversion.lead_grouped', 'onsite_web_lead', 'offsite_contact_website_add_meta_leads', 'leadgen_grouped'])) {
                                if ($val > $formLeads) $formLeads = $val;
                            }
                            // 2. Mensajes/conversaciones iniciadas (WhatsApp/Messenger)
                            if ($type === 'onsite_conversion.messaging_conversation_started_7d') {
                                if ($val > $msgLeads) $msgLeads = $val;
                            }
                        }

                        // El total de "Leads Meta" será la suma de los formularios nativos + las conversaciones de mensajes
                        $leads = $formLeads + $msgLeads;
                    }

                    return [
                        'spend' => (float) ($insight['spend'] ?? 0),
                        'clicks' => (int) ($insight['clicks'] ?? 0),
                        'impressions' => (int) ($insight['impressions'] ?? 0),
                        'reach' => (int) ($insight['reach'] ?? 0),
                        'leads' => $leads > 0 ? $leads : null,
                    ];
                }

                // Campaña sin datos aún (no ha gastado nada)
                return [
                    'spend' => 0.0,
                    'clicks' => 0,
                    'impressions' => 0,
                ];
            }

            Log::error("MetaAdsService: Error al consultar API", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error("MetaAdsService: Excepción al consultar API", ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Obtiene el estado actual de todas las campañas de una cuenta.
     */
    public function getAccountCampaignStatuses(string $accountId): array
    {
        if (empty($this->accessToken) || empty($accountId)) {
            return [];
        }

        if (!str_starts_with($accountId, 'act_')) {
            $accountId = 'act_' . $accountId;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                ->get("https://graph.facebook.com/v19.0/{$accountId}/campaigns", [
                    'access_token' => $this->accessToken,
                    'fields' => 'id,status',
                    'limit' => 500,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $statuses = [];
                if (isset($data['data'])) {
                    foreach ($data['data'] as $camp) {
                        // Mapear el status de Meta (ACTIVE, PAUSED, DELETED, ARCHIVED) al nuestro
                        $metaStatus = $camp['status'] ?? '';
                        $nuestro = 'planificada';
                        if ($metaStatus === 'ACTIVE') $nuestro = 'activa';
                        elseif ($metaStatus === 'PAUSED') $nuestro = 'pausada';
                        elseif (in_array($metaStatus, ['DELETED', 'ARCHIVED'])) $nuestro = 'finalizada';
                        
                        $statuses[$camp['id']] = $nuestro;
                    }
                }
                return $statuses;
            }
        } catch (\Exception $e) {
            Log::error("MetaAdsService: Error al consultar statuses", ['err' => $e->getMessage()]);
        }
        return [];
    }

    /**
     * Obtiene insights de TODAS las campañas de una cuenta publicitaria en UNA SOLA petición.
     * Ideal para consultas "en vivo" desde el dashboard.
     */
    public function getAccountLiveInsights(string $accountId, ?string $desde = null, ?string $hasta = null): array
    {
        if (empty($this->accessToken) || empty($accountId)) {
            return [];
        }

        // Asegurarse de que el accountId tenga el prefijo act_
        if (!str_starts_with($accountId, 'act_')) {
            $accountId = 'act_' . $accountId;
        }

        try {
            $params = [
                'access_token' => $this->accessToken,
                'level' => 'campaign',
                'fields' => 'campaign_id,spend,clicks,impressions,reach,actions',
                'limit' => 500, // Traer hasta 500 campañas de golpe
            ];

            if ($desde && $hasta) {
                $params['time_range'] = json_encode(['since' => $desde, 'until' => $hasta]);
            } else {
                $hace36meses = now()->subMonths(36)->format('Y-m-d');
                $hoy = now()->format('Y-m-d');
                $params['time_range'] = json_encode(['since' => $hace36meses, 'until' => $hoy]);
            }

            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                ->get("https://graph.facebook.com/v19.0/{$accountId}/insights", $params);

            if ($response->successful()) {
                $data = $response->json();
                $results = [];

                if (isset($data['data'])) {
                    foreach ($data['data'] as $insight) {
                        $campaignId = $insight['campaign_id'] ?? '';
                        if (!$campaignId) continue;

                        $leads = 0;
                        if (isset($insight['actions']) && is_array($insight['actions'])) {
                            $formLeads = 0;
                            $msgLeads = 0;
                            
                            foreach ($insight['actions'] as $act) {
                                $type = $act['action_type'] ?? '';
                                $val = (int) ($act['value'] ?? 0);
                                
                                if (in_array($type, ['lead', 'onsite_conversion.lead_grouped', 'onsite_web_lead', 'offsite_contact_website_add_meta_leads', 'leadgen_grouped'])) {
                                    if ($val > $formLeads) $formLeads = $val;
                                }
                                
                                if ($type === 'onsite_conversion.messaging_conversation_started_7d') {
                                    if ($val > $msgLeads) $msgLeads = $val;
                                }
                            }
                            
                            // El total de "Leads Meta" será la suma de los formularios nativos + las conversaciones de mensajes
                            $leads = $formLeads + $msgLeads;
                        }

                        $results[$campaignId] = [
                            'spend' => (float) ($insight['spend'] ?? 0),
                            'clicks' => (int) ($insight['clicks'] ?? 0),
                            'impressions' => (int) ($insight['impressions'] ?? 0),
                            'reach' => (int) ($insight['reach'] ?? 0),
                            'leads' => $leads > 0 ? $leads : null,
                        ];
                    }
                }
                return $results;
            }
        } catch (\Exception $e) {
            Log::error("MetaAdsService: Excepción en live insights de cuenta", ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Obtiene todas las campañas de una cuenta publicitaria.
     */
    public function getAccountCampaigns(string $accountId): ?array
    {
        if (empty($this->accessToken) || empty($accountId)) {
            Log::warning("MetaAdsService: Falta token o account ID");
            return null;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ]
                ])
                ->get("https://graph.facebook.com/v19.0/{$accountId}/campaigns", [
                'access_token' => $this->accessToken,
                'fields' => 'id,name,status,start_time,stop_time,objective',
                'limit' => 100 // Límite por página (simplificado para la importación inicial)
            ]);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }

            Log::error("MetaAdsService: Error al consultar campañas de cuenta", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error("MetaAdsService: Excepción al consultar campañas de cuenta", ['error' => $e->getMessage()]);
        }

        return null;
    }
}