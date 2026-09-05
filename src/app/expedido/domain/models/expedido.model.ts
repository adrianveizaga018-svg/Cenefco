export interface Expedido {
  id:         number;
  nombre:     string;
  orden:      number;
  activo:     boolean;
  created_at: string | null;
}

export interface ExpedidoListResponse { data: Expedido[]; total: number; }
export interface ExpedidoListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
