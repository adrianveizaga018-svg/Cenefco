export interface Curso {
  id_programa:              number;
  id_us_reg:                number;
  nombre_programa:          string;
  slug:                     string | null;
  descripcion:              string | null;
  objetivo:                 string | null;
  dirigido:                 string | null;
  requisitos:               string | null;
  inversion:                string | null;
  costo_monto:              number | null;
  creditaje:                string | null;
  nota:                     string | null;
  url_video:                string | null;
  url_whatsapp:             string | null;
  url_whatsapp2:            string | null;
  imagenes:                 string[] | null;
  foto:                     string | null;
  titulo_documento1:        string | null;
  documento1:               string | null;
  imagen_banner_url:        string | null;
  imagen_alt:               string | null;
  inicio_actividades:       string | null;
  finalizacion_actividades: string | null;
  inicio_inscripciones:     string | null;
  mes_facturacion:          string | null;
  tipo_honorario:           string | null;
  id_tipoprograma:          number | null;
  tipo_nombre:              string | null;
  id_plan:                  number | null;
  id_plandoc:               number | null;
  plan_titulo:              string | null;
  plan_titulo_alt:          string | null;
  plan_nro_cuotas:          string | null;
  plan_costo:               string | null;
  plan_costo_cuota:         string | null;
  id_imp:                   number | null;
  categoria_web_id:         number | null;
  categoria_nombre:         string | null;
  formulario_id:               number | null;
  vendedor_id:              number | null;
  vendedor_nombre:          string | null;
  estado:                   number;
  estado_web:               string;
  destacado:                boolean;
  orden:                    number;
  meta_titulo:              string | null;
  meta_descripcion:         string | null;
  mensaje_exito:            string | null;
  fecha_publicacion:        string | null;
  fecha_reg:                string | null;
  total_inscritos:          number;
  total_recaudado:          number;
}

export interface CursoListResponse {
  data:  Curso[];
  total: number;
}

export interface CursoListParams {
  query?:     string;
  pageIndex?: number;
  pageSize?:  number;
  refresh?:   number;
}

export interface CreateCursoPayload {
  nombre_programa:          string;
  slug?:                    string | null;
  descripcion?:             string | null;
  objetivo?:                string | null;
  dirigido?:                string | null;
  requisitos?:              string | null;
  inversion?:               string | null;
  costo_monto?:             number | null;
  creditaje?:               string | null;
  nota?:                    string | null;
  url_video?:               string | null;
  url_whatsapp?:            string | null;
  foto?:                    string | null;
  titulo_documento1?:       string | null;
  documento1?:              string | null;
  imagen_banner_url?:       string | null;
  imagen_alt?:              string | null;
  inicio_actividades?:      string | null;
  finalizacion_actividades?: string | null;
  inicio_inscripciones?:    string | null;
  mes_facturacion?:         string | null;
  tipo_honorario?:          string | null;
  id_tipoprograma?:         number | null;
  id_plan?:                 number | null;
  id_plandoc?:              number | null;
  id_imp?:                  number | null;
  categoria_web_id?:        number | null;
  formulario_id?:               number | null;
  estado_web?:              string;
  destacado?:               boolean;
  orden?:                   number;
  meta_titulo?:             string | null;
  meta_descripcion?:        string | null;
  mensaje_exito?:           string | null;
}

export type PeriodoEstadisticas = 'dia' | 'mes' | 'anio' | 'rango';

export interface CursoEstadisticas {
  periodo:      PeriodoEstadisticas;
  fecha_inicio: string;
  fecha_fin:    string;
  inscritos:    number;
  ingresos:     number;
}

export interface CursoEstadisticasInscrito {
  id_ins:               number;
  fecha_ins_efectiva:   string;
  estado:               number;
  canal_venta:          string;
  estudiante_nombre:    string;
  estudiante_ci:        string | null;
  curso_nombre:         string | null;
}

export interface CursoEstadisticasPago {
  id_pago:            number;
  fecha_deposito:     string;
  monto_pagado:       number;
  metodo_pago:        string;
  estudiante_nombre:  string;
  estudiante_ci:      string | null;
  curso_nombre:       string | null;
}

export interface CursoEstadisticasDetalle {
  periodo:         PeriodoEstadisticas;
  fecha_inicio:    string;
  fecha_fin:       string;
  total_inscritos: number;
  total_ingresos:  number;
  inscritos:       CursoEstadisticasInscrito[];
  pagos:           CursoEstadisticasPago[];
}

export interface CategoriaCurso {
  id:     number;
  nombre: string;
}

export interface TipoCurso {
  id_tipoprograma: number;
  nombre_tipoprograma: string;
}

export interface CategoriaCursoListResponse {
  data:  CategoriaCurso[];
  total: number;
}

export interface TipoCursoListResponse {
  data:  TipoCurso[];
  total: number;
}

export interface PlanDoc {
  id_plandoc:     number;
  titulo_plandoc: string | null;
}

export interface PlanDocListResponse {
  data:  PlanDoc[];
  total: number;
}

export interface EnvioDocumento {
  id_documento:         number;
  fecha_envio:          string;
  tipo_documento:       string | null;
  participante_nombre:  string;
  participante_ci:      string | null;
  participante_ciudad:  string | null;
  curso_nombre:         string | null;
}

export interface PlanCobrosCurso {
  id_plan:    number;
  titulo:     string;
  costo:      number;
  nro_cuotas: number;
  creado:     boolean;
}

export interface AlertaCobrosCurso {
  id_imp:    number;
  proximas:  number;
  vencidas:  number;
}

export interface ReporteEnviosDocumentos {
  fecha_inicio: string | null;
  fecha_fin:    string | null;
  total:        number;
  envios:       EnvioDocumento[];
}
