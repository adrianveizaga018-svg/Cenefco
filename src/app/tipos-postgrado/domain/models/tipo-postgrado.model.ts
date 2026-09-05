export interface TipoPostgrado {
  id_tipopost:        number;
  id_plan:            number;
  id_us_reg:          number;
  num_tipopost:       number;
  id_tipopago:        number | null;
  descuentopostgrado: string | null;
  calculo_cuota:      string | null;
  estado:             number;
  fecha_reg:          string | null;
}

export interface TipoPostgradoListResponse { data: TipoPostgrado[]; total: number; }
export interface TipoPostgradoListParams   { pageIndex?: number; pageSize?: number; id_plan?: number; refresh?: number; }
export interface CreateTipoPostgradoPayload {
  id_tipopost:         number;
  id_plan:             number;
  num_tipopost?:       number;
  id_tipopago?:        number | null;
  descuentopostgrado?: string | null;
  calculo_cuota?:      string | null;
  estado?:             number;
}
