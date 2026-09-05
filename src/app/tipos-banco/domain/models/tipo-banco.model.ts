export interface TipoBanco {
  id:         number;
  nombre:     string;
  activo:     boolean;
  orden:      number;
  created_at: string | null;
}

export interface TipoBancoListResponse { data: TipoBanco[]; total: number; }
export interface TipoBancoListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateTipoBancoPayload {
  nombre: string;
  activo?: boolean;
  orden?:  number;
}
