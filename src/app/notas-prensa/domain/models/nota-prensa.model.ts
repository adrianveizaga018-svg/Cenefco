export interface NotaPrensa {
  id:                 number;
  titulo:             string;
  medio:              string;
  logo_medio_url:     string | null;
  logo_medio_alt:     string | null;
  resumen:            string | null;
  url_noticia:        string | null;
  fecha_publicacion:  string;
  destacada:          boolean;
  orden:              number;
  activo:             boolean;
  created_at:         string | null;
}

export interface NotaPrensaListResponse { data: NotaPrensa[]; total: number; }
export interface NotaPrensaListParams { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateNotaPrensaPayload {
  titulo: string; medio: string; logo_medio_url?: string | null;
  logo_medio_alt?: string | null; resumen?: string | null;
  url_noticia?: string | null; fecha_publicacion: string;
  destacada?: boolean; orden?: number; activo?: boolean;
}
