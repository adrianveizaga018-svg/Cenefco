export type PropositoCampana = 'curso' | 'institucional' | 'evento' | 'reclutamiento' | 'otro';
export type PlataformaCampana = 'meta_ads' | 'google_ads' | 'tiktok_ads' | 'otro';
export type EstadoCampana = 'planificada' | 'activa' | 'pausada' | 'finalizada' | 'cancelada';
export type FuenteMetrica = 'manual' | 'meta_ads_manager' | 'google_ads' | 'tiktok_ads';

export interface CampanaMetrica {
  id: number;
  campana_publicidad_id: number;
  fecha_corte: string;
  alcance: number | null;
  impresiones: number | null;
  frecuencia: number | null;
  clics_enlace: number | null;
  ctr: number | null;
  cpc: number | null;
  cpm: number | null;
  resultados: number | null;
  tipo_resultado: string | null;
  costo_por_resultado: number | null;
  gasto_periodo: number | null;
  fuente: FuenteMetrica;
  notas: string | null;
  created_at: string | null;
}

export interface CampanaPublicidad {
  id: number;
  programa_id: number | null;
  programa_nombre: string | null;
  proposito: PropositoCampana;
  nombre: string;
  plataforma: PlataformaCampana;
  objetivo: string | null;
  fecha_inicio: string;
  fecha_fin: string | null;
  estado: EstadoCampana;
  leads: number | null;
  presupuesto_usd: number | null;
  presupuesto_bob: number | null;
  id_campana_externa: string | null;
  responsable: string | null;
  notas: string | null;
  total_gastado: number;
  created_at: string | null;
  updated_at: string | null;
  metricas: CampanaMetrica[];
}

export interface CampanaPublicidadListResponse {
  data: CampanaPublicidad[];
  total: number;
}

export interface CreateCampanaPublicidadPayload {
  nombre: string;
  plataforma: PlataformaCampana;
  fecha_inicio: string;
  programa_id?: number | null;
  proposito?: PropositoCampana;
  objetivo?: string | null;
  fecha_fin?: string | null;
  estado?: EstadoCampana;
  leads?: number | null;
  presupuesto_usd?: number | null;
  presupuesto_bob?: number | null;
  id_campana_externa?: string | null;
  responsable?: string | null;
  notas?: string | null;
}

export type UpdateCampanaPublicidadPayload = Partial<CreateCampanaPublicidadPayload>;

export interface RegistrarMetricaPayload {
  fecha_corte: string;
  alcance?: number | null;
  impresiones?: number | null;
  frecuencia?: number | null;
  clics_enlace?: number | null;
  ctr?: number | null;
  cpc?: number | null;
  cpm?: number | null;
  resultados?: number | null;
  tipo_resultado?: string | null;
  costo_por_resultado?: number | null;
  gasto_periodo?: number | null;
  fuente?: FuenteMetrica;
  notas?: string | null;
}

export interface ReporteCampana {
  programa_id: number | null;
  programa_nombre: string;
  total_invertido: number;
  total_alcance: number;
  total_resultados: number;
  costo_por_resultado_promedio: number | null;
  total_inscritos_curso: number | null;
  total_recaudado_curso: number | null;
  retorno_aproximado: number | null;
}
