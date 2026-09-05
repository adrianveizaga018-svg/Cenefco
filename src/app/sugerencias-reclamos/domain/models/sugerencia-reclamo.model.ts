export interface SugerenciaReclamo {
  id:          number;
  tipo:        string;
  nombre?:     string;
  email?:      string;
  mensaje:     string;
  estado:      string;
  respuesta?:  string;
  created_at?: string;
}
export interface SugerenciaReclamoListResponse {
  data:  SugerenciaReclamo[];
  total: number;
}
export interface SugerenciaReclamoListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
