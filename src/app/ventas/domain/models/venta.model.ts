export type CanalVenta = 'admin' | 'portal' | 'whatsapp' | 'referido';
export type MetodoPago = 'efectivo' | 'deposito_bancario' | 'pago_online' | 'qr';

export interface Venta {
  id_ins:             number;
  id_us:              number;
  id_imp:             number;
  id_programa:        number | null;
  id_plan:            number | null;
  periodo:            string | null;
  gestion:            number | null;
  fecha_ins:          string | null;
  estado:             number;
  fecha_reg:          string | null;
  estudiante_nombre:  string | null;
  estudiante_ci:      string | null;
  estudiante_celular: string | null;
  estudiante_email:   string | null;
  nombre_programa:    string | null;
  programa_slug:      string | null;
  total_a_pagar:      number;
  total_pagado:       number;
  saldo_pendiente:    number;
  nro_pagos:          number;
  estado_pago:        'pagado' | 'parcial' | 'pendiente';
  id_vendedor:        number | null;
  canal_venta:        CanalVenta | null;
  vendedor_nombre:    string | null;
}

export interface PagoDetalle {
  id_pago:             number;
  monto_pagado:        string | null;
  nro_boleta_bancaria: string | null;
  fecha_deposito:      string | null;
  nro_nit:             string | null;
  nombre_nit:          string | null;
  observacion_pago:    string | null;
  fecha_reg:           string | null;
  nro_pago:            number | null;
  monto_a_pagar:       string | null;
  metodo_pago:         MetodoPago | null;
  id_us_cajero:        number | null;
}

export interface VentaDetalle extends Venta {
  pagos: PagoDetalle[];
}

export interface VentaListResponse {
  data:  Venta[];
  total: number;
}

export interface VentaReporteResponse {
  data:    Venta[];
  totales: VentaTotales;
}

export interface VentaTotales {
  total_registros: number;
  total_a_pagar:   number;
  total_pagado:    number;
  saldo_pendiente: number;
  pagados:         number;
  parciales:       number;
  pendientes:      number;
}

export interface VentaListParams {
  pageIndex?:    number;
  pageSize?:     number;
  query?:        string;
  estado_pago?:  string;
  periodo?:      string;
  gestion?:      number;
  conInactivos?: boolean;
  id_vendedor?:  number;
  canal_venta?:  string;
}

export interface RegistrarPagoVentaPayload {
  id_us:                number;
  id_ins:               number;
  id_fechapago?:        number | null;
  monto_pagado:         number;
  metodo_pago:          MetodoPago;
  id_us_cajero?:        number | null;
  nro_boleta_bancaria?: string | null;
  fecha_deposito?:      string | null;
  observacion_pago?:    string | null;
}
