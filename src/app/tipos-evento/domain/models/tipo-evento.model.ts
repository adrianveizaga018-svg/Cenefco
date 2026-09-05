export interface TipoEvento {
  id:      number;
  nombre:  string;
  color?:  string;
  icono?:  string;
}
export interface TipoEventoListResponse {
  data:  TipoEvento[];
  total: number;
}
export interface TipoEventoListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
