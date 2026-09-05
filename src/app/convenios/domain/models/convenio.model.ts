export interface Convenio {
  id:                 number;
  nombre:             string;
  institucion:        string | null;
  tipo:               string | null;
  descripcion:        string | null;
  responsable:        string | null;
  contacto_email:     string | null;
  contacto_telefono:  string | null;
  fecha_inicio:       string | null;
  fecha_fin:          string | null;
  documento_url:      string | null;
  logo_url:           string | null;
  estado:             string;
  orden:              number;
  created_at:         string | null;
}

export interface ConvenioDetalle extends Convenio {
  planes: { id_plan: number; titulo: string; anio: string | null; costo: string | null }[];
}

export interface ConvenioOption { id: number; nombre: string; institucion: string | null; logo_url: string | null; }

export interface ConvenioListResponse { data: Convenio[]; total: number; }
export interface ConvenioListParams  { query?: string; pageIndex?: number; pageSize?: number; estado?: string; refresh?: number; }
export interface CreateConvenioPayload {
  nombre:             string;
  institucion?:       string | null;
  tipo?:              string | null;
  descripcion?:       string | null;
  responsable?:       string | null;
  contacto_email?:    string | null;
  contacto_telefono?: string | null;
  fecha_inicio?:      string | null;
  fecha_fin?:         string | null;
  estado?:            string;
  orden?:             number;
}
