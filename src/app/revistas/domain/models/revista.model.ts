export interface Revista {
  id_revista:          number;
  id_us_reg:           number;
  num_revista:         number;
  titulo_revista:      string;
  descripcion_revista: string | null;
  fecha_publicacion:   string | null;
  archivo:             string | null;
  estado:              number;
  fecha_reg:           string | null;
}

export interface RevistaListResponse { data: Revista[]; total: number; }
export interface RevistaListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateRevistaPayload {
  id_revista:           number;
  num_revista:          number;
  titulo_revista:       string;
  descripcion_revista?: string | null;
  fecha_publicacion?:   string | null;
  archivo?:             string | null;
  estado?:              number;
}
