export interface TipoDocumentoTransparencia {
  id:           number;
  nombre:       string;
  descripcion?: string;
}
export interface TipoDocumentoTransparenciaListResponse {
  data:  TipoDocumentoTransparencia[];
  total: number;
}
export interface TipoDocumentoTransparenciaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
