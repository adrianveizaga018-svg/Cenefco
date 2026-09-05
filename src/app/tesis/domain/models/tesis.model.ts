export interface Tesis {
  id_tesis:          number;
  id_us_reg:         number;
  num_tesis:         number;
  titulo_tesis:      string;
  descripcion_tesis: string | null;
  fecha_publicacion: string | null;
  autor:             string | null;
  tipo_tesis:        number | null;
  archivo:           string | null;
  estado:            number;
  fecha_reg:         string | null;
}

export interface TesisListResponse { data: Tesis[]; total: number; }
export interface TesisListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateTesisPayload {
  id_tesis:           number;
  num_tesis:          number;
  titulo_tesis:       string;
  descripcion_tesis?: string | null;
  fecha_publicacion?: string | null;
  autor?:             string | null;
  tipo_tesis?:        number | null;
  archivo?:           string | null;
  estado?:            number;
}
