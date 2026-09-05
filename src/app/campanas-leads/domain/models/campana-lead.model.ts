export type EstadoCampanaLead = 'activa' | 'cerrada';
export type EstadoLead = 'nuevo' | 'contactado' | 'interesado' | 'inscrito' | 'descartado';
export type TipoSeguimiento = 'llamada' | 'whatsapp' | 'correo' | 'reunion' | 'otro';
export type ResultadoSeguimiento = 'no_contesto' | 'contactado' | 'interesado' | 'no_interesado' | 'inscrito';

export interface Lead {
  id: number;
  campana_lead_id: number;
  nombre: string;
  celular: string;
  correo: string | null;
  profesion: string | null;
  estado: EstadoLead;
  vendedor_asignado_id: number | null;
  programa_id: number | null;
  total_seguimientos: number;
  created_at: string | null;
  updated_at: string | null;
}

export interface LeadSeguimiento {
  id: number;
  lead_id: number;
  vendedor_id: number | null;
  vendedor_nombre: string | null;
  tipo: TipoSeguimiento;
  resultado: ResultadoSeguimiento;
  nota: string | null;
  proxima_accion: string | null;
  fecha_proxima_accion: string | null;
  created_at: string | null;
}

export interface CreateSeguimientoPayload {
  tipo: TipoSeguimiento;
  resultado: ResultadoSeguimiento;
  nota?: string | null;
  proxima_accion?: string | null;
  fecha_proxima_accion?: string | null;
}

export interface ActualizarEstadoLeadPayload {
  estado: EstadoLead;
  vendedor_asignado_id?: number | null;
  programa_id?: number | null;
}

export interface CampanaLead {
  id: number;
  nombre: string;
  descripcion: string | null;
  estado: EstadoCampanaLead;
  fecha_inicio: string | null;
  fecha_fin: string | null;
  total_leads: number;
  created_at: string | null;
  updated_at: string | null;
}

export interface CampanaLeadListResponse {
  data: CampanaLead[];
  total: number;
}

export interface LeadListResponse {
  data: Lead[];
  total: number;
}

export interface CreateCampanaLeadPayload {
  nombre: string;
  descripcion?: string | null;
  estado?: EstadoCampanaLead;
  fecha_inicio?: string | null;
  fecha_fin?: string | null;
}

export type UpdateCampanaLeadPayload = Partial<CreateCampanaLeadPayload>;

export interface CreateLeadPayload {
  nombre: string;
  celular: string;
  correo?: string | null;
  profesion?: string | null;
}

export type UpdateLeadPayload = Partial<CreateLeadPayload>;

export interface ImportarLeadsResult {
  insertados: number;
  omitidos: number;
  errores: string[];
}
