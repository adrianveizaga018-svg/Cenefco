export interface DocumentoTransparencia {
  id:                 number;
  titulo:             string;
  tipo_id?:           number;
  archivo_url?:       string;
  fecha_publicacion?: string;
  estado:             string;
}
export interface DocumentoTransparenciaListResponse {
  data:  DocumentoTransparencia[];
  total: number;
}
export interface DocumentoTransparenciaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
