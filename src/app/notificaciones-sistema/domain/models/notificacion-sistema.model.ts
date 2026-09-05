export interface NotificacionSistema {
  id:          number;
  usuario_id:  number | null;
  titulo:      string;
  mensaje:     string | null;
  tipo:        string | null;
  url_accion:  string | null;
  leida:       boolean;
  created_at:  string | null;
}

export interface NotificacionSistemaListResponse {
  data:  NotificacionSistema[];
  total: number;
}

export interface NotificacionSistemaListParams {
  pageIndex:   number;
  pageSize:    number;
  query?:      string;
  id_usuario?: number;
  leida?:      number;
  refresh?:    number;
}
