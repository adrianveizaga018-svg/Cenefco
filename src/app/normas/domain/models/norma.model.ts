export interface Norma {
  id:                 number;
  titulo:             string;
  numero?:            string;
  tipo_norma_id?:     number;
  archivo_url?:       string;
  fecha_publicacion?: string;
  estado:             string;
}
export interface NormaListResponse {
  data:  Norma[];
  total: number;
}
export interface NormaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
