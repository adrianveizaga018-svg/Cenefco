export type EstadoComision = 'calculado' | 'aprobado' | 'pagado' | 'anulado';

export interface ComisionLiquidacionDetalle {
  id_ins:           number;
  id_pago:          number;
  categoria_id:     number | null;
  categoria_nombre: string | null;
  comision_monto:   number;
  fecha_deposito:   string;
}

export interface ComisionLiquidacion {
  id:                    number;
  vendedor_id:           number;
  vendedor_nombre:       string | null;
  fecha_desde:           string;
  fecha_hasta:           string;
  total_inscritos:       number | null;
  monto_comision:        number;
  estado:                EstadoComision;
  aprobado_por:          number | null;
  aprobado_at:           string | null;
  pagado_por:            number | null;
  pagado_at:             string | null;
  comprobante_pago_url:  string | null;
  nota:                  string | null;
  created_at:            string | null;
  detalle:               ComisionLiquidacionDetalle[];
}

export interface ComisionListResponse {
  data:  ComisionLiquidacion[];
  total: number;
}

export interface ComisionListParams {
  pageIndex?:   number;
  pageSize?:    number;
  vendedor_id?: number;
  estado?:      string;
}

export interface ComisionSugerida {
  vendedor_id:      number;
  vendedor_nombre:  string;
  fecha_desde:      string;
  fecha_hasta:      string;
  total_inscritos:  number;
  monto_comision:   number;
  inscritos:        ComisionLiquidacionDetalle[];
}

export interface CrearComisionPayload {
  vendedor_id: number;
  fecha_desde: string;
  fecha_hasta: string;
  nota?:       string | null;
}
