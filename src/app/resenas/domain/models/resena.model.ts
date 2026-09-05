export interface Resena {
  id:              number;
  programa_id:     number;
  usuario_id:      number | null;
  nombre:          string;
  cargo_actual:    string | null;
  foto_url:        string | null;
  calificacion:    number;
  titulo_resena:   string | null;
  resena:          string;
  estado:          string;
  verificado:      boolean;
  destacada:       boolean;
  motivo_rechazo:  string | null;
  created_at:      string | null;
  nombre_programa: string | null;
  u_nombre:        string | null;
  u_appaterno:     string | null;
  u_apmaterno:     string | null;
  u_email:         string | null;
}

export interface ResenaListResponse { data: Resena[]; total: number; }
export interface ResenaListParams   { query?: string; estado?: string; programa_id?: number; pageIndex?: number; pageSize?: number; refresh?: number; }

export interface EstudianteResena {
  id_us:           number;
  nombre_completo: string;
  email:           string | null;
}
