export interface Autoridad {
  id:                  number;
  nombre:              string;
  apellido:            string;
  cargo:               string;
  perfil_profesional?: string | null;
  foto_url?:           string | null;
  orden:               number;
  activo:              boolean;
  publicado_web:       boolean;
}
export interface AutoridadListResponse {
  data:  Autoridad[];
  total: number;
}
export interface AutoridadListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
