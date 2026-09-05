export interface Popup {
  id:                       number;
  titulo:                   string | null;
  contenido:                string | null;
  imagen_url:               string | null;
  enlace_url:               string | null;
  enlace_texto:             string | null;
  posicion:                 string | null;
  delay_segundos:           number;
  mostrar_una_vez_sesion:   boolean;
  mostrar_una_vez_siempre:  boolean;
  paginas_mostrar:          string | null;
  activo:                   boolean;
  fecha_inicio:             string | null;
  fecha_fin:                string | null;
  created_at:               string | null;
}

export interface PopupListResponse { data: Popup[]; total: number; }
export interface PopupListParams { pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreatePopupPayload {
  titulo?: string | null; contenido?: string | null; imagen_url?: string | null;
  enlace_url?: string | null; enlace_texto?: string | null; posicion?: string | null;
  delay_segundos?: number; mostrar_una_vez_sesion?: boolean;
  mostrar_una_vez_siempre?: boolean; paginas_mostrar?: string | null;
  activo?: boolean; fecha_inicio?: string | null; fecha_fin?: string | null;
}
