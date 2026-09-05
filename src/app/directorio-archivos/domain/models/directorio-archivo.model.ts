export interface CursoDirectorio {
  id_imp:                      number;
  nombre:                      string;
  docente:                     string | null;
  periodo:                     string | null;
  gestion:                     string | null;
  participantes_con_archivos:  number;
}

export interface CursoDirectorioListResponse {
  data:  CursoDirectorio[];
  total: number;
}

export interface ParticipanteDirectorio {
  id_ins:          number;
  id_us:           number;
  nombre_completo: string;
  ci:              string | null;
  email:           string | null;
  fecha_ins:       string | null;
  total_archivos:  number;
}

export interface ParticipanteDirectorioListResponse {
  data:  ParticipanteDirectorio[];
  total: number;
}

export interface ArchivoParticipante {
  id_ins:          number;
  id_us:           number;
  nombre_completo: string;
  ci:              string | null;
  curso_nombre:    string | null;
  archivos:        Record<string, string>;
}

export interface DirectorioListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
}
