export interface PagoAcademico {
  id_pago:             number;
  id_us_reg:           number;
  id_us:               number;
  estudiante_nombre:   string | null;
  estudiante_ci:       string | null;
  id_mat:              number | null;
  id_fechapago:        number | null;
  monto_pagado:        number | null;
  nro_boleta_bancaria: string | null;
  fecha_deposito:      string | null;
  nro_nit:             string | null;
  nombre_nit:          string | null;
  tipo_fechapago:      number | null;
  observacion_pago:    string | null;
  estado:              number;
  fecha_reg:           string | null;
  comprobante_archivo: string | null;
  estado_verificacion: 'pendiente' | 'verificado' | 'observado';
  nota_verificacion:   string | null;
}
export interface PagoAcademicoListResponse { data: PagoAcademico[]; total: number; }
export interface PagoAcademicoListParams   { pageIndex?: number; pageSize?: number; refresh?: number; }
export interface CreatePagoAcademicoPayload {
  id_us:                number;
  id_mat?:              number | null;
  id_fechapago?:        number | null;
  id_ins?:              number | null;
  monto_pagado:         number;
  nro_boleta_bancaria?: string | null;
  fecha_deposito?:      string | null;
  nro_nit?:             string | null;
  nombre_nit?:          string | null;
  tipo_fechapago?:      number | null;
  observacion_pago?:    string | null;
  estado?:              number;
}
