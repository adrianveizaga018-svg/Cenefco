export interface Nivel {
  id_niv:   number;
  id_us_reg: number;
  titulo:   string;
  estado:   number;
}

export interface NivelListResponse { data: Nivel[]; total: number; }
export interface NivelListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateNivelPayload {
  id_niv:  number;
  titulo:  string;
  estado?: number;
}
