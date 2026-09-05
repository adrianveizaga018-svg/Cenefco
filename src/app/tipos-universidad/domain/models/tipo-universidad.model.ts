export interface TipoUniversidad {
  id_tipouniversidad:     number;
  id_us_reg:              number;
  nombre_tipouniversidad: string;
  estado:                 number;
}

export interface TipoUniversidadListResponse { data: TipoUniversidad[]; total: number; }
export interface TipoUniversidadListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateTipoUniversidadPayload {
  id_tipouniversidad:     number;
  nombre_tipouniversidad: string;
  estado?:                number;
}
