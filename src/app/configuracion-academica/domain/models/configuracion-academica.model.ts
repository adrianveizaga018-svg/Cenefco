export interface ConfiguracionAcademica {
  id_conf:                 number;
  id_us_reg:               number;
  num_conf:                number;
  gestion:                 string | null;
  periodo_est:             string | null;
  gestion_est:             string | null;
  max_materias_cursar:     string | null;
  id_plan:                 number | null;
  id_plan_anterior:        number | null;
  periodo_doc:             string | null;
  gestion_doc:             string | null;
  correlativo:             string | null;
  nombre_kardista:         string | null;
  nombre_director:         string | null;
  titulo_carrera:          string | null;
  descripcion_resolucion:  string | null;
  cod_codigo:              string | null;
  lugar_x:                 string | null;
  carrera:                 string | null;
  area:                    string | null;
  periodo:                 string | null;
  estado:                  number;
  fecha_reg:               string | null;
}

export interface ConfiguracionAcademicaListResponse { data: ConfiguracionAcademica[]; total: number; }
export interface ConfiguracionAcademicaListParams   { pageIndex?: number; pageSize?: number; gestion?: string; refresh?: number; }
export interface CreateConfiguracionAcademicaPayload {
  id_conf:                  number;
  gestion?:                 string | null;
  periodo_est?:             string | null;
  gestion_est?:             string | null;
  max_materias_cursar?:     string | null;
  id_plan?:                 number | null;
  id_plan_anterior?:        number | null;
  periodo_doc?:             string | null;
  gestion_doc?:             string | null;
  correlativo?:             string | null;
  nombre_kardista?:         string | null;
  nombre_director?:         string | null;
  titulo_carrera?:          string | null;
  descripcion_resolucion?:  string | null;
  cod_codigo?:              string | null;
  lugar_x?:                 string | null;
  carrera?:                 string | null;
  area?:                    string | null;
  periodo?:                 string | null;
  estado?:                  number;
}
