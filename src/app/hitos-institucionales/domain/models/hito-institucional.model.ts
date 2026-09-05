export interface HitoInstitucional {
  id:          number;
  anio:        string;
  titulo:      string;
  descripcion: string | null;
  imagen_url:  string | null;
  imagen_alt:  string | null;
  orden:       number;
  activo:      boolean;
  created_at:  string | null;
}

export interface HitoInstitucionalListResponse { data: HitoInstitucional[]; total: number; }
export interface HitoInstitucionalListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
