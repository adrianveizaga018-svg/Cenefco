export interface Redireccion {
  id:           number;
  url_origen:   string;
  url_destino:  string;
  codigo_http:  number;
  hits:         number;
  activo:       boolean;
  notas:        string | null;
  created_at:   string | null;
}

export interface RedireccionListResponse { data: Redireccion[]; total: number; }
export interface RedireccionListParams { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateRedireccionPayload {
  url_origen: string; url_destino: string;
  codigo_http?: number; activo?: boolean; notas?: string | null;
}
