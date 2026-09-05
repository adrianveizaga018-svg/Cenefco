export interface Area {
  id:               number;
  titulo:           string;
  slug:             string;
  descripcion:      string | null;
  logo_url:         string | null;
  logo_alt:         string | null;
  galeria:          string[];
  color:            string | null;
  icono:            string | null;
  orden:            number;
  activo:           boolean;
  meta_titulo:      string | null;
  meta_descripcion: string | null;
  created_at:       string | null;
  updated_at:       string | null;
}

export interface AreaListResponse { data: Area[]; total: number; }
export interface AreaListParams   { pageIndex?: number; pageSize?: number; query?: string; }

export interface CreateAreaPayload {
  titulo:            string;
  slug?:             string | null;
  descripcion?:      string | null;
  logo_url?:         string | null;
  logo_alt?:         string | null;
  galeria?:          string[];
  color?:            string | null;
  icono?:            string | null;
  orden?:            number;
  activo?:           boolean;
  meta_titulo?:      string | null;
  meta_descripcion?: string | null;
}
