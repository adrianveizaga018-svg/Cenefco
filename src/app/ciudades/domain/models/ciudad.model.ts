export interface Ciudad {
  id_ciudad:    number;
  id_us_reg:    number;
  nombre_ciudad: string;
  estado:       number;
  fecha_reg:    string | null;
}

export interface CiudadListResponse { data: Ciudad[]; total: number; }
export interface CiudadListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateCiudadPayload {
  id_ciudad:    number;
  nombre_ciudad: string;
  estado?:      number;
}
