export interface PlanAcademico {
  id_plan:            number;
  id_us_reg:          number;
  titulo:             string;
  titulo_plan:        string | null;
  convenio:           string | null;
  anio:               string | null;
  numero_resolucion:  string | null;
  costo:              string | null;
  nro_cuotas:         string | null;
  descuento:          string | null;
  costo_por_cuota:    string | null;
  id_catplan:         number | null;
  estado:             number;
  fecha_reg:          string | null;
}
export interface PlanAcademicoListResponse { data: PlanAcademico[]; total: number; }
export interface PlanAcademicoListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreatePlanAcademicoPayload {
  id_plan:            number;
  titulo:             string;
  titulo_plan?:       string | null;
  convenio?:          string | null;
  anio?:              string | null;
  numero_resolucion?: string | null;
  costo?:             string | null;
  nro_cuotas?:        string | null;
  descuento?:         string | null;
  costo_por_cuota?:   string | null;
  id_catplan?:        number | null;
  estado?:            number;
}
