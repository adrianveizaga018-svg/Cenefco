export interface Etiqueta {
  id:     number;
  nombre: string;
  slug:   string;
}

export interface EtiquetaListResponse {
  data:  Etiqueta[];
  total: number;
}

export interface EtiquetaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
