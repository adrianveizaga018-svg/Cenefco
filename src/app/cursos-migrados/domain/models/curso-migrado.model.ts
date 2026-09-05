export interface LogoMigrado {
  id:         number;
  curso_id:   number;
  path:       string;
  nombre:     string | null;
  orden:      number;
  created_at: string | null;
  updated_at: string | null;
}

export interface CursoMigrado {
  id:             number;
  nombre:         string;
  slug:           string;
  url:            string;
  qr_path:        string | null;
  imagen_path:    string | null;
  periodo:        string | null;
  gestion:        string | null;
  fecha_inicio:   string | null;
  carga_horaria:  number | null;
  created_at:     string | null;
  updated_at:     string | null;
  participantes_count?: number;
  participantes?: ParticipanteMigrado[];
  logos?:         LogoMigrado[];
}

export interface CreateCursoMigradoPayload {
  nombre:         string;
  url:            string;
  periodo?:       string | null;
  gestion?:       string | null;
  fecha_inicio?:  string | null;
  carga_horaria?: number | null;
}

export interface UpdateCursoMigradoPayload {
  carga_horaria?: number | null;
  periodo?:       string | null;
  gestion?:       string | null;
  fecha_inicio?:  string | null;
}

export interface ParticipanteMigrado {
  id:              number;
  curso_id:        number;
  nombre_completo: string;
}

export interface ParticipanteBusqueda extends ParticipanteMigrado {
  curso: CursoMigrado | null;
}

export interface ParticipanteBusquedaListResponse {
  data:  ParticipanteBusqueda[];
  total: number;
}

export interface CursoMigradoListResponse {
  data:  CursoMigrado[];
  total: number;
}

export interface CursoMigradoListParams {
  query?:            string;
  pageIndex?:        number;
  pageSize?:         number;
  mes?:              number;
  participantesMin?: number | null;
  participantesMax?: number | null;
}

export interface CursoMigradoStats {
  total_cursos:        number;
  total_participantes: number;
}

export interface ImportarJsonResult {
  creados:  number;
  omitidos: number;
  errores:  string[];
}

export interface ImportarExcelResult {
  insertados: number;
  omitidos:   number;
  errores:    string[];
}
