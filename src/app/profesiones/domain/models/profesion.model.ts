export interface Profesion {
  id_prof:        number;
  id_us_reg:      number;
  nombre_profesion: string;
  estado:         number;
  fecha_reg:      string | null;
}

export interface ProfesionListResponse { data: Profesion[]; total: number; }
export interface ProfesionListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateProfesionPayload {
  id_prof:        number;
  nombre_profesion: string;
  estado?:        number;
}
