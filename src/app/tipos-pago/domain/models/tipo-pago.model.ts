export interface TipoPago {
  id_tipopago:          number;
  id_us_reg:            number;
  titulo:               string;
  grupo_pago:           string | null;
  id_categoriatipopago: number | null;
  estado:               number;
}

export interface TipoPagoListResponse { data: TipoPago[]; total: number; }
export interface TipoPagoListParams   { query?: string; pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreateTipoPagoPayload {
  id_tipopago:          number;
  titulo:               string;
  grupo_pago?:          string;
  id_categoriatipopago?: number;
  estado?:              number;
}
