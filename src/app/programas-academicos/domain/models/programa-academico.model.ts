export interface ProgramaAcademico {
  id_programa:              number;
  id_us_reg:                number;
  num_programa:             number;
  nombre_programa:          string;
  descripcion:              string | null;
  foto:                     string | null;
  inicio_actividades:       string | null;
  finalizacion_actividades: string | null;
  inicio_inscripciones:     string | null;
  dirigido:                 string | null;
  inversion:                string | null;
  requisitos:               string | null;
  creditaje:                string | null;
  objetivo:                 string | null;
  nota:                     string | null;
  id_tipoprograma:          number | null;
  url_video:                string | null;
  estado:                   number;
  fecha_reg:                string | null;
}
export interface ProgramaAcademicoListResponse { data: ProgramaAcademico[]; total: number; }
export interface ProgramaAcademicoListParams   { query?: string; id_tipoprograma?: number; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateProgramaAcademicoPayload {
  id_programa:               number;
  nombre_programa:           string;
  descripcion?:              string | null;
  foto?:                     string | null;
  inicio_actividades?:       string | null;
  finalizacion_actividades?: string | null;
  inicio_inscripciones?:     string | null;
  dirigido?:                 string | null;
  inversion?:                string | null;
  requisitos?:               string | null;
  creditaje?:                string | null;
  objetivo?:                 string | null;
  nota?:                     string | null;
  id_tipoprograma?:          number | null;
  url_video?:                string | null;
  estado?:                   number;
}
