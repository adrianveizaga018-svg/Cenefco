export interface DocentePerfil {
  id:               number;
  usuario_id:       number | null;
  nombre_completo:  string;
  slug:             string | null;
  titulo_academico: string | null;
  especialidad:     string | null;
  biografia:        string | null;
  foto_url:         string | null;
  foto_alt:         string | null;
  email_publico:    string | null;
  telefono:         string | null;
  linkedin_url:     string | null;
  twitter_url:      string | null;
  sitio_web_url:    string | null;
  tipo:             string | null;
  mostrar_en_web:   boolean;
  orden:            number;
  estado:           string;
  created_at:       string | null;
}

export interface DocenteReporteMaterias {
  id:              number;
  nombre_completo: string;
  titulo_academico: string | null;
  especialidad:    string | null;
  telefono:        string | null;
  email_publico:   string | null;
  tipo:            string | null;
  estado:          string;
  total_materias:  number;
}

export interface DocenteReporteMateriasResponse { data: DocenteReporteMaterias[]; }

export interface DocentePerfilListResponse { data: DocentePerfil[]; total: number; }
export interface DocentePerfilListParams { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateDocentePerfilPayload {
  nombre_completo: string; titulo_academico?: string | null;
  especialidad?: string | null; biografia?: string | null;
  foto_url?: string | null; foto_alt?: string | null;
  email_publico?: string | null; telefono?: string | null;
  linkedin_url?: string | null;
  twitter_url?: string | null; sitio_web_url?: string | null;
  tipo?: string | null; mostrar_en_web?: boolean; orden?: number; estado?: string;
}
