export interface Aliado {
  id:          number;
  nombre:      string;
  logo_url:    string;
  logo_alt:    string | null;
  url_sitio:   string | null;
  descripcion: string | null;
  tipo:        string | null;
  orden:       number;
  activo:      boolean;
  created_at:  string | null;
}

export interface AliadoListResponse {
  data:  Aliado[];
  total: number;
}

export interface AliadoListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
  refresh?:   number;
}

export interface CreateAliadoPayload {
  nombre:      string;
  logo_url:    string;
  logo_alt?:   string | null;
  url_sitio?:  string | null;
  descripcion?: string | null;
  tipo?:       string | null;
  orden?:      number;
  activo?:     boolean;
}
