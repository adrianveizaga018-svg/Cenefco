export interface FechaPago {
  id_fechapago:  number;
  id_plan:       number;
  plan_titulo:   string | null;
  id_us_reg:     number;
  num_fechapago: number;
  nro_pago:      string | null;
  id_tipopago:   number | null;
  tipo_tramite:  string | null;
  monto_a_pagar: number | null;
  fecha_inicio:  string | null;
  fecha_fin:     string | null;
  obligatorio:   number;
  tipo_fechapago: number | null;
  estado:        number;
  fecha_reg:     string | null;
}
export interface FechaPagoListResponse { data: FechaPago[]; total: number; }
export interface FechaPagoListParams   { id_plan?: number; pageIndex?: number; pageSize?: number; refresh?: number; conInactivos?: boolean; }
export interface CreateFechaPagoPayload {
  id_plan:        number;
  nro_pago?:      string | null;
  tipo_tramite?:  string | null;
  monto_a_pagar:  number;
  fecha_inicio?:  string | null;
  fecha_fin?:     string | null;
  obligatorio?:   number;
  estado?:        number;
}
