export interface Organigrama {
  id:          number;
  titulo:      string;
  imagen_url?: string;
  orden:       number;
  activo:      boolean;
}
export interface OrganigramaListResponse {
  data:  Organigrama[];
  total: number;
}
export interface OrganigramaListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
