export type EstadoComision = 'calculado' | 'aprobado' | 'pagado' | 'anulado';

/** Estado de la comisión de una venta según la tabla de comisiones. */
export type EstadoComisionVenta = 'tabla' | 'acordada' | 'por_negociar' | 'sin_tipo' | 'fuera_de_tabla';

export interface ComisionLiquidacionDetalle {
  id_ins:           number;
  id_pago:          number;
  /** Tipo de producto de la tabla de comisiones */
  categoria_id:     number | null;
  categoria_nombre: string | null;
  /** null mientras no se acuerde (negociación, sin tipo o fuera de la tabla) */
  comision_monto:   number | null;
  fecha_deposito:   string;
  estado_comision?: EstadoComisionVenta | null;
  regla?:           string | null;
  precio?:          number | null;
  pagado?:          number | null;
  estudiante?:      string | null;
  programa?:        string | null;
  vendedor_id?:     number | null;
  vendedor_nombre?: string | null;
  motivo_acuerdo?:  string | null;
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
  /** Ventas del periodo que esperan un monto acordado */
  pendientes?:      ComisionLiquidacionDetalle[];
}

export interface CrearComisionPayload {
  vendedor_id: number;
  fecha_desde: string;
  fecha_hasta: string;
  nota?:       string | null;
}
