export interface PagoIngreso {
  id_pago:             number;
  id_us:               number;
  monto_pagado:        string | null;
  nro_boleta_bancaria: string | null;
  fecha_deposito:      string | null;
  observacion_pago:    string | null;
  estado:              number;
  estudiante_nombre:   string | null;
  estudiante_ci:       string | null;
  cuota_nro:           string | null;
  tipo_tramite:        string | null;
  plan_titulo:         string | null;
}

export interface IngresoListResponse {
  data:          PagoIngreso[];
  total:         number;
  total_periodo: number;
}

export interface MesIngreso {
  mes:     string;
  n_pagos: number;
  total:   number;
}

export interface IngresoResumen {
  total_general: number;
  total_mes:     number;
  total_ano:     number;
  n_pagos:       number;
  n_pagos_mes:   number;
  promedio:      number;
  meses:         MesIngreso[];
}

export interface IngresoListParams {
  pageIndex?: number;
  pageSize?:  number;
  desde?:     string;
  hasta?:     string;
  refresh?:   number;
}
