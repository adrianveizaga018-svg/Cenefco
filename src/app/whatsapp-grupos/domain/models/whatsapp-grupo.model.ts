export interface WhatsappGrupo {
  id:                      number;
  imparte_id:              number;
  nombre:                  string;
  enlace_invitacion:       string;
  capacidad_maxima:        number | null;
  miembros_actuales:       number;
  descripcion:             string | null;
  activo:                  boolean;
  orden:                   number;
  fecha_expiracion_enlace: string | null;
  created_at:              string | null;
}

export interface WhatsappGrupoListResponse { data: WhatsappGrupo[]; total: number; }
export interface WhatsappGrupoListParams   { pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateWhatsappGrupoPayload {
  imparte_id:               number;
  nombre:                   string;
  enlace_invitacion:        string;
  capacidad_maxima?:        number | null;
  miembros_actuales?:       number;
  descripcion?:             string | null;
  activo?:                  boolean;
  orden?:                   number;
  fecha_expiracion_enlace?: string | null;
}
