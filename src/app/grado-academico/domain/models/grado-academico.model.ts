export interface GradoAcademico {
  id:               number;
  nombre:           string;
  abreviatura:      string;
  requiere_titulo:  boolean;
  orden:            number;
  activo:           boolean;
  created_at:       string | null;
}

export interface GradoAcademicoListResponse { data: GradoAcademico[]; total: number; }
export interface GradoAcademicoListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
