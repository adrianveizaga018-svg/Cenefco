export interface ManualInstitucional {
  id:           number;
  titulo:       string;
  archivo_url?: string;
  descripcion?: string;
  orden:        number;
  activo:       boolean;
}
export interface ManualInstitucionalListResponse {
  data:  ManualInstitucional[];
  total: number;
}
export interface ManualInstitucionalListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
