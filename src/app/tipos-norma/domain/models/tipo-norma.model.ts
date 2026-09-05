export interface TipoNorma {
  id:           number;
  nombre:       string;
  descripcion?: string;
}
export interface TipoNormaListResponse {
  data:  TipoNorma[];
  total: number;
}
export interface TipoNormaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
