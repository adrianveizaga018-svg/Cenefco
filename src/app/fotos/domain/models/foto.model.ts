export interface Foto {
  id_foto:         number;
  id_us_reg:       number;
  num_foto:        number;
  titulo_foto:     string;
  descripcion_foto: string | null;
  foto:            string | null;
  fecha_foto:      string | null;
  estado:          number;
}

export interface FotoListResponse { data: Foto[]; total: number; }
export interface FotoListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateFotoPayload {
  id_foto:          number;
  num_foto:         number;
  titulo_foto:      string;
  descripcion_foto?: string | null;
  foto?:            string | null;
  fecha_foto?:      string | null;
  estado?:          number;
}
