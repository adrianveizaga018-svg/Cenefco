export interface Secretaria {
  id:           number;
  nombre:       string;
  slug:         string;
  descripcion?: string;
  responsable?: string;
  foto_url?:    string;
  orden:        number;
  activo:       boolean;
}
export interface SecretariaListResponse {
  data:  Secretaria[];
  total: number;
}
export interface SecretariaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
