export interface Suscriptor {
  id:          number;
  email:       string;
  nombre:      string | null;
  origen:      string | null;
  confirmado:  boolean;
  activo:      boolean;
  created_at:  string | null;
}

export interface SuscriptorListResponse { data: Suscriptor[]; total: number; }
export interface SuscriptorListParams { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
