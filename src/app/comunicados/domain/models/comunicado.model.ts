export interface Comunicado {
  id:                 number;
  titulo:             string;
  slug:               string;
  contenido?:         string;
  estado:             string;
  fecha_publicacion?: string;
}
export interface ComunicadoListResponse {
  data:  Comunicado[];
  total: number;
}
export interface ComunicadoListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
