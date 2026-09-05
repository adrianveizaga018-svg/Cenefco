export interface Permiso {
  id:          number;
  codigo:      string;
  descripcion: string | null;
  modulo:      string | null;
}

export interface PermisoListResponse { data: Permiso[]; total: number; }
export interface PermisoListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreatePermisoPayload {
  codigo:       string;
  descripcion?: string | null;
  modulo?:      string | null;
}
