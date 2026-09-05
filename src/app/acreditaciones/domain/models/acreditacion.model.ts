export interface Acreditacion {
  id:                 number;
  nombre:             string;
  entidad_otorgante:  string;
  tipo:               string | null;
  descripcion:        string | null;
  logo_url:           string | null;
  logo_alt:           string | null;
  url_verificacion:   string | null;
  fecha_obtencion:    string | null;
  fecha_vencimiento:  string | null;
  orden:              number;
  activo:             boolean;
  created_at:         string | null;
}

export interface AcreditacionListResponse { data: Acreditacion[]; total: number; }
export interface AcreditacionListParams { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateAcreditacionPayload {
  nombre: string; entidad_otorgante: string; tipo?: string | null;
  descripcion?: string | null; logo_url?: string | null; logo_alt?: string | null;
  url_verificacion?: string | null; fecha_obtencion?: string | null;
  fecha_vencimiento?: string | null; orden?: number; activo?: boolean;
}
