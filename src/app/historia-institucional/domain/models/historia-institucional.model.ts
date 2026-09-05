export interface HistoriaInstitucional {
  id:          number;
  titulo:      string;
  contenido?:  string;
  imagen_url?: string;
  orden:       number;
  activo:      boolean;
}
export interface HistoriaInstitucionalListResponse {
  data:  HistoriaInstitucional[];
  total: number;
}
export interface HistoriaInstitucionalListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
