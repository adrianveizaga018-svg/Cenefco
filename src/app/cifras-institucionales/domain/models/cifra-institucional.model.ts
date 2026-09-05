export interface CifraInstitucional {
  id:          number;
  valor:       string;
  etiqueta:    string;
  descripcion: string | null;
  icono:       string | null;
  color:       string | null;
  orden:       number;
  activo:      boolean;
  created_at:  string | null;
}

export interface CifraInstitucionalListResponse { data: CifraInstitucional[]; total: number; }
export interface CifraInstitucionalListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
