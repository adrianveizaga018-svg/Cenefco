export interface DirectorioInstitucional {
  id:        number;
  nombre:    string;
  cargo:     string;
  email?:    string;
  telefono?: string;
  area?:     string;
  foto_url?: string;
  orden:     number;
}
export interface DirectorioInstitucionalListResponse {
  data:  DirectorioInstitucional[];
  total: number;
}
export interface DirectorioInstitucionalListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
